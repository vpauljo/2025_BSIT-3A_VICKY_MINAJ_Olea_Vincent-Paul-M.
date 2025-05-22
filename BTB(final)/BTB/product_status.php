<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['action'])) {
    $product_id = intval($_POST['product_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } else {
        die("Invalid action");
    }

    $stmt = $conn->prepare("UPDATE products SET status = ? WHERE product_id = ?");
    $stmt->bind_param("si", $status, $product_id);

    if ($stmt->execute()) {
        header("Location: product_approval.php");
        exit;
    } else {
        echo "Error updating status.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
