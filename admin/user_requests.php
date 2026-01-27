<?php
require_once '../db.php';
checkLogin();
checkRole(['admin']);

function generateServiceNumber($category, $conn) {
    $prefix = '6';
    if ($category == 'Commercial') $prefix = '7';
    if ($category == 'Industrial') $prefix = '8';
    
    $sql = "SELECT service_number FROM service_applications WHERE service_number LIKE '$prefix%' ORDER BY service_number DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_number = intval(substr($row['service_number'], 1));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    return $prefix . str_pad($new_number, 7, '0', STR_PAD_LEFT);
}

function generateMeterNumber() {
    return 'MTR' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $app_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'verify') {
        $sql = "UPDATE service_applications SET is_verified = TRUE WHERE id = $app_id";
        mysqli_query($conn, $sql);
        $_SESSION['success'] = "Document verified successfully.";
        header("Location: user_requests.php");
        exit();
    } elseif ($action == 'approve') {
        $sql = "SELECT * FROM service_applications WHERE id = $app_id AND is_verified = TRUE";
        $result = mysqli_query($conn, $sql);
        $application = mysqli_fetch_assoc($result);
        
        if ($application) {
            $service_number = generateServiceNumber($application['service_category'], $conn);
            $meter_number = generateMeterNumber();
            $username = $service_number;
            $password = password_hash('user123', PASSWORD_DEFAULT);
            
            $sql1 = "INSERT INTO users (username, password, role, first_login) VALUES ('$username', '$password', 'consumer', 1)";
            if (mysqli_query($conn, $sql1)) {
                $user_id = mysqli_insert_id($conn);
                
                $name = ucwords(strtolower($application['name']));
                $sql2 = "INSERT INTO consumers (service_number, name, address, phone, service_type, status, is_active, user_id) 
                         VALUES ('$service_number', '$name', '{$application['address']}', '{$application['mobile']}', '{$application['service_category']}', 'approved', TRUE, $user_id)";
                mysqli_query($conn, $sql2);
                
                $sql3 = "UPDATE service_applications SET status = 'approved', service_number = '$service_number', meter_number = '$meter_number', user_id = $user_id WHERE id = $app_id";
                mysqli_query($conn, $sql3);
                
                $sql4 = "INSERT INTO notifications (consumer_id, message) VALUES ($user_id, 'Your electricity service has been approved. Your Service Number is $service_number')";
                mysqli_query($conn, $sql4);
                
                $_SESSION['success'] = "Consumer approved! Service Number: $service_number";
            }
        } else {
            $_SESSION['error'] = "Document must be verified before approval.";
        }
    } elseif ($action == 'reject') {
        if (isset($_POST['reject_reason'])) {
            $reason = mysqli_real_escape_string($conn, $_POST['reject_reason']);
            $sql = "UPDATE service_applications SET status = 'rejected', rejection_reason = '$reason' WHERE id = $app_id";
            mysqli_query($conn, $sql);
            $_SESSION['success'] = "Application rejected.";
        }
    }
    
    header("Location: user_requests.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Consumer Requests</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
    <div class="header">
        <h2>Admin - Consumer Requests</h2>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> | 
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="menu">
        <a href="bills.php">Latest Bills</a>
        <a href="services.php">All Services</a>
        <a href="user_requests.php" class="active">Consumer Requests</a>
        <a href="employee_requests.php">Employee Requests</a>
    </div>
    
    <div class="container">
        <h3>Pending Consumer Applications</h3>
        
        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='error'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>
        
        <?php
        $sql = "SELECT * FROM service_applications WHERE status = 'pending' ORDER BY created_at DESC";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="application-card">';
                echo '<h4>Application ID: ' . $row['id'] . ' - ' . $row['name'] . '</h4>';
                echo '<p><strong>Mobile:</strong> ' . $row['mobile'] . '</p>';
                echo '<p><strong>Address:</strong> ' . $row['address'] . '</p>';
                echo '<p><strong>Pincode:</strong> ' . $row['pincode'] . '</p>';
                echo '<p><strong>Category:</strong> ' . $row['service_category'] . '</p>';
                echo '<p><strong>Applied Date:</strong> ' . date('d-m-Y H:i', strtotime($row['created_at'])) . '</p>';
                
                echo '<p><strong>Document Proof:</strong> ';
                if ($row['document_proof']) {
                    echo '<a href="../uploads/documents/' . $row['document_proof'] . '" target="_blank">View Document</a>';
                } else {
                    echo 'No document uploaded';
                }
                echo '</p>';
                
                echo '<p><strong>Document Verified:</strong> ' . ($row['is_verified'] ? 'Yes' : 'No') . '</p>';
                
                echo '<div class="action-buttons">';
                if (!$row['is_verified']) {
                    echo '<a href="?id=' . $row['id'] . '&action=verify" class="btn-small btn-info">Verify Document</a>';
                }
                if ($row['is_verified']) {
                    echo '<a href="?id=' . $row['id'] . '&action=approve" class="btn-small btn-success">Approve</a>';
                }
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
