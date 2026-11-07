<?php

class AuthHelper {
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            $_SESSION['error_msg'] = "You must be logged in to view that page.";
            header('Location: /APSkincek/SkinCheck/public/login');
            exit();
        }
    }

    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            $_SESSION['error_msg'] = "Access denied. Admins only.";
            header('Location: /APSkincek/SkinCheck/public/dashboard');
            exit();
        }
    }

    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_phone'] = $user['phone'] ?? '';
    }

    public static function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_phone']);
        session_destroy();
    }
}
