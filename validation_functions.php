<?php
// Validation functions for the project

function validateName($name) {
    // Name should contain only alphabets and spaces
    if (preg_match('/^[a-zA-Z\s]+$/', $name)) {
        return true;
    }
    return false;
}

function validatePhone($phone) {
    // Phone should be exactly 10 digits
    if (preg_match('/^[0-9]{10}$/', $phone)) {
        return true;
    }
    return false;
}

function validateServiceNumber($service_number, $conn) {
    // Service number should not be empty
    if (empty($service_number)) {
        return false;
    }
    
    // Check for duplicates in database
    $sql = "SELECT id FROM consumers WHERE service_number = '$service_number'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        return false; // Duplicate found
    }
    
    return true;
}

function validateReadings($previous, $current) {
    if ($current < $previous) {
        return false;
    }
    return true;
}
?>