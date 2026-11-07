<?php
require_once '../app/helpers/AuthHelper.php';
require_once '../app/helpers/FonnteHelper.php';

class AuthController extends Controller {
    public function login() {
        // If already logged in, redirect based on role
        if (AuthHelper::isLoggedIn()) {
            if (AuthHelper::isAdmin()) {
                header('Location: /APSkincek/SkinCheck/public/admin');
            } else {
                header('Location: /APSkincek/SkinCheck/public/dashboard');
            }
            exit;
        }

        $data = ['title' => 'Login'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            $userModel = $this->model('User');
            $user = $userModel->findByEmail($email);
            
            if ($user && $user['status'] === 'active') {
                if (password_verify($password, $user['password'])) {
                    AuthHelper::login($user);
                    
                    // Log action
                    $logModel = $this->model('ActivityLog');
                    $logModel->logAction($user['id'], 'User Login', 'Logged in successfully as ' . ($user['role'] ?? 'user'));
                    
                    // Direct admin to Admin Dashboard, customers to Customer Dashboard
                    if ($user['role'] === 'admin') {
                        header('Location: /APSkincek/SkinCheck/public/admin');
                    } else {
                        header('Location: /APSkincek/SkinCheck/public/dashboard');
                    }
                    exit;
                } else {
                    $data['error'] = 'Password salah. Silakan coba lagi.';
                }
            } else if ($user && $user['status'] !== 'active') {
                $data['error'] = 'Akun sedang non-aktif. Silakan hubungi admin.';
            } else {
                $data['error'] = 'Email tidak ditemukan.';
            }
        }
        
        $this->view('auth/login', $data);
    }

    /**
     * Meminta pengiriman kode OTP untuk login via WhatsApp
     */
    public function requestLoginOtp() {
        if (AuthHelper::isLoggedIn()) {
            header('Location: /APSkincek/SkinCheck/public/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /APSkincek/SkinCheck/public/login');
            exit;
        }

        $identifier = trim($_POST['identifier'] ?? '');
        if (empty($identifier)) {
            $_SESSION['error_msg'] = 'Silakan masukkan nomor WhatsApp atau email terdaftar Anda.';
            header('Location: /APSkincek/SkinCheck/public/login?tab=otp');
            exit;
        }

        $userModel = $this->model('User');
        $cleanPhone = preg_replace('/[^0-9]/', '', $identifier);
        
        // Cari user berdasarkan no wa atau email
        $user = null;
        if (!empty($cleanPhone) && strlen($cleanPhone) >= 9) {
            if (strpos($cleanPhone, '62') === 0) {
                $standardPhone = '0' . substr($cleanPhone, 2);
            } else if (strpos($cleanPhone, '0') === 0) {
                $standardPhone = $cleanPhone;
            } else {
                $standardPhone = '0' . $cleanPhone;
            }
            $user = $userModel->findByPhone($standardPhone) ?: $userModel->findByPhone($cleanPhone);
        }

        if (!$user) {
            $user = $userModel->findByEmail($identifier);
        }

        if (!$user) {
            $_SESSION['error_msg'] = 'Akun dengan nomor WhatsApp / Email tersebut tidak ditemukan. Silakan periksa kembali atau daftar baru.';
            header('Location: /APSkincek/SkinCheck/public/login?tab=otp');
            exit;
        }

        if ($user['status'] !== 'active') {
            $_SESSION['error_msg'] = 'Akun sedang non-aktif. Silakan hubungi admin.';
            header('Location: /APSkincek/SkinCheck/public/login?tab=otp');
            exit;
        }

        if (empty($user['phone'])) {
            $_SESSION['error_msg'] = 'Akun Anda belum memiliki nomor WhatsApp terdaftar. Silakan login menggunakan email & password.';
            header('Location: /APSkincek/SkinCheck/public/login');
            exit;
        }

        // Cek cooldown
        $otpModel = $this->model('OtpVerification');
        $canResend = $otpModel->canResend($user['phone'], 'login');
        if (!$canResend['allowed']) {
            $_SESSION['error_msg'] = "Harap tunggu {$canResend['wait_seconds']} detik sebelum meminta kode OTP kembali.";
            header('Location: /APSkincek/SkinCheck/public/login?tab=otp');
            exit;
        }

        // Buat OTP login baru
        $otpData = $otpModel->createOtp($user['phone'], 'login', [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role']
        ], $user['email']);

        // Kirim OTP via Fonnte WA
        $fonnteRes = FonnteHelper::sendOtp($user['phone'], $otpData['otp']);

        // Catat ke session
        $_SESSION['pending_otp'] = [
            'phone' => $user['phone'],
            'email' => $user['email'],
            'name' => $user['name'],
            'type' => 'login',
            'expires_at' => $otpData['expires_timestamp']
        ];

        // Jika gateway offline/device disconnected, simpan catatan bantuan uji coba
        if (empty($fonnteRes['success'])) {
            $_SESSION['otp_dev_hint'] = "Status Gateway Fonnte: " . ($fonnteRes['message'] ?? 'Device belum terhubung') . ". [Kode OTP Uji Coba: {$otpData['otp']}]";
        } else {
            unset($_SESSION['otp_dev_hint']);
        }

        $_SESSION['success_msg'] = "Kode OTP telah dikirimkan ke WhatsApp: " . htmlspecialchars($user['phone']);
        header('Location: /APSkincek/SkinCheck/public/verify-otp?type=login');
        exit;
    }

