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
    
    if (empty($name) || empty($mobile) || empty($address)) {
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
                $upload_dir = 'uploads/employee_docs/';
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
                    
                    $sql = "INSERT INTO employee_applications (name, mobile, address, document_proof, status) 
                            VALUES ('$name', '$mobile', '$address', '$file_name', 'pending')";
                    
                    if (mysqli_query($conn, $sql)) {
                        $message = "<div class='success'>Your job application has been submitted for admin approval.</div>";
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
            $message = "<div class='error'>Document proof is required.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Registration</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>⚡ Employee Job Application</h2>
        <div class="login-box">
            <h3>Apply for Employee Position</h3>
            
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
                    <label>Document Proof:*</label>
                    <input type="file" name="document_proof" required accept=".jpg,.jpeg,.png,.pdf">
                    <small>Upload ID proof (Aadhar, PAN, Passport)</small>
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
