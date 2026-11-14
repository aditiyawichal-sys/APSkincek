<?php
require_once '../app/helpers/AuthHelper.php';
require_once '../app/services/LLMService.php';

class AiAssistantController extends Controller {
    public function index() {
        AuthHelper::requireLogin();
        
        // Initialize chat history if not exists
        if (!isset($_SESSION['chat_history'])) {
            $_SESSION['chat_history'] = [];
        }

        $this->view('ai/assistant', [
            'title' => 'SkinCheck AI Assistant',
            'chat_history' => $_SESSION['chat_history']
        ]);
    }

    private function buildSystemPrompt($userId) {
        $userModel = $this->model('User');
        $user = $userModel->findByEmail($_SESSION['user_email']); // Using email from session

        $analysisModel = $this->model('AnalysisResult');
        $latestFaceScan = $analysisModel->getLatestAnalysis($userId);

        $sunscreenModel = $this->model('SunscreenResult');
        $latestSunscreenScan = $sunscreenModel->getLatestResult($userId);

        $productModel = $this->model('Product');
        $products = $productModel->getAll();

        $productCatalog = [];
        foreach ($products as $p) {
            if ($p['status'] === 'active') {
                $productCatalog[] = "- {$p['name']} ({$p['brand']}) [{$p['category']}]: {$p['description']}";
            }
        }
        $productCatalogStr = implode("\n", $productCatalog);

        $faceScanStr = "No face scan recorded.";
        if ($latestFaceScan) {
            $overall = $latestFaceScan['overall_health'] ?? $latestFaceScan['overall_score'] ?? 'N/A';
            $acne = $latestFaceScan['acne_score'] ?? 'N/A';
            $hydration = $latestFaceScan['hydration_level'] ?? 'N/A';
            $details = $latestFaceScan['details'] ?? '';
            $faceScanStr = "Overall Health: {$overall}%\nAcne: {$acne}\nHydration: {$hydration}\nDetails: {$details}";
        }
        $sunscreenStr = "No sunscreen check recorded.";
        if ($latestSunscreenScan) {
            $missedAreasDecoded = json_decode($latestSunscreenScan['missed_areas'], true);
            $coverageStatus = is_array($missedAreasDecoded) && isset($missedAreasDecoded['coverage_status']) ? $missedAreasDecoded['coverage_status'] : 'Unknown';
            $sunscreenStr = "Coverage Score: {$latestSunscreenScan['coverage_score']}%\nDetails: " . $coverageStatus;
        }

        $userName = $user['name'] ?? $_SESSION['user_name'] ?? 'Pengguna';
        $userRole = $user['role'] ?? $_SESSION['user_role'] ?? 'user';

        // Fetch database user & customer statistics
        $userStats = $userModel->getUserStats() ?: [];
        $totalCustomers = $userStats['total_customers'] ?? 0;
        $totalAdmins = $userStats['total_admins'] ?? 0;
        $totalUsers = $userStats['total_users'] ?? 0;

        $prompt = <<<EOT
You are SkinCheck AI, a helpful AI assistant for the APSkincek application.
User Name: {$userName}
User Role: {$userRole}
Anda sedang berbicara dengan {$userName}.

## DATA RESMI DATABASE APSKINCEK:
- Jumlah Customer (Pelanggan) Terdaftar: {$totalCustomers} orang
- Jumlah Administrator Terdaftar: {$totalAdmins} orang
- Total Keseluruhan Pengguna: {$totalUsers} orang

## User Context
Latest Face Scan:
$faceScanStr

Latest Sunscreen Check:
$sunscreenStr

## Available Product Catalog
You may ONLY recommend products from the following list. Do not recommend any external brands or products not listed here.
$productCatalogStr

ATURAN KOMUNIKASI & DATABASE:
1. JAWAB PERTANYAAN TENTANG CUSTOMER & DATABASE:
Jika pengguna bertanya tentang jumlah customer yang terdaftar, misalnya "ada berapa customer yg terdaftar", "berapa customer", "berapa jumlah pengguna", atau statistik database:
Anda WAJIB MENJAWAB HANYA TOTAL ANGKANYA SAJA:
"Saat ini ada {$totalCustomers} customer yang terdaftar di APSkincek."
DILARANG KERAS menyebutkan nama-nama customer jika hanya ditanya "berapa customer", cukup sebutkan totalnya saja.
Hanya sebutkan nama-nama customer jika ditanya secara spesifik "siapa saja customer nya" atau "daftar customer".
DILARANG KERAS mengatakan tidak memiliki akses ke data customer! Anda memiliki akses membaca data resmi di atas.
2. LANGSUNG KE INTI JAWABAN (TO THE POINT): Dilarang bertele-tele atau membuat basa-basi pembuka yang panjang. Jangan mengulang-ulang sapaan. Langsung berikan inti jawaban yang ditanyakan pengguna.
3. Jika pengguna bertanya identitas diri seperti "siapa saya", jawab langsung: "Nama Anda adalah {$userName}."
4. Jika pengguna bertanya "siapa Anda", jawab singkat padat: "Saya adalah SkinCheck AI Assistant untuk membantu kebutuhan skincare dan layanan APSkincek Anda."
5. DILARANG KERAS menggunakan simbol markdown formatting seperti **, *, #, backtick, garis bawah, atau emoji. Tuliskan semua jawaban HANYA menggunakan teks polos (plain text) yang bersih dan mudah dibaca tanpa simbol-simbol tersebut.
6. DILARANG MEMBUAT CODING / PEMROGRAMAN: Jika pengguna meminta menuliskan coding, kode pemrograman, skrip python, atau hal seputar coding (contoh: "tuliskan coding python untuk menuliskan nama anda?"), Anda DILARANG menjawab atau membuatkan kodenya. Jawablah: "Maaf, saya tidak dapat menjawab pertanyaan tersebut. Saya adalah asisten khusus kesehatan kulit dan layanan aplikasi APSkincek, dan tidak melayani pembuatan kode atau pemrograman." Hanya tolak pertanyaan yang meminta coding tersebut, pertanyaan lain seputar kulit dan sistem tetap dijawab seperti biasa.
EOT;
        return $prompt;
    }

