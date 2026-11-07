<?php require_once '../app/views/layouts/header.php'; ?>

<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <div class="alert alert-warning border-warning shadow-sm d-flex flex-wrap justify-content-between align-items-center mb-4 py-2 px-3 rounded-3">
        <div class="d-flex align-items-center me-3 my-1">
            <i class="bi bi-shield-exclamation text-dark fs-4 me-2"></i>
            <div>
                <strong class="text-dark">Mode Pratinjau Administrator</strong>
                <div class="small text-muted">Anda sedang melihat antarmuka pengguna dari sisi <strong>Customer / Pelanggan</strong>.</div>
            </div>
        </div>
        <div class="my-1">
            <a href="/APSkincek/SkinCheck/public/admin" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-shield-lock-fill text-warning me-1"></i> Kembali ke Panel Admin
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- 1 & 2. Hero Section & Profile -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card hero-card">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="profile-avatar me-3">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <h2 class="card-title mb-0">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
                        <p class="mb-0">Ready for your daily skin check?</p>
                    </div>
                </div>
                <!-- 3. Face Scan Button -->
                <a href="/APSkincek/SkinCheck/public/face-scan" class="btn btn-light btn-lg px-4 rounded-pill shadow-sm text-primary">
                    <i class="bi bi-camera me-2"></i> New Face Scan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- 4. Skin Analysis Terbaru -->
    <div class="col-md-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-emoji-smile text-primary me-2"></i> Skin Health</h5>
                <div class="score-ring my-3">
                    <?= isset($latestAnalysis['overall_score']) ? htmlspecialchars($latestAnalysis['overall_score']) . '%' : 'Belum Ada' ?>
                </div>
                <p class="card-text text-muted small">
                    <?= isset($latestAnalysis['created_at']) ? 'Scan: ' . date('d M Y, H:i', strtotime($latestAnalysis['created_at'])) : 'Lakukan scan pertamamu' ?>
                </p>
                <a href="/APSkincek/SkinCheck/public/skin-analysis" class="btn btn-outline-primary btn-sm rounded-pill mt-2">View Details</a>
            </div>
        </div>
    </div>

    <!-- 5. Sunscreen Check Terbaru -->
    <div class="col-md-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-brightness-high text-warning me-2"></i> Sunscreen Coverage</h5>
                <div class="score-ring sunscreen my-3">
                    <?= isset($latestSunscreen['coverage_score']) ? htmlspecialchars($latestSunscreen['coverage_score']) . '%' : 'Belum Ada' ?>
                </div>
                <p class="card-text text-muted small">
                    <?= isset($latestSunscreen['created_at']) ? 'Check: ' . date('d M Y, H:i', strtotime($latestSunscreen['created_at'])) : 'Belum ada pengecekan' ?>
                </p>
                <a href="/APSkincek/SkinCheck/public/sunscreen-check" class="btn btn-outline-primary btn-sm rounded-pill mt-2">Check Again</a>
            </div>
        </div>
    </div>

    <!-- 6. Progress Tracking -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-4"><i class="bi bi-graph-up text-info me-2"></i> Visual Metrics</h5>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Acne Level</span>
                        <span class="small font-weight-bold"><?= isset($latestAnalysis['acne_score']) ? htmlspecialchars($latestAnalysis['acne_score']) . '%' : 'N/A' ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?= isset($latestAnalysis['acne_score']) ? htmlspecialchars($latestAnalysis['acne_score']) : 0 ?>%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Texture Smoothness</span>
                        <span class="small font-weight-bold"><?= isset($latestAnalysis['texture_score']) ? (100 - (int)$latestAnalysis['texture_score']) . '%' : 'N/A' ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= isset($latestAnalysis['texture_score']) ? (100 - (int)$latestAnalysis['texture_score']) : 0 ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- 9. Product Recommendation -->
        <h5 class="mb-3 text-muted">Recommended for You</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="row g-0">
                        <div class="col-4 bg-light d-flex align-items-center justify-content-center">
                            <i class="bi bi-droplet text-primary display-4"></i>
                        </div>
                        <div class="col-8">
                            <div class="card-body">
                                <h6 class="card-title">Hydrating Cleanser</h6>
                                <p class="card-text small text-muted">Cetaphil - Based on your dry skin</p>
                                <a href="#" class="text-primary text-decoration-none small fw-bold">View details <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="row g-0">
                        <div class="col-4 bg-light d-flex align-items-center justify-content-center">
                            <i class="bi bi-sun text-warning display-4"></i>
                        </div>
                        <div class="col-8">
                            <div class="card-body">
                                <h6 class="card-title">UV Defender SPF 50</h6>
                                <p class="card-text small text-muted">L'Oreal - Daily protection</p>
                                <a href="#" class="text-primary text-decoration-none small fw-bold">View details <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 10. Skincare Education -->
        <h5 class="mb-3 text-muted">Learn</h5>
        <div class="card mb-4">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-4 bg-light d-none d-md-flex align-items-center justify-content-center">
                        <i class="bi bi-book text-muted display-1"></i>
                    </div>
                    <div class="col-md-8 p-4">
                        <span class="badge bg-secondary mb-2">Education</span>
                        <h5 class="card-title">How to Apply Sunscreen Correctly</h5>
                        <p class="card-text text-muted mb-3">Discover the two-finger rule and why reapplication every 2 hours is crucial for anti-aging.</p>
                        <a href="#" class="btn btn-outline-primary btn-sm rounded-pill">Read Article</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- 7. Scan History -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Recent Scans</h5>
                    <a href="/APSkincek/SkinCheck/public/scan-history" class="small text-decoration-none">View All</a>
                </div>
                
                <div class="history-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold small">Face Scan</div>
                        <div class="text-muted" style="font-size: 0.8rem;">Today, 10:00 AM</div>
                    </div>
                    <span class="badge bg-success rounded-pill">85%</span>
                </div>
                
                <div class="history-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold small">Sunscreen Check</div>
                        <div class="text-muted" style="font-size: 0.8rem;">Yesterday</div>
                    </div>
                    <span class="badge bg-info rounded-pill">Good</span>
                </div>
                
                <div class="history-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold small">Face Scan</div>
                        <div class="text-muted" style="font-size: 0.8rem;">Oct 24, 2023</div>
                    </div>
                    <span class="badge bg-success rounded-pill">80%</span>
                </div>
            </div>
        </div>

        <!-- 8. AI Assistant Widget -->
        <div class="card border-primary" style="border-width: 2px;">
            <div class="card-body bg-light">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-robot fs-2 text-primary me-3"></i>
                    <h5 class="card-title mb-0">AI Assistant</h5>
                </div>
                <div class="ai-bubble small">
                    Hi <?= htmlspecialchars($_SESSION['user_name']) ?>! Noticed you have some redness today. Want me to suggest a calming routine?
                </div>
                <a href="/APSkincek/SkinCheck/public/ai-assistant" class="btn btn-primary w-100 mt-2">Chat with AI</a>
            </div>
        </div>
    </div>
