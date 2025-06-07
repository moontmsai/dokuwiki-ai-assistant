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
            var userWantsFullText = confirm('텍스트가 선택되지 않았습니다. 전체 텍스트를 AI로 개선하시겠습니까?');
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
        showErrorModal('처리할 텍스트가 없습니다.');
        return;
    }
    
    // 요청사항 감지 및 안내
    if (selectedText.includes('요청사항:')) {
        var customRequestDetected = true;
        showLoadingModal('요청사항을 반영하여 AI가 텍스트를 수정하고 있습니다...');
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
            showErrorModal('AI 처리 중 오류가 발생했습니다: ' + data.error);
            return;
        }
        
        if (data.improved_text && data.improved_text.trim() !== '') {
            showPreviewModal(selectedText, data.improved_text, function(approved) {
                if (approved) {
                    replaceSelectedText(textarea, {start: start, end: end}, data.improved_text);
                }
            });
        } else {
            showErrorModal('AI 응답에 개선된 텍스트가 없습니다.');
        }
    })
    .catch(function(error) {
        // Reset processing flag
        isProcessing = false;
        
        hideLoadingModal();
        showErrorModal('서버 요청이 실패했습니다: ' + error.message);
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
    var message = customMessage || 'AI가 텍스트를 처리하고 있습니다...';
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
                    <h3>오류</h3>
                    <button class="aiassist-close-btn" onclick="closeErrorModal()">&times;</button>
                </div>
                <div class="aiassist-modal-body">
                    <p>${message}</p>
                </div>
                <div class="aiassist-modal-footer">
                    <button class="aiassist-btn aiassist-btn-cancel" onclick="closeErrorModal()">확인</button>
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
                    <h3>AI 텍스트 개선 미리보기</h3>
                    <button class="aiassist-close-btn" onclick="closePreviewModal()">&times;</button>
                </div>
                <div class="aiassist-modal-body">
                    <div class="aiassist-comparison">
                        <div class="aiassist-original">
                            <h4>원본 텍스트:</h4>
                            <div class="aiassist-text-box">
                                <pre>${escapeHtml(originalText)}</pre>
                            </div>
                        </div>
                        <div class="aiassist-improved">
                            <h4>AI 개선된 텍스트:</h4>
                            <div class="aiassist-text-box">
                                <pre>${escapeHtml(improvedText)}</pre>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="aiassist-modal-footer">
                    <button class="aiassist-btn aiassist-btn-cancel" onclick="closePreviewModal()">취소</button>
                    <button class="aiassist-btn aiassist-btn-apply" onclick="applyImprovement()">변경사항 적용</button>
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