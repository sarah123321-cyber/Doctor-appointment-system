<?php
require_once 'config.php';

class AppointmentScheduler {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Optimize appointment scheduling using a greedy algorithm
     * @param int $doctor_id The ID of the doctor
     * @param string $date The date to schedule appointments
     * @return array Array of optimized appointments
     */
    public function optimizeSchedule($doctor_id, $date) {
        // Get all available time slots for the doctor on the given date
        $availableSlots = $this->getAvailableSlots($doctor_id, $date);
        
        // Get all pending appointment requests
        $pendingRequests = $this->getPendingRequests($doctor_id, $date);
        
        // Sort pending requests by duration (ascending) to maximize the number of appointments
        usort($pendingRequests, function($a, $b) {
            return $a['duration'] - $b['duration'];
        });
        
        $scheduledAppointments = [];
        
        // Greedy approach: Try to fit each appointment in the earliest possible slot
        foreach ($pendingRequests as $request) {
            $slot = $this->findEarliestFittingSlot($availableSlots, $request['duration']);
            
            if ($slot) {
                $scheduledAppointments[] = [
                    'patient_id' => $request['patient_id'],
                    'slot_id' => $slot['slot_id'],
                    'start_time' => $slot['start_time'],
                    'end_time' => date('H:i:s', strtotime($slot['start_time'] . ' + ' . $request['duration'] . ' minutes')),
                    'duration' => $request['duration']
                ];
                
                // Update available slots
                $this->updateAvailableSlots($availableSlots, $slot, $request['duration']);
            }
        }
        
        return $scheduledAppointments;
    }
    
    /**
     * Get all available time slots for a doctor on a specific date
     */
    private function getAvailableSlots($doctor_id, $date) {
        $sql = "SELECT ts.* 
                FROM time_slots ts 
                JOIN schedule s ON ts.schedule_id = s.schedule_id 
                WHERE s.doctor_id = ? 
                AND s.schedule_date = ? 
                AND ts.is_booked = 0 
                ORDER BY ts.start_time";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $doctor_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $slots = [];
        while ($row = $result->fetch_assoc()) {
            $slots[] = $row;
        }
        
        return $slots;
    }
    
    /**
     * Get all pending appointment requests for a doctor on a specific date
     */
    private function getPendingRequests($doctor_id, $date) {
        $sql = "SELECT a.*, p.duration 
                FROM appointment a 
                JOIN patient p ON a.patient_id = p.patient_id 
                WHERE a.doctor_id = ? 
                AND a.appointment_date = ? 
                AND a.status = 'pending' 
                ORDER BY a.created_at";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $doctor_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        
        return $requests;
    }
    
    /**
     * Find the earliest slot that can fit an appointment of given duration
     */
    private function findEarliestFittingSlot($availableSlots, $duration) {
        foreach ($availableSlots as $slot) {
            $slotDuration = (strtotime($slot['end_time']) - strtotime($slot['start_time'])) / 60;
            if ($slotDuration >= $duration) {
                return $slot;
            }
        }
        return null;
    }
    
    /**
     * Update available slots after scheduling an appointment
     */
    private function updateAvailableSlots(&$availableSlots, $bookedSlot, $duration) {
        foreach ($availableSlots as $key => $slot) {
            if ($slot['slot_id'] == $bookedSlot['slot_id']) {
                $newStartTime = date('H:i:s', strtotime($bookedSlot['start_time'] . ' + ' . $duration . ' minutes'));
                
                if ($newStartTime < $slot['end_time']) {
                    // Split the slot
                    $availableSlots[] = [
                        'slot_id' => $slot['slot_id'],
                        'start_time' => $newStartTime,
                        'end_time' => $slot['end_time']
                    ];
                }
                
                unset($availableSlots[$key]);
                break;
            }
        }
        
        // Reindex array
        $availableSlots = array_values($availableSlots);
        
        // Sort slots by start time
        usort($availableSlots, function($a, $b) {
            return strtotime($a['start_time']) - strtotime($b['start_time']);
        });
    }
    
    /**
     * Save the optimized schedule to the database
     */
    public function saveSchedule($appointments) {
        foreach ($appointments as $appointment) {
            // Update the time slot
            $sql = "UPDATE time_slots 
                    SET is_booked = 1, 
                        appointment_id = ? 
                    WHERE slot_id = ?";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $appointment['appointment_id'], $appointment['slot_id']);
            $stmt->execute();
            
            // Update the appointment
            $sql = "UPDATE appointment 
                    SET status = 'confirmed', 
                        start_time = ?, 
                        end_time = ? 
                    WHERE appointment_id = ?";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $appointment['start_time'], $appointment['end_time'], $appointment['appointment_id']);
            $stmt->execute();
        }
    }
}

// Example usage:
if (isset($_POST['optimize_schedule'])) {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    
    $scheduler = new AppointmentScheduler($conn);
    $optimizedSchedule = $scheduler->optimizeSchedule($doctor_id, $date);
    
    if (!empty($optimizedSchedule)) {
        $scheduler->saveSchedule($optimizedSchedule);
        echo json_encode(['success' => true, 'message' => 'Schedule optimized successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No appointments could be scheduled']);
    }
}
?> 