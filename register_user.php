<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);
    $address = trim($_POST['address']);
    $pincode = trim($_POST['pincode']);
    $service_category = $_POST['service_category'];
    
    if (empty($name) || empty($mobile) || empty($address) || empty($pincode)) {
        $message = "<div class='error'>All fields are required!</div>";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $message = "<div class='error'>Name should contain only alphabets!</div>";
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $message = "<div class='error'>Mobile number must be exactly 10 digits!</div>";
    } else {
        if ($_FILES['document_proof']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            $file_type = $_FILES['document_proof']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $upload_dir = 'uploads/documents/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['document_proof']['name']);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['document_proof']['tmp_name'], $target_file)) {
                    $name = ucwords(strtolower($name));
                    $name = mysqli_real_escape_string($conn, $name);
                    $mobile = mysqli_real_escape_string($conn, $mobile);
                    $address = mysqli_real_escape_string($conn, $address);
                    $pincode = mysqli_real_escape_string($conn, $pincode);
                    $service_category = mysqli_real_escape_string($conn, $service_category);
                    
                    $sql = "INSERT INTO service_applications (name, mobile, address, pincode, document_proof, service_category, status) 
                            VALUES ('$name', '$mobile', '$address', '$pincode', '$file_name', '$service_category', 'pending')";
                    
                    if (mysqli_query($conn, $sql)) {
                        $message = "<div class='success'>Your request has been sent for admin approval.</div>";
                        $_POST = array();
                    } else {
                        $message = "<div class='error'>Error: " . mysqli_error($conn) . "</div>";
                    }
                } else {
                    $message = "<div class='error'>Failed to upload document.</div>";
                }
            } else {
                $message = "<div class='error'>Only JPG, PNG, PDF files are allowed.</div>";
            }
        } else {
            $message = "<div class='error'>Document proof is required for address verification.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Consumer Registration</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>⚡ New Consumer Registration</h2>
        <div class="login-box">
            <h3>Apply for Electricity Service</h3>
            
            <?php echo $message; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Full Name:*</label>
                    <input type="text" name="name" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Mobile Number:*</label>
                    <input type="text" name="mobile" required 
                           value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>" maxlength="10">
                </div>
                
                <div class="form-group">
                    <label>Address:*</label>
                    <textarea name="address" required rows="3"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Pincode:*</label>
                    <input type="text" name="pincode" required 
                           value="<?php echo isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Document Proof:*</label>
                    <input type="file" name="document_proof" required accept=".jpg,.jpeg,.png,.pdf">
                    <small>Upload address proof (Aadhar, Passport, Driving License, Utility Bill)</small>
                </div>
                
                <div class="form-group">
                    <label>Service Category:*</label>
                    <select name="service_category" required>
                        <option value="">-- Select Category --</option>
                        <option value="Household">Household</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Industrial">Industrial</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Submit Application</button>
                    <a href="index.php" class="btn btn-secondary">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
