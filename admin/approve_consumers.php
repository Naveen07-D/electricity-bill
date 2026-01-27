<?php
require_once '../db.php';
checkLogin();
checkRole(['admin']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Consumers</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
    <div class="header">
        <h2>Admin Dashboard - Approve Consumers</h2>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> (Admin) | 
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
   <div class="menu">
        <a href="approve_consumers.php">Approve Consumers</a>
        <a href="user_requests.php">Consumer Requests</a>
        <a href="employee_requests.php">Employee Requests</a>
        <a href="bills.php">View All Bills</a>
    </div>
    
    <div class="container">
        <h3>Pending Consumer Approvals</h3>
        
        <?php
        // Get pending consumers
        $sql = "SELECT c.*, u.username as created_by_name 
                FROM consumers c 
                LEFT JOIN users u ON c.created_by = u.id 
                WHERE c.status = 'pending'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            echo '<table class="data-table">';
            echo '<tr>
                    <th>Service No</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Service Type</th>
                    <th>Created By</th>
                    <th>Actions</th>
                  </tr>';
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['service_number'] . '</td>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td>' . $row['phone'] . '</td>';
                echo '<td>' . $row['service_type'] . '</td>';
                echo '<td>' . $row['created_by_name'] . '</td>';
                echo '<td>
                        <a href="approve_action.php?id=' . $row['id'] . '&action=approve" class="btn-small btn-success">Approve</a>
                        <a href="approve_action.php?id=' . $row['id'] . '&action=reject" class="btn-small btn-danger">Reject</a>
                      </td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No pending approvals.</p>';
        }
        ?>
    </div>
</body>
</html>
