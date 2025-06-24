<?php
require_once 'config.php';

class TimeSlotMatcher {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Find the nearest available time slot using greedy algorithm
     * @param int $doctorId Doctor ID
     * @param string $preferredDate Preferred date (Y-m-d)
     * @param string $preferredTime Preferred time (H:i)
     * @param int $duration Duration in minutes
     * @return array|null Nearest available slot or null if no slots available
     */
    public function findNearestSlot($doctorId, $preferredDate, $preferredTime, $duration = 30) {
        // Convert preferred datetime to timestamp
        $preferredTimestamp = strtotime("$preferredDate $preferredTime");
        
        // Get all available slots for the doctor on the preferred date
        $query = "SELECT ts.*, 
                        UNIX_TIMESTAMP(CONCAT(ts.date, ' ', ts.start_time)) as slot_timestamp
                 FROM time_slots ts
                 WHERE ts.doctor_id = ? 
                 AND ts.date = ?
                 AND ts.is_booked = 0
                 AND ts.duration = ?
                 ORDER BY ABS(UNIX_TIMESTAMP(CONCAT(ts.date, ' ', ts.start_time)) - ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issi", $doctorId, $preferredDate, $duration, $preferredTimestamp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // If no slots available on preferred date, look for slots on adjacent days
        if ($result->num_rows === 0) {
            return $this->findSlotOnAdjacentDays($doctorId, $preferredDate, $preferredTime, $duration);
        }
        
        // Get the nearest slot
        $slot = $result->fetch_assoc();
        return $this->formatSlot($slot);
    }
    
    /**
     * Find slots on adjacent days (day before and after)
     */
    private function findSlotOnAdjacentDays($doctorId, $preferredDate, $preferredTime, $duration) {
        $preferredTimestamp = strtotime("$preferredDate $preferredTime");
        $dayBefore = date('Y-m-d', strtotime($preferredDate . ' -1 day'));
        $dayAfter = date('Y-m-d', strtotime($preferredDate . ' +1 day'));
        
        $query = "SELECT ts.*, 
                        UNIX_TIMESTAMP(CONCAT(ts.date, ' ', ts.start_time)) as slot_timestamp
                 FROM time_slots ts
                 WHERE ts.doctor_id = ? 
                 AND ts.date IN (?, ?)
                 AND ts.is_booked = 0
                 AND ts.duration = ?
                 ORDER BY ABS(UNIX_TIMESTAMP(CONCAT(ts.date, ' ', ts.start_time)) - ?)
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issis", $doctorId, $dayBefore, $dayAfter, $duration, $preferredTimestamp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        $slot = $result->fetch_assoc();
        return $this->formatSlot($slot);
    }
    
    /**
     * Format slot data for response
     */
    private function formatSlot($slot) {
        if (!$slot) {
            return null;
        }
        
        return [
            'id' => $slot['id'],
            'date' => $slot['date'],
            'start_time' => $slot['start_time'],
            'end_time' => $slot['end_time'],
            'duration' => $slot['duration'],
            'is_exact_match' => $slot['slot_timestamp'] == strtotime($slot['date'] . ' ' . $slot['start_time'])
        ];
    }
    
    /**
     * Get all available slots for a doctor on a specific date
     */
    public function getAvailableSlots($doctorId, $date) {
        $query = "SELECT * FROM time_slots 
                 WHERE doctor_id = ? 
                 AND date = ? 
                 AND is_booked = 0 
                 ORDER BY start_time";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $doctorId, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $slots = [];
        while ($row = $result->fetch_assoc()) {
            $slots[] = $this->formatSlot($row);
        }
        
        return $slots;
    }
}
?> 