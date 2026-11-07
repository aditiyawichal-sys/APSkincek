<?php require_once '../app/views/layouts/admin_header.php'; ?>

<?php
$customerCount = count(array_filter($users, fn($u) => $u['role'] === 'user' || $u['role'] === 'customer'));
$adminCount = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark"><i class="bi bi-people-fill text-primary me-2"></i> Kelola Customer & Administrator</h3>
        <p class="text-muted small mb-0">Kelola daftar pelanggan, akun admin, serta hak akses sistem APSkincek.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> + Buat Akun Baru
        </button>
    </div>
</div>

<!-- INFO CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Total Pengguna</div>
                <div class="fs-4 fw-bold text-dark"><?= count($users) ?> Orang</div>
            </div>
            <div class="rounded-circle bg-light p-3 text-primary"><i class="bi bi-people fs-4"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Customer (Pelanggan)</div>
                <div class="fs-4 fw-bold text-primary"><?= $customerCount ?> Orang</div>
            </div>
            <div class="rounded-circle bg-primary-subtle p-3 text-primary"><i class="bi bi-person-check fs-4"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Administrator</div>
                <div class="fs-4 fw-bold text-danger"><?= $adminCount ?> Orang</div>
            </div>
            <div class="rounded-circle bg-danger-subtle p-3 text-danger"><i class="bi bi-shield-lock fs-4"></i></div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <!-- PENCARIAN & FILTER TOOLBAR -->
    <div class="card-header bg-white border-0 p-4 pb-0">
        <div class="row g-3 align-items-center">
            <!-- Input Pencarian Real-Time -->
            <div class="col-lg-6 col-md-7">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-5"></i>
                    <input type="text" id="userSearchInput" class="form-control form-control-lg rounded-pill ps-5 pe-5 border-2 shadow-sm" placeholder="Ketik nama customer atau email..." autocomplete="off">
                    <button id="clearSearchBtn" class="btn btn-sm btn-link position-absolute top-50 end-0 translate-middle-y me-2 text-muted text-decoration-none d-none" type="button" title="Hapus pencarian">
                        <i class="bi bi-x-circle-fill fs-5 text-secondary"></i>
                    </button>
                </div>
            </div>

            <!-- Filter Kategori (Customer / Admin / Semua) -->
            <div class="col-lg-6 col-md-5 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
                <span class="text-muted small fw-semibold me-1 d-none d-lg-inline"><i class="bi bi-funnel me-1"></i> Filter:</span>
                <div class="btn-group rounded-pill p-1 bg-light border shadow-sm" role="group">
                    <button type="button" class="btn btn-sm rounded-pill px-3 role-filter-btn btn-primary active" data-role="all">
                        Semua (<span id="countAllBadge"><?= count($users) ?></span>)
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 role-filter-btn btn-light" data-role="customer">
                        Customer (<span id="countCustomerBadge"><?= $customerCount ?></span>)
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 role-filter-btn btn-light" data-role="admin">
                        Admin (<span id="countAdminBadge"><?= $adminCount ?></span>)
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
            <div id="searchResultCount" class="text-muted small">
                Menampilkan <strong><span id="visibleCount"><?= count($users) ?></span></strong> dari <?= count($users) ?> pengguna
            </div>
            <div id="activeFilterBadge" class="d-none">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 small">
                    Hasil untuk: "<span id="activeQueryText"></span>"
                </span>
            </div>
        </div>
    </div>

    <div class="card-body p-4 pt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable" width="100%">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Nama</th>
                        <th>Email</th>
                        <th>No. WhatsApp</th>
                        <th>Role / Hak Akses</th>
                        <th>Status</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-end pe-3">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <?php $isCustomerRole = ($u['role'] === 'user' || $u['role'] === 'customer'); ?>
                            <tr class="user-row" 
                                data-name="<?= strtolower(htmlspecialchars($u['name'])) ?>" 
                                data-email="<?= strtolower(htmlspecialchars($u['email'])) ?>"
                                data-phone="<?= strtolower(htmlspecialchars($u['phone'] ?? '')) ?>"
                                data-id="<?= strtolower(htmlspecialchars($u['id'])) ?>"
                                data-role="<?= $isCustomerRole ? 'customer' : 'admin' ?>"
                                data-status="<?= strtolower($u['status']) ?>">
                                <td class="ps-3">
                                    <div class="fw-bold text-dark user-name-text"><?= htmlspecialchars($u['name']) ?></div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">ID: <?= substr($u['id'], 0, 8) ?>...</div>
                                </td>
                                <td><span class="user-email-text"><?= htmlspecialchars($u['email']) ?></span></td>
                                <td>
                                    <?php if (!empty($u['phone'])): ?>
                                        <?php 
                                            $waClean = preg_replace('/[^0-9]/', '', $u['phone']);
                                            if (strpos($waClean, '08') === 0) $waTarget = '628' . substr($waClean, 2);
                                            else if (strpos($waClean, '8') === 0) $waTarget = '62' . $waClean;
                                            else $waTarget = $waClean;
                                        ?>
                                        <a href="https://wa.me/<?= $waTarget ?>" target="_blank" class="btn btn-xs btn-outline-success rounded-pill px-2 py-1 text-decoration-none shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.8rem;" title="Buka WhatsApp untuk kirim pesan">
                                            <i class="bi bi-whatsapp"></i> <?= htmlspecialchars($u['phone']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">- Belum ada -</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-shield-lock-fill me-1"></i> Administrator</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary px-3 py-2 rounded-pill"><i class="bi bi-person me-1"></i> Customer</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $u['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?> px-2 py-1">
                                        <?= ucfirst($u['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y, H:i', strtotime($u['created_at'])) ?></td>
                                <td class="text-end pe-3">
                                    <?php if ($u['id'] === $_SESSION['user_id']): ?>
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 me-1 edit-user-trigger shadow-sm" 
                                            data-id="<?= $u['id'] ?>" 
                                            data-name="<?= htmlspecialchars($u['name']) ?>" 
                                            data-email="<?= htmlspecialchars($u['email']) ?>" 
                                            data-phone="<?= htmlspecialchars($u['phone'] ?? '') ?>"
                                            data-role="<?= $u['role'] ?>"
                                            data-is-self="1"
                                            title="Ganti Nama / Edit Akun Saya">
                                            <i class="bi bi-pencil-square me-1"></i> Ganti Nama Saya
                                        </button>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill small">
                                            <i class="bi bi-person-check-fill me-1"></i> Akun Anda
                                        </span>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 me-1 edit-user-trigger" 
                                            data-id="<?= $u['id'] ?>" 
                                            data-name="<?= htmlspecialchars($u['name']) ?>" 
                                            data-email="<?= htmlspecialchars($u['email']) ?>" 
                                            data-phone="<?= htmlspecialchars($u['phone'] ?? '') ?>"
                                            data-role="<?= $u['role'] ?>"
                                            data-is-self="0"
                                            title="Edit Data Pengguna">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </button>


                                        <!-- Toggle Status Form -->
                                        <form action="/APSkincek/SkinCheck/public/admin/users/status" method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="status" value="<?= $u['status'] === 'active' ? 'inactive' : 'active' ?>">
                                            <button type="submit" class="btn btn-sm <?= $u['status'] === 'active' ? 'btn-outline-secondary' : 'btn-outline-success' ?> rounded-pill px-2 me-1">
                                                <?= $u['status'] === 'active' ? '<i class="bi bi-pause-circle me-1"></i> Nonaktifkan' : '<i class="bi bi-check-circle me-1"></i> Aktifkan' ?>
                                            </button>
                                        </form>

                                        <!-- Hapus Akun Form -->
                                        <form action="/APSkincek/SkinCheck/public/admin/users/delete" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus akun <?= htmlspecialchars(addslashes($u['name'])) ?> secara permanen? Semua riwayat scan dan data terkait akan ikut terhapus.')">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Hapus Akun Permanen">
                                                <i class="bi bi-trash3-fill me-1"></i> Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data pengguna ditemukan.</td></tr>
                    <?php endif; ?>

                    <!-- Baris khusus jika pencarian tidak menemukan hasil -->
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="7" class="text-center py-5">
                            <div class="py-3">
                                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 64px; height: 64px;">
                                    <i class="bi bi-search text-secondary fs-2"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Customer / Pengguna Tidak Ditemukan</h5>
                                <p class="text-muted small mb-3">Tidak ada nama, email, atau no. WA yang cocok dengan kata kunci: <strong class="text-danger" id="noResultsQuery"></strong></p>
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 shadow-sm" onclick="resetUserSearch()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Tampilkan Semua Pengguna
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');
    const userRows = document.querySelectorAll('.user-row');
    const noResultsRow = document.getElementById('noResultsRow');
    const visibleCountSpan = document.getElementById('visibleCount');
    const activeFilterBadge = document.getElementById('activeFilterBadge');
    const activeQueryText = document.getElementById('activeQueryText');
    const noResultsQuery = document.getElementById('noResultsQuery');
    const roleFilterButtons = document.querySelectorAll('.role-filter-btn');

    let currentRole = 'all';

    function filterTable() {
        const query = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        // Toggle clear button
        if (query.length > 0) {
            clearBtn.classList.remove('d-none');
            activeFilterBadge.classList.remove('d-none');
            activeQueryText.textContent = searchInput.value.trim();
        } else {
            clearBtn.classList.add('d-none');
            activeFilterBadge.classList.add('d-none');
        }

        userRows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const email = row.getAttribute('data-email') || '';
            const phone = row.getAttribute('data-phone') || '';
            const id = row.getAttribute('data-id') || '';
            const role = row.getAttribute('data-role') || '';

            const matchesQuery = !query || name.includes(query) || email.includes(query) || phone.includes(query) || id.includes(query);
            const matchesRole = (currentRole === 'all') || (role === currentRole);

            if (matchesQuery && matchesRole) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results row
        if (visibleCount === 0 && (userRows.length > 0)) {
            noResultsRow.style.display = '';
            noResultsQuery.textContent = searchInput.value.trim() || currentRole;
        } else {
            noResultsRow.style.display = 'none';
        }

        visibleCountSpan.textContent = visibleCount;
    }

    // Event listener on typing - INSTANT REAL-TIME SEARCH
    searchInput.addEventListener('input', filterTable);

    // Clear search button
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterTable();
        searchInput.focus();
    });

    // Role filter buttons click
    roleFilterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            roleFilterButtons.forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-light');
            });
            this.classList.remove('btn-light');
            this.classList.add('btn-primary', 'active');

            currentRole = this.getAttribute('data-role');
            filterTable();
        });
    });

    // Reset helper
    window.resetUserSearch = function() {
        searchInput.value = '';
        currentRole = 'all';
        roleFilterButtons.forEach(b => {
            b.classList.remove('btn-primary', 'active');
            b.classList.add('btn-light');
        });
        const allBtn = document.querySelector('.role-filter-btn[data-role="all"]');
        if (allBtn) {
            allBtn.classList.remove('btn-light');
            allBtn.classList.add('btn-primary', 'active');
        }
        filterTable();
        searchInput.focus();
    };

    // Keyboard shortcut: Press Escape to clear search
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            resetUserSearch();
        }
    });

    // Modal Edit User Trigger
    const editModalEl = document.getElementById('editUserModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const editUserId = document.getElementById('edit_user_id');
    const editName = document.getElementById('edit_name');
    const editEmail = document.getElementById('edit_email');
    const editPhone = document.getElementById('edit_phone');
    const editRole = document.getElementById('edit_role');
    const editPassword = document.getElementById('edit_password');
    const editSelfNotice = document.getElementById('editUserSelfNotice');
    const editModalLabel = document.getElementById('editUserModalLabel');

    document.querySelectorAll('.edit-user-trigger').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            const phone = this.getAttribute('data-phone');
            const role = this.getAttribute('data-role');
            const isSelf = this.getAttribute('data-is-self') === '1';

            if (editUserId) editUserId.value = id;
            if (editName) editName.value = name;
            if (editEmail) editEmail.value = email;
            if (editPhone) editPhone.value = phone || '';
            if (editPassword) editPassword.value = '';

            if (editRole) {
                editRole.value = (role === 'admin') ? 'admin' : 'user';
            }

            if (isSelf) {
                if (editSelfNotice) editSelfNotice.classList.remove('d-none');
                if (editModalLabel) editModalLabel.innerHTML = '<i class="bi bi-person-gear text-warning me-2"></i> Ganti Nama / Edit Akun Saya';
            } else {
                if (editSelfNotice) editSelfNotice.classList.add('d-none');
                if (editModalLabel) editModalLabel.innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Pengguna (' + name + ')';
            }

            if (editModal) {
                editModal.show();
            }
        });
    });
});
</script>

