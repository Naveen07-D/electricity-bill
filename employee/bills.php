<?php
require_once '../db.php';
checkLogin();
checkRole(['employee']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Bills - Employee</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
    <div class="header">
        <h2>Employee Dashboard - All Bills</h2>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> (Employee) | 
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="menu">
        <a href="generate_bill.php">Generate Bill</a>
        <a href="bills.php" class="active">View All Bills</a>
    </div>
    
    <div class="container">
        <h3>All Electricity Bills</h3>
        
        <?php
        $sql = "SELECT b.*, c.name, c.service_number 
                FROM bills b 
                JOIN consumers c ON b.consumer_id = c.id 
                ORDER BY b.created_at DESC";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            echo '<table class="data-table">';
            echo '<tr>
                    <th>Bill No</th>
                    <th>Service No</th>
                    <th>Name</th>
                    <th>Units</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                  </tr>';
            
            while ($row = mysqli_fetch_assoc($result)) {
                $status = $row['status'];
                if ($status != 'paid' && date('Y-m-d') > $row['due_date']) {
                    $status = 'Overdue';
                }
                
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['service_number'] . '</td>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td>' . $row['units'] . '</td>';
                echo '<td>₹' . $row['total_amount'] . '</td>';
                echo '<td>' . $row['due_date'] . '</td>';
                echo '<td>';
                if ($status == 'paid') {
                    echo '<span class="success">Paid</span>';
                } elseif ($status == 'Overdue') {
                    echo '<span class="error">Overdue</span>';
                } else {
                    echo '<span>Unpaid</span>';
                }
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No bills generated yet.</p>';
        }
        ?>
    </div>
</body>
</html>
