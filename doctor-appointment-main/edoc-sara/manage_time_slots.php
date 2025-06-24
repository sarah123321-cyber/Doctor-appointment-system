<?php
session_start();
include 'connection.php';

// Function to generate time slots for a schedule
function generateTimeSlots($schedule_id, $start_time, $end_time, $duration_minutes = 30) {
    global $database;
    
    // Convert times to DateTime objects
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $interval = new DateInterval('PT' . $duration_minutes . 'M');
    
    // Create time slots
    $current = clone $start;
    while ($current < $end) {
        $slot_end = clone $current;
        $slot_end->add($interval);
        
        if ($slot_end > $end) {
            break;
        }
        
        // Insert time slot
        $stmt = $database->prepare("INSERT INTO time_slots (schedule_id, start_time, end_time) VALUES (?, ?, ?)");
        $start_time_str = $current->format('H:i:s');
        $end_time_str = $slot_end->format('H:i:s');
        $stmt->bind_param("iss", $schedule_id, $start_time_str, $end_time_str);
        $stmt->execute();
        
        $current->add($interval);
    }
}

// Function to get available time slots for a schedule
function getAvailableTimeSlots($schedule_id) {
    global $database;
    
    $query = "SELECT * FROM time_slots WHERE schedule_id = ? AND is_booked = 0 ORDER BY start_time";
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $slots = array();
    while ($row = $result->fetch_assoc()) {
        $slots[] = $row;
    }
    
    return $slots;
}

// Function to book a time slot
function bookTimeSlot($slot_id, $appointment_id) {
    global $database;
    
    $query = "UPDATE time_slots SET is_booked = 1, appointment_id = ? WHERE slot_id = ? AND is_booked = 0";
    $stmt = $database->prepare($query);
    $stmt->bind_param("ii", $appointment_id, $slot_id);
    return $stmt->execute();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'generate_slots':
            if (isset($_POST['schedule_id'], $_POST['start_time'], $_POST['end_time'])) {
                generateTimeSlots(
                    $_POST['schedule_id'],
                    $_POST['start_time'],
                    $_POST['end_time'],
                    $_POST['duration'] ?? 30
                );
                echo json_encode(['status' => 'success']);
            }
            break;
            
        case 'get_available_slots':
            if (isset($_POST['schedule_id'])) {
                $slots = getAvailableTimeSlots($_POST['schedule_id']);
                echo json_encode(['status' => 'success', 'slots' => $slots]);
            }
            break;
            
        case 'book_slot':
            if (isset($_POST['slot_id'], $_POST['appointment_id'])) {
                $success = bookTimeSlot($_POST['slot_id'], $_POST['appointment_id']);
                echo json_encode(['status' => $success ? 'success' : 'error']);
            }
            break;
    }
    exit;
}
?> 