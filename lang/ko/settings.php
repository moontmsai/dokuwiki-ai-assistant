<?php
/**
 * Korean language file for aiassist plugin settings
 *
 * @author moontwt <moontwt@example.com>
 */

$lang['openrouter_api_key'] = 'OpenRouter API 키<br/><small>openrouter.ai에서 발급받은 API 키를 입력하세요</small>';
$lang['openai_model'] = 'AI 모델 선택<br/><small><strong>모델 설명:</strong><br/>• <strong>openrouter/auto</strong> - 자동 최적 모델 선택 (기본 추천)<br/>• <strong>mistralai/mistral-large-2411</strong> - 고성능 유럽 모델<br/>• <strong>openai/gpt-4o</strong> - OpenAI 최신 모델<br/>• <strong>openai/gpt-4o-mini</strong> - 경제적 선택<br/>• <strong>anthropic/claude-sonnet-4</strong> - Claude 최신 모델<br/>• <strong>google/gemini-2.0-flash-001</strong> - Gemini 2.0<br/>• <strong>google/gemini-2.5-flash-preview-05-20</strong> - Gemini 2.5 프리뷰</small>';
$lang['max_tokens'] = '최대 토큰 수 (100-120,000)<br/><small>선택한 모델의 한도를 확인하세요. 높을수록 비용 증가</small>';
$lang['temperature'] = '온도 (0-1, 낮을수록 일관적, 높을수록 창의적)';
$lang['toolbar_priority'] = '툴바 우선순위 (0-99999)<br/><small>숫자가 클수록 더 뒤에 위치. 다른 플러그인보다 뒤에 두려면 큰 값 사용</small>';
$lang['context_length'] = '컨텍스트 길이 (100-2000)<br/><small>선택 텍스트 앞뒤로 참고할 글자 수. 클수록 더 정확하지만 비용 증가</small>';