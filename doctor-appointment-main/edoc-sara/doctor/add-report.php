<?php
session_start();
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
        header("location: ../login.php");
    }
}else{
    header("location: ../login.php");
}

include("../connection.php");
$useremail=$_SESSION["user"];
$userrow = $database->query("select * from doctor where docemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["docid"];

if($_GET){
    $appointment_id = $_GET["id"];
    
    // Get appointment details
    $query = "SELECT a.*, p.fullname, p.pid, s.title, s.scheduledate, s.scheduletime 
              FROM appointment a 
              JOIN patient p ON a.pid = p.pid 
              JOIN schedule s ON a.scheduleid = s.scheduleid 
              WHERE a.appoid = ? AND s.docid = ?";
    
    $stmt = $database->prepare($query);
    $stmt->bind_param("ii", $appointment_id, $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
    
    if(!$appointment) {
        header("location: appointment.php");
        exit();
    }
}

if($_POST){
    $appointment_id = $_POST["appointment_id"];
    $patient_id = $_POST["patient_id"];
    $diagnosis = $_POST["diagnosis"];
    $prescription = $_POST["prescription"];
    $notes = $_POST["notes"];
    $next_appointment_date = $_POST["next_appointment_date"];
    $next_appointment_time = $_POST["next_appointment_time"];
    
    $stmt = $database->prepare("INSERT INTO medical_reports (appointment_id, doctor_id, patient_id, diagnosis, prescription, notes, next_appointment_date, next_appointment_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisssss", $appointment_id, $userid, $patient_id, $diagnosis, $prescription, $notes, $next_appointment_date, $next_appointment_time);
    
    if($stmt->execute()){
        header("location: appointment.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medical Report</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .report-form {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="date"],
        .form-group input[type="time"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-submit {
            background: var(--primarycolor);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-submit:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($userfetch["docname"],0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($userfetch["docemail"],0,22)  ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appoinment-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Appointments</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">My Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <div class="report-form">
                <h2>Add Medical Report</h2>
                <p>Patient: <?php echo $appointment["fullname"]; ?></p>
                <p>Session: <?php echo $appointment["title"]; ?></p>
                <p>Date: <?php echo $appointment["scheduledate"]; ?></p>
                
                <form method="POST">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                    <input type="hidden" name="patient_id" value="<?php echo $appointment["pid"]; ?>">
                    
                    <div class="form-group">
                        <label for="diagnosis">Diagnosis</label>
                        <textarea name="diagnosis" id="diagnosis" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="prescription">Prescription</label>
                        <textarea name="prescription" id="prescription" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea name="notes" id="notes"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="next_appointment_date">Next Appointment Date</label>
                        <input type="date" name="next_appointment_date" id="next_appointment_date" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="next_appointment_time">Next Appointment Time</label>
                        <input type="time" name="next_appointment_time" id="next_appointment_time">
                    </div>
                    
                    <button type="submit" class="btn-submit">Save Report</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 