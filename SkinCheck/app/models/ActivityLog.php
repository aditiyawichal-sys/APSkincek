<?php
class ActivityLog {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function logAction($userId, $action, $description = null) {
        $stmt = $this->db->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (:user_id, :action, :description)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function getLogs($limit = 100) {
        $stmt = $this->db->prepare("
            SELECT l.*, u.name as user_name, u.email 
            FROM activity_logs l 
            JOIN users u ON l.user_id = u.id 
            ORDER BY l.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
