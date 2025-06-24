<?php
include 'connection.php';

function createNotification($user_type, $user_id, $title, $message) {
    global $database;
    
    $stmt = $database->prepare("INSERT INTO notifications (user_type, user_id, title, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $user_type, $user_id, $title, $message);
    return $stmt->execute();
}

function getUnreadNotifications($user_type, $user_id) {
    global $database;
    
    $stmt = $database->prepare("SELECT * FROM notifications WHERE user_type = ? AND user_id = ? AND is_read = 0 ORDER BY created_at DESC");
    $stmt->bind_param("si", $user_type, $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function markNotificationAsRead($notification_id) {
    global $database;
    
    $stmt = $database->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ?");
    $stmt->bind_param("i", $notification_id);
    return $stmt->execute();
}

function createAppointmentCancellationNotifications($appointment_id) {
    global $database;
    
    // Get appointment details
    $query = "SELECT a.*, p.fullname, p.pemail, s.title, s.scheduledate, s.scheduletime, d.docid, d.docname, d.docemail 
              FROM appointment a 
              JOIN patient p ON a.pid = p.pid 
              JOIN schedule s ON a.scheduleid = s.scheduleid 
              JOIN doctor d ON s.docid = d.docid 
              WHERE a.appoid = ?";
    
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
    
    if ($appointment) {
        // Create notification for doctor
        $doctor_title = "Appointment Cancellation";
        $doctor_message = "Patient {$appointment['fullname']} has cancelled their appointment for {$appointment['title']} on {$appointment['scheduledate']} at {$appointment['scheduletime']}.";
        createNotification('doctor', $appointment['docid'], $doctor_title, $doctor_message);
        
        // Create notification for admin
        $admin_query = "SELECT aid FROM admin LIMIT 1";
        $admin_result = $database->query($admin_query);
        $admin_id = $admin_result->fetch_assoc()['aid'];
        
        $admin_title = "Appointment Cancellation Alert";
        $admin_message = "Patient {$appointment['fullname']} has cancelled their appointment with Dr. {$appointment['docname']} for {$appointment['title']} on {$appointment['scheduledate']} at {$appointment['scheduletime']}.";
        createNotification('admin', $admin_id, $admin_title, $admin_message);
        
        return true;
    }
    
    return false;
}
?> 