<?php
require_once '../db.php';
checkLogin();
checkRole(['employee']);

// Include validation functions
require_once '../validation_functions.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_number = trim($_POST['service_number']);
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $service_type = $_POST['service_type'];
    $area_code = $_POST['area_code'];
    
    // Validate inputs using functions
    $errors = [];
    
    if (!validateServiceNumber($service_number, $conn)) {
        $errors[] = "Invalid or duplicate service number!";
    }
    
    if (!validateName($name)) {
        $errors[] = "Name should contain only alphabets!";
    }
    
    if (!validatePhone($phone)) {
        $errors[] = "Phone number must be 10 digits!";
    }
    
    if (empty($errors)) {
        // Insert consumer with pending status
        $employee_id = $_SESSION['user_id'];
        $sql = "INSERT INTO consumers (service_number, name, address, phone, service_type, area_code, created_by, status) 
                VALUES ('$service_number', '$name', '$address', '$phone', '$service_type', '$area_code', $employee_id, 'pending')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='success'>Consumer added successfully! Waiting for admin approval.</div>";
            // Clear form
            $_POST = array();
        } else {
            $message = "<div class='error'>Error adding consumer: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $message = "<div class='error'>" . implode("<br>", $errors) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Consumer</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
    <div class="header">
        <h2>Employee Dashboard - Add Consumer</h2>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> (Employee) | 
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="menu">
        <a href="add_consumer.php" class="active">Add Consumer</a>
        <a href="generate_bill.php">Generate Bill</a>
    </div>
    
    <div class="container">
        <h3>Add New Consumer</h3>
        
        <?php echo $message; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Service Number:*</label>
                <input type="text" name="service_number" required 
                       value="<?php echo isset($_POST['service_number']) ? $_POST['service_number'] : ''; ?>"
                       placeholder="Enter unique service number">
            </div>
            
            <div class="form-group">
                <label>Name:*</label>
                <input type="text" name="name" required 
                       value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>"
                       placeholder="Enter consumer name (alphabets only)">
            </div>
            
            <div class="form-group">
                <label>Address:*</label>
                <textarea name="address" required rows="3" 
                          placeholder="Enter complete address"><?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Phone:*</label>
                <input type="text" name="phone" required 
                       value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>"
                       placeholder="Enter 10-digit phone number">
            </div>
            
            <div class="form-group">
                <label>Service Type:*</label>
                <select name="service_type" required>
                    <option value="Household" <?php echo (isset($_POST['service_type']) && $_POST['service_type'] == 'Household') ? 'selected' : ''; ?>>Household</option>
                    <option value="Commercial" <?php echo (isset($_POST['service_type']) && $_POST['service_type'] == 'Commercial') ? 'selected' : ''; ?>>Commercial</option>
                    <option value="Industrial" <?php echo (isset($_POST['service_type']) && $_POST['service_type'] == 'Industrial') ? 'selected' : ''; ?>>Industrial</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Area Code:</label>
                <input type="text" name="area_code" 
                       value="<?php echo isset($_POST['area_code']) ? $_POST['area_code'] : ''; ?>"
                       placeholder="Enter area code">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Add Consumer</button>
                <button type="reset" class="btn btn-secondary">Clear</button>
            </div>
        </form>
    </div>
</body>
</html>