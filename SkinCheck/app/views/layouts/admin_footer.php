        </main> <!-- End Main Content Area -->
    </div> <!-- End Admin Wrapper -->

    <!-- ========================================== -->
    <!-- FLOATING ADMIN CHATBOT WIDGET              -->
    <!-- ========================================== -->
    <div id="admin-chatbot-widget" class="admin-chatbot-widget shadow-lg">
        <!-- Chatbot Header -->
        <div class="chat-header d-flex justify-content-between align-items-center bg-dark text-white p-3 rounded-top border-bottom border-danger">
            <div class="d-flex align-items-center">
                <div class="bg-danger rounded-circle p-2 d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                    <i class="bi bi-shield-lock-fill text-white fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-white">APSkincek Admin AI</h6>
                    <div class="small text-white-50" style="font-size: 0.72rem;">
                        <span class="badge bg-success p-1 me-1">Online</span> Akses Database Aktif
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button id="admin-clear-chat" class="btn btn-sm btn-outline-light border-0 p-1" title="Hapus Riwayat Chat" style="opacity: 0.85;">
                    <i class="bi bi-trash3 fs-5"></i>
                </button>
                <button id="admin-close-chat" class="btn-close btn-close-white" aria-label="Close"></button>
            </div>
        </div>

        <!-- Quick Database Chips -->
        <div class="bg-light px-3 py-2 border-bottom d-flex gap-2 overflow-x-auto flex-nowrap" style="scrollbar-width: thin;">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-0 px-2 small quick-chip" data-prompt="ada berapa customer yg terdaftar">
                <i class="bi bi-people me-1 text-primary"></i> Berapa customer?
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-0 px-2 small quick-chip" data-prompt="siapa saja customer yg terdaftar di database?">
                <i class="bi bi-person-lines-fill me-1 text-info"></i> Daftar customer
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-0 px-2 small quick-chip" data-prompt="berapa total scan kulit di sistem?">
                <i class="bi bi-camera me-1 text-success"></i> Total scan
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-0 px-2 small quick-chip" data-prompt="tampilkan ringkasan katalog produk di database">
                <i class="bi bi-box-seam me-1 text-warning"></i> Katalog produk
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-0 px-2 small quick-chip" data-prompt="apa log aktivitas sistem terbaru?">
                <i class="bi bi-journal-text me-1 text-danger"></i> Log aktivitas
            </button>
        </div>

        <!-- Chat Body -->
        <div id="admin-chat-body" class="admin-chat-body p-3 bg-white" style="height: 320px; overflow-y: auto;">
            <div class="d-flex mb-3">
                <div class="bg-light border p-2 rounded-3 text-dark small" style="max-width: 85%;">
                    Halo <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrator') ?></strong>! Saya adalah <strong>APSkincek Admin AI Assistant</strong> yang terhubung langsung ke database sistem.
                    <br><br>
                    Anda dapat menanyakan data jumlah customer, daftar customer, statistik scan, katalog produk, atau informasi sistem lainnya.
                </div>
            </div>
        </div>

        <!-- Chat Footer -->
        <div class="chat-footer p-2 bg-light border-top rounded-bottom d-flex align-items-center">
            <input type="text" id="admin-chat-input" class="form-control form-control-sm me-2 rounded-pill px-3" placeholder="Tanyakan data database APSkincek...">
            <button id="admin-send-chat" class="btn btn-danger btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px; flex-shrink: 0;" title="Kirim">
                <i class="bi bi-send-fill text-white" style="font-size: 0.85rem;"></i>
            </button>
        </div>
    </div>

    <!-- Floating Toggle Button -->
    <button id="admin-chatbot-toggle" class="btn btn-danger rounded-circle shadow-lg d-flex align-items-center justify-content-center position-fixed" style="width: 60px; height: 60px; bottom: 30px; right: 30px; z-index: 9999;" title="Admin AI Database Assistant">
        <i class="bi bi-shield-shaded fs-3 text-white"></i>
    </button>

    <style>
    .admin-chatbot-widget {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 380px;
        max-width: calc(100vw - 40px);
        background: #fff;
        border-radius: 14px;
        z-index: 9999;
        display: none;
        flex-direction: column;
        animation: adminSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(0,0,0,0.1);
    }
    .admin-chat-body .admin-user-msg {
        background-color: #dc3545;
        color: white;
        margin-left: auto;
        border-radius: 12px 12px 0 12px !important;
        box-shadow: 0 2px 4px rgba(220,53,69,0.2);
    }
    .admin-chat-body .admin-bot-msg {
        background-color: #f8f9fa;
        color: #212529;
        border: 1px solid #e9ecef;
        border-radius: 12px 12px 12px 0 !important;
        white-space: pre-wrap;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .quick-chip {
        font-size: 0.75rem !important;
        background-color: #fff;
    }
    .quick-chip:hover {
        background-color: #e9ecef;
    }
    @keyframes adminSlideUp {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatToggle = document.getElementById('admin-chatbot-toggle');
        const chatWidget = document.getElementById('admin-chatbot-widget');
        const closeChat = document.getElementById('admin-close-chat');
        const clearChatBtn = document.getElementById('admin-clear-chat');
        const sendChatBtn = document.getElementById('admin-send-chat');
        const chatInput = document.getElementById('admin-chat-input');
        const chatBody = document.getElementById('admin-chat-body');
        const quickChips = document.querySelectorAll('.quick-chip');

        const currentUserId = <?= json_encode($_SESSION['user_id'] ?? '') ?>;
        const currentUserName = <?= json_encode($_SESSION['user_name'] ?? 'Administrator') ?>;
        const currentUserEmail = <?= json_encode($_SESSION['user_email'] ?? '') ?>;

        const defaultGreeting = chatBody.innerHTML;

        let adminWidgetHistory = [];
        try {
            const storedHist = sessionStorage.getItem('skincheck_admin_widget_history');
            if (storedHist) adminWidgetHistory = JSON.parse(storedHist);
        } catch(e) {}

        // Load saved chat if exists
        const savedChat = sessionStorage.getItem('skincheck_admin_widget_chat');
        if (savedChat) {
            chatBody.innerHTML = savedChat;
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        // Toggle Chat Widget
        chatToggle.addEventListener('click', () => {
            const isVisible = chatWidget.style.display === 'flex';
            chatWidget.style.display = isVisible ? 'none' : 'flex';
            if (!isVisible) {
                chatInput.focus();
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        });

        closeChat.addEventListener('click', () => {
            chatWidget.style.display = 'none';
        });

        // Quick Chips Click Event
        quickChips.forEach(chip => {
            chip.addEventListener('click', () => {
                const prompt = chip.getAttribute('data-prompt');
                chatInput.value = prompt;
                sendMessage();
            });
        });

        // Clear Chat History
        if (clearChatBtn) {
            clearChatBtn.addEventListener('click', () => {
                if (confirm('Apakah Anda ingin membersihkan riwayat percakapan chatbot admin ini?')) {
                    sessionStorage.removeItem('skincheck_admin_widget_chat');
                    sessionStorage.removeItem('skincheck_admin_widget_history');
                    adminWidgetHistory = [];
                    chatBody.innerHTML = defaultGreeting;
                }
            });
        }

        function saveChat() {
            sessionStorage.setItem('skincheck_admin_widget_chat', chatBody.innerHTML);
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

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

        // Send Message
        async function sendMessage() {
            const text = chatInput.value.trim();
            if(!text) return;

            // Append user message
            const userDiv = document.createElement('div');
            userDiv.className = 'd-flex mb-3 justify-content-end';
            userDiv.innerHTML = `<div class="admin-user-msg p-2 rounded small" style="max-width: 80%;">${escapeHtml(text)}</div>`;
            chatBody.appendChild(userDiv);
            saveChat();
            
            chatInput.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;

            // Prepare history of previous messages only
            const prevHistory = adminWidgetHistory.slice(-6);

            // Add loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'd-flex mb-3 align-items-center bot-msg-container';
            loadingDiv.innerHTML = `<div class="admin-bot-msg p-2 rounded small text-muted"><span class="spinner-border spinner-border-sm me-1 text-danger"></span> Membaca database APSkincek...</div>`;
            chatBody.appendChild(loadingDiv);
            chatBody.scrollTop = chatBody.scrollHeight;

            try {
                // AbortController with 12s timeout to prevent hanging spinner
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
                botDiv.innerHTML = `<div class="admin-bot-msg p-2 rounded small" style="max-width: 85%;">${formatBotMessage(data.reply || '')}</div>`;
                chatBody.appendChild(botDiv);
                saveChat();

                adminWidgetHistory.push({ role: 'user', content: text });
                adminWidgetHistory.push({ role: 'assistant', content: cleanedReply });
                if (adminWidgetHistory.length > 10) adminWidgetHistory = adminWidgetHistory.slice(-10);
                sessionStorage.setItem('skincheck_admin_widget_history', JSON.stringify(adminWidgetHistory));
                
                chatBody.scrollTop = chatBody.scrollHeight;

            } catch (error) {
                if (chatBody.contains(loadingDiv)) {
                    chatBody.removeChild(loadingDiv);
                }
                const errDiv = document.createElement('div');
                errDiv.className = 'd-flex mb-3';
                errDiv.innerHTML = `<div class="bg-danger text-white p-2 rounded small" style="max-width: 85%;">Terjadi kesalahan: ${escapeHtml(error.message || 'Server error')}</div>`;
                chatBody.appendChild(errDiv);
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        }

        sendChatBtn.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') sendMessage();
        });
    });

    // ==========================================
    // ADMIN SIDEBAR TOGGLE LOGIC (BUKA / TUTUP)
    // ==========================================
    (function() {
        const adminWrapper = document.getElementById('adminWrapper');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const closeBtn = document.getElementById('sidebarCloseBtn');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleIcon = document.getElementById('sidebarToggleIcon');

        if (!adminWrapper || !toggleBtn) return;

        // Check initial state from localStorage
        const isCollapsedSaved = localStorage.getItem('admin_sidebar_collapsed') === 'true';
        if (isCollapsedSaved && window.innerWidth >= 992) {
            adminWrapper.classList.add('sidebar-collapsed');
            updateIcon(true);
        } else {
            updateIcon(false);
        }

        function updateIcon(isCollapsed) {
            if (!toggleIcon) return;
            if (isCollapsed) {
                toggleIcon.className = 'bi bi-layout-sidebar fs-5 text-dark';
                toggleBtn.setAttribute('title', 'Buka Sidebar (Alt + B)');
            } else {
                toggleIcon.className = 'bi bi-layout-sidebar-inset fs-5 text-dark';
                toggleBtn.setAttribute('title', 'Tutup Sidebar (Alt + B)');
            }
        }

        function toggleSidebar() {
            if (window.innerWidth >= 992) {
                // Desktop: collapse / expand
                const isNowCollapsed = adminWrapper.classList.toggle('sidebar-collapsed');
                localStorage.setItem('admin_sidebar_collapsed', isNowCollapsed ? 'true' : 'false');
                updateIcon(isNowCollapsed);
            } else {
                // Mobile: drawer show / hide
                const isNowMobileOpen = adminWrapper.classList.toggle('sidebar-mobile-open');
                document.body.style.overflow = isNowMobileOpen ? 'hidden' : '';
            }
        }

        function closeSidebar() {
            if (window.innerWidth >= 992) {
                adminWrapper.classList.add('sidebar-collapsed');
                localStorage.setItem('admin_sidebar_collapsed', 'true');
                updateIcon(true);
            } else {
                adminWrapper.classList.remove('sidebar-mobile-open');
                document.body.style.overflow = '';
            }
        }

        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });
        }

        // Keyboard shortcut: Alt + B
        document.addEventListener('keydown', function(e) {
            if (e.altKey && (e.key === 'b' || e.key === 'B')) {
                e.preventDefault();
                toggleSidebar();
            }
        });
    })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
