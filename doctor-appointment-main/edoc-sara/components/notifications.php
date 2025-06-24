<?php
include '../notification_helper.php';

function displayNotifications($user_type, $user_id) {
    $notifications = getUnreadNotifications($user_type, $user_id);
    
    if ($notifications->num_rows > 0) {
        echo '<div class="notification-container">';
        echo '<div class="notification-header">';
        echo '<h3>Notifications</h3>';
        echo '<span class="notification-count">' . $notifications->num_rows . '</span>';
        echo '</div>';
        
        echo '<div class="notification-list">';
        while ($notification = $notifications->fetch_assoc()) {
            echo '<div class="notification-item" data-id="' . $notification['notification_id'] . '">';
            echo '<div class="notification-title">' . htmlspecialchars($notification['title']) . '</div>';
            echo '<div class="notification-message">' . htmlspecialchars($notification['message']) . '</div>';
            echo '<div class="notification-time">' . date('M d, Y H:i', strtotime($notification['created_at'])) . '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        
        // Add JavaScript to mark notifications as read when clicked
        echo '<script>
            document.querySelectorAll(".notification-item").forEach(function(item) {
                item.addEventListener("click", function() {
                    const notificationId = this.dataset.id;
                    fetch("/doctor-appointment-main/edoc-sara/mark_notification_read.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: "notification_id=" + notificationId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the notification from the DOM
                            this.remove();
                            const count = document.querySelector(".notification-count");
                            count.textContent = Math.max(0, parseInt(count.textContent) - 1);
                        }
                    });
                });
            });
        </script>';
    }
}
?> 