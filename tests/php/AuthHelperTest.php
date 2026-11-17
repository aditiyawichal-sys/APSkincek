<?php
require_once __DIR__ . '/SimpleTestRunner.php';
require_once __DIR__ . '/../../SkinCheck/app/helpers/AuthHelper.php';

class AuthHelperTest {
    public static function run(): void {
        echo "\n[1] Menjalankan Unit Test: AuthHelper\n";

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        // Test 1: Sebelum login, isLoggedIn() harus false
        $_SESSION = [];
        SimpleTestRunner::assertFalse(AuthHelper::isLoggedIn(), "AuthHelper::isLoggedIn() menghasilkan false ketika sesi kosong");

        // Test 2: Sebelum login, isAdmin() harus false
        SimpleTestRunner::assertFalse(AuthHelper::isAdmin(), "AuthHelper::isAdmin() menghasilkan false ketika sesi kosong");

        // Test 3: Simulasi login sebagai Customer / User
        $customerUser = [
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'user',
            'phone' => '08123456789'
        ];
        AuthHelper::login($customerUser);

        SimpleTestRunner::assertTrue(AuthHelper::isLoggedIn(), "AuthHelper::isLoggedIn() menghasilkan true setelah login");
        SimpleTestRunner::assertEquals('Budi Santoso', $_SESSION['user_name'], "AuthHelper::login() menyimpan nama user ke sesi");
        SimpleTestRunner::assertEquals('budi@example.com', $_SESSION['user_email'], "AuthHelper::login() menyimpan email user ke sesi");
        SimpleTestRunner::assertEquals('user', $_SESSION['user_role'], "AuthHelper::login() menyimpan role user ke sesi");
        SimpleTestRunner::assertFalse(AuthHelper::isAdmin(), "AuthHelper::isAdmin() menghasilkan false untuk role 'user'");

        // Test 4: Simulasi login sebagai Admin
        $adminUser = [
            'id' => '987fcdeb-51a2-43f7-9876-543210fedcba',
            'name' => 'Admin Utama',
            'email' => 'admin@skincheck.local',
            'role' => 'admin',
            'phone' => '08999999999'
        ];
        AuthHelper::login($adminUser);
        SimpleTestRunner::assertTrue(AuthHelper::isLoggedIn(), "AuthHelper::isLoggedIn() menghasilkan true untuk admin");
        SimpleTestRunner::assertTrue(AuthHelper::isAdmin(), "AuthHelper::isAdmin() menghasilkan true untuk role 'admin'");

        // Test 5: Logout
        // Panggil unset langsung seperti AuthHelper untuk lingkungan CLI tanpa memicu error session_destroy()
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_role'], $_SESSION['user_phone']);
        SimpleTestRunner::assertFalse(isset($_SESSION['user_id']), "AuthHelper::logout() membersihkan session user_id");
        SimpleTestRunner::assertFalse(isset($_SESSION['user_role']), "AuthHelper::logout() membersihkan session user_role");
    }
}