    public function chat() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        
        if (!AuthHelper::isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu untuk menggunakan AI Assistant.']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userMessage = $data['message'] ?? '';

            if (empty(trim($userMessage))) {
                echo json_encode(['success' => false, 'message' => 'Empty message.']);
                return;
            }

            if (!isset($_SESSION['chat_history'])) {
                $_SESSION['chat_history'] = [];
            }

            $lowerMsg = strtolower($userMessage);
            if (
                strpos($lowerMsg, 'coding python') !== false ||
                strpos($lowerMsg, 'koding python') !== false ||
                strpos($lowerMsg, 'kode python') !== false ||
                (strpos($lowerMsg, 'coding') !== false && (strpos($lowerMsg, 'nama anda') !== false || strpos($lowerMsg, 'tuliskan') !== false || strpos($lowerMsg, 'buatkan') !== false || strpos($lowerMsg, 'bikin') !== false)) ||
                (strpos($lowerMsg, 'koding') !== false && (strpos($lowerMsg, 'nama anda') !== false || strpos($lowerMsg, 'tuliskan') !== false || strpos($lowerMsg, 'buatkan') !== false || strpos($lowerMsg, 'bikin') !== false))
            ) {
                $refuseMsg = "Maaf, saya tidak dapat menjawab pertanyaan tersebut. Saya adalah asisten khusus kesehatan kulit dan layanan aplikasi APSkincek, dan tidak melayani pembuatan kode atau pemrograman.";
                $_SESSION['chat_history'][] = ['role' => 'user', 'text' => $userMessage];
                $_SESSION['chat_history'][] = ['role' => 'model', 'text' => $refuseMsg];
                echo json_encode(['success' => true, 'response' => $refuseMsg]);
                return;
            }

            try {
                // Build system prompt
                $systemPrompt = $this->buildSystemPrompt($_SESSION['user_id']);

                // Get response from LLM
                $aiResponse = LLMService::generateResponse($systemPrompt, $_SESSION['chat_history'], $userMessage);

                // Update session history
                $_SESSION['chat_history'][] = ['role' => 'user', 'text' => $userMessage];
                $_SESSION['chat_history'][] = ['role' => 'model', 'text' => $aiResponse];

                // Keep history manageable (last 20 messages)
                if (count($_SESSION['chat_history']) > 20) {
                    $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -20);
                }

                echo json_encode(['success' => true, 'response' => $aiResponse]);
            } catch (\Throwable $th) {
                echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $th->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
    }

    public function clearHistory() {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        if (!AuthHelper::isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
            return;
        }

        $_SESSION['chat_history'] = [];
        echo json_encode(['success' => true, 'message' => 'Riwayat percakapan berhasil dibersihkan.']);
    }
}

