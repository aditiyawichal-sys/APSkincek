<?php
require_once __DIR__ . '/SimpleTestRunner.php';
require_once __DIR__ . '/../../SkinCheck/app/models/Product.php';

class ProductModelTest {
    public static function run(): void {
        echo "\n[4] Menjalankan Unit Test: Product Model\n";

        // Setup SQLite in-memory database
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("
            CREATE TABLE products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                brand VARCHAR(100) NOT NULL,
                category VARCHAR(50) NOT NULL,
                description TEXT,
                status VARCHAR(20) DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $productModel = new Product($pdo);

        // Test 1: Tambah produk pertama dengan timestamp lampau
        $pdo->exec("INSERT INTO products (name, brand, category, description, status, created_at) 
                    VALUES ('Gentle Skin Cleanser', 'Cetaphil', 'cleanser', 'Pembersih lembut.', 'active', '2026-01-01 10:00:00')");

        // Test 2: Tambah produk kedua melalui method create() (timestamp lebih baru)
        $created = $productModel->create(
            'UV Defender Sunscreen SPF 50',
            'L\'Oreal',
            'sunscreen',
            'Perlindungan UV maksimal dengan tekstur ringan.',
            'active'
        );
        SimpleTestRunner::assertTrue($created, "Product::create() berhasil menambahkan produk baru");

        // Test 3: getAll() mengambil seluruh produk dan mengurutkan dari yang terbaru
        $all = $productModel->getAll();
        SimpleTestRunner::assertEquals(2, count($all), "Product::getAll() mengembalikan 2 produk");
        SimpleTestRunner::assertEquals('UV Defender Sunscreen SPF 50', $all[0]['name'], "Produk terbaru muncul di urutan pertama (ORDER BY created_at DESC)");
        SimpleTestRunner::assertEquals('Gentle Skin Cleanser', $all[1]['name'], "Produk pertama muncul di urutan kedua");

        // Test 4: Hapus produk berdasarkan ID
        $deleted = $productModel->delete($all[0]['id']);
        SimpleTestRunner::assertTrue($deleted, "Product::delete() berhasil menghapus produk");

        $remaining = $productModel->getAll();
        SimpleTestRunner::assertEquals(1, count($remaining), "Sisa produk berjumlah 1 setelah penghapusan");
        SimpleTestRunner::assertEquals('Gentle Skin Cleanser', $remaining[0]['name'], "Produk tersisa adalah Gentle Skin Cleanser");
    }
}
