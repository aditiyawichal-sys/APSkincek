<?php
class Recommendation {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT r.*, p.name as product_name, s.scan_type, u.name as user_name 
            FROM recommendations r 
            JOIN products p ON r.product_id = p.id
            JOIN skin_scans s ON r.scan_id = s.id
            JOIN users u ON s.user_id = u.id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
