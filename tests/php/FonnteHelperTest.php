<?php
require_once __DIR__ . '/SimpleTestRunner.php';
require_once __DIR__ . '/../../SkinCheck/app/helpers/FonnteHelper.php';

class FonnteHelperTest {
    public static function run(): void {
        echo "\n[2] Menjalankan Unit Test: FonnteHelper\n";

        // Test 1: Normalisasi nomor berawalan 08
        $raw1 = '081234567890';
        $expected1 = '6281234567890';
        SimpleTestRunner::assertEquals($expected1, FonnteHelper::normalizePhone($raw1), "FonnteHelper::normalizePhone() mengonversi format '08...' menjadi '628...'");

        // Test 2: Normalisasi nomor dengan tanda plus dan spasi (+62 812-3456-7890)
        $raw2 = '+62 812-3456-7890';
        $expected2 = '6281234567890';
        SimpleTestRunner::assertEquals($expected2, FonnteHelper::normalizePhone($raw2), "FonnteHelper::normalizePhone() membersihkan spasi, tanda strip, dan plus");

        // Test 3: Normalisasi nomor yang sudah format 62
        $raw3 = '6285712345678';
        $expected3 = '6285712345678';
        SimpleTestRunner::assertEquals($expected3, FonnteHelper::normalizePhone($raw3), "FonnteHelper::normalizePhone() mempertahankan nomor yang sudah berformat '62...'");

        // Test 4: Nomor kosong menghasilkan string kosong
        SimpleTestRunner::assertEquals('', FonnteHelper::normalizePhone(''), "FonnteHelper::normalizePhone('') menghasilkan string kosong");
        SimpleTestRunner::assertEquals('', FonnteHelper::normalizePhone(null), "FonnteHelper::normalizePhone(null) menghasilkan string kosong");

        // Test 5: Pengecekan default konfigurasi Fonnte
        $config = FonnteHelper::getConfig();
        SimpleTestRunner::assertTrue(is_array($config), "FonnteHelper::getConfig() mengembalikan array konfigurasi");
        SimpleTestRunner::assertTrue(isset($config['endpoint']), "Konfigurasi Fonnte memiliki field 'endpoint'");
        SimpleTestRunner::assertTrue(isset($config['otp_message']), "Konfigurasi Fonnte memiliki template 'otp_message'");
    }
}
