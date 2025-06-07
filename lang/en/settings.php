<?php
/**
 * English language file for aiassist plugin settings
 *
 * @author moontwt <moontwt@example.com>
 */

$lang['openai_api_key'] = 'OpenAI API Key';
$lang['openai_model'] = 'OpenAI Model Selection<br/><small>• gpt-3.5-turbo: Max 16K tokens (fast & cheap)<br/>• gpt-4: Max 8K tokens (accurate but expensive)<br/>• gpt-4-turbo: Max 128K tokens (balanced performance)<br/>• gpt-4o: Max 128K tokens (latest model)<br/>• gpt-4o-mini: Max 128K tokens (economical)</small>';
$lang['max_tokens'] = 'Maximum Tokens (100-120,000)<br/><small>Check your selected model\'s limit. Higher = more expensive</small>';
$lang['temperature'] = 'Temperature (0-1, lower = more consistent, higher = more creative)';
$lang['toolbar_priority'] = 'Toolbar Priority (0-99999)<br/><small>Higher number = positioned later. Use large values to place after other plugins</small>';
$lang['context_length'] = 'Context Length (100-2000)<br/><small>Characters to include before/after selection. Larger = more accurate but more expensive</small>';