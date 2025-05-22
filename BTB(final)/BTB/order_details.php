<?php
include 'db_connect.php';

if (!isset($_GET['order_id'])) {
    echo "No order ID provided.";
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch order and buyer info
$sql = "SELECT o.order_id, o.order_date, o.status, o.order_total, 
               o.email, o.shipping_address, o.payment_method, o.gcash_number,
               c.name AS business_name
        FROM orders o
        JOIN companies c ON o.buyer_company_id = c.company_id
        WHERE o.order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Order not found.";
    exit;
}

$order = $result->fetch_assoc();
$stmt->close();

// Fetch ordered products
$sqlDetails = "SELECT od.product_id, p.name AS product_name, od.quantity, od.price
               FROM order_details od
               JOIN products p ON od.product_id = p.product_id
               WHERE od.order_id = ?";

$stmtDetails = $conn->prepare($sqlDetails);
$stmtDetails->bind_param("i", $order_id);
$stmtDetails->execute();
$detailsResult = $stmtDetails->get_result();
$order_items = $detailsResult->fetch_all(MYSQLI_ASSOC);
$stmtDetails->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link rel="stylesheet" href="orders.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 40px;
        }

        .orders-container {
            background: #fff;
            max-width: 800px;
            margin: auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        p {
            margin: 8px 0;
            color: #333;
        }

        .order-items {
            margin-top: 30px;
        }

        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-items th, .order-items td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        .order-items th {
            background: #f0f0f0;
        }

        .total {
            font-weight: bold;
        }

        .back-to-home {
            margin-top: 30px;
            text-align: right;
        }

        .btn-back-home {
            background: #2e7d32;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-back-home:hover {
            background: #256428;
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <h1>Order Details - #<?php echo $order['order_id']; ?></h1>

        <p><strong>Business Name:</strong> <?= htmlspecialchars($order['business_name']) ?></p>
        <p><strong>Order Date:</strong> <?= $order['order_date'] ?></p>
        <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
        <p><strong>Payment Method:</strong> <?= strtoupper($order['payment_method']) ?></p>

        <?php if ($order['payment_method'] === 'gcash'): ?>
            <p><strong>GCash Number:</strong> <?= htmlspecialchars($order['gcash_number']) ?></p>
        <?php endif; ?>

        <div class="order-items">
            <h2>Items Ordered</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>₱<?= number_format($item['price'], 2) ?></td>
                            <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total">
                        <td colspan="3" style="text-align: right;">Total:</td>
                        <td>₱<?= number_format($order['order_total'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px; text-align: right;">
    <a href="invoice.php?order_id=<?= $order['order_id'] ?>" target="_blank" 
       style="background:#007bff; color:#fff; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:600;">
       Generate Invoice
    </a>
</div>


        <div class="back-to-home">
            <a href="orders.php" class="btn-back-home">Back to Orders</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
