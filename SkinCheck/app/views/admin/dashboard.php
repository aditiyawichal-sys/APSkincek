<?php require_once '../app/views/layouts/admin_header.php'; ?>

<!-- STATS OVERVIEW CARDS -->
<div class="row g-3 mb-4">
    <!-- 1. Total Customer -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 text-uppercase small fw-bold mb-1">Customer Terdaftar</div>
                    <h2 class="fw-bold mb-0"><?= $stats['total_customers'] ?? 0 ?></h2>
                    <div class="small text-white-50 mt-1">Pelanggan Aktif</div>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-people-fill fs-3 text-white"></i>
                </div>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 py-2 px-4">
                <a href="/APSkincek/SkinCheck/public/admin/users" class="text-white text-decoration-none small d-flex justify-content-between align-items-center">
                    <span>Kelola Customer</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Total Admin -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-danger text-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 text-uppercase small fw-bold mb-1">Total Admin</div>
                    <h2 class="fw-bold mb-0"><?= $stats['total_admins'] ?? 0 ?></h2>
                    <div class="small text-white-50 mt-1">Pengelola Sistem</div>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-shield-lock-fill fs-3 text-white"></i>
                </div>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 py-2 px-4">
                <a href="/APSkincek/SkinCheck/public/admin/users" class="text-white text-decoration-none small d-flex justify-content-between align-items-center">
                    <span>Daftar Admin</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. Total Scans -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-success text-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 text-uppercase small fw-bold mb-1">Total Scan Kulit</div>
                    <h2 class="fw-bold mb-0"><?= $stats['total_scans'] ?? 0 ?></h2>
                    <div class="small text-white-50 mt-1">
                        <?= $stats['face_scans'] ?? 0 ?> Wajah | <?= $stats['sunscreen_scans'] ?? 0 ?> Sunscreen
                    </div>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-camera-fill fs-3 text-white"></i>
                </div>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 py-2 px-4">
                <a href="/APSkincek/SkinCheck/public/admin/scans" class="text-white text-decoration-none small d-flex justify-content-between align-items-center">
                    <span>Lihat Hasil Scan</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. Total Produk -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-dark text-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 text-uppercase small fw-bold mb-1">Katalog Produk</div>
                    <h2 class="fw-bold mb-0"><?= $stats['total_products'] ?? 0 ?></h2>
                    <div class="small text-white-50 mt-1">Item Aktif di Database</div>
                </div>
                <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-box-seam-fill fs-3 text-warning"></i>
                </div>
            </div>
            <div class="card-footer bg-white bg-opacity-10 border-0 py-2 px-4">
                <a href="/APSkincek/SkinCheck/public/admin/products" class="text-white text-decoration-none small d-flex justify-content-between align-items-center">
                    <span>Kelola Katalog</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- QUICK ACTION BAR -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center">
                <i class="bi bi-lightning-charge-fill text-warning fs-5 me-2"></i>
                <span class="fw-bold text-dark">Aksi Cepat Administrator:</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="/APSkincek/SkinCheck/public/admin/users#addUserModal" data-bs-toggle="modal" data-bs-target="#quickAddUserModal" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-person-plus-fill me-1"></i> + Buat Akun Customer
                </a>
                <a href="/APSkincek/SkinCheck/public/admin/products" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                    <i class="bi bi-plus-circle me-1"></i> + Tambah Produk
                </a>
                <a href="/APSkincek/SkinCheck/public/admin/education" class="btn btn-outline-info btn-sm rounded-pill px-3">
                    <i class="bi bi-journal-plus me-1"></i> + Buat Konten Edukasi
                </a>
            </div>
        </div>
    </div>
</div>

