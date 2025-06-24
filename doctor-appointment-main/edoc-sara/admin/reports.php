<?php
session_start();
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

include("../connection.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Reports & Ratings</title>
    <style>
        .report-container {
            padding: 20px;
            margin: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .rating-container {
            padding: 20px;
            margin: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .tab-container {
            display: flex;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: #f0f0f0;
            margin-right: 5px;
        }
        .tab.active {
            background: var(--primarycolor);
            color: white;
        }
        .content-section {
            display: none;
        }
        .content-section.active {
            display: block;
        }
        .report-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .rating-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .stars {
            color: gold;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php 
        include '../components/notifications.php';
        if (isset($_SESSION['adminemail'])) {
            displayNotifications('admin', 1);
        }
        ?>
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
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle"><?php echo $_SESSION['adminemail']; ?></p>
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
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-reports menu-active menu-icon-reports-active">
                        <a href="reports.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Reports & Ratings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <div class="tab-container">
                <button class="tab active" onclick="showTab('reports')">Medical Reports</button>
                <button class="tab" onclick="showTab('ratings')">Doctor Ratings</button>
            </div>

            <div id="reports" class="content-section active">
                <div class="report-container">
                    <h2>Medical Reports</h2>
                    <?php
                    $query = "SELECT mr.*, d.docname, p.fullname, a.appodate 
                             FROM medical_reports mr 
                             JOIN doctor d ON mr.doctor_id = d.docid 
                             JOIN patient p ON mr.patient_id = p.pid 
                             JOIN appointment a ON mr.appointment_id = a.appoid 
                             ORDER BY mr.created_at DESC";
                    $result = $database->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="report-card">';
                            echo '<h3>Report for ' . htmlspecialchars($row['fullname']) . '</h3>';
                            echo '<p><strong>Doctor:</strong> ' . htmlspecialchars($row['docname']) . '</p>';
                            echo '<p><strong>Date:</strong> ' . date('F j, Y', strtotime($row['created_at'])) . '</p>';
                            echo '<p><strong>Diagnosis:</strong> ' . htmlspecialchars($row['diagnosis']) . '</p>';
                            echo '<p><strong>Prescription:</strong> ' . htmlspecialchars($row['prescription']) . '</p>';
                            if ($row['notes']) {
                                echo '<p><strong>Notes:</strong> ' . htmlspecialchars($row['notes']) . '</p>';
                            }
                            if ($row['next_appointment_date']) {
                                echo '<p><strong>Next Appointment:</strong> ' . date('F j, Y', strtotime($row['next_appointment_date'])) . ' at ' . date('g:i A', strtotime($row['next_appointment_time'])) . '</p>';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No medical reports found.</p>';
                    }
                    ?>
                </div>
            </div>

            <div id="ratings" class="content-section">
                <div class="rating-container">
                    <h2>Doctor Ratings</h2>
                    <?php
                    $query = "SELECT dr.*, d.docname, p.fullname 
                             FROM doctor_ratings dr 
                             JOIN doctor d ON dr.doctor_id = d.docid 
                             JOIN patient p ON dr.patient_id = p.pid 
                             ORDER BY dr.created_at DESC";
                    $result = $database->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="rating-card">';
                            echo '<h3>Rating for Dr. ' . htmlspecialchars($row['docname']) . '</h3>';
                            echo '<p><strong>Patient:</strong> ' . htmlspecialchars($row['fullname']) . '</p>';
                            echo '<p><strong>Rating:</strong> <span class="stars">';
                            for ($i = 0; $i < $row['rating']; $i++) {
                                echo '★';
                            }
                            for ($i = $row['rating']; $i < 5; $i++) {
                                echo '☆';
                            }
                            echo '</span> (' . $row['rating'] . '/5)</p>';
                            if ($row['review']) {
                                echo '<p><strong>Review:</strong> ' . htmlspecialchars($row['review']) . '</p>';
                            }
                            echo '<p><strong>Date:</strong> ' . date('F j, Y', strtotime($row['created_at'])) . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No ratings found.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all content sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show the selected content section
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to the clicked tab
            event.target.classList.add('active');
        }
    </script>
</body>
</html> 