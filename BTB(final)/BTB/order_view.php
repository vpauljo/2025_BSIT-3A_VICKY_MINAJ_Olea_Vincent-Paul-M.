<?php
session_start();
include('db_connect.php');

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order ID.");
}

$order_id = intval($_GET['order_id']);

// Fetch order and company info
$order_sql = "
    SELECT o.order_id, o.order_date, o.status, o.order_total, c.name AS company_name
    FROM orders o
    JOIN companies c ON o.buyer_company_id = c.company_id
    WHERE o.order_id = ?
";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found.");
}

// Fetch order items
$item_sql = "
    SELECT oi.quantity, oi.price, p.name AS product_name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
";
$stmt = $conn->prepare($item_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$item_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Order Details - Order #<?= htmlspecialchars($order['order_id']) ?></title>
<link rel="stylesheet" href="order_view.css" />
</head>
<body>
<div class="container">
    <h1>Order Details - #<?= htmlspecialchars($order['order_id']) ?></h1>
    <p><strong>Company:</strong> <?= htmlspecialchars($order['company_name']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($order['order_date']))) ?></p>
    <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($order['status'])) ?></p>

    <h2>Items</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price (each)</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($item_result->num_rows > 0): ?>
                <?php while ($item = $item_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= intval($item['quantity']) ?></td>
                        <td>₱<?= number_format($item['price'], 2) ?></td>
                        <td>₱<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No items found for this order.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Total: ₱<?= number_format($order['order_total'], 2) ?></h3>

    <p><a href="order_history.php">Back to Order History</a></p>
</div>
</body>
</html>

<?php
$conn->close();
?>
