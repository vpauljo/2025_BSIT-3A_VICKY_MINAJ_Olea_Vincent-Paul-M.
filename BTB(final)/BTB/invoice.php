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
    <title>Invoice #<?= $order['order_id'] ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            max-width: 800px;
            margin: auto;
            background: #fff;
            color: #333;
        }
        h1, h2 {
            margin-bottom: 0;
        }
        hr {
            margin: 20px 0;
        }
        .header, .footer {
            text-align: center;
        }
        .details, .items {
            margin-top: 20px;
        }
        .details p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #333;
        }
        @media print {
            a#print-btn {
                display: none;
            }
        }
        #print-btn {
            margin-top: 30px;
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }
        #print-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice</h1>
        <h2>Order #<?= $order['order_id'] ?></h2>
        <hr>
    </div>

    <div class="details">
        <p><strong>Business Name:</strong> <?= htmlspecialchars($order['business_name']) ?></p>
        <p><strong>Order Date:</strong> <?= $order['order_date'] ?></p>
        <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Shipping Address:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
        <p><strong>Payment Method:</strong> <?= strtoupper($order['payment_method']) ?></p>
        <?php if ($order['payment_method'] === 'gcash'): ?>
            <p><strong>GCash Number:</strong> <?= htmlspecialchars($order['gcash_number']) ?></p>
        <?php endif; ?>
    </div>

    <div class="items">
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
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">Total:</td>
                    <td>₱<?= number_format($order['order_total'], 2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer" style="margin-top: 40px;">
        <p>Thank you for your business!</p>
    </div>

    <a href="#" id="print-btn" onclick="window.print();return false;">Print Invoice</a>
</body>
</html>

<?php
$conn->close();
?>
