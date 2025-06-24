<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}
include("../connection.php");

// Fetch all doctors for dropdown
$doctors = [];
$result = $database->query("SELECT docid, docname FROM doctor");
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Match Availability</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .slot-row { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Find Matching Doctor Availability</h2>
    <form method="post" action="check_doctor_availability.php">
        <label for="doctor_id">Select Doctor:</label>
        <select name="doctor_id" id="doctor_id" required>
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $doc): ?>
                <option value="<?php echo $doc['docid']; ?>"><?php echo htmlspecialchars($doc['docname']); ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <div id="slots">
            <div class="slot-row">
                Date: <input type="date" name="slots[0][date]" required>
                Start: <input type="time" name="slots[0][start]" required>
                End: <input type="time" name="slots[0][end]" required>
            </div>
        </div>
        <button type="button" onclick="addSlot()">Add Another Slot</button>
        <br><br>
        <button type="submit">Check Doctor Availability</button>
    </form>
    <script>
    let slotIndex = 1;
    function addSlot() {
        const div = document.createElement('div');
        div.className = 'slot-row';
        div.innerHTML = `Date: <input type="date" name="slots[${slotIndex}][date]" required>
                         Start: <input type="time" name="slots[${slotIndex}][start]" required>
                         End: <input type="time" name="slots[${slotIndex}][end]" required>`;
        document.getElementById('slots').appendChild(div);
        slotIndex++;
    }
    </script>
</body>
</html> 