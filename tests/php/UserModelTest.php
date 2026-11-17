<?php
require_once __DIR__ . '/SimpleTestRunner.php';
require_once __DIR__ . '/../../SkinCheck/app/models/User.php';

class UserModelTest {
    public static function run(): void {
        echo "\n[3] Menjalankan Unit Test: User Model\n";

        // 1. Uji Hashing Password (Keamanan NFR: Bcrypt)
        $passwordPlain = "Rahasia123!";
        $hashed = password_hash($passwordPlain, PASSWORD_BCRYPT);
        SimpleTestRunner::assertTrue(password_verify($passwordPlain, $hashed), "Password hash verifikasi berhasil menggunakan algoritma bcrypt");
        SimpleTestRunner::assertFalse(password_verify("PasswordSalah", $hashed), "Password salah ditolak oleh verifikasi");

        // 2. Setup SQLite in-memory database untuk menguji model tanpa bergantung ke MySQL asli
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Buat tabel users sementara di memori
        $pdo->exec("
            CREATE TABLE users (
                id VARCHAR(36) PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                phone VARCHAR(20),
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'user',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $userModel = new User($pdo);

        // Test 2: Registrasi user customer
        $registerSuccess = $userModel->register('Alvin Pratama', 'alvin@example.com', 'mypassword', 'user', '08123456789');
        SimpleTestRunner::assertTrue($registerSuccess, "User::register() berhasil menyimpan customer baru");

        // Test 3: Mencari user berdasarkan email
        $found = $userModel->findByEmail('alvin@example.com');
        SimpleTestRunner::assertTrue(is_array($found), "User::findByEmail() menemukan akun yang baru didaftarkan");
        SimpleTestRunner::assertEquals('Alvin Pratama', $found['name'], "Nama user sesuai dengan data registrasi");
        SimpleTestRunner::assertEquals('user', $found['role'], "Role user terdaftar sebagai 'user'");
        SimpleTestRunner::assertTrue(password_verify('mypassword', $found['password']), "Password tersimpan dalam bentuk hash terenkripsi");

        // Test 4: Registrasi admin
        $adminSuccess = $userModel->register('Super Admin', 'admin@example.com', 'adminpass', 'admin', '08999999999');
        SimpleTestRunner::assertTrue($adminSuccess, "User::register() berhasil menyimpan admin baru");

        // Test 5: Role whitelisting (jika role diisi sembarangan, model fallback ke 'user')
        $userModel->register('Hacker', 'hacker@example.com', 'pass', 'super_god_mode', '08111111111');
        $hackerUser = $userModel->findByEmail('hacker@example.com');
        SimpleTestRunner::assertEquals('user', $hackerUser['role'], "Role tidak valid otomatis di-fallback ke role default 'user'");

        // Test 6: getCustomers() hanya mengembalikan customer
        $customers = $userModel->getCustomers();
        SimpleTestRunner::assertEquals(2, count($customers), "User::getCustomers() mengembalikan 2 user dengan role customer/user");

        // Test 7: getAdmins() hanya mengembalikan admin
        $admins = $userModel->getAdmins();
        SimpleTestRunner::assertEquals(1, count($admins), "User::getAdmins() mengembalikan 1 akun admin");
    }
}
