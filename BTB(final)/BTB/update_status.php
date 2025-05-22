<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } else {
        // Invalid action
        exit('Invalid action.');
    }

    // Update product status
    $stmt = $conn->prepare("UPDATE products SET status = ? WHERE product_id = ?");
    $stmt->bind_param("si", $status, $product_id);

    if ($stmt->execute()) {
        // Redirect to product_approval.php after update
        header("Location: product_approval.php");
        exit();
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
