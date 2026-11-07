<?php require_once '../app/views/layouts/header.php'; ?>
<style>
    .chat-container {
        height: 60vh;
        overflow-y: auto;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }
    .chat-message {
        margin-bottom: 1.5rem;
        max-width: 85%;
        clear: both;
    }
    .message-user {
        float: right;
        background-color: #0d6efd;
        color: white;
        border-radius: 1rem 1rem 0 1rem;
        padding: 12px 18px;
    }
    .message-ai {
        float: left;
        background-color: white;
        color: #212529;
        border: 1px solid #dee2e6;
        border-radius: 1rem 1rem 1rem 0;
        padding: 12px 18px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }
    .typing-indicator {
        display: none;
        font-style: italic;
        color: #6c757d;
        margin-bottom: 1rem;
        clear: both;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white p-4 border-bottom-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="fw-bold mb-0"><i class="bi bi-stars text-primary me-2"></i> SkinCheck AI Assistant</h4>
                    <p class="text-muted small mt-1 mb-0">Ask me about your scan results, skincare routine, or product recommendations!</p>
                </div>
                <button id="clearHistoryBtn" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-none" title="Hapus Riwayat Chat">
                    <i class="bi bi-trash3 me-1"></i> Hapus Riwayat
                </button>
            </div>
            
            <div class="card-body p-4 pt-0">
                <div class="chat-container mb-3" id="chatBox">
                    <div class="chat-message message-ai">
                        Hello <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>! I'm your SkinCheck AI Assistant. I can help explain your latest skin scan, recommend products from our catalog, or build a daily skincare routine. How can I help you today?
                    </div>
                    
                    <?php if (!empty($chat_history)): ?>
                        <?php foreach ($chat_history as $msg): ?>
                            <div class="chat-message <?= $msg['role'] === 'user' ? 'message-user' : 'message-ai' ?>">
                                <?php 
                                    $htmlText = htmlspecialchars($msg['text']);
                                    $htmlText = preg_replace('/(\*\*|__)(.*?)\1/s', '$2', $htmlText);
                                    $htmlText = preg_replace('/(\*|_)(.*?)\1/s', '$2', $htmlText);
                                    $htmlText = preg_replace('/[`\*~#]/', '', $htmlText);
                                    $htmlText = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $htmlText);
                                    echo nl2br($htmlText); 
                                ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                </div>
                
                <div class="typing-indicator" id="typingIndicator">SkinCheck AI is typing...</div>
                
                <form id="chatForm">
                    <div class="input-group">
                        <input type="text" id="messageInput" class="form-control rounded-pill me-2" placeholder="Type your message here..." required autocomplete="off">
                        <button class="btn btn-primary rounded-pill px-4" type="submit" id="sendBtn">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const clearHistoryBtn = document.getElementById('clearHistoryBtn');

    // Auto-scroll to bottom
    chatBox.scrollTop = chatBox.scrollHeight;

    if (clearHistoryBtn) {
        clearHistoryBtn.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin menghapus seluruh riwayat percakapan dengan SkinCheck AI Assistant?')) {
                clearHistoryBtn.disabled = true;
                fetch('/APSkincek/SkinCheck/public/api/ai/clear-history', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    clearHistoryBtn.disabled = false;
                    if (data.success) {
                        const userName = <?= json_encode($_SESSION['user_name'] ?? 'User') ?>;
                        chatBox.innerHTML = `
                            <div class="chat-message message-ai">
                                Hello <strong>${escapeHtml(userName)}</strong>! I'm your SkinCheck AI Assistant. I can help explain your latest skin scan, recommend products from our catalog, or build a daily skincare routine. How can I help you today?
                            </div>
                            <div class="clearfix"></div>
                        `;
                    } else {
                        alert(data.message || 'Gagal menghapus riwayat.');
                    }
                })
                .catch(err => {
                    clearHistoryBtn.disabled = false;
                    alert('Terjadi kesalahan jaringan.');
                });
            }
        });
    }

    function escapeHtml(unsafe) {
        return (unsafe || '')
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function appendMessage(text, isUser) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-message ' + (isUser ? 'message-user' : 'message-ai');
        // Clean symbols and format newlines
        let formattedText = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        formattedText = formattedText.replace(/(\*\*|__)(.*?)\1/g, '$2');
        formattedText = formattedText.replace(/(\*|_)(.*?)\1/g, '$2');
        formattedText = formattedText.replace(/^#+\s+/gm, '');
        formattedText = formattedText.replace(/[`\*~#]/g, '');
        formattedText = formattedText.replace(/[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{1F700}-\u{1F77F}\u{1F780}-\u{1F7FF}\u{1F800}-\u{1F8FF}\u{1F900}-\u{1F9FF}\u{1FA00}-\u{1FA6F}\u{1FA70}-\u{1FAFF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}]/gu, '');
        formattedText = formattedText.replace(/\n/g, '<br>');
        msgDiv.innerHTML = formattedText;
        
        chatBox.appendChild(msgDiv);
        
        // Ensure clearfix stays at bottom
        let clearfix = chatBox.querySelector('.clearfix');
        if (clearfix) chatBox.removeChild(clearfix);
        clearfix = document.createElement('div');
        clearfix.className = 'clearfix';
        chatBox.appendChild(clearfix);
        
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // UI Updates
        appendMessage(message, true);
        messageInput.value = '';
        messageInput.disabled = true;
        sendBtn.disabled = true;
        typingIndicator.style.display = 'block';
        chatBox.scrollTop = chatBox.scrollHeight;

        // API Call
        fetch('/APSkincek/SkinCheck/public/api/ai/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            typingIndicator.style.display = 'none';
            messageInput.disabled = false;
            sendBtn.disabled = false;
            messageInput.focus();

            if (data.success) {
                appendMessage(data.response, false);
            } else {
                appendMessage("Error: " + (data.message || "An error occurred."), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            typingIndicator.style.display = 'none';
            messageInput.disabled = false;
            sendBtn.disabled = false;
            appendMessage("Sorry, I encountered a network error. Please try again.", false);
        });
    });
});
</script>
<?php require_once '../app/views/layouts/footer.php'; ?>
