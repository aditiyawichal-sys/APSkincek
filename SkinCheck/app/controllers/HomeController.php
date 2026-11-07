<?php
require_once '../app/helpers/AuthHelper.php';

class HomeController extends Controller {
    public function index() {
        $this->view('home/index', ['title' => 'Welcome to SkinCheck']);
    }

    public function dashboard() {
        AuthHelper::requireLogin();
        
        // Differentiate: If Administrator accesses dashboard, automatically direct to Admin Dashboard
        if (AuthHelper::isAdmin() && !isset($_GET['preview'])) {
            header('Location: /APSkincek/SkinCheck/public/admin');
            exit();
        }

        $userId = $_SESSION['user_id'];
        
        $analysisModel = $this->model('AnalysisResult');
        $latestAnalysis = $analysisModel->getLatestAnalysis($userId);
        
        $sunscreenModel = $this->model('SunscreenResult');
        $latestSunscreen = $sunscreenModel->getLatestResult($userId);

        $this->view('home/dashboard', [
            'title' => 'Dashboard',
            'latestAnalysis' => $latestAnalysis,
            'latestSunscreen' => $latestSunscreen,
            'isAdminPreview' => AuthHelper::isAdmin() && isset($_GET['preview'])
        ]);
    }
}