    public function register() {
        if (AuthHelper::isLoggedIn()) {
            if (AuthHelper::isAdmin()) {
                header('Location: /APSkincek/SkinCheck/public/admin');
            } else {
                header('Location: /APSkincek/SkinCheck/public/dashboard');
            }
            exit;
        }

        $data = ['title' => 'Register'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');
            
            $userModel = $this->model('User');

            // Format dan standarisasi nomor telepon
            $cleanDigits = preg_replace('/[^0-9]/', '', $phone);
            if (strpos($cleanDigits, '62') === 0) {
                $standardPhone = '0' . substr($cleanDigits, 2);
            } else if (strpos($cleanDigits, '0') === 0) {
                $standardPhone = $cleanDigits;
            } else {
                $standardPhone = '0' . $cleanDigits;
            }
            
            if (empty($name) || empty($email) || empty($phone) || empty($password)) {
                $data['error'] = 'Semua kolom termasuk No. WhatsApp / Telepon wajib diisi.';
            } else if (strlen($cleanDigits) < 9 || strlen($cleanDigits) > 16) {
                $data['error'] = 'Nomor WhatsApp / HP tidak valid. Masukkan minimal 9-15 digit angka (misal: 08123456789).';
            } else if (!empty($confirmPassword) && $password !== $confirmPassword) {
                $data['error'] = 'Konfirmasi password tidak cocok dengan password.';
            } else if (strlen($password) < 6) {
                $data['error'] = 'Password minimal harus 6 karakter.';
            } else if ($userModel->findByEmail($email)) {
                $data['error'] = 'Email tersebut sudah terdaftar. Silakan langsung login di halaman Login.';
            } else if ($userModel->findByPhone($standardPhone) || $userModel->findByPhone($phone)) {
                $data['error'] = 'Nomor WhatsApp tersebut sudah terdaftar pada akun lain. Silakan gunakan nomor lain.';
            } else {
                $assignedRole = 'user';

                // Cek cooldown jika ada percobaan sebelumnya
                $otpModel = $this->model('OtpVerification');
                $canResend = $otpModel->canResend($standardPhone, 'register');
                if (!$canResend['allowed']) {
                    $data['error'] = "Harap tunggu {$canResend['wait_seconds']} detik sebelum meminta kode OTP kembali.";
                    $this->view('auth/register', $data);
                    return;
                }

                // Data pendaftaran sementara yang akan disimpan saat OTP berhasil diverifikasi
                $pendingData = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $standardPhone,
                    'password' => $password,
                    'role' => $assignedRole
                ];

                // Buat kode OTP registrasi 6 digit
                $otpData = $otpModel->createOtp($standardPhone, 'register', $pendingData, $email);

                // Kirim kode OTP via Fonnte WA
                $fonnteRes = FonnteHelper::sendOtp($standardPhone, $otpData['otp']);

                // Simpan ke session untuk halaman verifikasi
                $_SESSION['pending_otp'] = [
                    'phone' => $standardPhone,
                    'email' => $email,
                    'name' => $name,
                    'type' => 'register',
                    'expires_at' => $otpData['expires_timestamp']
                ];

                // Jika device Fonnte disconnect atau error, sediakan petunjuk untuk mempermudah testing
                if (empty($fonnteRes['success'])) {
                    $_SESSION['otp_dev_hint'] = "Status Gateway Fonnte: " . ($fonnteRes['message'] ?? 'Device belum terhubung') . ". [Kode OTP Uji Coba: {$otpData['otp']}]";
                } else {
                    unset($_SESSION['otp_dev_hint']);
                }

                $_SESSION['success_msg'] = "Kode verifikasi OTP 6-digit telah dikirimkan ke WhatsApp Anda (" . htmlspecialchars($standardPhone) . ").";
                header('Location: /APSkincek/SkinCheck/public/verify-otp?type=register');
                exit;
            }
        }

