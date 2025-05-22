<?php
session_start();
include('db_connect.php');

$username = $_SESSION['username'] ?? null;
if (!$username) {
    header("Location: login.php");
    exit();
}

$error_message = '';

$cart_ids = $_POST['cart_ids'] ?? [];
if (!is_array($cart_ids) || count($cart_ids) === 0) {
    die("No products selected for checkout.");
}

$cart_ids = array_map('intval', $cart_ids);

$placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
$sql = "
    SELECT p.product_id, p.name, p.price, c.quantity, co.company_id, co.name AS company_name, co.gcash_qr_url
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    JOIN companies co ON p.company_id = co.company_id
    WHERE c.username = ? AND c.cart_id IN ($placeholders)
";

$stmt = $conn->prepare($sql);
if (!$stmt) die("Prepare failed: " . $conn->error);

$types = 's' . str_repeat('i', count($cart_ids));
$params = array_merge([$username], $cart_ids);
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();

$products = [];
$company_id = null;
$company_name = null;
$gcash_qr_url = null;

while ($row = $result->fetch_assoc()) {
    if ($company_id === null) {
        $company_id = $row['company_id'];
        $company_name = $row['company_name'];
        $gcash_qr_url = $row['gcash_qr_url'];
    } elseif ($company_id != $row['company_id']) {
        die("Selected products must belong to the same company.");
    }
    $products[] = $row;
}
$stmt->close();

if (empty($products)) {
    die("No valid products found for checkout.");
}

$company = htmlspecialchars($company_name);
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$address = htmlspecialchars(trim($_POST['address'] ?? ''));
$payment_method = $_POST['payment-method'] ?? '';
$gcash_number = trim($_POST['gcash-number'] ?? '');

$total_qty = 0;
$total_price = 0;
foreach ($products as $p) {
    $total_qty += $p['quantity'];
    $total_price += $p['price'] * $p['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_payment'])) {
    if (empty($company) || empty($email) || empty($address) || empty($payment_method)) {
        $error_message = "Please fill in all required fields.";
    } elseif ($payment_method === 'gcash' && empty($gcash_number)) {
        $error_message = "Please enter your GCash mobile number.";
    } else {
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("INSERT INTO orders (buyer_company_id, email, shipping_address, payment_method, gcash_number, order_total) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

            $stmt->bind_param("issssd", $company_id, $email, $address, $payment_method, $gcash_number, $total_price);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $order_id = $stmt->insert_id;
            $stmt->close();

            // Store order_id in session for invoice page
            $_SESSION['last_order_id'] = $order_id;

            $stmtDetails = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            if (!$stmtDetails) throw new Exception("Prepare failed: " . $conn->error);

            foreach ($products as $p) {
                $stmtDetails->bind_param("iiid", $order_id, $p['product_id'], $p['quantity'], $p['price']);
                if (!$stmtDetails->execute()) {
                    throw new Exception("Execute failed: " . $stmtDetails->error);
                }
            }
            $stmtDetails->close();

            $placeholders_del = implode(',', array_fill(0, count($cart_ids), '?'));
            $stmtDel = $conn->prepare("DELETE FROM cart WHERE username = ? AND cart_id IN ($placeholders_del)");
            if (!$stmtDel) throw new Exception("Prepare failed: " . $conn->error);

            $stmtDel->bind_param($types, ...$params);
            if (!$stmtDel->execute()) {
                throw new Exception("Execute failed: " . $stmtDel->error);
            }
            $stmtDel->close();

            $conn->commit();
            $conn->close();

            header("Location: payment_successful.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Transaction failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout | B2B E-commerce</title>
  <link rel="stylesheet" href="checkout.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
</head>
<body>
  <div class="checkout-wrapper">
    <h1 class="page-title">Secure Checkout</h1>

    <div class="checkout-grid">
      <section class="form-section">
        <h2>Billing Details</h2>

        <?php if (!empty($error_message)): ?>
          <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <form class="checkout-form" method="POST" action="checkout.php">
          <?php foreach ($cart_ids as $id): ?>
            <input type="hidden" name="cart_ids[]" value="<?= $id ?>" />
          <?php endforeach; ?>

          <label for="company">Company Name</label>
          <input type="text" id="company" name="company" value="<?= $company ?>" readonly />

          <label for="email">Business Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="you@example.com" required />

          <label for="address">Shipping Address</label>
          <textarea id="address" name="address" rows="3" placeholder="Street, City, ZIP" required><?= htmlspecialchars($address) ?></textarea>

          <h3>Payment Method</h3>
          <label for="payment-method">Choose Payment Method</label>
          <select id="payment-method" name="payment-method" required>
            <option value="gcash" <?= $payment_method == 'gcash' ? 'selected' : '' ?>>GCash</option>
          </select>

          <div class="payment-detail" id="gcash-fields">
            <label for="gcash-number">GCash Mobile Number</label>
            <input type="text" id="gcash-number" name="gcash-number" value="<?= htmlspecialchars($gcash_number) ?>" placeholder="09XXXXXXXXX" required />
          </div>

          <button type="submit" name="confirm_payment">Confirm &amp; Pay</button>
        </form>
      </section>

      <section class="summary-section">
        <h2>Order Summary</h2>

        <?php foreach ($products as $p): ?>
          <div class="summary-item">
            <span><?= htmlspecialchars($p['name']) ?></span>
            <span>Qty: <?= $p['quantity'] ?> &times; ₱<?= number_format($p['price'], 2) ?></span>
          </div>
        <?php endforeach; ?>

        <div class="summary-item">
          <span><strong>Total Quantity</strong></span>
          <span><?= $total_qty ?></span>
        </div>
        <div class="summary-total">
          <span><strong>Total Price</strong></span>
          <span>₱<?= number_format($total_price, 2) ?></span>
        </div>

        <p class="note">Shipping &amp; VAT will be calculated at confirmation.</p>

        <?php if ($gcash_qr_url): ?>
          <h3>GCash QR Code for <?= htmlspecialchars($company_name) ?></h3>
          <img src="<?= htmlspecialchars($gcash_qr_url) ?>" alt="GCash QR Code" style="max-width: 200px" />
        <?php endif; ?>
      </section>
    </div>
  </div>
</body>
</html>
