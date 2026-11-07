<?php require_once '../app/views/layouts/header.php'; ?>

<?php
$activeTab = ($_GET['tab'] ?? 'password') === 'otp' ? 'otp' : 'password';
?>

<div class="row justify-content-center my-4">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3 shadow-xs" style="width: 58px; height: 58px;">
                    <i class="bi bi-box-arrow-in-right fs-3"></i>
                </div>
                <h4 class="fw-bold mb-1">Masuk ke Akun</h4>
                <p class="text-muted small mb-0">Silakan pilih metode masuk yang Anda inginkan</p>
            </div>

            <div class="card-body p-4 pt-2">
                <!-- Flash Message Alerts -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_msg'])): ?>
                    <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success_msg'])): ?>
                    <div class="alert alert-success py-2 px-3 small rounded-3 mb-3">
                        <i class="bi bi-check-circle-fill me-1"></i> <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                    </div>
                <?php endif; ?>

                <!-- Tab Navigasi Pilihan Login -->
                <ul class="nav nav-pills nav-fill bg-light p-1 rounded-pill mb-4 border" id="loginMethodTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill py-2 small fw-semibold <?= $activeTab === 'password' ? 'active' : '' ?>" 
                                id="password-tab" 
                                data-bs-toggle="pill" 
                                data-bs-target="#password-login-pane" 
                                type="button" 
                                role="tab">
                            <i class="bi bi-key-fill me-1"></i> Password
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill py-2 small fw-semibold <?= $activeTab === 'otp' ? 'active' : '' ?>" 
                                id="otp-tab" 
                                data-bs-toggle="pill" 
                                data-bs-target="#otp-login-pane" 
                                type="button" 
                                role="tab">
                            <i class="bi bi-whatsapp text-success me-1"></i> OTP WhatsApp
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="loginTabContent">
                    <!-- Tab 1: Login dengan Password -->
                    <div class="tab-pane fade <?= $activeTab === 'password' ? 'show active' : '' ?>" id="password-login-pane" role="tabpanel">
                        <form action="/APSkincek/SkinCheck/public/login" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-semibold">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control rounded-end-3" id="email" name="email" placeholder="nama@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label small fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control rounded-end-3" id="password" name="password" placeholder="Masukkan password Anda" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 mt-2 rounded-pill shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk dengan Password
                            </button>
                        </form>
                    </div>

                    <!-- Tab 2: Login Cepat via OTP WhatsApp -->
                    <div class="tab-pane fade <?= $activeTab === 'otp' ? 'show active' : '' ?>" id="otp-login-pane" role="tabpanel">
                        <div class="p-3 bg-success-subtle rounded-3 border border-success-subtle mb-3">
                            <div class="d-flex align-items-center gap-2 text-success fw-bold small mb-1">
                                <i class="bi bi-shield-check fs-5"></i>
                                <span>Login Cepat Tanpa Password</span>
                            </div>
                            <p class="small text-muted mb-0">
                                Masukkan nomor WhatsApp atau email terdaftar Anda. Kami akan mengirimkan 6 digit kode OTP langsung ke WhatsApp Anda.
                            </p>
                        </div>

                        <form action="/APSkincek/SkinCheck/public/login/request-otp" method="POST">
                            <div class="mb-3">
                                <label for="identifier" class="form-label small fw-semibold">
                                    <i class="bi bi-whatsapp text-success me-1"></i> No. WhatsApp / Email Terdaftar
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-chat-dots-fill"></i></span>
                                    <input type="text" class="form-control rounded-end-3" id="identifier" name="identifier" placeholder="Contoh: 081224846267 atau email" required>
                                </div>
                                <div class="form-text small text-muted">
                                    Pastikan nomor WhatsApp Anda aktif untuk menerima pesan kode verifikasi.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2 mt-2 rounded-pill shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Kirim Kode OTP ke WhatsApp
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted small mb-0">Belum memiliki akun?</p>
                    <a href="/APSkincek/SkinCheck/public/register" class="btn btn-outline-primary btn-sm rounded-pill px-4 mt-2">
                        <i class="bi bi-person-plus me-1"></i> Buat Akun Baru (Register)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
