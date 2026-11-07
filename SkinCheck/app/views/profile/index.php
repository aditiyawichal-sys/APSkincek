<?php require_once '../app/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Flash Message Alerts -->
            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="bg-primary bg-gradient p-4 text-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 64px; height: 64px; font-size: 1.8rem; font-weight: bold;">
                            <?= strtoupper(substr($user['name'] ?? $_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-1 text-white"><?= htmlspecialchars($user['name'] ?? $_SESSION['user_name'] ?? 'User') ?></h3>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge <?= ($user['role'] ?? $_SESSION['user_role'] ?? 'user') === 'admin' ? 'bg-danger' : 'bg-light text-primary' ?> px-3 py-1 rounded-pill">
                                    <i class="bi <?= ($user['role'] ?? $_SESSION['user_role'] ?? 'user') === 'admin' ? 'bi-shield-lock-fill' : 'bi-person-fill' ?> me-1"></i>
                                    <?= ($user['role'] ?? $_SESSION['user_role'] ?? 'user') === 'admin' ? 'Administrator' : 'Customer' ?>
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                    <i class="bi bi-check2-circle me-1"></i> <?= ucfirst($user['status'] ?? 'Active') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php if (($user['role'] ?? $_SESSION['user_role'] ?? 'user') === 'admin'): ?>
                        <a href="/APSkincek/SkinCheck/public/admin" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="bi bi-speedometer2 me-1"></i> Ke Panel Admin
                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                        <i class="bi bi-person-gear text-primary me-2"></i> Pengaturan Informasi Profil
                    </h5>

                    <form action="/APSkincek/SkinCheck/public/profile/update" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($user['name'] ?? $_SESSION['user_name'] ?? '') ?>" required>
                                <div class="form-text small">Nama Anda yang akan ditampilkan di seluruh sistem APSkincek.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Alamat Email</label>
                                <input type="email" name="email" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($user['email'] ?? $_SESSION['user_email'] ?? '') ?>" required>
                                <div class="form-text small">Email utama untuk login akun Anda.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">
                                    <i class="bi bi-whatsapp text-success me-1"></i> No. WhatsApp / Telepon
                                </label>
                                <input type="tel" name="phone" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($user['phone'] ?? $_SESSION['user_phone'] ?? '') ?>" placeholder="08xxxxxxxxxx" required>
                                <div class="form-text small">Nomor WhatsApp untuk menerima notifikasi sistem via Fonnte.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Ganti Password (Opsional)</label>
                                <input type="password" name="password" class="form-control rounded-3 py-2" placeholder="Kosongkan jika tidak ingin mengubah password">
                                <div class="form-text small">Hanya diisi jika ingin memperbarui kata sandi.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Tipe Kulit</label>
                                <input type="text" class="form-control rounded-3 py-2 bg-light text-muted" value="<?= htmlspecialchars($user['skin_type'] ?? 'Belum terdeteksi (Lakukan Face Scan)') ?>" disabled>
                                <div class="form-text small">Tipe kulit didapatkan dari hasil analisa Face Scan AI.</div>
                            </div>

                            <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i> Terdaftar sejak: <?= !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-' ?>
                                </span>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Profil
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card Hapus Akun Mandiri -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border border-danger border-opacity-25">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="fw-bold text-danger mb-1">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Hapus Akun Saya
                            </h6>
                            <p class="text-muted small mb-0">
                                Setelah akun dihapus, seluruh data profil, riwayat scan kulit, dan analisa Anda akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                        <div>
                            <form action="/APSkincek/SkinCheck/public/profile/delete" method="POST" onsubmit="return confirm('PERINGATAN TERAKHIR: Apakah Anda yakin ingin menghapus akun Anda secara permanen? Seluruh riwayat scan dan data akun tidak dapat dipulihkan kembali.')">
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm text-nowrap">
                                    <i class="bi bi-trash3-fill me-1"></i> Hapus Akun Saya
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
