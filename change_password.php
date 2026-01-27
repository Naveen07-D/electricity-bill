<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Only allow if first login
if (!isset($_SESSION['first_login']) || !$_SESSION['first_login']) {
    // Redirect based on role
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/approve_consumers.php");
    } elseif ($_SESSION['role'] == 'employee') {
        header("Location: employee/add_consumer.php");
    } elseif ($_SESSION['role'] == 'consumer') {
        header("Location: consumer/dashboard.php");
    }
    exit();
}

require_once 'db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password != $confirm_password) {
        $message = "<div class='error'>Passwords do not match!</div>";
    } elseif (strlen($new_password) < 6) {
        $message = "<div class='error'>Password must be at least 6 characters!</div>";
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $user_id = $_SESSION['user_id'];
        
        $sql = "UPDATE users SET password = '$hashed_password', first_login = FALSE WHERE id = $user_id";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['first_login'] = false;
            $message = "<div class='success'>Password changed successfully! Redirecting...</div>";
            
            // Redirect after 2 seconds
            header("refresh:2;url=" . getRedirectURL($_SESSION['role']));
        } else {
            $message = "<div class='error'>Error changing password!</div>";
        }
    }
}

function getRedirectURL($role) {
    switch ($role) {
        case 'admin': return 'admin/approve_consumers.php';
        case 'employee': return 'employee/add_consumer.php';
        case 'consumer': return 'consumer/dashboard.php';
        default: return 'index.php';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <p>You must change your password on first login.</p>
        
        <?php echo $message; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password" required 
                       placeholder="Enter new password (min 6 chars)">
            </div>
            
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required 
                       placeholder="Confirm new password">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Change Password</button>
            </div>
        </form>
    </div>
</body>
</html>