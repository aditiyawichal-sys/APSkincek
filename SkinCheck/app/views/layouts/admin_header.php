<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> - SkinCheck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Check localStorage immediately to prevent layout shift on load
        (function() {
            try {
                if (localStorage.getItem('admin_sidebar_collapsed') === 'true' && window.innerWidth >= 992) {
                    document.documentElement.classList.add('sidebar-init-collapsed');
                }
            } catch(e) {}
        })();
    </script>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background-color: #f4f6f9; }
        
        /* Layout Wrapper */
        .admin-layout-wrapper {
            min-height: 100vh;
            display: flex;
            position: relative;
            overflow-x: hidden;
            width: 100%;
        }

        /* Sidebar Styling */
        .admin-sidebar {
            width: 260px;
            min-width: 260px;
            height: 100vh;
            position: sticky;
            top: 0;
            background-color: #1a1e24 !important;
            color: #fff;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1045;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .admin-sidebar a {
            color: rgba(255,255,255,.75);
            text-decoration: none;
            padding: 11px 16px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 5px;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .admin-sidebar a:hover {
            background-color: rgba(255,255,255,0.08);
            color: #fff;
            transform: translateX(3px);
        }

        .admin-sidebar a.active {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.35);
        }

        /* Main Content */
        .admin-main-content {
            flex-grow: 1;
            min-width: 0;
            padding: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f8f9fa;
        }

        /* Desktop Collapse Behavior */
        @media (min-width: 992px) {
            .admin-layout-wrapper.sidebar-collapsed .admin-sidebar,
            html.sidebar-init-collapsed .admin-layout-wrapper .admin-sidebar {
                margin-left: -260px;
            }
        }

        /* Mobile & Tablet Drawer Behavior */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                height: 100vh;
                margin-left: 0 !important;
                transform: translateX(-100%);
                box-shadow: 0 0 30px rgba(0,0,0,0.5);
            }

            .admin-layout-wrapper.sidebar-mobile-open .admin-sidebar {
                transform: translateX(0);
            }

            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.6);
                z-index: 1040;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }

            .admin-layout-wrapper.sidebar-mobile-open .sidebar-backdrop {
                opacity: 1;
                visibility: visible;
            }
        }

        /* Topbar Header Card */
        .admin-topbar-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 16px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.06);
            margin-bottom: 24px;
        }

        .btn-sidebar-toggle {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content-center;
            border-radius: 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #343a40;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-sidebar-toggle:hover {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #ffffff;
            transform: scale(1.04);
        }

        .btn-sidebar-toggle:hover i {
            color: #ffffff !important;
        }
    </style>
</head>
<body>
    <div class="admin-layout-wrapper" id="adminWrapper">
        <!-- Backdrop for mobile overlay -->
        <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

        <!-- Sidebar -->
        <aside id="adminSidebar" class="admin-sidebar p-3 shadow-sm">
            <!-- Sidebar Header with Brand and Close Button -->
            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-secondary">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger rounded-circle text-white shadow-sm" style="width: 42px; height: 42px;">
                        <i class="bi bi-shield-lock-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white" style="letter-spacing: 0.5px;">APSkincek</h5>
                        <span class="badge bg-danger text-uppercase px-2 py-0" style="font-size: 0.68rem; letter-spacing: 0.5px;">Administrator</span>
                    </div>
                </div>
                <!-- Tombol tutup cepat sidebar -->
                <button type="button" id="sidebarCloseBtn" class="btn btn-sm text-white-50 p-1 border-0 rounded-circle" title="Tutup Sidebar" style="width: 32px; height: 32px;">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <!-- Profile Info Banner -->
            <div class="bg-dark bg-opacity-50 rounded-3 p-2 mb-3 text-center border border-secondary border-opacity-25">
                <div class="small text-white-50">
                    <i class="bi bi-person-circle text-warning me-1"></i>
                    <strong class="text-white"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong>
                </div>
            </div>

            <nav class="flex-grow-1">
                <a href="/APSkincek/SkinCheck/public/admin" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false && !strpos($_SERVER['REQUEST_URI'], '/admin/') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2 me-2 fs-5"></i> Dashboard Overview
                </a>
                <a href="/APSkincek/SkinCheck/public/admin/users" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>">
                    <i class="bi bi-people-fill me-2 fs-5"></i> Kelola Customer & Admin
                </a>
                <a href="/APSkincek/SkinCheck/public/admin/products" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/products') !== false ? 'active' : '' ?>">
                    <i class="bi bi-box-seam-fill me-2 fs-5"></i> Katalog Produk
                </a>
                <a href="/APSkincek/SkinCheck/public/admin/scans" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/scans') !== false ? 'active' : '' ?>">
                    <i class="bi bi-camera-fill me-2 fs-5"></i> Riwayat Scan Kulit
                </a>
                <a href="/APSkincek/SkinCheck/public/admin/education" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/education') !== false ? 'active' : '' ?>">
                    <i class="bi bi-journal-bookmark-fill me-2 fs-5"></i> Konten Edukasi
                </a>
                <a href="/APSkincek/SkinCheck/public/admin/logs" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/logs') !== false ? 'active' : '' ?>">
                    <i class="bi bi-clock-history me-2 fs-5"></i> Log Aktivitas Sistem
                </a>
                <a href="/APSkincek/SkinCheck/public/admin/ai-config" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/ai-config') !== false ? 'active' : '' ?>">
                    <i class="bi bi-cpu-fill me-2 fs-5"></i> Konfigurasi AI & API Key
                </a>
                <a href="/APSkincek/SkinCheck/public/admin/wa-config" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/wa-config') !== false ? 'active' : '' ?>">
                    <i class="bi bi-whatsapp me-2 fs-5 text-success"></i> Notifikasi WA (Fonnte)
                </a>
            </nav>

            <div class="pt-3 border-top border-secondary">
                <a href="/APSkincek/SkinCheck/public/dashboard?preview=customer" class="text-info mb-2">
                    <i class="bi bi-eye-fill me-2"></i> Mode Customer
                </a>
                <a href="/APSkincek/SkinCheck/public/logout" class="text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main id="adminMainContent" class="admin-main-content">
            <!-- Topbar with Toggle Button & Actions -->
            <div class="admin-topbar-card d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <!-- Tombol Buka / Tutup Sidebar -->
                    <button type="button" id="sidebarToggleBtn" class="btn-sidebar-toggle" title="Buka / Tutup Sidebar (Alt + B)">
                        <i class="bi bi-layout-sidebar-inset fs-5 text-dark" id="sidebarToggleIcon"></i>
                    </button>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark"><?= $title ?? 'Admin Dashboard' ?></h4>
                        <span class="text-muted small">Panel Kontrol Manajemen Sistem APSkincek</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill small d-none d-md-inline-block">
                        <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y') ?>
                    </span>
                    <a href="/APSkincek/SkinCheck/public/dashboard?preview=customer" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-eye me-1"></i> Pratinjau Customer
                    </a>
                </div>
            </div>

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
