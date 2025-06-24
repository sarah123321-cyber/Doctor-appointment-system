<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Symptoms & Recommendations</title>
    <style>
        .symptoms-container {
            animation: transitionIn-Y-over 0.5s;
        }
        .recommendations-container {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .symptom-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .recommendation-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            border-left: 4px solid #2ecc71;
        }
        .doctor-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .doctor-info {
            flex: 1;
        }
        .doctor-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .doctor-specialty {
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        .doctor-email {
            color: #95a5a6;
            font-size: 14px;
        }
        .book-btn {
            background: #2ecc71;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .book-btn:hover {
            background: #27ae60;
        }
        .search-container {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .symptom-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }
        .symptom-item {
            background: #e9ecef;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .symptom-item:hover {
            background: #2ecc71;
            color: white;
        }
        .symptom-item.selected {
            background: #2ecc71;
            color: white;
        }
        .doctors-section {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
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
                                    <p class="profile-title"><?php echo $username ? substr($username, 0, 13) . '..' : 'User'; ?></p>
                                    <p class="profile-subtitle"><?php echo $useremail ? substr($useremail, 0, 22) : ''; ?></p>
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
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor menu-active menu-icon-doctor-active">
                        <a href="recommend.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Symptoms & Recommend</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-reports">
                        <a href="reports.php" class="non-style-link-menu"><div><p class="menu-text">Medical Reports</p></a></div>
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
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="1" class="nav-bar">
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Symptoms & Recommendations</p>
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
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="symptoms-container">
                            <div class="search-container">
                                <h3>Search for Symptoms</h3>
                                <form method="POST" action="">
                                    <input type="text" name="search" class="input-text" placeholder="Search symptoms..." style="width: 50%;">
                                    <input type="submit" value="Search" class="login-btn btn-primary btn">
                                </form>
                            </div>

                            <?php
                            if(isset($_POST['search'])) {
                                $search = $_POST['search'];
                                $query = "SELECT * FROM symptoms WHERE name LIKE ?";
                                $stmt = $database->prepare($query);
                                $search_param = "%$search%";
                                $stmt->bind_param("s", $search_param);
                                $stmt->execute();
                                $result = $stmt->get_result();
                            } else {
                                $result = $database->query("SELECT * FROM symptoms ORDER BY name");
                            }

                            if($result->num_rows > 0) {
                                echo '<div class="symptom-list">';
                                while($row = $result->fetch_assoc()) {
                                    echo '<div class="symptom-item" onclick="getRecommendations('.$row['id'].')">';
                                    echo htmlspecialchars($row['name']);
                                    echo '</div>';
                                }
                                echo '</div>';
                            } else {
                                echo '<p>No symptoms found.</p>';
                            }
                            ?>
                        </div>

                        <div class="recommendations-container" id="recommendations">
                            <h3>Recommendations</h3>
                            <div id="recommendation-content">
                                <p>Select a symptom to see recommendations.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
    function getRecommendations(symptomId) {
        // Add selected class to clicked symptom
        document.querySelectorAll('.symptom-item').forEach(item => {
            item.classList.remove('selected');
        });
        event.target.classList.add('selected');

        // Fetch recommendations
        fetch('get_recommendations.php?symptom_id=' + symptomId)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recommendation-content');
                if(data.status === 'success') {
                    let html = '<div class="recommendation-card">';
                    html += '<h4>' + data.disease + '</h4>';
                    html += '<p><strong>Symptoms:</strong> ' + data.symptoms + '</p>';
                    html += '<p><strong>Recommendations:</strong> ' + data.recommendations + '</p>';
                    html += '</div>';

                    // Add doctors section
                    if(data.doctors && data.doctors.length > 0) {
                        html += '<div class="doctors-section">';
                        html += '<h3>Recommended Doctors</h3>';
                        html += '<p>Based on your symptoms, we recommend consulting with these specialists:</p>';
                        html += '<div class="doctors-grid">';
                        
                        data.doctors.forEach(doctor => {
                            html += '<div class="doctor-card">';
                            html += '<div class="doctor-info">';
                            html += '<div class="doctor-name">Dr. ' + doctor.name + '</div>';
                            html += '<div class="doctor-specialty">' + doctor.specialty + '</div>';
                            html += '<div class="doctor-email">' + doctor.email + '</div>';
                            html += '</div>';
                            html += '<a href="schedule.php?search=' + encodeURIComponent(doctor.name) + '" class="book-btn">Book Appointment</a>';
                            html += '</div>';
                        });
                        
                        html += '</div></div>';
                    } else {
                        html += '<div class="doctors-section">';
                        html += '<p>No specific doctors found for this condition. Please check our <a href="doctors.php">complete doctor list</a>.</p>';
                        html += '</div>';
                    }

                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p>No recommendations found for this symptom.</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('recommendation-content').innerHTML = 
                    '<p>Error loading recommendations. Please try again.</p>';
            });
    }
    </script>
</body>
</html>