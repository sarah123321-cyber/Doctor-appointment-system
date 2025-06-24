<?php

session_start();
include 'notification_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = intval($_POST['notification_id']);
    
    // Ensure the user is logged in before marking as read (optional but recommended)
    // You might want to add a check here to ensure the notification belongs to the logged-in user
    // For now, we will just mark it as read if the ID is valid
    
    if (markNotificationAsRead($notification_id)) {
        echo json_encode(['success' => true]);
    } else {
        // Log error or return more specific failure reason if needed
        echo json_encode(['success' => false, 'message' => 'Failed to update notification.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

?> 