<?php require_once '../app/views/layouts/header.php'; ?>
<div class="row justify-content-center my-4">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 56px; height: 56px;">
                    <i class="bi bi-person-plus text-primary fs-3"></i>
                </div>
                <h4 class="fw-bold mb-1">Daftar Akun Baru</h4>
                <p class="text-muted small mb-0">Halaman Registrasi Pengguna SkinCheck</p>
            </div>
            <div class="card-body p-4 pt-2">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form action="/APSkincek/SkinCheck/public/register" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control rounded-3" id="name" name="name" placeholder="Masukkan nama lengkap Anda" autocomplete="off" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold">Alamat Email</label>
                        <input type="email" class="form-control rounded-3" id="email" name="email" placeholder="nama@email.com" autocomplete="off" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label small fw-semibold d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-whatsapp text-success me-1"></i> No. WhatsApp / Telepon</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.72rem;">
                                <i class="bi bi-bell-fill me-1"></i>Notifikasi Fonnte WA
                            </span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light rounded-start-3 text-success border-end-0"><i class="bi bi-telephone-fill"></i></span>
                            <input type="tel" class="form-control rounded-end-3" id="phone" name="phone" placeholder="Contoh: 08123456789 atau 628123456789" autocomplete="tel" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                        </div>
                        <div class="form-text small text-muted">
                            Nomor WhatsApp aktif untuk menerima pesan konfirmasi & pemberitahuan akun.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label small fw-semibold">Password</label>
                        <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Minimal 6 karakter" autocomplete="new-password" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label small fw-semibold">Konfirmasi Password</label>
                        <input type="password" class="form-control rounded-3" id="confirm_password" name="confirm_password" placeholder="Ulangi password di atas" autocomplete="new-password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 mt-2 shadow-sm rounded-pill">
                        <i class="bi bi-shield-lock-fill me-1"></i> Daftar & Dapatkan Kode OTP WhatsApp
                    </button>
                    <div class="text-center mt-2 small text-muted">
                        <i class="bi bi-whatsapp text-success me-1"></i> Kode OTP 6-digit akan dikirimkan ke WhatsApp Anda untuk verifikasi keamanan.
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted small mb-0">Sudah memiliki akun terdaftar?</p>
                    <a href="/APSkincek/SkinCheck/public/login" class="btn btn-outline-primary btn-sm rounded-pill px-4 mt-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk / Login di Sini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../app/views/layouts/footer.php'; ?>
