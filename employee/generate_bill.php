<?php
require_once '../db.php';
checkLogin();
checkRole(['employee']);

require_once '../calculation_functions.php';

$message = "";
$consumers = [];
$previous_reading = 0;

$sql = "SELECT * FROM consumers WHERE status = 'approved' AND is_active = TRUE ORDER BY name";
$result = mysqli_query($conn, $sql);
if ($result) {
    $consumers = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $consumer_id = $_POST['consumer_id'];
    $current_reading = floatval($_POST['current_reading']);
    
    $prev_sql = "SELECT current_reading FROM bills WHERE consumer_id = $consumer_id ORDER BY id DESC LIMIT 1";
    $prev_result = mysqli_query($conn, $prev_sql);
    if (mysqli_num_rows($prev_result) > 0) {
        $prev_row = mysqli_fetch_assoc($prev_result);
        $previous_reading = $prev_row['current_reading'];
    } else {
        $previous_reading = 0;
    }
    
    if ($current_reading < $previous_reading) {
        $message = "<div class='error'>Current reading cannot be less than previous reading!</div>";
    } else {
        $units = $current_reading - $previous_reading;
        
        $sql = "SELECT service_type FROM consumers WHERE id = $consumer_id";
        $result = mysqli_query($conn, $sql);
        $consumer = mysqli_fetch_assoc($result);
        $service_type = $consumer['service_type'];
        
        $amount = calculateBillAmount($units, $service_type);
        
        $pending_sql = "SELECT total_amount FROM bills 
                       WHERE consumer_id = $consumer_id AND status = 'unpaid'
                       ORDER BY id DESC LIMIT 1";
        $pending_result = mysqli_query($conn, $pending_sql);
        $previous_pending = 0;
        if (mysqli_num_rows($pending_result) > 0) {
            $pending_row = mysqli_fetch_assoc($pending_result);
            $previous_pending = $pending_row['total_amount'];
        }
        
        $bill_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+15 days'));
        
        $total_amount = $amount + $previous_pending;
        
        $employee_id = $_SESSION['user_id'];
        $insert_sql = "INSERT INTO bills (consumer_id, previous_reading, current_reading, 
                      units, amount, previous_pending, total_amount, bill_date, due_date, generated_by) 
                      VALUES ($consumer_id, $previous_reading, $current_reading, 
                      $units, $amount, $previous_pending, $total_amount, '$bill_date', '$due_date', $employee_id)";
        
        if (mysqli_query($conn, $insert_sql)) {
            $bill_id = mysqli_insert_id($conn);
            
            $notification_sql = "INSERT INTO notifications (consumer_id, message) 
                               VALUES ($consumer_id, 'New electricity bill generated. Please check your dashboard.')";
            mysqli_query($conn, $notification_sql);
            
            $message = "<div class='success'>Bill generated successfully! 
                       <a href='../view_bill.php?id=$bill_id' target='_blank'>View Bill</a></div>";
        } else {
            $message = "<div class='error'>Error generating bill: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Bill</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <script>
        function getPreviousReading() {
            var consumerId = document.getElementById('consumer_id').value;
            if (consumerId) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'get_previous_reading.php?consumer_id=' + consumerId, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('previous_reading').value = xhr.responseText;
                    }
                };
                xhr.send();
            } else {
                document.getElementById('previous_reading').value = '0';
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <h2>Employee Dashboard - Generate Bill</h2>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> (Employee) | 
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="menu">
        <a href="generate_bill.php" class="active">Generate Bill</a>
        <a href="bills.php">View All Bills</a>
    </div>
    
    <div class="container">
        <h3>Generate Electricity Bill</h3>
        
        <?php echo $message; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Select Consumer:*</label>
                <select name="consumer_id" id="consumer_id" required onchange="getPreviousReading()">
                    <option value="">-- Select Consumer --</option>
                    <?php foreach ($consumers as $consumer): ?>
                        <option value="<?php echo $consumer['id']; ?>">
                            <?php echo $consumer['name'] . ' (' . $consumer['service_number'] . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Previous Reading:*</label>
                <input type="text" id="previous_reading" readonly value="0">
                <small>System auto-filled from last bill</small>
            </div>
            
            <div class="form-group">
                <label>Current Reading:*</label>
                <input type="number" step="0.01" name="current_reading" required 
                       placeholder="Enter current meter reading">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Generate Bill</button>
            </div>
        </form>
    </div>
</body>
</html>
