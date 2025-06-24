<?php
session_start();

$_SESSION["user"]="";
$_SESSION["usertype"]="";

// Set the new timezone
date_default_timezone_set('Asia/kathmandu');
$date = date('Y-m-d');

$_SESSION["date"]=$date;

//import database
include("connection.php");

$errors = array();

if($_POST){
    $result= $database->query("select * from webuser");

    $fname=$_SESSION['personal']['fname'];
    $lname=$_SESSION['personal']['lname'];
    $name=$fname." ".$lname;
    $address=$_SESSION['personal']['address'];
    $nic=$_SESSION['personal']['nic'];
    $dob=$_SESSION['personal']['dob'];
    $email=$_POST['newemail'];
    $tele=$_POST['text'];
    $newpassword=$_POST['newpassword'];
    $cpassword=$_POST['cpassword'];
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    // Validate phone number (10 digits)
    if (!preg_match("/^[0-9]{10}$/", $tele)) {
        $errors[] = "Phone number must be exactly 10 digits";
    }
    
    // Validate password strength
    if (strlen($newpassword) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if (!preg_match("/[A-Z]/", $newpassword)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (!preg_match("/[a-z]/", $newpassword)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (!preg_match("/[0-9]/", $newpassword)) {
        $errors[] = "Password must contain at least one number";
    }
    
    if (!preg_match("/[^A-Za-z0-9]/", $newpassword)) {
        $errors[] = "Password must contain at least one special character";
    }
    
    // Check if passwords match
    if ($newpassword != $cpassword) {
        $errors[] = "Passwords do not match";
    }
    
    // If no validation errors, proceed with account creation
    if (empty($errors)) {
        $result= $database->query("select * from webuser where email='$email';");
        if($result->num_rows==1){
            $errors[] = "An account with this email address already exists";
        } else {
            $database->query("insert into patient(pemail,fullname,ppassword, paddress, pnic,pdob,ptel) values('$email','$name','$newpassword','$address','$nic','$dob','$tele');");
            $database->query("insert into webuser values('$email','p')");

            $_SESSION["user"]=$email;
            $_SESSION["usertype"]="p";
            $_SESSION["username"]=$fname;

            header('Location: patient/index.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
    <title>Create Account</title>
    <style>
        .container{
            animation: transitionIn-X 0.5s;
        }
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            list-style-type: none;
        }
        .error-message li {
            margin: 5px 0;
        }
        .input-error {
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <center>
    <div class="container">
        <table border="0" style="width: 69%;">
            <tr>
                <td colspan="2">
                    <p class="header-text">Let's Get Started</p>
                    <p class="sub-text">It's Okey, Now Create User Account.</p>
                </td>
            </tr>
            <?php if (!empty($errors)): ?>
            <tr>
                <td colspan="2">
                    <ul class="error-message">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <form action="" method="POST" >
                <td class="label-td" colspan="2">
                    <label for="newemail" class="form-label">Email: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="email" name="newemail" class="input-text <?php echo isset($_POST['newemail']) && !filter_var($_POST['newemail'], FILTER_VALIDATE_EMAIL) ? 'input-error' : ''; ?>" 
                           placeholder="Email Address" value="<?php echo isset($_POST['newemail']) ? htmlspecialchars($_POST['newemail']) : ''; ?>" required>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="text" class="form-label">Mobile Number: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="text" name="text" class="input-text <?php echo isset($_POST['text']) && !preg_match("/^[0-9]{10}$/", $_POST['text']) ? 'input-error' : ''; ?>" 
                           placeholder="ex: 9865432178" value="<?php echo isset($_POST['text']) ? htmlspecialchars($_POST['text']) : ''; ?>" required>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="newpassword" class="form-label">Create New Password: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="password" name="newpassword" class="input-text <?php echo isset($_POST['newpassword']) && strlen($_POST['newpassword']) < 8 ? 'input-error' : ''; ?>" 
                           placeholder="New Password" required>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <label for="cpassword" class="form-label">Confirm Password: </label>
                </td>
            </tr>
            <tr>
                <td class="label-td" colspan="2">
                    <input type="password" name="cpassword" class="input-text <?php echo isset($_POST['cpassword']) && $_POST['newpassword'] != $_POST['cpassword'] ? 'input-error' : ''; ?>" 
                           placeholder="Confirm Password" required>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >
                </td>
                <td>
                    <input type="submit" value="Sign Up" class="login-btn btn-primary btn">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br>
                    <label for="" class="sub-text" style="font-weight: 280;">Already have an account? &#63; </label>
                    <a href="login.php" class="hover-link1 non-style-link">Login</a>
                    <br><br><br>
                </td>
            </tr>
            </form>
            </tr>
        </table>
    </div>
    </center>
</body>
</html>