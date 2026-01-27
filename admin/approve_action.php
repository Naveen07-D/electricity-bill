<?php
require_once '../db.php';
checkLogin();
checkRole(['admin']);

if (isset($_GET['id']) && isset($_GET['action'])) {
    $consumer_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        // Get consumer details
        $sql = "SELECT * FROM consumers WHERE id = $consumer_id";
        $result = mysqli_query($conn, $sql);
        $consumer = mysqli_fetch_assoc($result);
        
        // Create user account for consumer
        // Generate username from service number
        $username = 'con' . $consumer['service_number'];
        $password = password_hash('user123', PASSWORD_DEFAULT);
        
        $user_sql = "INSERT INTO users (username, password, role) 
                     VALUES ('$username', '$password', 'consumer')";
        
        if (mysqli_query($conn, $user_sql)) {
            $user_id = mysqli_insert_id($conn);
            
            // Update consumer with user_id
            $update_sql = "UPDATE consumers SET 
                          status = 'approved', 
                          user_id = $user_id 
                          WHERE id = $consumer_id";
            
            if (mysqli_query($conn, $update_sql)) {
                $_SESSION['message'] = "Consumer approved successfully! Default password: user123";
            } else {
                $_SESSION['message'] = "Error updating consumer!";
            }
        } else {
            $_SESSION['message'] = "Error creating user account!";
        }
    } elseif ($action == 'reject') {
        $sql = "UPDATE consumers SET status = 'rejected' WHERE id = $consumer_id";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Consumer rejected!";
        } else {
            $_SESSION['message'] = "Error rejecting consumer!";
        }
    }
}

header("Location: approve_consumers.php");
exit();
?>