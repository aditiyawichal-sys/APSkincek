<?php require_once '../app/views/layouts/admin_header.php'; ?>

<?php
$isConfigured = !empty(trim($config['api_token'] ?? ''));
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="bi bi-whatsapp text-success me-2"></i> Konfigurasi Notifikasi WhatsApp (Fonnte)
        </h3>
        <p class="text-muted small mb-0">Kelola token API Fonnte, status notifikasi registrasi, dan template pesan otomatis WhatsApp.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if ($isConfigured): ?>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                <i class="bi bi-check-circle-fill me-1"></i> Fonnte Token Terkonfigurasi
            </span>
        <?php else: ?>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Token Belum Diisi
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Form Konfigurasi Utama -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-sliders text-primary me-2"></i> Pengaturan Gateway Fonnte
                </h5>
                <p class="text-muted small mb-0">Masukkan token API dari akun Fonnte Anda untuk mengaktifkan notifikasi otomatis.</p>
            </div>
            <div class="card-body p-4">
                <form action="/APSkincek/SkinCheck/public/admin/wa-config/save" method="POST">
                    <!-- Fonnte API Token -->
                    <div class="mb-4">
                        <label for="api_token" class="form-label small fw-semibold text-secondary">
                            Fonnte API Token <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-key-fill text-warning"></i></span>
                            <input type="password" name="api_token" id="api_token" class="form-control rounded-0" placeholder="Masukkan token API Fonnte Anda" value="<?= htmlspecialchars($config['api_token'] ?? '') ?>" autocomplete="off" required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleTokenBtn" title="Lihat / Sembunyikan Token">
                                <i class="bi bi-eye" id="toggleTokenIcon"></i>
                            </button>
                        </div>
                        <div class="form-text small text-muted">
                            Token didapatkan dari dashboard Fonnte pada menu <strong>Device</strong> setelah Anda menghubungkan (scan QR) nomor WhatsApp Anda.
                        </div>
                    </div>

                    <!-- Kode Negara & Toggle Notifikasi -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="country_code" class="form-label small fw-semibold text-secondary">Default Kode Negara</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">ID</span>
                                <input type="text" name="country_code" id="country_code" class="form-control" value="<?= htmlspecialchars($config['country_code'] ?? '62') ?>" placeholder="62">
                            </div>
                            <div class="form-text small text-muted">Gunakan <code>62</code> untuk Indonesia.</div>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch pt-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="enable_registration_notification" name="enable_registration_notification" value="1" <?= !empty($config['enable_registration_notification']) ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-semibold text-dark" for="enable_registration_notification">
                                    Kirim Notifikasi Saat Registrasi
                                </label>
                                <div class="form-text small text-muted">Kirim WA otomatis saat ada customer baru yang mendaftar.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Pesan Kode OTP WhatsApp -->
                    <div class="mb-4">
                        <label for="otp_message" class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-shield-lock-fill text-warning me-1"></i> Template Pesan Kode OTP (Registrasi & Login)</span>
                            <span class="badge bg-light text-secondary border">Wajib Ada: <code>{otp}</code></span>
                        </label>
                        <textarea name="otp_message" id="otp_message" class="form-control font-monospace" rows="3" style="font-size: 0.88rem;"><?= htmlspecialchars($config['otp_message'] ?? 'Halo! Kode OTP Anda untuk registrasi/login adalah: *{otp}*. Demi keamanan, jangan bagikan kode ini kepada siapa pun, termasuk pihak admin. Kode ini berlaku selama 5 menit.') ?></textarea>
                        <div class="form-text small text-muted">
                            Gunakan variabel <code>{otp}</code> sebagai penempatan 6 digit angka kode verifikasi.
                        </div>
                    </div>

                    <!-- Template Pesan Registrasi -->
                    <div class="mb-4">
                        <label for="welcome_message" class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-bell-fill text-primary me-1"></i> Template Pesan WhatsApp Sambutan Registrasi</span>
                            <span class="badge bg-light text-secondary border">Format Teks WhatsApp Didukung</span>
                        </label>
                        <textarea name="welcome_message" id="welcome_message" class="form-control font-monospace" rows="6" style="font-size: 0.88rem;"><?= htmlspecialchars($config['welcome_message'] ?? '') ?></textarea>
                        
                        <div class="mt-2 p-2 bg-light rounded-3 border">
                            <div class="small fw-semibold text-secondary mb-1"><i class="bi bi-code-square me-1"></i> Variabel Dinamis Sambutan:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill small" onclick="insertVariable('{name}')">{name} (Nama)</button>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill small" onclick="insertVariable('{email}')">{email} (Email)</button>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill small" onclick="insertVariable('{phone}')">{phone} (No. WA)</button>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill small" onclick="insertVariable('{role}')">{role} (Tipe Akun)</button>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill small" onclick="insertVariable('{login_url}')">{login_url} (Link Login)</button>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill small" onclick="insertVariable('{date}')">{date} (Waktu)</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <span class="text-muted small">
                            <i class="bi bi-clock me-1"></i> Terakhir diperbarui: <?= htmlspecialchars($config['updated_at'] ?? '-') ?>
                        </span>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Uji Coba Pengiriman & Petunjuk Singkat -->
    <div class="col-lg-5">
        <!-- Card Uji Coba -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-send-check-fill text-success me-2"></i> Uji Coba Kirim WhatsApp
                </h5>
                <p class="text-muted small mb-0">Tes apakah token Fonnte aktif dan berhasil mengirim pesan ke nomor WA Anda.</p>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label for="test_phone" class="form-label small fw-semibold text-secondary">Nomor WhatsApp Penerima Uji Coba</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-success"><i class="bi bi-whatsapp"></i></span>
                        <input type="tel" id="test_phone" class="form-control rounded-end-3" placeholder="Contoh: 08123456789 atau 628123456789">
                    </div>
                    <div class="form-text small text-muted">Masukkan nomor WhatsApp aktif Anda untuk menerima pesan tes.</div>
                </div>

                <div id="testResultAlert" class="alert d-none py-2 px-3 small rounded-3 mb-3"></div>

                <button type="button" id="btnSendTest" class="btn btn-success w-100 rounded-pill py-2 shadow-sm" onclick="sendTestMessage()">
                    <i class="bi bi-send-fill me-1"></i> Kirim Pesan Uji Coba Sekarang
                </button>
            </div>
        </div>

        <!-- Card Panduan Integrasi Fonnte -->
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-dark mb-1">
                    <i class="bi bi-info-circle-fill text-info me-2"></i> Panduan Penggunaan Fonnte WA
                </h6>
            </div>
            <div class="card-body p-4 pt-3">
                <ol class="small text-secondary ps-3 mb-3" style="line-height: 1.6;">
                    <li class="mb-2">Buka situs resmi Fonnte di <a href="https://fonnte.com" target="_blank" class="fw-bold text-decoration-none">fonnte.com <i class="bi bi-box-arrow-up-right"></i></a> dan buat akun gratis/berlangganan.</li>
                    <li class="mb-2">Buka menu <strong>Device</strong> di dashboard Fonnte, lalu tambahkan device baru.</li>
                    <li class="mb-2">Scan kode QR yang tampil menggunakan aplikasi WhatsApp di HP Anda (seperti WhatsApp Web).</li>
                    <li class="mb-2">Salin <strong>API Token</strong> yang diberikan Fonnte ke formulir di sebelah kiri.</li>
                    <li>Klik <strong>Simpan Konfigurasi</strong>, lalu lakukan tes pengiriman menggunakan form uji coba di atas.</li>
                </ol>

                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <span class="fw-bold small text-dark">Notifikasi Otomatis Registrasi</span>
                    </div>
                    <p class="small text-muted mb-0">
                        Setiap kali ada pengguna atau admin yang mendaftar di halaman registrasi, sistem akan secara otomatis mengirim pesan WhatsApp selamat datang beserta rincian akun ke nomor yang didaftarkan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle Show / Hide API Token
