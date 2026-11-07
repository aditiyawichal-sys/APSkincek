<?php
class Product {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM products ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $brand, $category, $description, $status) {
        $stmt = $this->db->prepare("INSERT INTO products (name, brand, category, description, status) VALUES (:name, :brand, :category, :description, :status)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
