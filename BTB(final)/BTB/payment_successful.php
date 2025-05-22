<?php
session_start();
include('db_connect.php');

$order_id = $_SESSION['last_order_id'] ?? null;

if (!$order_id) {
    echo "No order ID found.";
    exit();
}

// Fetch order info
$stmt = $conn->prepare("
    SELECT o.*, c.name AS company_name
    FROM orders o
    JOIN companies c ON o.buyer_company_id = c.company_id
    WHERE o.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch order details
$stmt = $conn->prepare("
    SELECT od.*, p.name
    FROM order_details od
    JOIN products p ON od.product_id = p.product_id
    WHERE od.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Payment Successful</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f7f8fa;
      margin: 0;
      padding: 40px;
    }

    .invoice-box {
      background: #fff;
      padding: 40px;
      max-width: 800px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 10px;
    }

    h1, h2 {
      margin-top: 0;
    }

    .invoice-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .invoice-header h1 {
      font-size: 24px;
      color: #333;
    }

    .invoice-info p {
      margin: 4px 0;
      color: #555;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    .total {
      font-weight: bold;
    }

    .success {
      text-align: center;
      padding: 20px;
      background: #e6ffed;
      border: 1px solid #b2f2bb;
      color: #2f9e44;
      border-radius: 5px;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>
  <div class="invoice-box">
    <div class="success">
      <h2>✅ Payment Successful!</h2>
      <p>Your order has been placed successfully.</p>
    </div>

    <div class="invoice-header">
      <h1>Invoice #<?= htmlspecialchars($order_id) ?></h1>
      <div class="invoice-info">
        <p><strong>Company:</strong> <?= htmlspecialchars($order['company_name']) ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
      </div>
    </div>

    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
    <p><strong>Payment Method:</strong> <?= strtoupper(htmlspecialchars($order['payment_method'])) ?></p>
    <?php if ($order['gcash_number']): ?>
      <p><strong>GCash #:</strong> <?= htmlspecialchars($order['gcash_number']) ?></p>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php $grand_total = 0; ?>
        <?php foreach ($items as $item): ?>
          <?php $subtotal = $item['quantity'] * $item['price']; ?>
          <?php $grand_total += $subtotal; ?>
          <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($subtotal, 2) ?></td>
          </tr>
        <?php endforeach; ?>
        <tr class="total">
          <td colspan="3">Total</td>
          <td>₱<?= number_format($grand_total, 2) ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>
