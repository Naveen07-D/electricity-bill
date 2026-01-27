<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/bills.php");
    } elseif ($_SESSION['role'] == 'employee') {
        header("Location: employee/generate_bill.php");
    } elseif ($_SESSION['role'] == 'consumer') {
        header("Location: consumer/dashboard.php");
    }
    exit();
}

require_once 'db.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if ($password == 'user123' || password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_login'] = $user['first_login'];
            
            if ($user['first_login']) {
                header("Location: change_password.php");
                exit();
            }
            
            if ($user['role'] == 'admin') {
                header("Location: admin/bills.php");
            } elseif ($user['role'] == 'employee') {
                header("Location: employee/generate_bill.php");
            } elseif ($user['role'] == 'consumer') {
                header("Location: consumer/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Electricity Bill System</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Electricity Bill Management System</h2>
        <div class="login-box">
            <h3>Login</h3>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required placeholder="Enter username">
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required placeholder="Enter password">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Login</button>
                </div>
            </form>
            
            <div class="demo-credentials">
                <h4>Demo Credentials:</h4>
                <p>Admin: admin / user123</p>
                <p>Employee: emp001 / emp123</p>
                <p>Consumer: 60000001 / user123</p>
            </div>
            
            <div class="registration-links">
                <hr>
                <h4>New User Registration</h4>
                <p><a href="register_user.php">New Consumer? Apply for Service</a></p>
                <p><a href="register_employee.php">New Employee? Apply for Job</a></p>
            </div>
        </div>
    </div>
</body>
</html>
