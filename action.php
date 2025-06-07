<?php
/**
 * DokuWiki Plugin aiassist (Action Component)
 *
 * @author moontwt <moontwt@example.com>
 */

if (!defined('DOKU_INC')) die();

class action_plugin_aiassist extends DokuWiki_Action_Plugin
{
    /**
     * Registers a callback function for a given event
     */
    public function register(Doku_Event_Handler $controller)
    {
        // 설정 가능한 우선순위로 등록하여 가장 끝에 버튼이 추가되도록 함 (숫자가 클수록 나중에 실행)
        $priority = (int)($this->getConf('toolbar_priority') ?: 9999);
        $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insertButton', array(), $priority);
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'addScript', array());
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handleAjax', array());
    }

    /**
     * Add AI button to toolbar
     */
    public function insertButton(&$event, $param)
    {
        $event->data[] = array(
            'type' => 'format',                    // 지원되는 타입으로 복원
            'title' => 'AI Text Assistant',       // 툴팁용
            'icon' => '../../plugins/aiassist/images/ai-icon.png',
            'key' => null,                         // 키보드 단축키 비활성화
            'close' => '',                         // 텍스트 삽입 방지
            'sample' => '',                        // 텍스트 삽입 방지  
            'insert' => '',                        // 텍스트 삽입 방지
            'open' => '',                          // 텍스트 삽입 방지
            'class' => 'aiassist_button',          // 고유 클래스
            'block' => false,
        );
    }

    /**
     * Add JavaScript and CSS to page
     */
    public function addScript(&$event, $param)
    {
        global $ACT;
        if ($ACT == 'edit') {
            $timestamp = time() . '_' . mt_rand(1000, 9999); // Strong cache busting
            $contextLength = (int)($this->getConf('context_length') ?: 500);
            echo '<script>window.AIASSIST_CONTEXT_LENGTH = ' . $contextLength . ';</script>' . "\n";
            echo '<script src="' . DOKU_BASE . 'lib/plugins/aiassist/script.js?v=' . $timestamp . '"></script>' . "\n";
            echo '<link rel="stylesheet" href="' . DOKU_BASE . 'lib/plugins/aiassist/style.css?v=' . $timestamp . '">' . "\n";
        }
    }

    /**
     * Handle AJAX requests
     */
    public function handleAjax(&$event, $param)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && 
            isset($_POST['call']) && $_POST['call'] == 'aiassist') {
            
            $event->preventDefault();
            $event->stopPropagation();
            
            // Get selected text and context with proper encoding
            $selectedText = trim($_POST['selected_text'] ?? '');
            $beforeText = $_POST['before_text'] ?? '';
            $afterText = $_POST['after_text'] ?? '';
            
            // Clean UTF-8 encoding - remove invalid sequences
            $selectedText = mb_convert_encoding($selectedText, 'UTF-8', 'UTF-8');
            $beforeText = mb_convert_encoding($beforeText, 'UTF-8', 'UTF-8');
            $afterText = mb_convert_encoding($afterText, 'UTF-8', 'UTF-8');
            
            // Additional cleanup - remove control characters except line breaks
            $selectedText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $selectedText);
            $beforeText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $beforeText);
            $afterText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $afterText);
            
            // 더 엄격한 텍스트 검증
            if (empty($selectedText) || trim($selectedText) === '') {
                http_response_code(400);
                echo json_encode(['error' => 'No text selected']);
                exit;
            }
            
            // 최소 길이 검증 (1글자 이상)
            if (strlen(trim($selectedText)) < 1) {
                http_response_code(400);
                echo json_encode(['error' => 'Selected text is too short']);
                exit;
            }
            
            // Process with AI
            $result = $this->processWithAI($selectedText, $beforeText, $afterText);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }

    /**
     * Process text with OpenAI API
     */
    private function processWithAI($selectedText, $beforeText, $afterText)
    {
        $apiKey = $this->getConf('openai_api_key');
        if (empty($apiKey)) {
            return ['error' => 'OpenAI API key not configured'];
        }
        
        // 요청사항 처리 로직 추가
        $customRequest = $this->extractCustomRequest($selectedText);
        $prompt = $this->buildPrompt($selectedText, $beforeText, $afterText, $customRequest);
        
        // Get configuration settings with safer defaults
        $rawModel = $this->getConf('openai_model') ?: 'gpt-3.5-turbo';
        $maxTokens = (int)($this->getConf('max_tokens') ?: 1000);  // Much safer default
        $temperature = (float)($this->getConf('temperature') ?: 0.3);
        
        // 모델명 정규화 및 검증
        $validModels = [
            'gpt-3.5-turbo' => 'gpt-3.5-turbo',
            'gpt-4' => 'gpt-4',
            'gpt-4-turbo' => 'gpt-4-turbo',
            'gpt-4o' => 'gpt-4o',
            'gpt-4o-mini' => 'gpt-4o-mini',
            // 잘못된 모델명 자동 수정
            '4o' => 'gpt-4o',
            'gpt4o' => 'gpt-4o',
            '4o-mini' => 'gpt-4o-mini',
            'gpt4o-mini' => 'gpt-4o-mini',
            'turbo' => 'gpt-3.5-turbo',
            'gpt-turbo' => 'gpt-3.5-turbo'
        ];
        
        $model = isset($validModels[$rawModel]) ? $validModels[$rawModel] : 'gpt-4o';
        
        // Validate completion token limits for each model
        $completionTokenLimits = [
            'gpt-3.5-turbo' => 4096,      // max completion tokens
            'gpt-4' => 4096,              // max completion tokens  
            'gpt-4-turbo' => 4096,        // max completion tokens
            'gpt-4o' => 4096,             // max completion tokens
            'gpt-4o-mini' => 16384        // max completion tokens
        ];
        
        // Adjust max tokens if exceeds model's completion limit
        if (isset($completionTokenLimits[$model])) {
            $maxTokens = min($maxTokens, $completionTokenLimits[$model]);
        }
        
        // Adjust for GPT-4o: allow more tokens for better results
        $maxTokens = min($maxTokens, 2000);
        
        $data = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => '당신은 DokuWiki 공식 문법 전문가입니다. 규칙: 1) 목록문법 엄격 적용: "  * 항목"(번호없는), "  - 항목"(번호), 하위목록 2칸 추가들여쓰기 2) 모든 "1.", "2." 형태를 DokuWiki 목록으로 완전변환 3) 기존 DokuWiki 문법 보존: >, ^, [[]], {{}}, **, //, __ 등 4) 들여쓰기 일관성 5) 맞춤법 수정 6) 원본 의미/고유명사 보존 7) 예외 없이 모든 번호목록 변환!'
                ],
                [
                    'role' => 'user', 
                    'content' => $prompt
                ]
            ],
            'max_tokens' => $maxTokens,
            'temperature' => $temperature
        ];
        
        // Additional UTF-8 cleanup before JSON encoding
        array_walk_recursive($data, function(&$item) {
            if (is_string($item)) {
                // Clean UTF-8 encoding
                $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                // Remove control characters
                $item = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $item);
                // Validate UTF-8
                if (!mb_check_encoding($item, 'UTF-8')) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'auto');
                }
            }
        });
        
        // Ensure proper UTF-8 encoding
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if ($jsonData === false) {
            return ['error' => 'JSON encoding failed: ' . json_last_error_msg()];
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json; charset=utf-8'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            return ['error' => 'cURL Error: ' . $curlError];
        }
        
        if ($httpCode !== 200) {
            $errorMsg = 'HTTP ' . $httpCode;
            if ($response) {
                $errorData = json_decode($response, true);
                if (isset($errorData['error']['message'])) {
                    $errorMsg .= ': ' . $errorData['error']['message'];
                }
            }
            return ['error' => $errorMsg];
        }
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'JSON decode error: ' . json_last_error_msg()];
        }
        
        if (isset($result['choices'][0]['message']['content'])) {
            $improvedText = trim($result['choices'][0]['message']['content']);
            
            // 불필요한 마커 제거
            $improvedText = preg_replace('/<<<[^>]*>>>/u', '', $improvedText);
            $improvedText = preg_replace('/```[^`]*```/u', '', $improvedText);
            $improvedText = preg_replace('/\*\*[^*]*\*\*/u', '', $improvedText);
            $improvedText = preg_replace('/^수정된?\s*텍스트:?\s*/iu', '', $improvedText);
            $improvedText = preg_replace('/^결과:?\s*/iu', '', $improvedText);
            
            // 요청사항: 부분이 남아있다면 제거
            $improvedText = $this->removeCustomRequest($improvedText);
            
            $improvedText = trim($improvedText);
            
            // AI 응답 검증 - 임의 텍스트 추가 방지
            $originalLength = mb_strlen($selectedText, 'UTF-8');
            $improvedLength = mb_strlen($improvedText, 'UTF-8');
            
            // 길이가 원본보다 30% 이상 늘어나면 거부
            if ($improvedLength > $originalLength * 1.3) {
                return ['error' => 'AI가 원본보다 너무 많은 내용을 추가했습니다. 원본 텍스트만 사용합니다.', 'improved_text' => $selectedText];
            }
            
            // 금지된 문구 검사
            $forbiddenPhrases = ['AI 어시스턴트', 'AI Assistant', '도움말', '안내', '참고'];
            foreach ($forbiddenPhrases as $phrase) {
                if (mb_strpos($improvedText, $phrase) !== false && mb_strpos($selectedText, $phrase) === false) {
                    return ['error' => 'AI가 금지된 내용을 추가했습니다. 원본 텍스트만 사용합니다.', 'improved_text' => $selectedText];
                }
            }
            
            return ['improved_text' => $improvedText];
        } else {
            return ['error' => 'Invalid API response format'];
        }
    }

    /**
     * Extract custom request from selected text
     */
    private function extractCustomRequest($selectedText)
    {
        // 요청사항: 형태의 패턴을 찾아서 추출
        $pattern = '/요청사항\s*:\s*(.+?)(?=\n|$)/u';
        if (preg_match($pattern, $selectedText, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
    
    /**
     * Remove custom request from text
     */
    private function removeCustomRequest($text)
    {
        // 요청사항: 라인 전체를 제거
        $pattern = '/요청사항\s*:\s*.+?(?=\n|$)\n?/u';
        return preg_replace($pattern, '', $text);
    }

    /**
     * Build prompt for AI processing
     */
    private function buildPrompt($selectedText, $beforeText, $afterText, $customRequest = null)
    {
        // UTF-8 인코딩 정리
        $selectedText = mb_convert_encoding($selectedText, 'UTF-8', 'UTF-8');
        $beforeText = mb_convert_encoding($beforeText, 'UTF-8', 'UTF-8');
        $afterText = mb_convert_encoding($afterText, 'UTF-8', 'UTF-8');
        
        // 제어 문자 제거
        $selectedText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $selectedText);
        $beforeText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $beforeText);
        $afterText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $afterText);
        
        // 선택된 텍스트 재검증
        if (empty(trim($selectedText))) {
            return ['error' => 'Empty text cannot be processed'];
        }
        
        // 요청사항이 있는 경우와 없는 경우 구분하여 처리
        if ($customRequest) {
            // 요청사항:내용이 있는 경우 - 맞춤 프롬프트
            $cleanText = $this->removeCustomRequest($selectedText);
            
            $prompt = "다음 텍스트를 주어진 요청사항에 따라 수정해주세요:\n\n";
            $prompt .= "**원본 텍스트:**\n";
            $prompt .= $cleanText . "\n\n";
            $prompt .= "**요청사항:**\n";
            $prompt .= $customRequest . "\n\n";
            $prompt .= "**수정 규칙:**\n";
            $prompt .= "1. 요청사항을 정확히 반영하여 텍스트를 수정하세요\n";
            $prompt .= "2. DokuWiki 공식 문법을 준수하세요\n";
            $prompt .= "3. 기존 DokuWiki 문법 완전 보존: '**굵게**', '//기울임//', '__밑줄__', '[[링크]]', '{{이미지}}', '^|' 표 등\n";
            $prompt .= "4. 목록 문법: '  * 항목' (번호없는), '  - 항목' (번호), 하위목록은 2칸 더 들여쓰기\n";
            $prompt .= "5. 맞춤법과 띄어쓰기도 함께 수정하세요\n";
            $prompt .= "6. 원본의 의미와 고유명사는 보존하세요\n";
            $prompt .= "7. '요청사항:' 부분은 최종 결과에 포함하지 마세요\n\n";
            $prompt .= "수정된 텍스트:";
        } else {
            // 기존의 일반적인 최적화 프롬프트
            $prompt = "다음 텍스트를 DokuWiki 공식 문법에 맞게 최적화하고 맞춤법을 수정해주세요:\n\n";
            $prompt .= $selectedText . "\n\n";
            $prompt .= "DokuWiki 공식 문법 최적화 규칙:\n\n";
            $prompt .= "**목록 문법 (중요!):**\n";
            $prompt .= "- 번호 없는 목록: '  * 항목' (스페이스 2칸 + 별표)\n";
            $prompt .= "- 번호 목록: '  - 항목' (스페이스 2칸 + 대시)\n";
            $prompt .= "- 하위 목록: 상위보다 2칸 더 들여쓰기 (예: '    * 하위항목')\n";
            $prompt .= "- 모든 '1.', '2.', '3.' 형태를 반드시 DokuWiki 문법으로 변환\n\n";
            $prompt .= "**기존 DokuWiki 문법 완전 보존:**\n";
            $prompt .= "- 인용문: '>', '>>', '>>>' 유지\n";
            $prompt .= "- 제목: '=====제목=====' 형식 유지\n";
            $prompt .= "- 텍스트 서식: '**굵게**', '//기울임//', '__밑줄__' 유지\n";
            $prompt .= "- 링크: '[[링크]]', '[[링크|표시명]]' 유지\n";
            $prompt .= "- 코드: '<code>', '<file>', 들여쓰기 유지\n";
            $prompt .= "- 그림: '{{이미지}}' 유지\n";
            $prompt .= "- 표: '^'(헤더), '|'(셀) 유지\n\n";
            $prompt .= "**문서 개선:**\n";
            $prompt .= "- 맞춤법, 띄어쓰기, 문법 수정\n";
            $prompt .= "- 들여쓰기 일관성 확보\n";
            $prompt .= "- URL, 이메일, 회사명 등 고유명사 보존\n";
            $prompt .= "- 원본 내용과 의미 완전 보존\n\n";
            $prompt .= "**변환 예시:**\n";
            $prompt .= "'1. 회원가입 및 로그인' → '    * 회원가입 및 로그인'\n";
            $prompt .= "'2. 온라인교육 선택' → '    * 온라인교육 선택'\n";
            $prompt .= "'   - 세부사항' → '      - 세부사항'\n\n";
            $prompt .= "DokuWiki 최적화 결과:";
        }
        
        return $prompt;
    }
}