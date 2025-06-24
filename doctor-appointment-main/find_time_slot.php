<?php
session_start();
require_once 'config.php';
require_once 'time_slot_matcher.php';

header('Content-Type: application/json');

if (!isset($_SESSION['pid'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['doctor_id']) || !isset($data['preferred_date']) || !isset($data['preferred_time'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit();
}

$doctorId = $data['doctor_id'];
$preferredDate = $data['preferred_date'];
$preferredTime = $data['preferred_time'];
$duration = isset($data['duration']) ? $data['duration'] : 30;

// Validate date and time format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $preferredDate) || !preg_match('/^\d{2}:\d{2}$/', $preferredTime)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date or time format']);
    exit();
}

// Initialize time slot matcher
$matcher = new TimeSlotMatcher($conn);

// Find nearest available slot
$slot = $matcher->findNearestSlot($doctorId, $preferredDate, $preferredTime, $duration);

if (!$slot) {
    echo json_encode([
        'success' => false,
        'message' => 'No available slots found on the preferred date or adjacent days'
    ]);
    exit();
}

// Get all available slots for the same date
$availableSlots = $matcher->getAvailableSlots($doctorId, $slot['date']);

echo json_encode([
    'success' => true,
    'slot' => $slot,
    'available_slots' => $availableSlots,
    'message' => $slot['is_exact_match'] 
        ? 'Exact time slot is available!' 
        : 'Found the nearest available time slot'
]);
?> 