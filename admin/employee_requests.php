<?php
require_once '../db.php';
checkLogin();
checkRole(['admin']);

function getNextEmployeeNumber($conn) {
    $sql = "SELECT username FROM users WHERE username LIKE 'emp%' ORDER BY username DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_number = intval(substr($row['username'], 3));
        $next_number = $last_number + 1;
    } else {
        $next_number = 1;
    }
    
    return 'emp' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $app_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $sql = "SELECT * FROM employee_applications WHERE id = $app_id";
        $result = mysqli_query($conn, $sql);
        $application = mysqli_fetch_assoc($result);
        
        if ($application) {
            $username = getNextEmployeeNumber($conn);
            $password = password_hash('emp123', PASSWORD_DEFAULT);
            
            $sql1 = "INSERT INTO users (username, password, role, first_login) VALUES ('$username', '$password', 'employee', 1)";
            if (mysqli_query($conn, $sql1)) {
                $user_id = mysqli_insert_id($conn);
                
                $sql2 = "UPDATE employee_applications SET status = 'approved', user_id = $user_id WHERE id = $app_id";
                mysqli_query($conn, $sql2);
                
                $_SESSION['success'] = "Employee approved! Username: $username, Password: emp123";
            }
        }
    } elseif ($action == 'reject') {
        if (isset($_POST['reject_reason'])) {
            $reason = mysqli_real_escape_string($conn, $_POST['reject_reason']);
            $sql = "UPDATE employee_applications SET status = 'rejected', rejection_reason = '$reason' WHERE id = $app_id";
            mysqli_query($conn, $sql);
            $_SESSION['success'] = "Application rejected.";
        }
    }
    
    header("Location: employee_requests.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Requests</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
    <div class="header">
        <h2>Admin - Employee Requests</h2>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> | 
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="menu">
        <a href="bills.php">Latest Bills</a>
        <a href="services.php">All Services</a>
        <a href="user_requests.php">Consumer Requests</a>
        <a href="employee_requests.php" class="active">Employee Requests</a>
    </div>
    
    <div class="container">
        <h3>Pending Employee Applications</h3>
        
        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        ?>
        
        <?php
        $sql = "SELECT * FROM employee_applications WHERE status = 'pending' ORDER BY created_at DESC";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="application-card">';
                echo '<h4>Application ID: ' . $row['id'] . ' - ' . $row['name'] . '</h4>';
                echo '<p><strong>Mobile:</strong> ' . $row['mobile'] . '</p>';
                echo '<p><strong>Address:</strong> ' . $row['address'] . '</p>';
                echo '<p><strong>Applied Date:</strong> ' . date('d-m-Y H:i', strtotime($row['created_at'])) . '</p>';
                
                echo '<p><strong>Document Proof:</strong> ';
                if ($row['document_proof']) {
                    echo '<a href="../uploads/employee_docs/' . $row['document_proof'] . '" target="_blank">View Document</a>';
                } else {
                    echo 'No document uploaded';
                }
                echo '</p>';
                
                echo '<div class="action-buttons">';
                echo '<a href="?id=' . $row['id'] . '&action=approve" class="btn-small btn-success">Approve</a>';
                echo '<form method="POST" action="?id=' . $row['id'] . '&action=reject" style="display:inline;">
                        <input type="text" name="reject_reason" placeholder="Reason for rejection" required>
                        <button type="submit" class="btn-small btn-danger">Reject</button>
                      </form>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No pending applications.</p>';
        }
        ?>
    </div>
</body>
</html>
