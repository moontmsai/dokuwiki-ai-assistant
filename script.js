/**
 * DokuWiki AI Assistant Plugin JavaScript
 * Version: 2024-12-19-FINAL (Clean Version)
 */

function handleAIAssist() {
    // Prevent duplicate requests
    if (!isProcessing) {
        isProcessing = true;
    }
    
    var textarea = document.getElementById('wiki__text');
    if (!textarea) {
        textarea = document.querySelector('textarea[name="wikitext"]') || 
                   document.querySelector('textarea.edit') ||
                   document.querySelector('#edit textarea');
    }
    
    if (!textarea) {
        return;
    }
    
    // Get selection immediately
    textarea.focus();
    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var selectedText = textarea.value.substring(start, end).trim();
    
    continueAIAssist(textarea, start, end, selectedText);
}

function continueAIAssist(textarea, start, end, selectedText) {
    
    // 선택 텍스트 처리 로직
    if (start !== end && selectedText.length > 0) {
        // Selected text mode
    }
    else {
        // Full text mode
        
        // Ctrl+A 체크
        if (ctrlAPressed) {
            start = 0;
            end = textarea.value.length;
            selectedText = textarea.value.trim();
            ctrlAPressed = false;
        }
        // 커서가 끝에 있는 경우
        else if (start === textarea.value.length) {
            start = 0;
            end = textarea.value.length;
            selectedText = textarea.value.trim();
        }
        // 진짜 아무것도 선택되지 않은 경우
        else {
            var userWantsFullText = confirm(window.AIASSIST_LANG.no_text_selected_confirm || 'No text selected. Do you want to improve the entire text with AI?');
            if (userWantsFullText) {
                start = 0;
                end = textarea.value.length;
                selectedText = textarea.value.trim();
            } else {
                isProcessing = false;
                return;
            }
        }
    }
    
    // 최종 검증 - 빈 텍스트 차단
    if (!selectedText || selectedText.trim() === '') {
        isProcessing = false;
        showErrorModal(window.AIASSIST_LANG.no_text_to_process || 'No text to process.');
        return;
    }
    
    // 요청사항 또는 request 감지 및 안내
    if (selectedText.includes('요청사항:') || selectedText.toLowerCase().includes('request:')) {
        var customRequestDetected = true;
        showLoadingModal(window.AIASSIST_LANG.processing_with_request || 'Processing text with your custom request...');
    } else {
        showLoadingModal();
    }
    
    if (!selectedText) {
        return;
    }
    
    // Set processing flag
    isProcessing = true;
    
    var fullText = textarea.value;
    // 컨텍스트 길이는 설정에서 가져오거나 기본값 500 사용
    var contextLength = window.AIASSIST_CONTEXT_LENGTH || 500;
    var beforeText = fullText.substring(Math.max(0, start - contextLength), start);
    var afterText = fullText.substring(end, Math.min(fullText.length, end + contextLength));
    
    var formData = new FormData();
    formData.append('call', 'aiassist');
    formData.append('selected_text', selectedText);
    formData.append('before_text', beforeText);
    formData.append('after_text', afterText);
    
    fetch(DOKU_BASE + 'doku.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        // Reset processing flag
        isProcessing = false;
        
        hideLoadingModal();
        
        if (data.error) {
            showErrorModal((window.AIASSIST_LANG.error_ai_processing || 'An error occurred during AI processing: ') + data.error);
            return;
        }
        
        if (data.improved_text && data.improved_text.trim() !== '') {
            showPreviewModal(selectedText, data.improved_text, function(approved) {
                if (approved) {
                    replaceSelectedText(textarea, {start: start, end: end}, data.improved_text);
                }
            });
        } else {
            showErrorModal(window.AIASSIST_LANG.error_no_improved_text || 'No improved text in AI response.');
        }
    })
    .catch(function(error) {
        // Reset processing flag
        isProcessing = false;
        
        hideLoadingModal();
        showErrorModal((window.AIASSIST_LANG.error_server_request || 'Server request failed: ') + error.message);
    });
}

function replaceSelectedText(textarea, selection, newText) {
    var fullText = textarea.value;
    var newFullText = fullText.substring(0, selection.start) + newText + fullText.substring(selection.end);
    
    textarea.value = newFullText;
    
    var newCursorPos = selection.start + newText.length;
    textarea.setSelectionRange(newCursorPos, newCursorPos);
    textarea.focus();
}

