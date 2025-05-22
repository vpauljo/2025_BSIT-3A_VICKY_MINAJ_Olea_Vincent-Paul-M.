<?php
session_start();
include('db_connect.php');

$seller_company_id = $_SESSION['company_id'] ?? null;
if (!$seller_company_id) {
    echo "You must be logged in as a seller.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];
    $allowed_statuses = ['pending', 'completed', 'shipped'];

    if (!in_array($new_status, $allowed_statuses)) {
        echo "Invalid status.";
        exit();
    }

    $stmt = $conn->prepare("
        SELECT 1 FROM order_details od
        JOIN products p ON od.product_id = p.product_id
        WHERE od.order_id = ? AND p.company_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $order_id, $seller_company_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows == 0) {
        echo "Order does not contain your products or you are not authorized.";
        exit();
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_orders.php");
    exit();
}

$sql = "
SELECT o.order_id, o.buyer_company_id, o.order_date, o.status, o.shipping_address, o.payment_method, o.order_total,
       c.name AS buyer_company_name
FROM orders o
JOIN companies c ON o.buyer_company_id = c.company_id
WHERE EXISTS (
    SELECT 1 FROM order_details od
    JOIN products p ON od.product_id = p.product_id
    WHERE od.order_id = o.order_id AND p.company_id = ?
)
ORDER BY o.order_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_company_id);
$stmt->execute();
$orders_result = $stmt->get_result();

$orders = [];
while ($order = $orders_result->fetch_assoc()) {
    $orders[$order['order_id']] = $order;
}
$stmt->close();

$order_ids = array_keys($orders);
$order_items = [];
if (!empty($order_ids)) {
    $placeholders = implode(',', array_fill(0, count($order_ids), '?'));
    $types = str_repeat('i', count($order_ids));

    $sql = "
    SELECT od.order_id, od.product_id, od.quantity, od.price, p.name AS product_name
    FROM order_details od
    JOIN products p ON od.product_id = p.product_id
    WHERE od.order_id IN ($placeholders)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$order_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $order_items[$row['order_id']][] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="manage_orders.css" />
<title>Manage Orders</title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
  h1 { margin-bottom: 20px; }
  table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
  th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
  th { background-color: #007bff; color: white; }
  select, button { padding: 5px 10px; margin-top: 4px; }
  form { margin: 0; }
  .products-list {
    white-space: pre-line;
    font-size: 0.9em;
    max-width: 300px;
  }
</style>
</head>
<body>
<button onclick="location.href='useller.php'">← Back</button>
<h1>Manage Orders for Your Products</h1>

<?php if (empty($orders)): ?>
    <p>No orders found containing your products.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Buyer Company</th>
                <th>Order Date</th>
                <th>Shipping Address</th>
                <th>Payment Method</th>
                <th>Products</th>
                <th>Order Total</th>
                <th>Status</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['order_id']) ?></td>
                <td><?= htmlspecialchars($order['buyer_company_name']) ?></td>
                <td><?= htmlspecialchars($order['order_date']) ?></td>
                <td><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></td>
                <td><?= htmlspecialchars(ucfirst($order['payment_method'])) ?></td>
                <td class="products-list">
                    <?php
                        $items = $order_items[$order['order_id']] ?? [];
                        foreach ($items as $item) {
                            echo htmlspecialchars($item['product_name']) . " x" . intval($item['quantity']) . " - ₱" . number_format($item['price'],2) . "\n";
                        }
                    ?>
                </td>
                <td>₱<?= number_format($order['order_total'], 2) ?></td>
                <td><?= ucfirst(htmlspecialchars($order['status'])) ?></td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="order_id" value="<?= intval($order['order_id']) ?>" />
                        <select name="new_status">
                            <?php
                                $statuses = ['pending', 'completed', 'shipped'];
                                foreach ($statuses as $status) {
                                    $selected = ($order['status'] === $status) ? 'selected' : '';
                                    echo "<option value=\"$status\" $selected>" . ucfirst($status) . "</option>";
                                }
                            ?>
                        </select>
                        <br>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
