<?php
/**
 * Configuration metadata for aiassist plugin
 *
 * @author moontwt <moontwt@example.com>
 */

$meta['openrouter_api_key'] = array('password', '_caution' => 'security');
$meta['openai_model'] = array('multichoice', '_choices' => array(
    'openrouter/auto' => 'openrouter/auto',
    'mistralai/mistral-large-2411' => 'mistralai/mistral-large-2411',
    'openai/gpt-4o' => 'openai/gpt-4o',
    'openai/gpt-4o-mini' => 'openai/gpt-4o-mini',
    'anthropic/claude-sonnet-4' => 'anthropic/claude-sonnet-4',
    'google/gemini-2.0-flash-001' => 'google/gemini-2.0-flash-001',
    'google/gemini-2.5-flash-preview-05-20' => 'google/gemini-2.5-flash-preview-05-20'
));
$meta['max_tokens'] = array('numeric', '_min' => 100, '_max' => 120000);
$meta['temperature'] = array('numeric', '_min' => 0, '_max' => 1, '_pattern' => '/^[0-9]*\.?[0-9]+$/');
$meta['toolbar_priority'] = array('numeric', '_min' => 0, '_max' => 99999);
$meta['context_length'] = array('numeric', '_min' => 100, '_max' => 2000);