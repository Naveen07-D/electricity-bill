<?php
require_once '../db.php';
checkLogin();
checkRole(['consumer']);

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.* FROM consumers c WHERE c.user_id = $user_id";
$result = mysqli_query($conn, $sql);
$consumer = mysqli_fetch_assoc($result);

$bills_sql = "SELECT * FROM bills WHERE consumer_id = {$consumer['id']} ORDER BY created_at DESC";
$bills_result = mysqli_query($conn, $sql);

$notif_sql = "SELECT * FROM notifications WHERE consumer_id = {$consumer['id']} ORDER BY created_at DESC";
$notif_result = mysqli_query($conn, $notif_sql);

$update_notif_sql = "UPDATE notifications SET is_read = TRUE WHERE consumer_id = {$consumer['id']}";
mysqli_query($conn, $update_notif_sql);

if (isset($_GET['pay']) && isset($_GET['bill_id'])) {
    $bill_id = $_GET['bill_id'];
    $pay_sql = "UPDATE bills SET status = 'paid' WHERE id = $bill_id AND consumer_id = {$consumer['id']}";
    if (mysqli_query($conn, $pay_sql)) {
        $notify_sql = "INSERT INTO notifications (consumer_id, message) VALUES ({$consumer['id']}, 'Your bill for SC NO {$consumer['service_number']} has been paid successfully.')";
        mysqli_query($conn, $notify_sql);
        header("Location: dashboard.php?paid=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Consumer Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
    <div class="header">
        <h2>Consumer Dashboard</h2>
        <div class="user-info">
            Welcome, <?php echo $consumer['name']; ?> | 
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="consumer-info">
            <h3>Your Information</h3>
            <p><strong>Service Number:</strong> <?php echo $consumer['service_number']; ?></p>
            <p><strong>Name:</strong> <?php echo $consumer['name']; ?></p>
            <p><strong>Address:</strong> <?php echo $consumer['address']; ?></p>
            <p><strong>Phone:</strong> <?php echo $consumer['phone']; ?></p>
            <p><strong>Service Type:</strong> <?php echo $consumer['service_type']; ?></p>
        </div>
        
        <div class="notifications">
            <h3>Notifications</h3>
            <?php if (mysqli_num_rows($notif_result) > 0): ?>
                <ul>
                    <?php while ($notif = mysqli_fetch_assoc($notif_result)): ?>
                        <li><?php echo $notif['message']; ?> 
                            <small><?php echo date('d-m-Y H:i', strtotime($notif['created_at'])); ?></small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No notifications.</p>
            <?php endif; ?>
        </div>
        
        <div class="bills-section">
            <h3>Your Bills</h3>
            <?php if (isset($_GET['paid'])): ?>
                <div class="success">Payment successful!</div>
            <?php endif; ?>
            
            <?php
            $bills_query = "SELECT * FROM bills WHERE consumer_id = {$consumer['id']} ORDER BY created_at DESC";
            $bills_result = mysqli_query($conn, $bills_query);
            
            if (mysqli_num_rows($bills_result) > 0): ?>
                <table class="data-table">
                    <tr>
                        <th>Bill ID</th>
                        <th>Bill Date</th>
                        <th>Due Date</th>
                        <th>Units</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($bill = mysqli_fetch_assoc($bills_result)): 
                        $status = $bill['status'];
                        if ($status != 'paid' && date('Y-m-d') > $bill['due_date']) {
                            $status = 'Overdue';
                        }
                    ?>
                        <tr>
                            <td><?php echo $bill['id']; ?></td>
                            <td><?php echo $bill['bill_date']; ?></td>
                            <td><?php echo $bill['due_date']; ?></td>
                            <td><?php echo $bill['units']; ?></td>
                            <td>₹<?php echo $bill['total_amount']; ?></td>
                            <td>
                                <?php if ($status == 'paid'): ?>
                                    <span class="success">Paid</span>
                                <?php elseif ($status == 'Overdue'): ?>
                                    <span class="error">Overdue</span>
                                <?php else: ?>
                                    <span>Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="../view_bill.php?id=<?php echo $bill['id']; ?>" target="_blank" class="btn-small">View</a>
                                <?php if ($bill['status'] == 'unpaid'): ?>
                                    <a href="?pay=1&bill_id=<?php echo $bill['id']; ?>" class="btn-small btn-success" onclick="return confirm('Pay this bill?')">Pay Now</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No bills generated yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
