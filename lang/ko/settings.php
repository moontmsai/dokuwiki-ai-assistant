<?php
/**
 * Korean language file for aiassist plugin settings
 *
 * @author moontwt <moontwt@example.com>
 */

$lang['openai_api_key'] = 'OpenAI API 키';
$lang['openai_model'] = 'OpenAI 모델 선택<br/><small>• gpt-3.5-turbo: 최대 16K 토큰 (빠르고 저렴)<br/>• gpt-4: 최대 8K 토큰 (정확하지만 비쌈)<br/>• gpt-4-turbo: 최대 128K 토큰 (균형잡힌 성능)<br/>• gpt-4o: 최대 128K 토큰 (최신 모델)<br/>• gpt-4o-mini: 최대 128K 토큰 (경제적)</small>';
$lang['max_tokens'] = '최대 토큰 수 (100-120,000)<br/><small>선택한 모델의 한도를 확인하세요. 높을수록 비용 증가</small>';
$lang['temperature'] = '온도 (0-1, 낮을수록 일관적, 높을수록 창의적)';
$lang['toolbar_priority'] = '툴바 우선순위 (0-99999)<br/><small>숫자가 클수록 더 뒤에 위치. 다른 플러그인보다 뒤에 두려면 큰 값 사용</small>';
$lang['context_length'] = '컨텍스트 길이 (100-2000)<br/><small>선택 텍스트 앞뒤로 참고할 글자 수. 클수록 더 정확하지만 비용 증가</small>';