        $this->view('auth/register', $data);
    }

    /**
     * Halaman dan aksi verifikasi kode OTP (untuk registrasi maupun login)
     */
    public function verifyOtp() {
        if (AuthHelper::isLoggedIn()) {
            header('Location: /APSkincek/SkinCheck/public/dashboard');
            exit;
        }

        $type = $_GET['type'] ?? ($_SESSION['pending_otp']['type'] ?? 'register');
        $pending = $_SESSION['pending_otp'] ?? null;

        if (!$pending) {
            $_SESSION['error_msg'] = 'Sesi verifikasi telah kedaluwarsa atau tidak valid. Silakan ulangi proses pendaftaran/login.';
            header('Location: /APSkincek/SkinCheck/public/login');
            exit;
        }

        $data = [
            'title' => 'Verifikasi Kode OTP WhatsApp',
            'phone' => $pending['phone'],
            'email' => $pending['email'] ?? '',
            'name' => $pending['name'] ?? '',
            'type' => $type,
            'expires_at' => $pending['expires_at'] ?? (time() + 300),
            'otp_dev_hint' => $_SESSION['otp_dev_hint'] ?? null
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otp = trim($_POST['otp'] ?? '');
            $cleanOtp = preg_replace('/[^0-9]/', '', $otp);

            if (empty($cleanOtp) || strlen($cleanOtp) < 6) {
                $data['error'] = 'Silakan masukkan 6 digit kode OTP yang telah dikirimkan ke WhatsApp Anda.';
            } else {
                $otpModel = $this->model('OtpVerification');
                $verifyResult = $otpModel->verifyOtp($pending['phone'], $cleanOtp, $type);

                if ($verifyResult['valid']) {
                    $userModel = $this->model('User');
                    $logModel = $this->model('ActivityLog');

                    if ($type === 'register') {
                        $payload = $verifyResult['payload'];
                        if (!empty($payload)) {
                            // Daftarkan akun baru ke tabel users
                            $userModel->register(
                                $payload['name'],
                                $payload['email'],
                                $payload['password'],
                                $payload['role'],
                                $payload['phone']
                            );

                            $newUser = $userModel->findByEmail($payload['email']);
                            if ($newUser) {
                                $roleLabel = ($payload['role'] === 'admin') ? 'Administrator' : 'Customer';
                                $logModel->logAction($newUser['id'], 'User Register & OTP Verified', "Registrasi berhasil diverifikasi dengan OTP WA: {$payload['phone']}");

                                // Kirim pesan selamat datang via WA
                                try {
                                    FonnteHelper::sendRegistrationNotification($newUser);
                                } catch (Exception $e) {}

                                // Hapus session pending OTP
                                unset($_SESSION['pending_otp']);
                                unset($_SESSION['otp_dev_hint']);

                                // Login otomatis
                                AuthHelper::login($newUser);

                                $_SESSION['success_msg'] = "Selamat! Nomor WhatsApp Anda berhasil diverifikasi dan akun Anda telah aktif.";

                                if ($newUser['role'] === 'admin') {
                                    header('Location: /APSkincek/SkinCheck/public/admin');
                                } else {
                                    header('Location: /APSkincek/SkinCheck/public/dashboard');
                                }
                                exit;
                            }
                        }
                    } else if ($type === 'login') {
                        $payload = $verifyResult['payload'];
                        $userId = $payload['user_id'] ?? null;
                        $user = $userModel->findById($userId);

                        if ($user && $user['status'] === 'active') {
                            $logModel->logAction($user['id'], 'User Login OTP', "Login berhasil via kode OTP WhatsApp");

                            // Hapus session pending OTP
                            unset($_SESSION['pending_otp']);
                            unset($_SESSION['otp_dev_hint']);

                            // Login user
                            AuthHelper::login($user);

                            $_SESSION['success_msg'] = "Berhasil masuk via OTP WhatsApp! Selamat datang kembali, " . htmlspecialchars($user['name']) . ".";

                            if ($user['role'] === 'admin') {
                                header('Location: /APSkincek/SkinCheck/public/admin');
                            } else {
                                header('Location: /APSkincek/SkinCheck/public/dashboard');
                            }
                            exit;
                        } else {
                            $data['error'] = 'Akun tidak ditemukan atau sedang dinonaktifkan.';
                        }
                    }
                } else {
                    $data['error'] = $verifyResult['error'] ?? 'Kode OTP yang Anda masukkan salah atau telah kedaluwarsa.';
                }
            }
        }

        $this->view('auth/verify_otp', $data);
    }

    /**
     * Mengirim ulang kode OTP via WhatsApp
     */
    public function resendOtp() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /APSkincek/SkinCheck/public/login');
            exit;
        }

        $pending = $_SESSION['pending_otp'] ?? null;
        if (!$pending) {
            $_SESSION['error_msg'] = 'Sesi verifikasi tidak ditemukan. Silakan lakukan proses dari awal.';
            header('Location: /APSkincek/SkinCheck/public/login');
            exit;
        }

        $otpModel = $this->model('OtpVerification');
        $phone = $pending['phone'];
        $type = $pending['type'];

        // Cek cooldown 60 detik
        $canResend = $otpModel->canResend($phone, $type);
        if (!$canResend['allowed']) {
            $_SESSION['error_msg'] = "Mohon tunggu {$canResend['wait_seconds']} detik sebelum meminta kode OTP baru.";
            header("Location: /APSkincek/SkinCheck/public/verify-otp?type={$type}");
            exit;
        }

        // Ambil payload dari record lama jika tipe register
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT payload, email FROM otp_verifications WHERE phone = :phone AND type = :type ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        $oldRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        $payload = !empty($oldRecord['payload']) ? json_decode($oldRecord['payload'], true) : null;
        $email = $oldRecord['email'] ?? ($pending['email'] ?? null);

        // Buat OTP baru
        $newOtp = $otpModel->createOtp($phone, $type, $payload, $email);

        // Kirim via Fonnte WA
        $fonnteRes = FonnteHelper::sendOtp($phone, $newOtp['otp']);

        // Perbarui masa berlaku di session
        $_SESSION['pending_otp']['expires_at'] = $newOtp['expires_timestamp'];

        if (empty($fonnteRes['success'])) {
            $_SESSION['otp_dev_hint'] = "Status Gateway Fonnte: " . ($fonnteRes['message'] ?? 'Device belum terhubung') . ". [Kode OTP Uji Coba: {$newOtp['otp']}]";
        } else {
            unset($_SESSION['otp_dev_hint']);
        }

        $_SESSION['success_msg'] = "Kode OTP baru telah berhasil dikirimkan ke WhatsApp Anda!";
        header("Location: /APSkincek/SkinCheck/public/verify-otp?type={$type}");
        exit;
    }

    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $logModel = $this->model('ActivityLog');
            $logModel->logAction($_SESSION['user_id'], 'User Logout', 'Logged out successfully.');
        }
        AuthHelper::logout();
        header('Location: /APSkincek/SkinCheck/public/login');
        exit;
    }
}
