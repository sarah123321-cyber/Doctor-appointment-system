<?php
require_once 'config.php';

class RecommendationEngine {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Update patient preferences based on appointment history
     */
    public function updatePatientPreferences($patientId) {
        // Get patient's appointment history
        $query = "SELECT d.specid, COUNT(*) as visit_count 
                 FROM appointment a 
                 JOIN doctor d ON a.docid = d.did 
                 WHERE a.pid = ? 
                 GROUP BY d.specid";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Update preferences based on visit count
        while ($row = $result->fetch_assoc()) {
            $specialtyId = $row['specid'];
            $visitCount = $row['visit_count'];
            
            // Calculate preference score (simple formula: 1 + (visit_count * 0.2))
            $preferenceScore = 1 + ($visitCount * 0.2);
            
            // Update or insert preference
            $upsertQuery = "INSERT INTO patient_preferences (patient_id, specialty_id, preference_score) 
                           VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE preference_score = ?";
            
            $upsertStmt = $this->conn->prepare($upsertQuery);
            $upsertStmt->bind_param("iidd", $patientId, $specialtyId, $preferenceScore, $preferenceScore);
            $upsertStmt->execute();
        }
    }
    
    /**
     * Get recommended doctors for a patient
     */
    public function getRecommendedDoctors($patientId, $limit = 5) {
        // First, update patient preferences
        $this->updatePatientPreferences($patientId);
        
        // Get recommended doctors based on preferences and ratings
        $query = "SELECT 
                    d.*,
                    s.sname as specialty_name,
                    COALESCE(AVG(dr.rating), 0) as avg_rating,
                    COUNT(dr.id) as review_count,
                    COALESCE(pp.preference_score, 1.0) as preference_score
                 FROM doctor d
                 JOIN specilization s ON d.specid = s.id
                 LEFT JOIN doctor_ratings dr ON d.did = dr.doctor_id
                 LEFT JOIN patient_preferences pp ON d.specid = pp.specialty_id AND pp.patient_id = ?
                 WHERE d.status = 1
                 GROUP BY d.did
                 ORDER BY (preference_score * COALESCE(AVG(dr.rating), 0)) DESC
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $patientId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $recommendations = [];
        while ($row = $result->fetch_assoc()) {
            $recommendations[] = $row;
        }
        
        return $recommendations;
    }
    
    /**
     * Get similar patients based on appointment history
     */
    private function getSimilarPatients($patientId) {
        $query = "SELECT a2.pid, COUNT(*) as common_doctors
                 FROM appointment a1
                 JOIN appointment a2 ON a1.docid = a2.docid AND a1.pid != a2.pid
                 WHERE a1.pid = ?
                 GROUP BY a2.pid
                 HAVING common_doctors > 0
                 ORDER BY common_doctors DESC
                 LIMIT 5";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $similarPatients = [];
        while ($row = $result->fetch_assoc()) {
            $similarPatients[] = $row['pid'];
        }
        
        return $similarPatients;
    }
}
?> 