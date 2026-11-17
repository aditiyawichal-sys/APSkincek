<?php
/**
 * Master Test Suite Runner untuk PHP
 * Menjalankan seluruh pengujian unit PHP secara berurutan.
 */

// Mulai session sebelum output apa pun agar tidak ada warning header
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

require_once __DIR__ . '/SimpleTestRunner.php';
require_once __DIR__ . '/AuthHelperTest.php';
require_once __DIR__ . '/FonnteHelperTest.php';
require_once __DIR__ . '/UserModelTest.php';
require_once __DIR__ . '/ProductModelTest.php';

echo "==================================================\n";
echo "   APSkincek - PHP Unit Testing Suite (CI/CD)\n";
echo "==================================================\n";

try {
    AuthHelperTest::run();
    FonnteHelperTest::run();
    UserModelTest::run();
    ProductModelTest::run();
} catch (Throwable $e) {
    echo "\n\033[31m[ERROR CRITICAL]\033[0m Terjadi error tidak tertangani: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

$exitCode = SimpleTestRunner::summary();
exit($exitCode);
