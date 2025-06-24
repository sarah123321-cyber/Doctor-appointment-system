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
$userrow = $database->query("select * from patient where email='$useremail'");
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["pid"];

if($_POST){
    $doctor_id = $_POST['doctor_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];
    
    // Check if patient has already reviewed this doctor
    $check_query = "SELECT * FROM doctor_reviews WHERE doctor_id = $doctor_id AND patient_id = $userid";
    $check_result = $database->query($check_query);
    
    if($check_result->num_rows > 0){
        // Update existing review
        $database->query("UPDATE doctor_reviews SET rating = $rating, review_text = '$review_text' WHERE doctor_id = $doctor_id AND patient_id = $userid");
    } else {
        // Insert new review
        $database->query("INSERT INTO doctor_reviews(doctor_id, patient_id, rating, review_text) VALUES ($doctor_id, $userid, $rating, '$review_text')");
    }
    
    header("location: doctors.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .rating > input {
            display: none;
        }
        .rating > label {
            position: relative;
            width: 1.1em;
            font-size: 30px;
            color: #FFD700;
            cursor: pointer;
        }
        .rating > label::before {
            content: "\2605";
            position: absolute;
            opacity: 0;
        }
        .rating > label:hover:before,
        .rating > label:hover ~ label:before {
            opacity: 1 !important;
        }
        .rating > input:checked ~ label:before {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Submit Review</h2>
        <form action="" method="POST">
            <input type="hidden" name="doctor_id" value="<?php echo $_GET['doctor_id']; ?>">
            
            <div class="form-group">
                <label>Rating:</label>
                <div class="rating">
                    <input type="radio" name="rating" value="5" id="5"><label for="5">☆</label>
                    <input type="radio" name="rating" value="4" id="4"><label for="4">☆</label>
                    <input type="radio" name="rating" value="3" id="3"><label for="3">☆</label>
                    <input type="radio" name="rating" value="2" id="2"><label for="2">☆</label>
                    <input type="radio" name="rating" value="1" id="1"><label for="1">☆</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Review:</label>
                <textarea name="review_text" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    </div>
</body>
</html> 