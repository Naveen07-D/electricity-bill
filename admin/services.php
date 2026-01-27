<?php
require_once '../db.php';
checkLogin();
checkRole(['admin']);

if (isset($_GET['action']) && isset($_GET['id'])) {
    $consumer_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'activate') {
        $sql = "UPDATE consumers SET is_active = TRUE WHERE id = $consumer_id";
        mysqli_query($conn, $sql);
        $_SESSION['success'] = "Service activated successfully.";
    } elseif ($action == 'deactivate') {
        $sql = "UPDATE consumers SET is_active = FALSE WHERE id = $consumer_id";
        mysqli_query($conn, $sql);
        $_SESSION['success'] = "Service deactivated successfully.";
    }
    
    header("Location: services.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - All Services</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
    <div class="header">
        <h2>Admin Dashboard - All Services</h2>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> | 
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="menu">
        <a href="bills.php">Latest Bills</a>
        <a href="services.php" class="active">All Services</a>
        <a href="user_requests.php">Consumer Requests</a>
        <a href="employee_requests.php">Employee Requests</a>
    </div>
    
    <div class="container">
        <h3>All Electricity Services</h3>
        
        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        ?>
        
        <?php
        $sql = "SELECT c.*, u.username, 
                (SELECT COUNT(*) FROM bills WHERE consumer_id = c.id) as total_bills,
                (SELECT COUNT(*) FROM bills WHERE consumer_id = c.id AND status = 'unpaid') as unpaid_bills
                FROM consumers c 
                JOIN users u ON c.user_id = u.id 
                ORDER BY c.created_at DESC";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            echo '<table class="data-table">';
            echo '<tr>
                    <th>Service No</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Service Type</th>
                    <th>Username</th>
                    <th>Total Bills</th>
                    <th>Unpaid Bills</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                  </tr>';
            
            while ($row = mysqli_fetch_assoc($result)) {
                $status_class = $row['is_active'] ? 'success' : 'error';
                $status_text = $row['is_active'] ? 'Active' : 'Inactive';
                
                echo '<tr>';
                echo '<td>' . $row['service_number'] . '</td>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td>' . $row['phone'] . '</td>';
                echo '<td>' . $row['service_type'] . '</td>';
                echo '<td>' . $row['username'] . '</td>';
                echo '<td>' . $row['total_bills'] . '</td>';
                echo '<td>' . $row['unpaid_bills'] . '</td>';
                echo '<td><span class="' . $status_class . '">' . $status_text . '</span></td>';
                echo '<td>' . date('d-m-Y', strtotime($row['created_at'])) . '</td>';
                echo '<td>';
                if ($row['is_active']) {
                    echo '<a href="?id=' . $row['id'] . '&action=deactivate" class="btn-small btn-danger">Deactivate</a>';
                } else {
                    echo '<a href="?id=' . $row['id'] . '&action=activate" class="btn-small btn-success">Activate</a>';
                }
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No services found.</p>';
        }
        ?>
        
        <h3>Service Statistics</h3>
        <?php
        $stats_sql = "SELECT 
                      service_type,
                      COUNT(*) as total_services,
                      SUM(CASE WHEN is_active = TRUE THEN 1 ELSE 0 END) as active_services,
                      SUM(CASE WHEN is_active = FALSE THEN 1 ELSE 0 END) as inactive_services
                      FROM consumers 
                      GROUP BY service_type";
        $stats_result = mysqli_query($conn, $stats_sql);
        ?>
        
        <table class="data-table">
            <tr>
                <th>Service Type</th>
                <th>Total Services</th>
                <th>Active Services</th>
                <th>Inactive Services</th>
            </tr>
            <?php while ($stat = mysqli_fetch_assoc($stats_result)): ?>
            <tr>
                <td><?php echo $stat['service_type']; ?></td>
                <td><?php echo $stat['total_services']; ?></td>
                <td><?php echo $stat['active_services']; ?></td>
                <td><?php echo $stat['inactive_services']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
