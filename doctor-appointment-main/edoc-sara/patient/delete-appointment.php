<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    
    if($_GET){
        //import database
        include("../connection.php");
        include("../notification_helper.php");
        
        $id = $_GET["id"];
        
        // Create notifications before deleting the appointment
        createAppointmentCancellationNotifications($id);
        
        // Delete the appointment
        $sql = $database->query("DELETE FROM appointment WHERE appoid='$id'");
        
        header("location: appointment.php");
    }


?>