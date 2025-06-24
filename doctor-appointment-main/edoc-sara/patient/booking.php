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
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .time-slot {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .time-slot:hover {
            background-color: #f0f0f0;
        }
        .time-slot.selected {
            background-color: #2ecc71;
            color: white;
            border-color: #27ae60;
        }
        .time-slot.booked {
            background-color: #e74c3c;
            color: white;
            border-color: #c0392b;
            cursor: not-allowed;
        }
        .time-slots-container {
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .booking-form {
            margin-top: 20px;
        }
        .booking-form button {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .booking-form button:hover {
            background: #27ae60;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
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


    //echo $userid;
    //echo $username;
    


    date_default_timezone_set('Asia/Kathmandu');

    $today = date('Y-m-d');


    $scheduleid = isset($_GET['id']) ? $_GET['id'] : null;
    $schedule_data = null;
    $time_slots = [];

    if ($scheduleid) {
        // Fetch schedule details
        $schedule_query = "SELECT s.*, d.docname FROM schedule s JOIN doctor d ON s.docid = d.docid WHERE s.scheduleid = ?";
        $stmt = $database->prepare($schedule_query);
        $stmt->bind_param("i", $scheduleid);
        $stmt->execute();
        $schedule_result = $stmt->get_result();
        $schedule_data = $schedule_result->fetch_assoc();

        if ($schedule_data) {
            // Fetch available time slots (where is_booked is 0)
            $time_slots_query = "SELECT * FROM time_slots WHERE schedule_id = ? AND is_booked = 0 ORDER BY time_slot ASC";
            $stmt = $database->prepare($time_slots_query);
            $stmt->bind_param("i", $scheduleid);
            $stmt->execute();
            $time_slots_result = $stmt->get_result();
            while ($row = $time_slots_result->fetch_assoc()) {
                $time_slots[] = $row;
            }
        }
    }



 //echo $userid;
 ?>
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
                                 <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                 <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
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
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%" >
                    <a href="schedule.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td >
                            <form action="schedule.php" method="post" class="header-search">

                                        <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email or Date (YYYY-MM-DD)" list="doctors" >&nbsp;&nbsp;
                                        
                                        <?php
                                            echo '<datalist id="doctors">';
                                            $list11 = $database->query("select DISTINCT * from  doctor;");
                                            $list12 = $database->query("select DISTINCT * from  schedule GROUP BY title;");
                                            

                                            


                                            for ($y=0;$y<$list11->num_rows;$y++){
                                                $row00=$list11->fetch_assoc();
                                                $d=$row00["docname"];
                                               
                                                echo "<option value='$d'><br/>";
                                               
                                            };


                                            for ($y=0;$y<$list12->num_rows;$y++){
                                                $row00=$list12->fetch_assoc();
                                                $d=$row00["title"];
                                               
                                                echo "<option value='$d'><br/>";
                                                                                         };

                                        echo ' </datalist>';
            ?>
                                        
                                
                                        <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 

                                
                                echo $today;

                                

                        ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>


                </tr>
                
                
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                        <!-- <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49);font-weight:400;">Scheduled Sessions / Booking / <b>Review Booking</b></p> -->
                        
                    </td>
                    
                </tr>
                
                
                
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px;border:none">
                            
                        <tbody>
                        
                            <?php
                            
                            if(isset($_GET['id'])){
                                $scheduleid = $_GET['id'];
                                $show_slots = false;
                                $patient_slots = [];
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slots'])) {
                                    $show_slots = true;
                                    $patient_slots = $_POST['slots'];
                                }
                                // Get schedule details
                                $schedule_query = "SELECT s.*, d.docname 
                                                 FROM schedule s 
                                                 JOIN doctor d ON s.docid = d.docid 
                                                 WHERE s.scheduleid = ?";
                                $stmt = $database->prepare($schedule_query);
                                $stmt->bind_param("i", $scheduleid);
                                $stmt->execute();
                                $schedule_result = $stmt->get_result();
                                $schedule = $schedule_result->fetch_assoc();
                                
                                if($schedule) {
                                    echo '<tr>
                                            <td colspan="4">
                                                <div class="dashboard-items search-items" style="width:100%;">
                                                    <div style="width:100%">
                                                        <div class="h1-search">' . htmlspecialchars($schedule['title']) . '</div><br>
                                                        <div class="h3-search">' . htmlspecialchars($schedule['docname']) . '</div>
                                                        <div class="h4-search">' . $schedule['scheduledate'] . '<br>Starts: <b>@' . date('h:i A', strtotime($schedule['scheduletime'])) . '</b></div>
                                                        <br>';
                                    if (!$show_slots) {
                                        // Step 1: Ask for patient available slot(s)
                                        echo '<form method="post" class="booking-form">
                                                <h3>Enter Your Available Time Slot(s)</h3>
                                                <div id="slots">
                                                    <div class="slot-row">
                                                        Date: <input type="date" name="slots[0][date]" value="' . htmlspecialchars($schedule['scheduledate']) . '" required>
                                                        Start: <input type="time" name="slots[0][start]" class="start-time" required>
                                                        End: <input type="time" name="slots[0][end]" class="end-time" required>
                                                    </div>
                                                </div>
                                                <button type="button" onclick="addSlot()">Add Another Slot</button>
                                                <br><br>
                                                <button type="submit">Find Matching Slots</button>
                                            </form>';
                                    } else {
                                        // Step 2: Show only matching slots
                                        echo '<form action="booking-complete.php" method="post" class="booking-form">
                                                <input type="hidden" name="scheduleid" value="' . $scheduleid . '">
                                                <input type="hidden" name="date" value="' . $schedule['scheduledate'] . '">
                                                <div class="time-slots-container">';
                                        // Get available time slots
                                        $slots_query = "SELECT * FROM time_slots 
                                                       WHERE schedule_id = ? AND is_booked = 0 
                                                       ORDER BY time_slot";
                                        $stmt = $database->prepare($slots_query);
                                        $stmt->bind_param("i", $scheduleid);
                                        $stmt->execute();
                                        $slots_result = $stmt->get_result();
                                        $matching_slots = [];
                                        while($slot = $slots_result->fetch_assoc()) {
                                            foreach ($patient_slots as $pslot) {
                                                if ($pslot['date'] === $schedule['scheduledate'] && $slot['time_slot'] >= $pslot['start'] && $slot['time_slot'] < $pslot['end']) {
                                                    $matching_slots[] = $slot;
                                                    break;
                                                }
                                            }
                                        }
                                        if(count($matching_slots) > 0) {
                                            echo '<h3>Select a Matching Time Slot:</h3>';
                                            foreach($matching_slots as $slot) {
                                                echo '<div class="time-slot" data-slot-id="' . $slot['slot_id'] . '">' . date('h:i A', strtotime($slot['time_slot'])) . '</div>';
                                            }
                                            echo '<input type="hidden" name="time_slot" id="selected_slot" required>';
                                            echo '<button type="submit" name="booknow" class="login-btn btn-primary-soft btn" style="width:100%;margin-top:20px;"><font class="tn-in-text">Book Appointment</font></button>';
                                        } else {
                                            echo '<p>No matching slots available for your preferences.</p>';
                                        }
                                        echo '</div></form>';
                                    }
                                    echo '</div></div></td></tr>';
                                } else {
                                    echo '<tr><td colspan="4"><center><img src="../img/notfound.svg" width="25%"><br><p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Invalid Session!</p><a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn">Show all Sessions</button></a></center></td></tr>';
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
    
    
   
    </div>

    <?php
    if(isset($_GET['id'])){
        $scheduleid = $_GET['id'];
        
        // Get schedule details
        $schedule_query = "SELECT s.*, d.docname 
                         FROM schedule s 
                         JOIN doctor d ON s.docid = d.docid 
                         WHERE s.scheduleid = ?";
        $stmt = $database->prepare($schedule_query);
        $stmt->bind_param("i", $scheduleid);
        $stmt->execute();
        $schedule_result = $stmt->get_result();
        $schedule = $schedule_result->fetch_assoc();
        
        if($schedule) {
            // Get available time slots
            $slots_query = "SELECT * FROM time_slots 
                           WHERE schedule_id = ? AND is_booked = 0 
                           ORDER BY time_slot";
            $stmt = $database->prepare($slots_query);
            $stmt->bind_param("i", $scheduleid);
            $stmt->execute();
            $slots_result = $stmt->get_result();
            $available_slots = $slots_result->fetch_all(MYSQLI_ASSOC);
        }
    }
    ?>

<script>
function addSlot() {
    if(typeof slotIndex === 'undefined') window.slotIndex = 1;
    const div = document.createElement('div');
    div.className = 'slot-row';
    div.innerHTML = `Date: <input type="date" name="slots[${slotIndex}][date]" required>
                     Start: <input type="time" name="slots[${slotIndex}][start]" class="start-time" required>
                     End: <input type="time" name="slots[${slotIndex}][end]" class="end-time" required>`;
    document.getElementById('slots').appendChild(div);
    slotIndex++;
    attachStartTimeListeners();
}

function attachStartTimeListeners() {
    // Attach to all start-time inputs
    document.querySelectorAll('.slot-row .start-time').forEach(function(startInput) {
        startInput.onchange = function() {
            const endInput = this.parentElement.querySelector('.end-time');
            if (this.value) {
                // Calculate end time (start + 30 min)
                let [h, m] = this.value.split(":").map(Number);
                m += 30;
                if (m >= 60) { h += 1; m -= 60; }
                if (h >= 24) h -= 24;
                const endTime = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
                endInput.value = endTime;
            }
        };
    });
}

document.addEventListener('DOMContentLoaded', function() {
    attachStartTimeListeners();
});

$(document).ready(function() {
    $(document).on('click', '.time-slot', function() {
        $('.time-slot').removeClass('selected');
        $(this).addClass('selected');
        $('#selected_slot').val($(this).data('slot-id'));
    });
});
</script>
</body>
</html>