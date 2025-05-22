<?php
include 'db_connect.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Reject user in users table
    $conn->query("UPDATE users SET status = 'Rejected' WHERE id = $id");

    // Optionally update status in businesses
    $userResult = $conn->query("SELECT * FROM users WHERE id = $id");
    if ($userResult && $row = $userResult->fetch_assoc()) {
        $business_name = $conn->real_escape_string($row['business_name']);

        $conn->query("UPDATE businesses SET status = 'Rejected' WHERE business_name = '$business_name'");
    }
}

header("Location: userapp.php");
exit;
?>
