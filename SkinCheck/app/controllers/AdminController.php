<?php
require_once '../app/helpers/AuthHelper.php';

class AdminController extends Controller {

    public function __construct() {
        AuthHelper::requireAdmin();
    }

    private function logAdminAction($action, $description = null) {
        $logModel = $this->model('ActivityLog');
        $logModel->logAction($_SESSION['user_id'], $action, $description);
    }

    public function dashboard() {
        $userModel = $this->model('User');
        $scanModel = $this->model('Scan');
        $productModel = $this->model('Product');
        
        $users = $userModel->getAllUsers();
        $scans = $scanModel->getAllScans();
        $products = $productModel->getAll();

        $customers = array_filter($users, fn($u) => $u['role'] === 'user' || $u['role'] === 'customer');
        $admins = array_filter($users, fn($u) => $u['role'] === 'admin');

        $stats = [
            'total_users' => count($users),
            'total_customers' => count($customers),
            'total_admins' => count($admins),
            'total_products' => count($products),
            'total_scans' => count($scans),
            'face_scans' => count(array_filter($scans, fn($s) => $s['scan_type'] === 'face')),
            'sunscreen_scans' => count(array_filter($scans, fn($s) => $s['scan_type'] === 'sunscreen'))
        ];

        $recentUsers = $users;
        $recentScans = array_slice($scans, 0, 10);

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard', 
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentScans' => $recentScans
        ]);
    }

    public function users() {
        $userModel = $this->model('User');
        $users = $userModel->getAllUsers();
        $this->view('admin/users', ['title' => 'Kelola Customer & Admin', 'users' => $users]);
    }

    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            // Administrator sudah tetap 3 orang, pendaftaran baru selalu sebagai user (customer)
            $role = 'user';

            // Format dan standarisasi nomor telepon
            $cleanDigits = preg_replace('/[^0-9]/', '', $phone);
            if (!empty($cleanDigits)) {
                if (strpos($cleanDigits, '62') === 0) {
                    $standardPhone = '0' . substr($cleanDigits, 2);
                } else if (strpos($cleanDigits, '0') === 0) {
                    $standardPhone = $cleanDigits;
                } else {
                    $standardPhone = '0' . $cleanDigits;
                }
            } else {
                $standardPhone = null;
            }
            
            if (!empty($name) && !empty($email) && !empty($password)) {
                $userModel = $this->model('User');
                if ($userModel->findByEmail($email)) {
                    $_SESSION['error_msg'] = "Gagal membuat akun: Email {$email} sudah terdaftar.";
                } else if (!empty($standardPhone) && $userModel->findByPhone($standardPhone)) {
                    $_SESSION['error_msg'] = "Gagal membuat akun: Nomor WhatsApp {$standardPhone} sudah terdaftar pada pengguna lain.";
                } else {
                    $userModel->register($name, $email, $password, $role, $standardPhone);
                    $roleLabel = ($role === 'admin') ? 'Administrator' : 'Customer';
                    $this->logAdminAction('Created User', "Created new {$roleLabel}: {$email} (WA: {$standardPhone})");
                    $_SESSION['success_msg'] = "Berhasil membuat akun {$roleLabel} baru: {$name} ({$email}).";
                }
            } else {
                $_SESSION['error_msg'] = "Semua kolom (Nama, Email, Password, Role) wajib diisi.";
            }
        }
        header('Location: /APSkincek/SkinCheck/public/admin/users');
        exit;
    }

    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['user_id'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $role = trim($_POST['role'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Format dan standarisasi nomor telepon
            $cleanDigits = preg_replace('/[^0-9]/', '', $phone);
            if (!empty($cleanDigits)) {
                if (strpos($cleanDigits, '62') === 0) {
                    $standardPhone = '0' . substr($cleanDigits, 2);
                } else if (strpos($cleanDigits, '0') === 0) {
                    $standardPhone = $cleanDigits;
                } else {
                    $standardPhone = '0' . $cleanDigits;
                }
            } else {
                $standardPhone = '';
            }

            if (!empty($id) && !empty($name) && !empty($email)) {
                $userModel = $this->model('User');
                $targetUser = $userModel->findById($id);
                if (!$targetUser) {
                    $_SESSION['error_msg'] = "Pengguna tidak ditemukan.";
                    header('Location: /APSkincek/SkinCheck/public/admin/users');
                    exit;
                }
                // Pertahankan role asli agar admin tetap 3 orang
                $role = $targetUser['role'];
                
                // Check if email or phone already used by another user
                $existingEmail = $userModel->findByEmailExceptId($email, $id);
                $existingPhone = !empty($standardPhone) ? $userModel->findByPhoneExceptId($standardPhone, $id) : false;

                if ($existingEmail) {
                    $_SESSION['error_msg'] = "Gagal memperbarui: Email {$email} sudah digunakan oleh akun lain.";
                } else if ($existingPhone) {
                    $_SESSION['error_msg'] = "Gagal memperbarui: Nomor WhatsApp {$standardPhone} sudah digunakan oleh akun lain.";
                } else {
                    $userModel->updateUser($id, $name, $email, $role, !empty($password) ? $password : null, $standardPhone);
                    
                    // If the updated user is the currently logged-in admin, update session immediately
                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $id) {
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_phone'] = $standardPhone;
                    }

                    $this->logAdminAction('Updated User Info', "Updated user ID: {$id}, Name: {$name}, Email: {$email}, Phone: {$standardPhone}");
                    $_SESSION['success_msg'] = "Data pengguna/admin \"{$name}\" berhasil diperbarui!";
                }
            } else {
                $_SESSION['error_msg'] = "Nama dan Email wajib diisi.";
            }
        }
        header('Location: /APSkincek/SkinCheck/public/admin/users');
        exit;
    }

    public function updateUserRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['error_msg'] = "Fitur perubahan role dinonaktifkan. Jumlah Administrator sudah tetap.";
        }
        header('Location: /APSkincek/SkinCheck/public/admin/users');
        exit;
    }

    public function updateUserStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['user_id'] ?? null;
            $status = $_POST['status'] ?? null;

            if ($id && $status) {
                $userModel = $this->model('User');
                $userModel->updateUserStatus($id, $status);
                $this->logAdminAction('Updated User Status', "User ID: $id set to $status");
                $_SESSION['success_msg'] = "Status pengguna berhasil diperbarui menjadi $status.";
            }
        }
        header('Location: /APSkincek/SkinCheck/public/admin/users');
        exit;
    }

    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['user_id'] ?? null;
            if ($id) {
                // Cegah admin menghapus akunnya sendiri saat sedang login
                if (isset($_SESSION['user_id']) && $id === $_SESSION['user_id']) {
                    $_SESSION['error_msg'] = "Anda tidak dapat menghapus akun Anda sendiri saat sedang login.";
                    header('Location: /APSkincek/SkinCheck/public/admin/users');
                    exit;
                }

                $userModel = $this->model('User');
                $targetUser = $userModel->findById($id);

                if ($targetUser) {
                    $deleted = $userModel->deleteUser($id);
                    if ($deleted) {
                        $roleLabel = ($targetUser['role'] === 'admin') ? 'Administrator' : 'Customer';
                        $this->logAdminAction('Deleted User', "Deleted {$roleLabel}: {$targetUser['name']} ({$targetUser['email']})");
                        $_SESSION['success_msg'] = "Akun {$roleLabel} \"{$targetUser['name']}\" ({$targetUser['email']}) berhasil dihapus secara permanen.";
                    } else {
                        $_SESSION['error_msg'] = "Gagal menghapus akun pengguna dari database.";
                    }
                } else {
                    $_SESSION['error_msg'] = "Pengguna tidak ditemukan.";
                }
            }
        }
        header('Location: /APSkincek/SkinCheck/public/admin/users');
        exit;
    }

    public function scans() {
        $scanModel = $this->model('Scan');
        $scans = $scanModel->getAllScans();
        $this->view('admin/scans', ['title' => 'All Scans', 'scans' => $scans]);
    }

    public function products() {
        $productModel = $this->model('Product');
        $products = $productModel->getAll();
        $this->view('admin/products', ['title' => 'Manage Products', 'products' => $products]);
    }

    public function createProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = $this->model('Product');
            $productModel->create(
                $_POST['name'], 
                $_POST['brand'], 
                $_POST['category'], 
                $_POST['description'], 
                $_POST['status']
            );
            $this->logAdminAction('Created Product', "Product: " . $_POST['name']);
            $_SESSION['success_msg'] = "Product created successfully.";
        }
        header('Location: /APSkincek/SkinCheck/public/admin/products');
        exit;
    }

    public function deleteProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $productModel = $this->model('Product');
            $productModel->delete($_POST['id']);
            $this->logAdminAction('Deleted Product', "Product ID: " . $_POST['id']);
            $_SESSION['success_msg'] = "Product deleted successfully.";
        }
        header('Location: /APSkincek/SkinCheck/public/admin/products');
        exit;
    }

    public function education() {
        $eduModel = $this->model('EducationContent');
        $contents = $eduModel->getAll();
        $this->view('admin/education', ['title' => 'Education Content', 'contents' => $contents]);
    }

    public function createEducation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eduModel = $this->model('EducationContent');
            $eduModel->create(
                $_POST['title'], 
                $_POST['content'], 
                $_POST['category'], 
                $_SESSION['user_id'], 
                $_POST['status']
            );
            $this->logAdminAction('Created Education Content', "Title: " . $_POST['title']);
            $_SESSION['success_msg'] = "Content published successfully.";
        }
        header('Location: /APSkincek/SkinCheck/public/admin/education');
        exit;
    }

    public function deleteEducation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $eduModel = $this->model('EducationContent');
            $eduModel->delete($_POST['id']);
            $this->logAdminAction('Deleted Education Content', "ID: " . $_POST['id']);
            $_SESSION['success_msg'] = "Content deleted successfully.";
        }
        header('Location: /APSkincek/SkinCheck/public/admin/education');
        exit;
    }

    public function logs() {
        $logModel = $this->model('ActivityLog');
        $logs = $logModel->getLogs(200);
        $this->view('admin/logs', ['title' => 'System Logs', 'logs' => $logs]);
    }

    private function getAiConfigFile() {
        return realpath(__DIR__ . '/../../config') . '/ai_config.json';
    }

    private function getAiConfig() {
        $file = $this->getAiConfigFile();
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [
            'provider' => 'groq',
            'model' => 'qwen/qwen3.8-27b',
            'primary_api_key' => '',
            'backup_api_key_1' => '',
            'backup_api_key_2' => '',
            'backup_api_key_3' => '',
            'active_key' => 'primary',
            'auto_failover' => true,
            'last_active_key_used' => 'primary',
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    public function aiConfig() {
        $config = $this->getAiConfig();
        $this->view('admin/ai_config', [
            'title' => 'Konfigurasi AI & API Key',
            'config' => $config
        ]);
    }

    public function saveAiConfig() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $config = $this->getAiConfig();
            $config['provider'] = trim($_POST['provider'] ?? 'groq');
            $config['model'] = trim($_POST['model'] ?? 'qwen/qwen3.8-27b');
            $config['primary_api_key'] = trim($_POST['primary_api_key'] ?? '');
            $config['backup_api_key_1'] = trim($_POST['backup_api_key_1'] ?? '');
            $config['backup_api_key_2'] = trim($_POST['backup_api_key_2'] ?? '');
            $config['backup_api_key_3'] = trim($_POST['backup_api_key_3'] ?? '');
            $config['auto_failover'] = isset($_POST['auto_failover']) ? true : false;
            $config['updated_at'] = date('Y-m-d H:i:s');

            file_put_contents($this->getAiConfigFile(), json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            
            $this->logAdminAction('Updated AI Configuration', 'Updated AI API Keys and Failover Settings');
            $_SESSION['success_msg'] = 'Konfigurasi AI dan 3 API Key Cadangan berhasil disimpan!';
        }
        header('Location: /APSkincek/SkinCheck/public/admin/ai-config');
        exit;
    }

    public function switchPrimaryAiKey() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $targetSlot = trim($_POST['target_slot'] ?? '');
            $config = $this->getAiConfig();
            
            $slotFieldMap = [
                'backup_1' => 'backup_api_key_1',
                'backup_2' => 'backup_api_key_2',
                'backup_3' => 'backup_api_key_3'
            ];
            
            if (isset($slotFieldMap[$targetSlot])) {
                $targetField = $slotFieldMap[$targetSlot];
                $oldPrimary = $config['primary_api_key'] ?? '';
                $newPrimary = $config[$targetField] ?? '';

                if (!empty($newPrimary)) {
                    $config['primary_api_key'] = $newPrimary;
                    $config[$targetField] = $oldPrimary;
                    $config['last_active_key_used'] = 'primary';
                    $config['updated_at'] = date('Y-m-d H:i:s');

                    file_put_contents($this->getAiConfigFile(), json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    $this->logAdminAction('Switched Primary API Key', "Swapped Primary Key with {$targetSlot}");
                    $_SESSION['success_msg'] = "Berhasil! Kunci Cadangan telah ditukar menjadi KUNCI UTAMA (Primary Key). Kunci lama dijadikan cadangan.";
                } else {
                    $_SESSION['error_msg'] = "Gagal mengganti: Kunci cadangan yang dipilih masih kosong.";
                }
            }
        }
        header('Location: /APSkincek/SkinCheck/public/admin/ai-config');
        exit;
    }

    public function testAiConfig() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Metode tidak valid.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $apiKey = trim($input['apiKey'] ?? '');
        $provider = trim($input['provider'] ?? 'groq');
        $model = trim($input['model'] ?? 'qwen/qwen3.8-27b');

        if (empty($apiKey)) {
            echo json_encode(['success' => false, 'error' => 'API Key masih kosong.']);
            exit;
        }

        // Auto-detect provider if key has clear prefix
        if (str_starts_with($apiKey, 'gsk_')) {
            $provider = 'groq';
        } elseif (preg_match('/^sk-[0-9a-f]{32}$/i', $apiKey)) {
            $provider = 'deepseek';
        } elseif (str_starts_with($apiKey, 'sk-')) {
            $provider = 'openai';
        } elseif (str_starts_with($apiKey, 'AIza') || str_starts_with($apiKey, 'AQ.')) {
            $provider = 'gemini';
        }

        $startTime = microtime(true);

        if ($provider === 'groq') {
            $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
            $payload = [
                'model' => (!empty($model) && !str_contains($model, 'gemini') && !str_contains($model, 'gpt') && !str_contains($model, 'deepseek')) ? $model : 'qwen/qwen3.8-27b',
                'messages' => [
                    ['role' => 'user', 'content' => 'Test connection. Respond with OK.']
                ],
                'max_tokens' => 5
            ];

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $latency = round((microtime(true) - $startTime) * 1000);

            if ($httpCode === 200) {
                echo json_encode([
                    'success' => true,
                    'latency_ms' => $latency,
                    'message' => "Groq API Key AKTIF & Valid! Waktu respons: {$latency}ms."
                ]);
            } else {
                $errDetail = '';
                if ($response) {
                    $jsonRes = json_decode($response, true);
                    $errDetail = $jsonRes['error']['message'] ?? $response;
                }
                echo json_encode([
                    'success' => false,
                    'http_code' => $httpCode,
                    'error' => "HTTP {$httpCode}: " . ($errDetail ?: $curlError ?: 'Gagal terhubung ke API Groq.')
                ]);
            }
            exit;
        } elseif ($provider === 'deepseek') {
            $ch = curl_init('https://api.deepseek.com/chat/completions');
            $payload = [
                'model' => (!empty($model) && str_contains($model, 'deepseek')) ? $model : 'deepseek-chat',
                'messages' => [
                    ['role' => 'user', 'content' => 'Test connection. Respond with OK.']
                ],
                'max_tokens' => 5
            ];

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $latency = round((microtime(true) - $startTime) * 1000);

            if ($httpCode === 200) {
                echo json_encode([
                    'success' => true,
                    'latency_ms' => $latency,
                    'message' => "DeepSeek API Key AKTIF & Valid! Waktu respons: {$latency}ms."
                ]);
            } elseif ($httpCode === 402) {
                echo json_encode([
                    'success' => false,
                    'http_code' => 402,
                    'error' => "API Key VALID & Terhubung ke akun DeepSeek Anda, tetapi SALDO akun DeepSeek saat ini masih $0.00 (Insufficient Balance). DeepSeek mewajibkan isi saldo (top-up) di platform.deepseek.com sebelum API bisa digunakan. Sebagai alternatif cadangan 100% GRATIS tanpa isi saldo, Anda bisa membuat API Key Google Gemini di aistudio.google.com/apikey."
                ]);
            } else {
                $errDetail = '';
                if ($response) {
                    $jsonRes = json_decode($response, true);
                    $errDetail = $jsonRes['error']['message'] ?? $response;
                }
                echo json_encode([
                    'success' => false,
                    'http_code' => $httpCode,
                    'error' => "HTTP {$httpCode}: " . ($errDetail ?: $curlError ?: 'Gagal terhubung ke API DeepSeek.')
                ]);
            }
            exit;
        } elseif ($provider === 'openai') {
            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            $payload = [
                'model' => (!empty($model) && str_starts_with($model, 'gpt')) ? $model : 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => 'Test connection. Respond with OK.']
                ],
                'max_tokens' => 5
            ];

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $latency = round((microtime(true) - $startTime) * 1000);

            if ($httpCode === 200) {
                echo json_encode([
                    'success' => true,
                    'latency_ms' => $latency,
                    'message' => "OpenAI API Key AKTIF & Valid! Waktu respons: {$latency}ms."
                ]);
            } else {
                $errDetail = '';
                if ($response) {
                    $jsonRes = json_decode($response, true);
                    $errDetail = $jsonRes['error']['message'] ?? $response;
                }
                echo json_encode([
                    'success' => false,
                    'http_code' => $httpCode,
                    'error' => "HTTP {$httpCode}: " . ($errDetail ?: $curlError ?: 'Gagal terhubung ke API OpenAI.')
                ]);
            }
            exit;
        } else {
            $geminiModel = (!empty($model) && str_contains($model, 'gemini')) ? $model : 'gemini-1.5-flash';
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $geminiModel . ':generateContent?key=' . $apiKey;
            $ch = curl_init($url);
            $payload = [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => 'ping']]]
                ]
            ];
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $latency = round((microtime(true) - $startTime) * 1000);
            curl_close($ch);

            if ($httpCode === 200) {
                echo json_encode([
                    'success' => true,
                    'latency_ms' => $latency,
                    'message' => "Gemini API Key AKTIF & Valid! Waktu respons: {$latency}ms."
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'http_code' => $httpCode,
                    'error' => "HTTP {$httpCode}: API Key Gemini tidak valid atau kuota habis."
                ]);
            }
            exit;
        }
    }

    public function waConfig() {
        require_once '../app/helpers/FonnteHelper.php';
        $config = FonnteHelper::getConfig();
        $this->view('admin/wa_config', [
            'title' => 'Konfigurasi Notifikasi WhatsApp (Fonnte)',
            'config' => $config
        ]);
    }

    public function saveWaConfig() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../app/helpers/FonnteHelper.php';
            $apiToken = trim($_POST['api_token'] ?? '');
            $countryCode = trim($_POST['country_code'] ?? '62');
            $enableNotification = isset($_POST['enable_registration_notification']) ? true : false;
            $welcomeMessage = trim($_POST['welcome_message'] ?? '');
            $otpMessage = trim($_POST['otp_message'] ?? '');

            $saved = FonnteHelper::saveConfig([
                'api_token' => $apiToken,
                'country_code' => $countryCode ?: '62',
                'enable_registration_notification' => $enableNotification,
                'welcome_message' => $welcomeMessage,
                'otp_message' => $otpMessage
            ]);

            if ($saved) {
                $this->logAdminAction('Updated WhatsApp Config', 'Updated Fonnte WA API token and notification settings');
                $_SESSION['success_msg'] = 'Pengaturan Notifikasi WhatsApp (Fonnte) berhasil disimpan!';
            } else {
                $_SESSION['error_msg'] = 'Gagal menyimpan konfigurasi Fonnte ke file fonnte_config.json.';
            }
        }
        header('Location: /APSkincek/SkinCheck/public/admin/wa-config');
        exit;
    }

    public function testWaConfig() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Metode tidak valid.']);
            exit;
        }

        require_once '../app/helpers/FonnteHelper.php';
        $input = json_decode(file_get_contents('php://input'), true);
        $target = trim($input['target'] ?? '');
        $token = trim($input['token'] ?? '');

        if (empty($target)) {
            echo json_encode(['success' => false, 'error' => 'Nomor tujuan WhatsApp wajib diisi untuk tes.']);
            exit;
        }

        // Simpan token sementara jika diinput langsung pada form tes
        if (!empty($token)) {
            $currentConfig = FonnteHelper::getConfig();
            $currentConfig['api_token'] = $token;
            FonnteHelper::saveConfig($currentConfig);
        }

        $testMsg = "Halo! Ini adalah pesan uji coba dari sistem *APSkincek* via gateway *Fonnte WhatsApp*.\n\nKoneksi berhasil dan notifikasi WhatsApp siap digunakan! 🚀✨";
        $result = FonnteHelper::sendWhatsApp($target, $testMsg);

        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Pesan uji coba berhasil terkirim ke WhatsApp: ' . $target
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => $result['message'] ?? 'Pengiriman pesan gagal.'
            ]);
        }
        exit;
    }
}
