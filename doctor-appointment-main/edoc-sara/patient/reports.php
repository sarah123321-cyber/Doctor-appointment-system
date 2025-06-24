<?php
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

include("../connection.php");
$userrow = $database->query("select * from patient where pemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["pid"];
$username=$userfetch["fullname"];

// Get all medical reports for this patient
$reports_query = "SELECT mr.*, d.docname, a.appodate 
                 FROM medical_reports mr 
                 JOIN appointment a ON mr.appointment_id = a.appoid 
                 JOIN doctor d ON a.scheduleid = d.docid 
                 WHERE a.pid = ? 
                 ORDER BY mr.created_at DESC";
$stmt = $database->prepare($reports_query);
$stmt->bind_param("i", $userid);
$stmt->execute();
$reports_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Medical Reports</title>
    <style>
        .report-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .report-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .report-title {
            font-size: 1.2em;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .report-meta {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .report-content {
            margin: 15px 0;
        }
        .report-section {
            margin: 10px 0;
        }
        .report-section h4 {
            color: #34495e;
            margin-bottom: 5px;
        }
        .no-reports {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
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
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-reports menu-active menu-icon-reports-active">
                        <a href="reports.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Medical Reports</p></a></div>
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
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="index.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;color: var(--mycolor);">Medical Reports</p>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                            date_default_timezone_set('Asia/Kolkata');
                            echo date('Y-m-d');
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4">
                        <div style="display: flex;margin-top: 40px;justify-content: center;">
                            <div class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Your Medical Reports</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="padding: 20px;">
                            <?php
                            if($reports_result->num_rows > 0) {
                                while($report = $reports_result->fetch_assoc()) {
                                    echo '<div class="report-card">
                                            <div class="report-header">
                                                <div class="report-title">Medical Report</div>
                                                <div class="report-meta">
                                                    Doctor: Dr. '.$report['docname'].'<br>
                                                    Date: '.date('F j, Y', strtotime($report['created_at'])).'
                                                </div>
                                            </div>
                                            <div class="report-content">
                                                <div class="report-section">
                                                    <h4>Diagnosis</h4>
                                                    <p>'.nl2br(htmlspecialchars($report['diagnosis'])).'</p>
                                                </div>
                                                <div class="report-section">
                                                    <h4>Prescription</h4>
                                                    <p>'.nl2br(htmlspecialchars($report['prescription'])).'</p>
                                                </div>
                                                <div class="report-section">
                                                    <h4>Notes</h4>
                                                    <p>'.nl2br(htmlspecialchars($report['notes'])).'</p>
                                                </div>
                                            </div>
                                        </div>';
                                }
                            } else {
                                echo '<div class="no-reports">
                                        <h3>No Medical Reports Found</h3>
                                        <p>Your medical reports will appear here after your appointments.</p>
                                    </div>';
                            }
                            ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html> 