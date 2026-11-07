<?php

class Scan {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createScan($userId, $scanType, $imageUrl) {
        $stmt = $this->db->prepare("INSERT INTO skin_scans (user_id, scan_type, image_url, status) VALUES (:user_id, :scan_type, :image_url, 'completed')");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':scan_type', $scanType);
        $stmt->bindParam(':image_url', $imageUrl);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getUserHistory($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                s.id as scan_id, 
                s.scan_type, 
                s.created_at,
                a.overall_score as analysis_score,
                sr.coverage_score as sunscreen_score,
                sr.missed_areas as sunscreen_details
            FROM skin_scans s
            LEFT JOIN analysis_results a ON s.id = a.scan_id AND s.scan_type = 'face'
            LEFT JOIN sunscreen_results sr ON s.id = sr.scan_id AND s.scan_type = 'sunscreen'
            WHERE s.user_id = :user_id
            ORDER BY s.created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllScans() {
        $stmt = $this->db->prepare("
            SELECT s.*, u.name as user_name, u.email as user_email
            FROM skin_scans s
            JOIN users u ON s.user_id = u.id
            ORDER BY s.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