const toggleBtn = document.getElementById('toggleTokenBtn');
const tokenInput = document.getElementById('api_token');
const toggleIcon = document.getElementById('toggleTokenIcon');

toggleBtn.addEventListener('click', function() {
    if (tokenInput.type === 'password') {
        tokenInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        tokenInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
});

// Helper insert variable into textarea
function insertVariable(text) {
    const textarea = document.getElementById('welcome_message');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const val = textarea.value;
    textarea.value = val.substring(0, start) + text + val.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + text.length;
}

// Ajax send test message
function sendTestMessage() {
    const phone = document.getElementById('test_phone').value.trim();
    const token = document.getElementById('api_token').value.trim();
    const btn = document.getElementById('btnSendTest');
    const alertBox = document.getElementById('testResultAlert');

    if (!phone) {
        alertBox.className = 'alert alert-danger py-2 px-3 small rounded-3 mb-3';
        alertBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Silakan masukkan nomor WhatsApp untuk uji coba.';
        alertBox.classList.remove('d-none');
        return;
    }

    if (!token) {
        alertBox.className = 'alert alert-warning py-2 px-3 small rounded-3 mb-3';
        alertBox.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1"></i> Fonnte API Token belum diisi pada form di sebelah kiri.';
        alertBox.classList.remove('d-none');
        return;
    }

    // Set loading state
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim ke WhatsApp...';
    alertBox.className = 'alert alert-info py-2 px-3 small rounded-3 mb-3';
    alertBox.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Menghubungi gateway Fonnte API...';
    alertBox.classList.remove('d-none');

    fetch('/APSkincek/SkinCheck/public/admin/wa-config/test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            target: phone,
            token: token
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        if (data.success) {
            alertBox.className = 'alert alert-success py-2 px-3 small rounded-3 mb-3';
            alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> <strong>Berhasil!</strong> ' + data.message;
        } else {
            alertBox.className = 'alert alert-danger py-2 px-3 small rounded-3 mb-3';
            alertBox.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> <strong>Gagal:</strong> ' + (data.error || 'Terjadi kesalahan saat pengiriman.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        alertBox.className = 'alert alert-danger py-2 px-3 small rounded-3 mb-3';
        alertBox.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Terjadi kesalahan jaringan: ' + err.message;
    });
}
</script>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>