</div>

<!-- Floating Chatbot Widget -->
<div id="chatbot-widget" class="chatbot-widget">
    <div class="chat-header d-flex justify-content-between align-items-center bg-primary text-white p-3 rounded-top">
        <div class="d-flex align-items-center">
            <i class="bi bi-robot fs-4 me-2"></i>
            <h6 class="mb-0">Skincek AI</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button id="clear-chat" class="btn btn-sm text-white p-0 border-0" title="Hapus Riwayat Chat" style="opacity: 0.85; line-height: 1;">
                <i class="bi bi-trash3 fs-5"></i>
            </button>
            <button id="close-chat" class="btn-close btn-close-white" aria-label="Close"></button>
        </div>
    </div>
    <div id="chat-body" class="chat-body p-3 bg-white" style="height: 300px; overflow-y: auto;">
        <div class="d-flex mb-3">
            <div class="bg-light p-2 rounded text-dark small" style="max-width: 80%;">
                Halo <?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest') ?>! Ada yang bisa saya bantu terkait kesehatan kulitmu?
            </div>
        </div>
    </div>
    <div class="chat-footer p-2 bg-light border-top rounded-bottom d-flex align-items-center">
        <input type="text" id="chat-input" class="form-control form-control-sm me-2 rounded-pill" placeholder="Ketik pesan Anda...">
        <button id="send-chat" class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</div>

<button id="chatbot-toggle" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center position-fixed" style="width: 60px; height: 60px; bottom: 30px; right: 30px; z-index: 9999;">
    <i class="bi bi-chat-dots-fill fs-3 text-white"></i>
</button>

