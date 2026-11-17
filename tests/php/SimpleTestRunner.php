<?php
/**
 * Test Runner Sederhana untuk Unit Testing PHP
 * Dibuat khusus agar dapat berjalan di lingkungan lokal (XAMPP) maupun di GitHub Actions CI/CD
 * tanpa perlu instalasi library eksternal yang rumit.
 */

class SimpleTestRunner {
    private static int $passed = 0;
    private static int $failed = 0;
    private static array $failures = [];

    public static function assert(bool $condition, string $testName): void {
        if ($condition) {
            self::$passed++;
            echo "  \033[32m✔ PASS:\033[0m {$testName}\n";
        } else {
            self::$failed++;
            self::$failures[] = $testName;
            echo "  \033[31m✘ FAIL:\033[0m {$testName}\n";
        }
    }

    public static function assertEquals($expected, $actual, string $testName): void {
        $condition = ($expected === $actual);
        if (!$condition) {
            $testName .= " (Expected: " . var_export($expected, true) . ", Got: " . var_export($actual, true) . ")";
        }
        self::assert($condition, $testName);
    }

    public static function assertTrue($actual, string $testName): void {
        self::assert($actual === true, $testName);
    }

    public static function assertFalse($actual, string $testName): void {
        self::assert($actual === false, $testName);
    }

    public static function summary(): int {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "HASIL PENGUJIAN UNIT TEST PHP:\n";
        echo "  Total Pengujian: " . (self::$passed + self::$failed) . "\n";
        echo "  \033[32mBerhasil (PASS): " . self::$passed . "\033[0m\n";
        if (self::$failed > 0) {
            echo "  \033[31mGagal (FAIL): " . self::$failed . "\033[0m\n";
            echo "\nDaftar pengujian yang gagal:\n";
            foreach (self::$failures as $failure) {
                echo "  - {$failure}\n";
            }
            return 1; // Exit code error untuk CI/CD
        } else {
            echo "  \033[32mSemua pengujian unit test PHP BERHASIL (100% OK)!\033[0m\n";
            return 0; // Exit code success untuk CI/CD
        }
    }
}
