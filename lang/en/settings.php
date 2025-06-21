<?php
/**
 * English language file for aiassist plugin settings
 *
 * @author moontwt <moontwt@example.com>
 */

$lang['openrouter_api_key'] = 'OpenRouter API Key<br/><small>Enter your API key obtained from openrouter.ai</small>';
$lang['openai_model'] = 'AI Model Selection<br/><small><strong>Model Descriptions:</strong><br/>• <strong>openrouter/auto</strong> - Auto optimal model selection (recommended)<br/>• <strong>mistralai/mistral-large-2411</strong> - High-performance European model<br/>• <strong>openai/gpt-4o</strong> - OpenAI latest model<br/>• <strong>openai/gpt-4o-mini</strong> - Economic choice<br/>• <strong>anthropic/claude-sonnet-4</strong> - Latest Claude model<br/>• <strong>google/gemini-2.0-flash-001</strong> - Gemini 2.0<br/>• <strong>google/gemini-2.5-flash-preview-05-20</strong> - Gemini 2.5 Preview</small>';
$lang['max_tokens'] = 'Maximum Tokens (100-120,000)<br/><small>Check your selected model\'s limit. Higher = more expensive</small>';
$lang['temperature'] = 'Temperature (0-1, lower = more consistent, higher = more creative)';
$lang['toolbar_priority'] = 'Toolbar Priority (0-99999)<br/><small>Higher number = positioned later. Use large values to place after other plugins</small>';
$lang['context_length'] = 'Context Length (100-2000)<br/><small>Characters to include before/after selection. Larger = more accurate but more expensive</small>';