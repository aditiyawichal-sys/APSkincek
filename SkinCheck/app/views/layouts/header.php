<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'SkinCheck'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/APSkincek/SkinCheck/public/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg <?= (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'navbar-dark bg-dark border-bottom border-danger' : 'navbar-dark bg-primary' ?> shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="/APSkincek/SkinCheck/public/">
                <i class="bi bi-heart-pulse-fill me-2 <?= (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'text-danger' : 'text-warning' ?>"></i>
                SkinCheck
                <?php if (isset($_SESSION['user_role'])): ?>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <span class="badge bg-danger text-white ms-2 px-2 py-1 small fw-normal"><i class="bi bi-shield-lock-fill me-1"></i> Admin Portal</span>
                    <?php else: ?>
                        <span class="badge bg-light text-primary ms-2 px-2 py-1 small fw-normal"><i class="bi bi-person-check-fill me-1"></i> Customer</span>
                    <?php endif; ?>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <!-- ADMIN NAVIGATION -->
                            <li class="nav-item">
                                <a class="nav-link text-warning fw-semibold" href="/APSkincek/SkinCheck/public/admin">
                                    <i class="bi bi-speedometer2 me-1"></i> Admin Panel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/APSkincek/SkinCheck/public/admin/users">
                                    <i class="bi bi-people me-1"></i> Kelola Customer
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/APSkincek/SkinCheck/public/admin/products">
                                    <i class="bi bi-box-seam me-1"></i> Katalog Produk
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/APSkincek/SkinCheck/public/admin/scans">
                                    <i class="bi bi-camera me-1"></i> Data Scan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-info" href="/APSkincek/SkinCheck/public/dashboard?preview=customer" title="Lihat Tampilan Customer">
                                    <i class="bi bi-eye me-1"></i> Tampilan Customer
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- CUSTOMER NAVIGATION -->
                            <li class="nav-item">
                                <a class="nav-link" href="/APSkincek/SkinCheck/public/dashboard">
                                    <i class="bi bi-speedometer me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="scanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-camera me-1"></i> Scan Kulit
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="scanDropdown">
                                    <li><a class="dropdown-item" href="/APSkincek/SkinCheck/public/face-scan"><i class="bi bi-person-bounding-box me-2 text-primary"></i> Pindai Wajah Baru</a></li>
                                    <li><a class="dropdown-item" href="/APSkincek/SkinCheck/public/sunscreen-check"><i class="bi bi-sun me-2 text-warning"></i> Cek Sunscreen</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/APSkincek/SkinCheck/public/scan-history"><i class="bi bi-clock-history me-2 text-muted"></i> Riwayat Scan Saya</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/APSkincek/SkinCheck/public/ai-assistant">
                                    <i class="bi bi-robot me-1"></i> Asisten AI
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle btn btn-sm <?= (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'btn-outline-danger text-white' : 'btn-outline-light text-white' ?> px-3 rounded-pill mt-2 mt-lg-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars($_SESSION['user_name']) ?>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <span class="badge bg-danger ms-1">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark ms-1">User</span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <li><a class="dropdown-item fw-bold text-danger" href="/APSkincek/SkinCheck/public/admin"><i class="bi bi-shield-lock me-2"></i> Buka Panel Admin</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="/APSkincek/SkinCheck/public/profile"><i class="bi bi-person me-2"></i> Profil Akun</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/APSkincek/SkinCheck/public/logout"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <?php 
                            $reqUri = $_SERVER['REQUEST_URI'] ?? ''; 
                            $isLogin = strpos($reqUri, 'login') !== false;
                            $isRegister = strpos($reqUri, 'register') !== false;
                        ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $isLogin ? 'active fw-bold text-warning' : '' ?>" href="/APSkincek/SkinCheck/public/login">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $isRegister ? 'active fw-bold text-warning' : '' ?>" href="/APSkincek/SkinCheck/public/register">
                                <i class="bi bi-person-plus me-1"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error_msg'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success_msg'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>
