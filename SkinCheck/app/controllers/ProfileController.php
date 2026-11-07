<?php
require_once '../app/helpers/AuthHelper.php';

class ProfileController extends Controller {
    public function index() {
        AuthHelper::requireLogin();
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id'] ?? '');
        $this->view('profile/index', ['title' => 'My Profile', 'user' => $user]);
    }

    public function update() {
        AuthHelper::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Format dan standarisasi nomor telepon
            $cleanDigits = preg_replace('/[^0-9]/', '', $phone);
            if (strpos($cleanDigits, '62') === 0) {
                $standardPhone = '0' . substr($cleanDigits, 2);
            } else if (strpos($cleanDigits, '0') === 0) {
                $standardPhone = $cleanDigits;
            } else {
                $standardPhone = '0' . $cleanDigits;
            }

            if (!empty($name) && !empty($email)) {
                $userModel = $this->model('User');
                $userId = $_SESSION['user_id'] ?? '';
                
                $existingEmail = $userModel->findByEmailExceptId($email, $userId);
                $existingPhone = !empty($standardPhone) ? $userModel->findByPhoneExceptId($standardPhone, $userId) : false;

                if ($existingEmail) {
                    $_SESSION['error_msg'] = "Email {$email} sudah digunakan oleh akun lain.";
                } else if ($existingPhone) {
                    $_SESSION['error_msg'] = "Nomor WhatsApp {$standardPhone} sudah digunakan oleh akun lain.";
                } else if (!empty($cleanDigits) && (strlen($cleanDigits) < 9 || strlen($cleanDigits) > 16)) {
                    $_SESSION['error_msg'] = "Format nomor WhatsApp tidak valid (minimal 9 digit).";
                } else {
                    $userModel->updateUser($userId, $name, $email, $_SESSION['user_role'] ?? null, !empty($password) ? $password : null, $standardPhone);
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_phone'] = $standardPhone;
                    $_SESSION['success_msg'] = "Profil dan nomor WhatsApp Anda berhasil diperbarui!";
                }
            } else {
                $_SESSION['error_msg'] = "Nama dan Email wajib diisi.";
            }
        }
        header('Location: /APSkincek/SkinCheck/public/profile');
        exit;
    }

    public function deleteAccount() {
        AuthHelper::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? null;
            if ($userId) {
                $userModel = $this->model('User');
                $deleted = $userModel->deleteUser($userId);
                if ($deleted) {
                    AuthHelper::logout();
                    session_start();
                    $_SESSION['success_msg'] = "Akun Anda telah berhasil dihapus. Terima kasih telah menggunakan layanan APSkincek.";
                    header('Location: /APSkincek/SkinCheck/public/login');
                    exit;
                } else {
                    $_SESSION['error_msg'] = "Terjadi kesalahan saat menghapus akun Anda.";
                }
            }
        }
        header('Location: /APSkincek/SkinCheck/public/profile');
        exit;
    }
}
