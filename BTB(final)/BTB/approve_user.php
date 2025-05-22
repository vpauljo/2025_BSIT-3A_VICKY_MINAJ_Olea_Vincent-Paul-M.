<?php
include 'db_connect.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Approve user in users table
    $conn->query("UPDATE users SET status = 'Approved' WHERE id = $id");

    // Fetch user details
    $result = $conn->query("SELECT * FROM users WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        $business_name = $conn->real_escape_string($row['business_name']);
        $owner_name = $conn->real_escape_string($row['business_name']); // Assuming this is owner
        $email = $conn->real_escape_string($row['email']);

        // Insert or update in businesses
        $conn->query("
            INSERT INTO businesses (business_name, owner_name, email, registration_date, status)
            VALUES ('$business_name', '$owner_name', '$email', NOW(), 'Approved')
            ON DUPLICATE KEY UPDATE status = 'Approved'
        ");
    }
}

header("Location: userapp.php");
exit;
?>