<!-- TABLES ROW -->
<div class="row g-4">
    <!-- Customer & User Terbaru -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-people text-primary me-2"></i> Pengguna & Customer Terbaru
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-primary active py-1 px-3 btn-dash-filter rounded-start-pill" data-filter="all">Semua</button>
                        <button type="button" class="btn btn-outline-primary py-1 px-3 btn-dash-filter" data-filter="customer">Customer</button>
                        <button type="button" class="btn btn-outline-primary py-1 px-3 btn-dash-filter rounded-end-pill" data-filter="admin">Admin</button>
                    </div>
                    <a href="/APSkincek/SkinCheck/public/admin/users" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <!-- Toolbar Pencarian Cepat Email / Nama Customer -->
            <div class="px-4 py-2 border-bottom bg-light bg-opacity-50">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="dashUserSearchInput" class="form-control form-control-sm rounded-pill ps-5 pe-5 bg-white border shadow-none" placeholder="Ketik email atau nama customer..." autocomplete="off">
                    <button id="clearDashSearchBtn" class="btn btn-sm btn-link position-absolute top-50 end-0 translate-middle-y me-2 text-muted text-decoration-none d-none p-0" type="button" title="Hapus pencarian">
                        <i class="bi bi-x-circle-fill text-secondary"></i>
                    </button>
                </div>
                <div id="dashSearchFeedback" class="small text-muted mt-1 ps-2 d-none">
                    Menemukan <strong id="dashMatchCount">0</strong> pengguna yang cocok
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-4">Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="pe-4">Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentUsers)): ?>
                                <?php foreach ($recentUsers as $u): ?>
                                    <tr class="dash-user-row" 
                                        data-role="<?= ($u['role'] === 'admin') ? 'admin' : 'customer' ?>"
                                        data-name="<?= strtolower(htmlspecialchars($u['name'])) ?>"
                                        data-email="<?= strtolower(htmlspecialchars($u['email'])) ?>">
                                        <td class="ps-4 fw-semibold"><?= htmlspecialchars($u['name']) ?></td>
                                        <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
                                        <td>
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">Administrator</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Customer</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $u['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $u['status'] ?>
                                            </span>
                                        </td>
                                        <td class="pe-4 text-muted small"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada pengguna terdaftar</td></tr>
                            <?php endif; ?>
                            <tr id="dashNoMatchRow" class="d-none">
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-search me-1"></i> Tidak ada customer atau email yang cocok dengan pencarian
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Scan Terbaru -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-camera text-success me-2"></i> Riwayat Scan Terbaru
                </h5>
                <a href="/APSkincek/SkinCheck/public/admin/scans" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tipe Scan</th>
                                <th>Status</th>
                                <th class="pe-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentScans)): ?>
                                <?php foreach ($recentScans as $s): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php if ($s['scan_type'] === 'face'): ?>
                                                <span class="badge bg-primary-subtle text-primary border"><i class="bi bi-person me-1"></i> Scan Wajah</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-dark border"><i class="bi bi-sun me-1"></i> Sunscreen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-success small"><?= $s['status'] ?></span>
                                        </td>
                                        <td class="pe-4 text-muted small"><?= date('d M Y, H:i', strtotime($s['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada scan kulit</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quick Add User -->
<div class="modal fade" id="quickAddUserModal" tabindex="-1" aria-labelledby="quickAddUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold" id="quickAddUserModalLabel"><i class="bi bi-person-plus-fill me-2"></i> Buat Akun Customer Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/APSkincek/SkinCheck/public/admin/users/create" method="POST">
                <input type="hidden" name="role" value="user">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Masukkan nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat Email</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="nama@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.btn-dash-filter');
    const userRows = document.querySelectorAll('.dash-user-row');
    const searchInput = document.getElementById('dashUserSearchInput');
    const clearBtn = document.getElementById('clearDashSearchBtn');
    const feedbackBox = document.getElementById('dashSearchFeedback');
    const matchCountEl = document.getElementById('dashMatchCount');
    const noMatchRow = document.getElementById('dashNoMatchRow');

    let currentRoleFilter = 'all';

    function applyFilterAndSearch() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let visibleCount = 0;

        if (clearBtn) {
            clearBtn.classList.toggle('d-none', query.length === 0);
        }

        userRows.forEach(row => {
            const role = row.getAttribute('data-role');
            const name = row.getAttribute('data-name') || '';
            const email = row.getAttribute('data-email') || '';

            const matchesRole = (currentRoleFilter === 'all' || role === currentRoleFilter);
            const matchesQuery = (query === '' || email.includes(query) || name.includes(query));

            if (matchesRole && matchesQuery) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (feedbackBox && matchCountEl) {
            if (query.length > 0) {
                feedbackBox.classList.remove('d-none');
                matchCountEl.textContent = visibleCount;
            } else {
                feedbackBox.classList.add('d-none');
            }
        }

        if (noMatchRow) {
            noMatchRow.classList.toggle('d-none', visibleCount > 0);
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilterAndSearch);
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.focus();
            applyFilterAndSearch();
        });
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-primary');
            });
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-primary');

            currentRoleFilter = this.getAttribute('data-filter');
            applyFilterAndSearch();
        });
    });
});
</script>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>
