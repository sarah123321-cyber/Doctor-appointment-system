<?php
session_start();
include('config.php');
include('recommendation_engine.php');

if (!isset($_SESSION['pid'])) {
    header('location:login.php');
    exit();
}

$pid = $_SESSION['pid'];
$recommendationEngine = new RecommendationEngine($conn);
$recommendedDoctors = $recommendationEngine->getRecommendedDoctors($pid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommended Doctors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .recommendation-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .recommendation-card:hover {
            transform: translateY(-5px);
        }
        
        .recommendation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .recommendation-score {
            background: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
        
        .doctor-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .doctor-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .doctor-details h3 {
            margin: 0;
            color: #333;
        }
        
        .doctor-details p {
            margin: 5px 0;
            color: #666;
        }
        
        .rating {
            color: #FFD700;
            margin-right: 5px;
        }
        
        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-secondary {
            background: #2196F3;
            color: white;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    
    <div class="container">
        <h2>Recommended Doctors for You</h2>
        <p class="text-muted">Based on your appointment history and preferences</p>
        
        <div class="recommendations">
            <?php if (empty($recommendedDoctors)): ?>
                <div class="alert alert-info">
                    No recommendations available yet. Book some appointments to get personalized recommendations!
                </div>
            <?php else: ?>
                <?php foreach ($recommendedDoctors as $doctor): ?>
                    <div class="recommendation-card">
                        <div class="recommendation-header">
                            <div class="doctor-info">
                                <img src="<?php echo $doctor['docpic'] ? 'uploads/'.$doctor['docpic'] : 'images/default-avatar.png'; ?>" 
                                     alt="Doctor" class="doctor-avatar">
                                <div class="doctor-details">
                                    <h3>Dr. <?php echo htmlspecialchars($doctor['fname'] . ' ' . $doctor['lname']); ?></h3>
                                    <p><i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($doctor['specialty_name']); ?></p>
                                    <p>
                                        <i class="fas fa-star rating"></i>
                                        <?php echo number_format($doctor['avg_rating'], 1); ?> 
                                        (<?php echo $doctor['review_count']; ?> reviews)
                                    </p>
                                </div>
                            </div>
                            <div class="recommendation-score">
                                Match Score: <?php echo number_format($doctor['preference_score'] * $doctor['avg_rating'], 1); ?>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="book-appointment.php?docid=<?php echo $doctor['did']; ?>" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Book Appointment
                            </a>
                            <a href="doctor-profile.php?docid=<?php echo $doctor['did']; ?>" class="btn btn-secondary">
                                <i class="fas fa-user-md"></i> View Profile
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include('footer.php'); ?>
</body>
</html> 