<style>
.chatbot-widget {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 350px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    border-radius: 12px;
    z-index: 9999;
    display: none;
    flex-direction: column;
    animation: slideUp 0.3s ease;
}
.chat-body .user-msg {
    background-color: #0d6efd;
    color: white;
    margin-left: auto;
    border-radius: 10px 10px 0 10px !important;
}
.chat-body .bot-msg {
    background-color: #f8f9fa;
    color: #212529;
    border-radius: 10px 10px 10px 0 !important;
    white-space: pre-wrap;
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chatbot-toggle');
    const chatWidget = document.getElementById('chatbot-widget');
    const closeChat = document.getElementById('close-chat');
    const clearChatBtn = document.getElementById('clear-chat');
    const sendChatBtn = document.getElementById('send-chat');
    const chatInput = document.getElementById('chat-input');
    const chatBody = document.getElementById('chat-body');

    const currentUserId = <?= json_encode($_SESSION['user_id'] ?? '') ?>;
    const currentUserName = <?= json_encode($_SESSION['user_name'] ?? 'Guest') ?>;
    const currentUserEmail = <?= json_encode($_SESSION['user_email'] ?? '') ?>;

    const defaultGreeting = chatBody.innerHTML;

    let widgetHistory = [];
    try {
        const storedHist = sessionStorage.getItem('skincheck_widget_history');
        if (storedHist) widgetHistory = JSON.parse(storedHist);
    } catch(e) {}

    // Load saved conversation if exists
    const savedChat = sessionStorage.getItem('skincheck_widget_chat');
    if (savedChat) {
        chatBody.innerHTML = savedChat;
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // Toggle Chat Widget
    chatToggle.addEventListener('click', () => {
        chatWidget.style.display = chatWidget.style.display === 'flex' ? 'none' : 'flex';
        if(chatWidget.style.display === 'flex') chatInput.focus();
    });

    closeChat.addEventListener('click', () => {
        chatWidget.style.display = 'none';
    });

    // Clear Chat History
    if (clearChatBtn) {
        clearChatBtn.addEventListener('click', () => {
            if (confirm('Apakah Anda yakin ingin menghapus seluruh riwayat chat ini?')) {
                sessionStorage.removeItem('skincheck_widget_chat');
                sessionStorage.removeItem('skincheck_widget_history');
                widgetHistory = [];
                chatBody.innerHTML = defaultGreeting;
            }
        });
    }

    function saveChat() {
        sessionStorage.setItem('skincheck_widget_chat', chatBody.innerHTML);
    }

    // Send Message
    async function sendMessage() {
        const text = chatInput.value.trim();
        if(!text) return;

        // Append user message
        const userDiv = document.createElement('div');
        userDiv.className = 'd-flex mb-3 justify-content-end';
        userDiv.innerHTML = `<div class="user-msg p-2 rounded small" style="max-width: 80%;">${escapeHtml(text)}</div>`;
        chatBody.appendChild(userDiv);
        saveChat();
        
        chatInput.value = '';
        chatBody.scrollTop = chatBody.scrollHeight;

        const prevHistory = widgetHistory.slice(-6);

        // Add loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'd-flex mb-3 align-items-center bot-msg-container';
        loadingDiv.innerHTML = `<div class="bot-msg p-2 rounded small text-muted">Sedang mengetik...</div>`;
        chatBody.appendChild(loadingDiv);
        chatBody.scrollTop = chatBody.scrollHeight;

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 12000);

            const response = await fetch('http://localhost:3000/api/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                signal: controller.signal,
                body: JSON.stringify({ 
                    message: text,
                    userId: currentUserId,
                    userName: currentUserName,
                    userEmail: currentUserEmail,
                    history: prevHistory
                })
            });
            clearTimeout(timeoutId);
            const data = await response.json();
            
            // Remove loading
            if (chatBody.contains(loadingDiv)) {
                chatBody.removeChild(loadingDiv);
            }

            if (data.error) {
                throw new Error(data.error);
            }

            function formatBotMessage(text) {
                if (!text) return '';
                let escaped = escapeHtml(cleanSymbols(text));
                return escaped
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>');
            }

            // Append bot message
            const cleanedReply = cleanSymbols(data.reply || '');
            const botDiv = document.createElement('div');
            botDiv.className = 'd-flex mb-3';
            botDiv.innerHTML = `<div class="bot-msg p-2 rounded small" style="max-width: 80%;">${formatBotMessage(data.reply || '')}</div>`;
            chatBody.appendChild(botDiv);
            saveChat();

            widgetHistory.push({ role: 'user', content: text });
            widgetHistory.push({ role: 'assistant', content: cleanedReply });
            if (widgetHistory.length > 10) widgetHistory = widgetHistory.slice(-10);
            sessionStorage.setItem('skincheck_widget_history', JSON.stringify(widgetHistory));
            
            chatBody.scrollTop = chatBody.scrollHeight;

        } catch (error) {
            if (chatBody.contains(loadingDiv)) {
                chatBody.removeChild(loadingDiv);
            }
            const errDiv = document.createElement('div');
            errDiv.className = 'd-flex mb-3';
            errDiv.innerHTML = `<div class="bg-danger text-white p-2 rounded small" style="max-width: 80%;">Maaf, terjadi kesalahan: ${escapeHtml(error.message || 'Server error')}</div>`;
            chatBody.appendChild(errDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }

    sendChatBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') sendMessage();
    });

    function cleanSymbols(text) {
        if (!text) return '';
        return text
            .replace(/\p{Extended_Pictographic}/gu, '')
            .replace(/^#+\s+/gm, '')
            .replace(/[`~]/g, '')
            .replace(/[^\S\r\n]{2,}/g, ' ')
            .replace(/\n{3,}/g, '\n\n')
            .trim();
    }

    function escapeHtml(unsafe) {
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
