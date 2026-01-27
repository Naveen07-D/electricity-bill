<?php
require_once '../db.php';
checkLogin();
checkRole(['employee']);

if (isset($_GET['consumer_id'])) {
    $consumer_id = $_GET['consumer_id'];
    $sql = "SELECT current_reading FROM bills WHERE consumer_id = $consumer_id ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo $row['current_reading'];
    } else {
        echo '0';
    }
}
?>
