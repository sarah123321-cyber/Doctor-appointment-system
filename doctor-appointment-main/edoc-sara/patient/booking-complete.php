<?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $userrow = $database->query("select * from patient where pemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["fullname"];


    if($_POST){
        if(isset($_POST["booknow"])){
            $scheduleid=$_POST["scheduleid"];
            $date=$_POST["date"];
            $time_slot_id=$_POST["time_slot"];
            
            // Get the doctor ID for this schedule
            $schedule_query = "SELECT docid FROM schedule WHERE scheduleid = ?";
            $stmt = $database->prepare($schedule_query);
            $stmt->bind_param("i", $scheduleid);
            $stmt->execute();
            $schedule_result = $stmt->get_result();
            $schedule_data = $schedule_result->fetch_assoc();
            $doctor_id = $schedule_data['docid'];
            
            // Check if patient already has an appointment with this doctor
            $check_query = "SELECT a.appoid FROM appointment a 
                           JOIN schedule s ON a.scheduleid = s.scheduleid 
                           WHERE a.pid = ? AND s.docid = ? 
                           AND s.scheduledate >= ?";
            $stmt = $database->prepare($check_query);
            $stmt->bind_param("iis", $userid, $doctor_id, $date);
            $stmt->execute();
            $check_result = $stmt->get_result();
            
            if($check_result->num_rows > 0) {
                // Patient already has an appointment with this doctor
                header("location: schedule.php?error=already-booked");
                exit();
            }
            
            // --- NEW: Check if the selected time slot is already booked ---
            $time_slot_check_query = "SELECT is_booked FROM time_slots WHERE slot_id = ?";
            $stmt = $database->prepare($time_slot_check_query);
            $stmt->bind_param("i", $time_slot_id);
            $stmt->execute();
            $time_slot_result = $stmt->get_result();
            $time_slot_data = $time_slot_result->fetch_assoc();
            
            if ($time_slot_data && $time_slot_data['is_booked'] == 1) {
                // Time slot is already booked
                header("location: schedule.php?error=time-slot-booked");
                exit();
            }
            // --- END NEW CHECK ---
            
            // Get the next appointment number
            $result = $database->query("SELECT MAX(apponum) as max_apponum FROM appointment");
            $row = $result->fetch_assoc();
            $next_apponum = ($row['max_apponum'] > 0) ? $row['max_apponum'] + 1 : 1;
            
            // Start transaction
            $database->begin_transaction();
            
            try {
                // Insert the appointment
                $sql2 = "INSERT INTO appointment(pid, apponum, scheduleid, appodate) VALUES (?, ?, ?, ?)";
                $stmt = $database->prepare($sql2);
                $stmt->bind_param("iiis", $userid, $next_apponum, $scheduleid, $date);
                $result = $stmt->execute();
                
                if($result) {
                    // Get the appointment ID
                    $appointment_id = $database->insert_id;
                    
                    // Update the time slot
                    $update_slot = "UPDATE time_slots SET is_booked = 1, appointment_id = ? WHERE slot_id = ?";
                    $stmt = $database->prepare($update_slot);
                    $stmt->bind_param("ii", $appointment_id, $time_slot_id);
                    $stmt->execute();
                    
                    // Commit transaction
                    $database->commit();
                    
                    header("location: appointment.php?action=booking-added&id=".$next_apponum."&titleget=none");
                } else {
                    throw new Exception("Failed to create appointment");
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                $database->rollback();
                header("location: schedule.php?error=booking-failed");
            }
        }
    }
 ?>