<?php
// Simple Router

$request_uri = $_SERVER['REQUEST_URI'];
$url_path = parse_url($request_uri, PHP_URL_PATH);
$path = preg_replace('#^/APSkincek/SkinCheck(/public)?#i', '', $url_path);

// Remove leading/trailing slashes
$path = trim($path, '/');

// Mapping routes to controllers
$routes = [
    '' => ['HomeController', 'index'],
    'login' => ['AuthController', 'login'],
    'login/request-otp' => ['AuthController', 'requestLoginOtp'],
    'register' => ['AuthController', 'register'],
    'verify-otp' => ['AuthController', 'verifyOtp'],
    'resend-otp' => ['AuthController', 'resendOtp'],
    'logout' => ['AuthController', 'logout'],
    'onboarding' => ['OnboardingController', 'index'],
    'onboarding/submit' => ['OnboardingController', 'submit'],
    'dashboard' => ['HomeController', 'dashboard'],
    'face-scan' => ['ScanController', 'faceScan'],
    'api/scan/submit' => ['ScanController', 'submitFaceScan'],
    'skin-analysis' => ['ScanController', 'skinAnalysis'],
    'sunscreen-check' => ['ScanController', 'sunscreenCheck'],
    'api/sunscreen/submit' => ['ScanController', 'submitSunscreenCheck'],
    'sunscreen-analysis' => ['ScanController', 'sunscreenAnalysis'],
    'scan-history' => ['ScanController', 'history'],
    'ai-assistant' => ['AiAssistantController', 'index'],
    'api/ai/chat' => ['AiAssistantController', 'chat'],
    'api/ai/clear-history' => ['AiAssistantController', 'clearHistory'],
    'profile' => ['ProfileController', 'index'],
    'profile/update' => ['ProfileController', 'update'],
    'profile/delete' => ['ProfileController', 'deleteAccount'],
    'admin' => ['AdminController', 'dashboard'],
    'admin/users' => ['AdminController', 'users'],
    'admin/users/create' => ['AdminController', 'createUser'],
    'admin/users/update' => ['AdminController', 'updateUser'],
    'admin/users/role' => ['AdminController', 'updateUserRole'],
    'admin/users/status' => ['AdminController', 'updateUserStatus'],
    'admin/users/delete' => ['AdminController', 'deleteUser'],
    'admin/scans' => ['AdminController', 'scans'],
    'admin/products' => ['AdminController', 'products'],
    'admin/products/create' => ['AdminController', 'createProduct'],
    'admin/products/delete' => ['AdminController', 'deleteProduct'],
    'admin/education' => ['AdminController', 'education'],
    'admin/education/create' => ['AdminController', 'createEducation'],
    'admin/education/delete' => ['AdminController', 'deleteEducation'],
    'admin/logs' => ['AdminController', 'logs'],
    'admin/ai-config' => ['AdminController', 'aiConfig'],
    'admin/ai-config/save' => ['AdminController', 'saveAiConfig'],
    'admin/ai-config/test' => ['AdminController', 'testAiConfig'],
    'admin/ai-config/switch-primary' => ['AdminController', 'switchPrimaryAiKey'],
    'admin/wa-config' => ['AdminController', 'waConfig'],
    'admin/wa-config/save' => ['AdminController', 'saveWaConfig'],
    'admin/wa-config/test' => ['AdminController', 'testWaConfig']
];

if (array_key_exists($path, $routes)) {
    $controllerName = $routes[$path][0];
    $methodName = $routes[$path][1];
} else {
    // 404 Route
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit;
}

$controllerFile = '../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    
    if (method_exists($controller, $methodName)) {
        $controller->$methodName();
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "404 - Method not found";
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "404 - Controller not found";
}
