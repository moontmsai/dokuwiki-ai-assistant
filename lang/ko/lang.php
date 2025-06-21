<?php
/**
 * Korean language file for aiassist plugin
 *
 * @author moontwt <moontwt@example.com>
 */

$lang['ai_assistant'] = 'AI 어시스턴트';
$lang['select_text_first'] = '먼저 텍스트를 선택해주세요';
$lang['processing'] = 'AI가 텍스트를 처리하고 있습니다...';
$lang['error_no_api_key'] = 'OpenAI API 키가 설정되지 않았습니다';
$lang['error_api_request'] = 'OpenAI API 요청이 실패했습니다';
$lang['original_text'] = '원본 텍스트:';
$lang['improved_text'] = 'AI 개선된 텍스트:';
$lang['apply_changes'] = '변경사항 적용';
$lang['cancel'] = '취소';

// JavaScript에서 사용하는 추가 문자열
$lang['no_text_selected_confirm'] = '텍스트가 선택되지 않았습니다. 전체 텍스트를 AI로 개선하시겠습니까?';
$lang['no_text_to_process'] = '처리할 텍스트가 없습니다.';
$lang['processing_with_request'] = '요청사항을 반영하여 AI가 텍스트를 수정하고 있습니다...';
$lang['error'] = '오류';
$lang['error_ai_processing'] = 'AI 처리 중 오류가 발생했습니다: ';
$lang['error_server_request'] = '서버 요청이 실패했습니다: ';
$lang['error_no_improved_text'] = 'AI 응답에 개선된 텍스트가 없습니다.';
$lang['preview_title'] = 'AI 텍스트 개선 미리보기';
$lang['confirm'] = '확인';

// PHP 에러 메시지
$lang['error_no_text_selected'] = '선택된 텍스트가 없습니다';
$lang['error_text_too_short'] = '선택된 텍스트가 너무 짧습니다';
$lang['error_openrouter_key_not_configured'] = 'OpenRouter API 키가 설정되지 않았습니다';
$lang['error_json_encoding_failed'] = 'JSON 인코딩 실패: ';
$lang['error_curl_error'] = 'cURL 오류: ';
$lang['error_http'] = 'HTTP ';
$lang['error_json_decode'] = 'JSON 디코드 오류: ';
$lang['error_invalid_api_response'] = '잘못된 API 응답 형식';
$lang['error_ai_added_unnecessary_text'] = 'AI가 불필요한 안내문을 추가했습니다. 원본 텍스트를 사용합니다.';
$lang['error_ai_response_empty'] = 'AI 응답이 비어있습니다. 원본 텍스트를 사용합니다.';
$lang['error_empty_text'] = '빈 텍스트는 처리할 수 없습니다';