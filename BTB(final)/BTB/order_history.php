<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['company'])) {
    echo "Please login to see your orders.";
    exit();
}

$company = $_SESSION['company'];

// Fetch orders for this company
$stmt = $conn->prepare("SELECT * FROM orders WHERE company = ? ORDER BY order_id DESC");
$stmt->bind_param("s", $company);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Order History | B2B E-commerce</title>
  <link rel="stylesheet" href="order_history.css" />
</head>
<body>
  <h1>Order History for <?= htmlspecialchars($company) ?></h1>
  <?php if ($result->num_rows > 0): ?>
    <table border="1" cellpadding="10" cellspacing="0">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Email</th>
          <th>Address</th>
          <th>Payment Method</th>
          <th>GCash Number</th>
          <th>Order Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($order = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $order['order_id'] ?></td>
          <td><?= htmlspecialchars($order['email']) ?></td>
          <td><?= htmlspecialchars($order['address']) ?></td>
          <td><?= htmlspecialchars($order['payment_method']) ?></td>
          <td><?= htmlspecialchars($order['gcash_number']) ?></td>
          <td><?= htmlspecialchars($order['created_at']) // if you have a timestamp ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No orders found.</p>
  <?php endif; ?>
</body>
</html>
