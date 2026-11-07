<?php
class EducationContent {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT e.*, u.name as author_name 
            FROM education_contents e 
            LEFT JOIN users u ON e.author_id = u.id 
            ORDER BY e.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($title, $content, $category, $authorId, $status) {
        $stmt = $this->db->prepare("INSERT INTO education_contents (title, content, category, author_id, status) VALUES (:title, :content, :category, :author_id, :status)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':author_id', $authorId);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM education_contents WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