function showLoadingModal(customMessage) {
    var message = customMessage || window.AIASSIST_LANG.processing || 'AI is processing your text...';
    var modal = document.createElement('div');
    modal.id = 'aiassist-loading-modal';
    modal.innerHTML = `
        <div class="aiassist-modal-overlay">
            <div class="aiassist-modal-content">
                <div class="aiassist-loading">
                    <div class="aiassist-spinner"></div>
                    <p>${message}</p>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function hideLoadingModal() {
    var modal = document.getElementById('aiassist-loading-modal');
    if (modal) modal.remove();
}

function showErrorModal(message) {
    var modal = document.createElement('div');
    modal.id = 'aiassist-error-modal';
    modal.innerHTML = `
        <div class="aiassist-modal-overlay">
            <div class="aiassist-modal-content">
                <div class="aiassist-modal-header">
                    <h3>${window.AIASSIST_LANG.error || 'Error'}</h3>
                    <button class="aiassist-close-btn" onclick="closeErrorModal()">&times;</button>
                </div>
                <div class="aiassist-modal-body">
                    <p>${message}</p>
                </div>
                <div class="aiassist-modal-footer">
                    <button class="aiassist-btn aiassist-btn-cancel" onclick="closeErrorModal()">${window.AIASSIST_LANG.confirm || 'OK'}</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function closeErrorModal() {
    var modal = document.getElementById('aiassist-error-modal');
    if (modal) modal.remove();
}

function showPreviewModal(originalText, improvedText, callback) {
    // Remove existing modal
    var existingModal = document.getElementById('aiassist-preview-modal');
    if (existingModal) existingModal.remove();
    
    var modal = document.createElement('div');
    modal.id = 'aiassist-preview-modal';
    modal.innerHTML = `
        <div class="aiassist-modal-overlay">
            <div class="aiassist-modal-content aiassist-preview-modal">
                <div class="aiassist-modal-header">
                    <h3>${window.AIASSIST_LANG.preview_title || 'AI Text Improvement Preview'}</h3>
                    <button class="aiassist-close-btn" onclick="closePreviewModal()">&times;</button>
                </div>
                <div class="aiassist-modal-body">
                    <div class="aiassist-comparison">
                        <div class="aiassist-original">
                            <h4>${window.AIASSIST_LANG.original_text || 'Original Text:'}</h4>
                            <div class="aiassist-text-box">
                                <pre>${escapeHtml(originalText)}</pre>
                            </div>
                        </div>
                        <div class="aiassist-improved">
                            <h4>${window.AIASSIST_LANG.improved_text || 'AI Improved Text:'}</h4>
                            <div class="aiassist-text-box">
                                <pre>${escapeHtml(improvedText)}</pre>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="aiassist-modal-footer">
                    <button class="aiassist-btn aiassist-btn-cancel" onclick="closePreviewModal()">${window.AIASSIST_LANG.cancel || 'Cancel'}</button>
                    <button class="aiassist-btn aiassist-btn-apply" onclick="applyImprovement()">${window.AIASSIST_LANG.apply_changes || 'Apply Changes'}</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'block';
    
    window.aiassistCallback = callback;
}

function closePreviewModal() {
    var modal = document.getElementById('aiassist-preview-modal');
    if (modal) modal.remove();
    if (window.aiassistCallback) {
        window.aiassistCallback(false);
        delete window.aiassistCallback;
    }
}

function applyImprovement() {
    var modal = document.getElementById('aiassist-preview-modal');
    if (modal) modal.remove();
    
    if (window.aiassistCallback) {
        window.aiassistCallback(true);
        delete window.aiassistCallback;
    }
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Track Ctrl+A usage
var ctrlAPressed = false;
var isProcessing = false;

// Initialize
if (!window.aiAssistantInitialized) {
    window.aiAssistantInitialized = true;

    function initAIAssistant() {
        function bindButton() {
            var buttons = document.querySelectorAll('.aiassist_button');
            buttons.forEach(function(btn) {
                btn.removeEventListener('click', handleButtonClick);
                btn.onclick = null;
                btn.removeAttribute('onclick');
                btn.addEventListener('click', handleButtonClick, true);
                
                btn.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                }, true);
                
                // AI 버튼을 툴바의 가장 끝으로 이동
                moveButtonToEnd(btn);
            });
        }
        
        function moveButtonToEnd(button) {
            var toolbar = button.closest('.toolbar');
            if (toolbar && button.parentNode) {
                // 버튼을 툴바의 맨 끝으로 이동
                toolbar.appendChild(button.parentNode);
                
                // 추가 보장: 다른 플러그인들이 나중에 추가되는 경우를 대비
                setTimeout(function() {
                    if (toolbar && button.parentNode) {
                        toolbar.appendChild(button.parentNode);
                    }
                }, 100);
                
                // 한번 더 지연 실행으로 확실하게 보장
                setTimeout(function() {
                    if (toolbar && button.parentNode) {
                        toolbar.appendChild(button.parentNode);
                    }
                }, 500);
            }
        }
        
        function handleButtonClick(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (e.defaultPrevented === false) {
                e.preventDefault();
            }
            
            if (isProcessing) {
                return false;
            }
            
            isProcessing = true;
            
            setTimeout(function() {
                handleAIAssist();
            }, 10);
            
            return false;
        }
        
        bindButton();
        setTimeout(bindButton, 500);
        setTimeout(bindButton, 1500);
        
        // Track Ctrl+A presses
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'a') {
                ctrlAPressed = true;
                setTimeout(function() { ctrlAPressed = false; }, 3000);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAIAssistant);
    } else {
        initAIAssistant();
    }
    
    window.addEventListener('load', initAIAssistant);
}