<?php
/**
 * Configuration metadata for aiassist plugin
 *
 * @author moontwt <moontwt@example.com>
 */

$meta['openai_api_key'] = array('password', '_caution' => 'security');
$meta['openai_model'] = array('multichoice', '_choices' => array(
    'gpt-3.5-turbo' => 'GPT-3.5 Turbo [최대 16K 토큰] - 빠르고 저렴',
    'gpt-4' => 'GPT-4 [최대 8K 토큰] - 정확하지만 비쌈',
    'gpt-4-turbo' => 'GPT-4 Turbo [최대 128K 토큰] - 균형잡힌 성능',
    'gpt-4o' => 'GPT-4o [최대 128K 토큰] - 최신 모델',
    'gpt-4o-mini' => 'GPT-4o Mini [최대 128K 토큰] - 경제적'
));
$meta['max_tokens'] = array('numeric', '_min' => 100, '_max' => 120000);
$meta['temperature'] = array('numeric', '_min' => 0, '_max' => 1, '_pattern' => '/^[0-9]*\.?[0-9]+$/');
$meta['toolbar_priority'] = array('numeric', '_min' => 0, '_max' => 99999);
$meta['context_length'] = array('numeric', '_min' => 100, '_max' => 2000);