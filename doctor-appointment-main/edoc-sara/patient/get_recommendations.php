<?php
session_start();
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
    }
}else{
    header("location: ../login.php");
}

include("../connection.php");

if(isset($_GET['symptom_id'])) {
    $symptom_id = $_GET['symptom_id'];
    
    // Get the symptom name
    $symptom_query = "SELECT name FROM symptoms WHERE id = ?";
    $stmt = $database->prepare($symptom_query);
    $stmt->bind_param("i", $symptom_id);
    $stmt->execute();
    $symptom_result = $stmt->get_result();
    $symptom = $symptom_result->fetch_assoc();
    
    if($symptom) {
        // Find diseases that have this symptom
        $disease_query = "SELECT d.*, r.recommendation, s.sname as specialty_name
                         FROM diseases d 
                         LEFT JOIN recommendations r ON d.id = r.disease_id 
                         LEFT JOIN specialties s ON d.specialty_id = s.id
                         WHERE d.symptoms LIKE ?";
        $stmt = $database->prepare($disease_query);
        $search_param = "%" . $symptom['name'] . "%";
        $stmt->bind_param("s", $search_param);
        $stmt->execute();
        $disease_result = $stmt->get_result();
        
        if($disease_result->num_rows > 0) {
            $disease = $disease_result->fetch_assoc();
            
            // Get recommended doctors based on specialty
            $doctors_query = "SELECT d.*, s.sname as specialty_name 
                            FROM doctor d 
                            JOIN specialties s ON d.specialties = s.id 
                            WHERE d.specialties = ?";
            $stmt = $database->prepare($doctors_query);
            $stmt->bind_param("i", $disease['specialty_id']);
            $stmt->execute();
            $doctors_result = $stmt->get_result();
            
            $doctors = [];
            while($doctor = $doctors_result->fetch_assoc()) {
                $doctors[] = [
                    'id' => $doctor['docid'],
                    'name' => $doctor['docname'],
                    'email' => $doctor['docemail'],
                    'specialty' => $doctor['specialty_name']
                ];
            }
            
            echo json_encode([
                'status' => 'success',
                'disease' => $disease['name'],
                'symptoms' => $disease['symptoms'],
                'recommendations' => $disease['recommendation'] ?? 'No specific recommendations available.',
                'specialty' => $disease['specialty_name'],
                'doctors' => $doctors
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No diseases found for this symptom.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid symptom ID.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No symptom ID provided.'
    ]);
}
?> 