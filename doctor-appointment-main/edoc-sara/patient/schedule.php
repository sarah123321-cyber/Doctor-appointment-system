<?php
session_start();

// Check if user is logged in and is of type 'patient'
if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

// Import database connection
include("../connection.php");

$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();

// Initialize variables with default values
$userid = null;
$username = null;

// Only set values if we got results from the database
if ($userfetch) {
    $userid = $userfetch["pid"];
    $username = $userfetch["fullname"];
}

// Set timezone and get today's date
date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// Check for error message
$error_message = "";
if(isset($_GET['error']) && $_GET['error'] == 'already-booked') {
    $error_message = "You already have an appointment with this doctor. Please book a different doctor or date.";
}

// Handle search logic
$searchtype = "All";
$insertkey = "";
$q = "";
$sqlmain = "SELECT * FROM schedule 
            INNER JOIN doctor ON schedule.docid = doctor.docid 
            WHERE schedule.scheduledate >= '$today' 
            ORDER BY schedule.scheduledate ASC";

// Handle both POST and GET search requests
if ($_POST || isset($_GET['search'])) {
    $keyword = $_POST['search'] ?? $_GET['search'];
    if (!empty($keyword)) {
        $sqlmain = "SELECT * FROM schedule 
                    INNER JOIN doctor ON schedule.docid = doctor.docid 
                    WHERE schedule.scheduledate >= '$today' AND (
                        doctor.docname LIKE '%$keyword%' OR 
                        schedule.title LIKE '%$keyword%' OR 
                        schedule.scheduledate LIKE '%$keyword%'
                    ) 
                    ORDER BY schedule.scheduledate ASC";
        $insertkey = $keyword;
        $searchtype = "Search Result : ";
        $q = '"';
    }
}

// Run the query to fetch the schedule
$result = $database->query($sqlmain);

// Convert result to an array and filter non-overlapping sessions
$sessions = [];
while ($row = $result->fetch_assoc()) {
    $start = strtotime($row['scheduledate'] . ' ' . $row['scheduletime']);
    $end = $start + 3600; // Assuming 1-hour duration
    $row['start'] = $start;
    $row['end'] = $end;
    
    // Check if time slots exist for this schedule
    $slots_check = $database->prepare("SELECT COUNT(*) as count FROM time_slots WHERE schedule_id = ?");
    $slots_check->bind_param("i", $row['scheduleid']);
    $slots_check->execute();
    $slots_count = $slots_check->get_result()->fetch_assoc()['count'];
    
    // If no time slots exist, create them
    if ($slots_count == 0) {
        $start_time = strtotime($row['scheduletime']);
        $end_time = $start_time + 3600; // 1 hour duration
        
        // Create 4 time slots (15 minutes each)
        for ($i = 0; $i < 4; $i++) {
            $slot_time = date('H:i:s', $start_time + ($i * 900)); // 900 seconds = 15 minutes
            $insert_slot = $database->prepare("INSERT INTO time_slots (schedule_id, time_slot) VALUES (?, ?)");
            $insert_slot->bind_param("is", $row['scheduleid'], $slot_time);
            $insert_slot->execute();
        }
    }
    
    $sessions[] = $row;
}

// Sort sessions by end time
usort($sessions, function($a, $b) {
    return $a['end'] <=> $b['end'];
});

// Filter for non-overlapping sessions (greedy algorithm)
$filtered_sessions = [];
$last_end = 0;

foreach ($sessions as $session) {
    if ($session['start'] >= $last_end) {
        $filtered_sessions[] = $session;
        $last_end = $session['end'];
    }
}
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
    <title>Sessions</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            margin: 10px 45px;
            border-radius: 4px;
            border: 1px solid #ef9a9a;
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
                                    <p class="profile-title"><?php echo $username ? substr($username, 0, 13) . '..' : 'User'; ?></p>
                                    <p class="profile-subtitle"><?php echo $useremail ? substr($useremail, 0, 22) : ''; ?></p>
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
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-home " >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Home</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="dash-body">
            <?php if($error_message != ""): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <table border="0" width="100%" style="border-spacing: 0; margin:0; padding:0; margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="schedule.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <form action="" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Date (YYYY-MM-DD)" list="doctors" value="<?php echo $insertkey; ?>" />
                            <input type="submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119); padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php echo $today; ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">
                            <?php echo $searchtype . " Sessions(" . count($filtered_sessions) . ")"; ?>
                        </p>
                        <p class="heading-main12" style="margin-left: 45px;font-size:22px;color:rgb(49, 49, 49)">
                            <?php echo $q . $insertkey . $q; ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px; border:none;">
                                    <tbody>
                                        <?php
                                        if (count($filtered_sessions) == 0) {
                                            echo '<tr>
                                                    <td colspan="4">
                                                    <br><br><br><br>
                                                    <center>
                                                    <img src="../img/notfound.svg" width="25%">
                                                    <br>
                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">No sessions found!</p>
                                                    <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn">Show all Sessions</button></a>
                                                    </center>
                                                    <br><br><br><br>
                                                    </td>
                                                  </tr>';
                                        } else {
                                            foreach ($filtered_sessions as $session) {
                                                // Check if the session is fully booked
                                                $session_id = $session['scheduleid'];
                                                $max_patients = $session['nop'];
                                                
                                                // Count current bookings for this session
                                                $booking_count_sql = "SELECT COUNT(*) as count FROM appointment WHERE scheduleid = $session_id";
                                                $booking_count_result = $database->query($booking_count_sql);
                                                $booking_count = $booking_count_result->fetch_assoc()['count'];
                                                
                                                // Determine if the session is fully booked
                                                $is_fully_booked = ($booking_count >= $max_patients);
                                                
                                                // Format the time for display
                                                $time_display = date('h:i A', strtotime($session['scheduletime']));
                                                
                                                echo '<tr>
                                                        <td style="width: 25%;">
                                                        <div class="dashboard-items search-items">
                                                            <div style="width:100%">
                                                                <div class="h1-search">' . substr($session['title'], 0, 21) . '</div><br>
                                                                <div class="h3-search">' . substr($session['docname'], 0, 30) . '</div>
                                                                <div class="h4-search">' . $session['scheduledate'] . '<br>Starts: <b>@' . $time_display . '</b></div>
                                                                <div class="h4-search">Bookings: <b>' . $booking_count . '/' . $max_patients . '</b></div>
                                                                <br>';
                                                
                                                if ($is_fully_booked) {
                                                    echo '<button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%;background-color:#f0f0f0;color:#999;" disabled>
                                                                <font class="tn-in-text">Fully Booked</font></button>';
                                                } else {
                                                    // Check if patient already has an appointment with this doctor
                                                    $check_query = "SELECT a.appoid FROM appointment a 
                                                                   JOIN schedule s ON a.scheduleid = s.scheduleid 
                                                                   WHERE a.pid = $userid AND s.docid = {$session['docid']} 
                                                                   AND s.scheduledate >= '$today'";
                                                    $check_result = $database->query($check_query);
                                                    
                                                    if($check_result->num_rows > 0) {
                                                        echo '<button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%;background-color:#f0f0f0;color:#999;" disabled>
                                                                    <font class="tn-in-text">Already Booked</font></button>';
                                                    } else {
                                                        echo '<a href="booking.php?id=' . $session['scheduleid'] . '">
                                                                    <button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%">
                                                                    <font class="tn-in-text">Book Now</font></button></a>';
                                                    }
                                                }
                                                
                                                echo '</div>
                                                        </div>
                                                        </td>
                                                    </tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