<!-- Modal Tambah Akun Baru -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold" id="addUserModalLabel"><i class="bi bi-person-plus-fill me-2"></i> Buat Akun Customer Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/APSkincek/SkinCheck/public/admin/users/create" method="POST">
                <input type="hidden" name="role" value="user">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Masukkan nama pengguna" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat Email</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="nama@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><i class="bi bi-whatsapp text-success me-1"></i> No. WhatsApp / Telepon</label>
                        <input type="tel" name="phone" class="form-control rounded-3" placeholder="Contoh: 08123456789" required>
                        <div class="form-text small text-muted">Nomor WhatsApp untuk menerima notifikasi Fonnte.</div>
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

<!-- Modal Edit Akun Pengguna / Admin -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold" id="editUserModalLabel">
                    <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Akun
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/APSkincek/SkinCheck/public/admin/users/update" method="POST">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body p-4">
                    <div id="editUserSelfNotice" class="alert alert-primary py-2 px-3 small rounded-3 d-none mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i> 
                        <strong>Mengedit Akun Anda:</strong> Nama yang Anda ubah di sini akan langsung diperbarui di nama profil dan navigasi admin.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap / Nama Admin <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control rounded-3" placeholder="Masukkan nama Anda" required>
                        <div class="form-text small text-muted">Ubah nama ini sesuai nama asli admin atau pengguna.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-3" placeholder="nama@email.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><i class="bi bi-whatsapp text-success me-1"></i> No. WhatsApp / Telepon</label>
                        <input type="tel" name="phone" id="edit_phone" class="form-control rounded-3" placeholder="Contoh: 08123456789">
                        <div class="form-text small text-muted">Nomor WhatsApp akun ini untuk notifikasi via Fonnte.</div>
                    </div>

                    <input type="hidden" name="role" id="edit_role">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password Baru (Opsional)</label>
                        <input type="password" name="password" id="edit_password" class="form-control rounded-3" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                        <div class="form-text small text-muted">Kosongkan jika password tidak ingin diubah.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/admin_footer.php'; ?>
