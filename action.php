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
            'title' => $this->getLang('ai_assistant'), // 툴팁용
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
            
            // Prepare language strings for JavaScript
            $jsLang = array(
                'no_text_selected_confirm' => $this->getLang('no_text_selected_confirm'),
                'no_text_to_process' => $this->getLang('no_text_to_process'),
                'processing' => $this->getLang('processing'),
                'processing_with_request' => $this->getLang('processing_with_request'),
                'error' => $this->getLang('error'),
                'error_ai_processing' => $this->getLang('error_ai_processing'),
                'error_server_request' => $this->getLang('error_server_request'),
                'error_no_improved_text' => $this->getLang('error_no_improved_text'),
                'preview_title' => $this->getLang('preview_title'),
                'original_text' => $this->getLang('original_text'),
                'improved_text' => $this->getLang('improved_text'),
                'apply_changes' => $this->getLang('apply_changes'),
                'cancel' => $this->getLang('cancel'),
                'confirm' => $this->getLang('confirm')
            );
            
            echo '<script>window.AIASSIST_CONTEXT_LENGTH = ' . $contextLength . ';</script>' . "\n";
            echo '<script>window.AIASSIST_LANG = ' . json_encode($jsLang, JSON_UNESCAPED_UNICODE) . ';</script>' . "\n";
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
                echo json_encode(['error' => $this->getLang('error_no_text_selected')]);
                exit;
            }
            
            // 최소 길이 검증 (1글자 이상)
            if (strlen(trim($selectedText)) < 1) {
                http_response_code(400);
                echo json_encode(['error' => $this->getLang('error_text_too_short')]);
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
     * Process text with OpenRouter API
     */
    private function processWithAI($selectedText, $beforeText, $afterText)
    {
        // OpenRouter API 키 확인
        $apiKey = $this->getConf('openrouter_api_key');
        if (empty($apiKey)) {
            return ['error' => $this->getLang('error_openrouter_key_not_configured')];
        }
        
        $apiEndpoint = 'https://openrouter.ai/api/v1/chat/completions';
        
        // 요청사항 처리 로직 추가
        $customRequest = $this->extractCustomRequest($selectedText);
        $prompt = $this->buildPrompt($selectedText, $beforeText, $afterText, $customRequest);
        
        // Get configuration settings with safer defaults
        $rawModel = $this->getConf('openai_model') ?: 'gpt-3.5-turbo';
        $maxTokens = (int)($this->getConf('max_tokens') ?: 1000);  // Much safer default
        $temperature = (float)($this->getConf('temperature') ?: 0.3);
        
        // OpenRouter 모델명 정규화 및 검증
        $model = $rawModel ?: 'openrouter/auto';
        
        // 일반적인 수정만 수행
        $openrouterCorrections = [
            'auto' => 'openrouter/auto',
            'gpt-4o' => 'openai/gpt-4o',
            'gpt-4o-mini' => 'openai/gpt-4o-mini',
            'gpt-4' => 'openai/gpt-4',
            'gpt-3.5-turbo' => 'openai/gpt-3.5-turbo',
            'claude-3.5-sonnet' => 'anthropic/claude-3.5-sonnet',
            'claude-sonnet-4' => 'anthropic/claude-sonnet-4',
            'mistral-large-2411' => 'mistralai/mistral-large-2411',
            'gemini-pro' => 'google/gemini-pro',
            'gemini-2.0-flash-001' => 'google/gemini-2.0-flash-001',
            'gemini-2.5-flash-preview-05-20' => 'google/gemini-2.5-flash-preview-05-20'
        ];
        
        if (isset($openrouterCorrections[$rawModel])) {
            $model = $openrouterCorrections[$rawModel];
        }
        
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
                    'content' => '당신은 DokuWiki 공식 문법 전문가입니다. 핸심 원칙: 1) 원본 내용과 의미 100% 보존 2) 모든 중요한 정보 누락 금지 3) 목록문법 엄격 적용: "  * 항목"(번호없는), "  - 항목"(번호), 하위목록 2칸 추가들여쓰기 4) 모든 "1.", "2." 형태를 DokuWiki 목록으로 완전변환 5) 기존 DokuWiki 문법 보존: >, ^, [[]], {{}}, **, //, __ 등 6) 들여쓰기 일관성 7) 맞춤법 수정 8) 원본 의미/고유명사 보존 9) 예외 없이 모든 번호목록 변환! 10) 불필요한 설명이나 안내문 추가 금지!'
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
            return ['error' => $this->getLang('error_json_encoding_failed') . json_last_error_msg()];
        }
        
        // OpenRouter에 특정 헤더 추가
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json; charset=utf-8',
            'HTTP-Referer: https://dokuwiki.org', // 애플리케이션 식별용
            'X-Title: DokuWiki AI Assistant' // 애플리케이션 이름
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            return ['error' => $this->getLang('error_curl_error') . $curlError];
        }
        
        if ($httpCode !== 200) {
            $errorMsg = $this->getLang('error_http') . $httpCode;
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
            return ['error' => $this->getLang('error_json_decode') . json_last_error_msg()];
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
            
            // AI 응답 검증 - 내용 보존 및 의미 유지 확인
            $improvedText = trim($improvedText);
            
            // 금지된 문구 검사 (AI가 불필요한 안내문 추가하는 것 방지)
            $forbiddenPhrases = ['AI 어시스턴트', 'AI Assistant', '도움말', '안내', '참고', '요약하면', '결론적으로'];
            foreach ($forbiddenPhrases as $phrase) {
                if (mb_strpos($improvedText, $phrase) !== false && mb_strpos($selectedText, $phrase) === false) {
                    return ['error' => $this->getLang('error_ai_added_unnecessary_text'), 'improved_text' => $selectedText];
                }
            }
            
            // 빈 결과 검사
            if (empty($improvedText)) {
                return ['error' => $this->getLang('error_ai_response_empty'), 'improved_text' => $selectedText];
            }
            
            return ['improved_text' => $improvedText];
        } else {
            return ['error' => $this->getLang('error_invalid_api_response')];
        }
    }

    /**
     * Extract custom request from selected text
     */
    private function extractCustomRequest($selectedText)
    {
        // 요청사항: 또는 request: 형태의 패턴을 찾아서 추출
        $patterns = [
            '/요청사항\s*:\s*(.+?)(?=\n|$)/u',    // Korean: 요청사항:
            '/request\s*:\s*(.+?)(?=\n|$)/iu'     // English: request: (case insensitive)
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $selectedText, $matches)) {
                return trim($matches[1]);
            }
        }
        return null;
    }
    
    /**
     * Remove custom request from text
     */
    private function removeCustomRequest($text)
    {
        // 요청사항: 또는 request: 라인 전체를 제거
        $patterns = [
            '/요청사항\s*:\s*.+?(?=\n|$)\n?/u',    // Korean: 요청사항:
            '/request\s*:\s*.+?(?=\n|$)\n?/iu'     // English: request: (case insensitive)
        ];
        
        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }
        return $text;
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
            return ['error' => $this->getLang('error_empty_text')];
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
            $prompt .= "7. '요청사항:' 또는 'request:' 부분은 최종 결과에 포함하지 마세요\n\n";
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