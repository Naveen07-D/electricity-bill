<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "electricity_bill";

$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) {
    die("Connection failed");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }
}

function checkRole($allowed_roles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: ../index.php");
        exit();
    }
}
?>
