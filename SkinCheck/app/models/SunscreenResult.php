<?php

class SunscreenResult {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function saveResult($scanId, $coverageScore, $missedAreasJson) {
        $stmt = $this->db->prepare("INSERT INTO sunscreen_results (scan_id, coverage_score, missed_areas) VALUES (:scan_id, :coverage_score, :missed_areas)");
        $stmt->bindParam(':scan_id', $scanId);
        $stmt->bindParam(':coverage_score', $coverageScore);
        $stmt->bindParam(':missed_areas', $missedAreasJson);
        
        return $stmt->execute();
    }

    public function getLatestResult($userId) {
        $stmt = $this->db->prepare("
            SELECT a.*, s.image_url 
            FROM sunscreen_results a 
            JOIN skin_scans s ON a.scan_id = s.id 
            WHERE s.user_id = :user_id AND s.scan_type = 'sunscreen'
            ORDER BY a.id DESC 
            LIMIT 1
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSunscreenHistory($userId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT a.coverage_score, a.created_at 
            FROM sunscreen_results a 
            JOIN skin_scans s ON a.scan_id = s.id 
            WHERE s.user_id = :user_id AND s.scan_type = 'sunscreen'
            ORDER BY a.created_at ASC 
            LIMIT :limit
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
