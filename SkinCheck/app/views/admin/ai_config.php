<?php require_once '../app/views/layouts/admin_header.php'; ?>

<?php
$primaryKey = $config['primary_api_key'] ?? '';
$backup1 = $config['backup_api_key_1'] ?? '';
$backup2 = $config['backup_api_key_2'] ?? '';
$backup3 = $config['backup_api_key_3'] ?? '';
$autoFailover = !empty($config['auto_failover']);
$currentModel = $config['model'] ?? 'qwen/qwen3.8-27b';
$currentProvider = $config['provider'] ?? 'groq';

$filledBackups = 0;
if (!empty($backup1)) $filledBackups++;
if (!empty($backup2)) $filledBackups++;
if (!empty($backup3)) $filledBackups++;
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="bi bi-cpu-fill text-primary me-2"></i> Konfigurasi AI & Multi API Key Cadangan
        </h3>
        <p class="text-muted small mb-0">Kelola API Key utama dan 3 cadangan otomatis agar chatbot & AI asisten tidak pernah macet atau down saat kuota/limit habis.</p>
    </div>
    <div>
        <button type="button" id="btnTestAllKeys" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-lightning-charge-fill me-1"></i> Tes Semua API Key
        </button>
    </div>
</div>

<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>
        <?= htmlspecialchars($_SESSION['success_msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success_msg']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i>
        <?= htmlspecialchars($_SESSION['error_msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error_msg']); ?>
<?php endif; ?>

<!-- STATUS STATS CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between h-100">
            <div>
                <div class="text-muted small fw-semibold">Status Kunci Utama</div>
                <div class="fs-5 fw-bold text-success">
                    <?= !empty($primaryKey) ? '<i class="bi bi-shield-fill-check me-1"></i> Terpasang' : '<i class="bi bi-exclamation-circle text-danger me-1"></i> Kosong' ?>
                </div>
            </div>
            <div class="rounded-circle bg-success-subtle p-3 text-success"><i class="bi bi-key-fill fs-4"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between h-100">
            <div>
                <div class="text-muted small fw-semibold">Cadangan Siaga</div>
                <div class="fs-5 fw-bold text-primary"><?= $filledBackups ?> dari 3 Cadangan</div>
            </div>
            <div class="rounded-circle bg-primary-subtle p-3 text-primary"><i class="bi bi-layers-fill fs-4"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between h-100">
            <div>
                <div class="text-muted small fw-semibold">Auto-Failover</div>
                <div class="fs-5 fw-bold <?= $autoFailover ? 'text-success' : 'text-warning' ?>">
                    <?= $autoFailover ? '<i class="bi bi-check-circle-fill me-1"></i> Aktif Otomatis' : '<i class="bi bi-dash-circle me-1"></i> Manual' ?>
                </div>
            </div>
            <div class="rounded-circle bg-warning-subtle p-3 text-warning"><i class="bi bi-arrow-repeat fs-4"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between h-100">
            <div>
                <div class="text-muted small fw-semibold">Model AI Aktif</div>
                <div class="fs-6 fw-bold text-dark text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($currentModel) ?>">
                    <?= htmlspecialchars($currentModel) ?>
                </div>
            </div>
            <div class="rounded-circle bg-info-subtle p-3 text-info"><i class="bi bi-robot fs-4"></i></div>
        </div>
    </div>
</div>

<!-- FORM UTAMA KONFIGURASI -->
<form action="/APSkincek/SkinCheck/public/admin/ai-config/save" method="POST">
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Kunci Utama & Pengaturan Provider -->
        <div class="col-lg-6">
            <!-- CARD KUNCI UTAMA -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-star-fill text-warning me-2"></i> Kunci Utama (Primary API Key)
                    </h5>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">Prioritas 1</span>
                </div>
                <div class="card-body px-4 pb-4 pt-1">
                    <p class="text-muted small">Kunci ini akan selalu digunakan pertama kali oleh Chatbot dan Sistem Analisis Kulit untuk menjawab percakapan.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Groq / Gemini API Key Utama:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                            <input type="password" id="input_primary_key" name="primary_api_key" class="form-control font-monospace" placeholder="gsk_..." value="<?= htmlspecialchars($primaryKey) ?>" required>
                            <button type="button" class="btn btn-outline-secondary btn-toggle-pw" data-target="input_primary_key" title="Tampilkan/Sembunyikan">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-copy-key" data-target="input_primary_key" title="Salin">
                                <i class="bi bi-clipboard"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-test-single" data-target="input_primary_key" data-label="Primary Key">
                                <i class="bi bi-play-circle me-1"></i> Tes
                            </button>
                        </div>
                        <div id="test_status_input_primary_key" class="small mt-2 d-none"></div>
                    </div>
                </div>
            </div>

            <!-- CARD PENGATURAN MODEL & PROVIDER -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-sliders text-primary me-2"></i> Pengaturan Model & Fitur Failover
                    </h5>
                </div>
                <div class="card-body px-4 pb-4 pt-1">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Provider AI:</label>
                        <select name="provider" id="providerSelect" class="form-select rounded-3">
                            <option value="groq" <?= $currentProvider === 'groq' ? 'selected' : '' ?>>Groq Cloud (Rekomendasi - Respon Kilat 100-300ms)</option>
                            <option value="gemini" <?= $currentProvider === 'gemini' ? 'selected' : '' ?>>Google Gemini (generativelanguage.googleapis.com)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Model AI:</label>
                        <select name="model" id="modelSelect" class="form-select rounded-3 font-monospace">
                            <option value="qwen/qwen3.8-27b" <?= $currentModel === 'qwen/qwen3.8-27b' ? 'selected' : '' ?>>qwen/qwen3.8-27b (Default Stabil APSkincek)</option>
                            <option value="llama-3.3-70b-versatile" <?= $currentModel === 'llama-3.3-70b-versatile' ? 'selected' : '' ?>>llama-3.3-70b-versatile (Model Besar Pintar)</option>
                            <option value="llama-3.1-8b-instant" <?= $currentModel === 'llama-3.1-8b-instant' ? 'selected' : '' ?>>llama-3.1-8b-instant (Sangat Ringan & Cepat)</option>
                            <option value="mixtral-8x7b-32768" <?= $currentModel === 'mixtral-8x7b-32768' ? 'selected' : '' ?>>mixtral-8x7b-32768 (Kontek Panjang)</option>
                            <option value="gemini-1.5-flash" <?= $currentModel === 'gemini-1.5-flash' ? 'selected' : '' ?>>gemini-1.5-flash (Google Gemini)</option>
                        </select>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input fs-5" type="checkbox" name="auto_failover" id="autoFailoverSwitch" <?= $autoFailover ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-dark pt-1" for="autoFailoverSwitch">
                                Aktifkan Auto-Failover Otomatis
                            </label>
                        </div>
                        <div class="small text-muted ps-1">
                            Jika Kunci Utama mengalami <strong>Rate Limit (429)</strong>, kuota harian habis, atau macet, server akan langsung secara otomatis berpindah mencoba Cadangan 1, 2, atau 3 dalam hitungan milidetik sehingga pengguna tidak mengalami error!
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: 3 API KEY CADANGAN -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-shield-plus text-success me-2"></i> 3 API Key Cadangan (Backup Keys)
                    </h5>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Standby Failover</span>
                </div>
                <div class="card-body px-4 pb-4 pt-1">
                    <p class="text-muted small">Masukkan hingga 3 API Key cadangan dari Groq / Google. Ketika kunci utama macet, sistem akan otomatis beralih ke kunci-kunci cadangan ini.</p>

                    <!-- CADANGAN 1 -->
                    <div class="p-3 mb-3 bg-light rounded-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark small"><i class="bi bi-1-circle-fill text-primary me-1"></i> API Key Cadangan 1</span>
                            <?php if (!empty($backup1)): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill btn-swap-primary" data-slot="backup_1">
                                    <i class="bi bi-arrow-down-up me-1"></i> Jadikan Kunci Utama
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="input-group">
                            <input type="password" id="input_backup_1" name="backup_api_key_1" class="form-control font-monospace" placeholder="Kunci cadangan 1 (opsional)" value="<?= htmlspecialchars($backup1) ?>">
                            <button type="button" class="btn btn-outline-secondary btn-toggle-pw" data-target="input_backup_1">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-copy-key" data-target="input_backup_1">
                                <i class="bi bi-clipboard"></i>
                            </button>
                            <button type="button" class="btn btn-outline-success btn-test-single" data-target="input_backup_1" data-label="Cadangan 1">
                                <i class="bi bi-play-circle me-1"></i> Tes
                            </button>
                        </div>
                        <div id="test_status_input_backup_1" class="small mt-2 d-none"></div>
                    </div>

                    <!-- CADANGAN 2 -->
                    <div class="p-3 mb-3 bg-light rounded-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark small"><i class="bi bi-2-circle-fill text-primary me-1"></i> API Key Cadangan 2</span>
                            <?php if (!empty($backup2)): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill btn-swap-primary" data-slot="backup_2">
                                    <i class="bi bi-arrow-down-up me-1"></i> Jadikan Kunci Utama
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="input-group">
                            <input type="password" id="input_backup_2" name="backup_api_key_2" class="form-control font-monospace" placeholder="Kunci cadangan 2 (opsional)" value="<?= htmlspecialchars($backup2) ?>">
                            <button type="button" class="btn btn-outline-secondary btn-toggle-pw" data-target="input_backup_2">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-copy-key" data-target="input_backup_2">
                                <i class="bi bi-clipboard"></i>
                            </button>
                            <button type="button" class="btn btn-outline-success btn-test-single" data-target="input_backup_2" data-label="Cadangan 2">
                                <i class="bi bi-play-circle me-1"></i> Tes
                            </button>
                        </div>
                        <div id="test_status_input_backup_2" class="small mt-2 d-none"></div>
                    </div>

                    <!-- CADANGAN 3 -->
                    <div class="p-3 mb-2 bg-light rounded-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark small"><i class="bi bi-3-circle-fill text-primary me-1"></i> API Key Cadangan 3</span>
                            <?php if (!empty($backup3)): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill btn-swap-primary" data-slot="backup_3">
                                    <i class="bi bi-arrow-down-up me-1"></i> Jadikan Kunci Utama
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="input-group">
                            <input type="password" id="input_backup_3" name="backup_api_key_3" class="form-control font-monospace" placeholder="Kunci cadangan 3 (opsional)" value="<?= htmlspecialchars($backup3) ?>">
                            <button type="button" class="btn btn-outline-secondary btn-toggle-pw" data-target="input_backup_3">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-copy-key" data-target="input_backup_3">
                                <i class="bi bi-clipboard"></i>
                            </button>
                            <button type="button" class="btn btn-outline-success btn-test-single" data-target="input_backup_3" data-label="Cadangan 3">
                                <i class="bi bi-play-circle me-1"></i> Tes
                            </button>
                        </div>
                        <div id="test_status_input_backup_3" class="small mt-2 d-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Simpan -->
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="/APSkincek/SkinCheck/public/admin" class="btn btn-light rounded-pill px-4">Kembali ke Dashboard</a>
        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
            <i class="bi bi-check-lg me-1"></i> Simpan Semua Konfigurasi
        </button>
    </div>
</form>

<!-- Hidden Form for Swap to Primary -->
<form id="swapPrimaryForm" action="/APSkincek/SkinCheck/public/admin/ai-config/switch-primary" method="POST" class="d-none">
    <input type="hidden" name="target_slot" id="swapTargetSlot" value="">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Toggle Password Visibility
    document.querySelectorAll('.btn-toggle-pw').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });

    // 2. Copy Key to Clipboard
    document.querySelectorAll('.btn-copy-key').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input.value) {
                navigator.clipboard.writeText(input.value).then(() => {
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check text-success"></i>';
                    setTimeout(() => { this.innerHTML = originalHtml; }, 1500);
                });
            }
        });
    });

    // 3. Swap Backup Slot with Primary
    document.querySelectorAll('.btn-swap-primary').forEach(btn => {
        btn.addEventListener('click', function() {
            const slot = this.getAttribute('data-slot');
            if (confirm('Jadikan kunci cadangan ini sebagai KUNCI UTAMA (Primary Key)? Kunci utama yang lama akan dipindah menjadi cadangan.')) {
                document.getElementById('swapTargetSlot').value = slot;
                document.getElementById('swapPrimaryForm').submit();
            }
        });
    });

    // 4. Test Single API Key via AJAX
    async function testKey(inputId, label) {
        const input = document.getElementById(inputId);
        const statusEl = document.getElementById('test_status_' + inputId);
        const provider = document.getElementById('providerSelect').value;
        const model = document.getElementById('modelSelect').value;
        const key = input.value.trim();

        if (!key) {
            statusEl.className = 'small mt-2 text-warning';
            statusEl.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Masukkan API Key terlebih dahulu untuk diuji.';
            statusEl.classList.remove('d-none');
            return;
        }

        statusEl.className = 'small mt-2 text-primary';
        statusEl.innerHTML = '<div class="spinner-border spinner-border-sm me-1" role="status"></div> Menguji koneksi ' + label + ' ke server ' + provider + '...';
        statusEl.classList.remove('d-none');

        try {
            const res = await fetch('/APSkincek/SkinCheck/public/admin/ai-config/test', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ apiKey: key, provider: provider, model: model })
            });
            const data = await res.json();

            if (data.success) {
                statusEl.className = 'small mt-2 text-success fw-semibold';
                statusEl.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + data.message;
            } else {
                statusEl.className = 'small mt-2 text-danger fw-semibold';
                statusEl.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Gagal: ' + (data.error || 'API Key tidak valid.');
            }
        } catch (err) {
            statusEl.className = 'small mt-2 text-danger';
            statusEl.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Terjadi kesalahan jaringan saat menguji.';
        }
    }

    document.querySelectorAll('.btn-test-single').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const label = this.getAttribute('data-label') || 'Key';
            testKey(targetId, label);
        });
    });

    // 5. Test All Keys Simultaneously
    const btnTestAll = document.getElementById('btnTestAllKeys');
    if (btnTestAll) {
        btnTestAll.addEventListener('click', function() {
            testKey('input_primary_key', 'Primary Key');
            if (document.getElementById('input_backup_1').value.trim()) {
                testKey('input_backup_1', 'Cadangan 1');
            }
            if (document.getElementById('input_backup_2').value.trim()) {
                testKey('input_backup_2', 'Cadangan 2');
            }
            if (document.getElementById('input_backup_3').value.trim()) {
                testKey('input_backup_3', 'Cadangan 3');
            }
        });
    }
});
</script>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>
