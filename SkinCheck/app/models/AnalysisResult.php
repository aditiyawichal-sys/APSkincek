<?php

class AnalysisResult {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function saveAnalysis($scanId, $acneScore, $rednessScore, $textureScore, $hyperpigmentationScore, $overallScore, $detailsJson, $hydrationLevel = null, $overallHealth = null) {
        if ($hydrationLevel === null) {
            $hydrationLevel = max(20, min(100, (int)(100 - ($textureScore + $rednessScore) / 2)));
        }
        if ($overallHealth === null) {
            $overallHealth = (int)$overallScore;
        }

        $stmt = $this->db->prepare("INSERT INTO analysis_results (scan_id, acne_score, redness_score, texture_score, hyperpigmentation_score, overall_score, hydration_level, overall_health, details) VALUES (:scan_id, :acne_score, :redness_score, :texture_score, :hyperpigmentation_score, :overall_score, :hydration_level, :overall_health, :details)");
        $stmt->bindParam(':scan_id', $scanId);
        $stmt->bindParam(':acne_score', $acneScore);
        $stmt->bindParam(':redness_score', $rednessScore);
        $stmt->bindParam(':texture_score', $textureScore);
        $stmt->bindParam(':hyperpigmentation_score', $hyperpigmentationScore);
        $stmt->bindParam(':overall_score', $overallScore);
        $stmt->bindParam(':hydration_level', $hydrationLevel);
        $stmt->bindParam(':overall_health', $overallHealth);
        $stmt->bindParam(':details', $detailsJson);
        
        return $stmt->execute();
    }

    public function getLatestAnalysis($userId) {
        // Join with skin_scans to ensure it belongs to this user
        $stmt = $this->db->prepare("
            SELECT a.*, s.image_url 
            FROM analysis_results a 
            JOIN skin_scans s ON a.scan_id = s.id 
            WHERE s.user_id = :user_id AND s.scan_type = 'face'
            ORDER BY a.id DESC 
            LIMIT 1
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAnalysisHistory($userId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT a.overall_score, a.created_at 
            FROM analysis_results a 
            JOIN skin_scans s ON a.scan_id = s.id 
            WHERE s.user_id = :user_id AND s.scan_type = 'face'
            ORDER BY a.created_at ASC 
            LIMIT :limit
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
