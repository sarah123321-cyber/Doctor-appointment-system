<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}
include("../connection.php");

if (!isset($_POST['doctor_id']) || !isset($_POST['slots'])) {
    echo "<p>Invalid request.</p>";
    exit();
}
$doctor_id = $_POST['doctor_id'];
$slots = $_POST['slots'];
$available = [];
foreach ($slots as $slot) {
    $date = $slot['date'];
    $start = $slot['start'];
    $end = $slot['end'];
    // Find all schedules for this doctor on this date
    $schedule_query = "SELECT scheduleid FROM schedule WHERE docid = ? AND scheduledate = ?";
    $stmt = $database->prepare($schedule_query);
    $stmt->bind_param("is", $doctor_id, $date);
    $stmt->execute();
    $schedule_result = $stmt->get_result();
    while ($sched = $schedule_result->fetch_assoc()) {
        $scheduleid = $sched['scheduleid'];
        // Find available time slots that overlap with patient's slot
        $slot_query = "SELECT * FROM time_slots WHERE schedule_id = ? AND is_booked = 0 AND time_slot >= ? AND time_slot < ? ORDER BY time_slot";
        $stmt2 = $database->prepare($slot_query);
        $stmt2->bind_param("iss", $scheduleid, $start, $end);
        $stmt2->execute();
        $slot_result = $stmt2->get_result();
        while ($row = $slot_result->fetch_assoc()) {
            $available[] = [
                'date' => $date,
                'time' => $row['time_slot'],
                'slot_id' => $row['slot_id'],
                'schedule_id' => $scheduleid
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Doctor Slots</title>
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
    <h2>Matching Available Slots</h2>
    <?php if ($available): ?>
        <form method="post" action="booking-complete.php">
            <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($doctor_id); ?>">
            <table border="1" cellpadding="8">
                <tr><th>Date</th><th>Time</th><th>Select</th></tr>
                <?php foreach ($available as $slot): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($slot['date']); ?></td>
                        <td><?php echo date('h:i A', strtotime($slot['time'])); ?></td>
                        <td>
                            <input type="radio" name="slot_id" value="<?php echo $slot['slot_id']; ?>" required>
                            <input type="hidden" name="schedule_id" value="<?php echo $slot['schedule_id']; ?>">
                            <input type="hidden" name="date" value="<?php echo $slot['date']; ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <button type="submit" name="booknow">Book Selected Slot</button>
        </form>
    <?php else: ?>
        <p>No matching slots available for your preferences.</p>
    <?php endif; ?>
    <br><a href="match-availability.php">Back</a>
</body>
</html> 