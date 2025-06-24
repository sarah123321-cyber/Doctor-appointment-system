<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['pid'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please login to rate a doctor']);
        exit;
    }

    $doctor_id = $_POST['doctor_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'] ?? '';
    $patient_id = $_SESSION['pid'];

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid rating value']);
        exit;
    }

    // Check if patient has already rated this doctor
    $check_query = "SELECT rating_id FROM doctor_ratings WHERE doctor_id = ? AND patient_id = ?";
    $stmt = $database->prepare($check_query);
    $stmt->bind_param("ii", $doctor_id, $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing rating
        $update_query = "UPDATE doctor_ratings SET rating = ?, review = ? WHERE doctor_id = ? AND patient_id = ?";
        $stmt = $database->prepare($update_query);
        $stmt->bind_param("isii", $rating, $review, $doctor_id, $patient_id);
    } else {
        // Insert new rating
        $insert_query = "INSERT INTO doctor_ratings (doctor_id, patient_id, rating, review) VALUES (?, ?, ?, ?)";
        $stmt = $database->prepare($insert_query);
        $stmt->bind_param("iiis", $doctor_id, $patient_id, $rating, $review);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Rating submitted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit rating']);
    }
    exit;
}

// Get average rating for a doctor
if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];
    $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM doctor_ratings WHERE doctor_id = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    echo json_encode([
        'status' => 'success',
        'average_rating' => round($data['avg_rating'], 1),
        'total_ratings' => $data['total_ratings']
    ]);
    exit;
}
?> 