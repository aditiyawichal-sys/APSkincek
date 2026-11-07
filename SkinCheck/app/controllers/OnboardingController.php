<?php
require_once '../app/helpers/AuthHelper.php';

class OnboardingController extends Controller {
    public function index() {
        AuthHelper::requireLogin();
        $this->view('home/onboarding', ['title' => 'Welcome - Let\'s get started']);
    }

    public function submit() {
        AuthHelper::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $skinType = $_POST['skin_type'] ?? '';
            $concerns = $_POST['concerns'] ?? [];
            
            $_SESSION['skin_type'] = $skinType;
            $_SESSION['concerns'] = $concerns;
            
            header('Location: /APSkincek/SkinCheck/public/dashboard');
            exit;
        }
        
        header('Location: /APSkincek/SkinCheck/public/onboarding');
        exit;
    }
}
