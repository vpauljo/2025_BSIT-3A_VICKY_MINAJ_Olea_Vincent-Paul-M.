<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Determine which company to show
$selected_company_id = isset($_GET['company_id']) ? intval($_GET['company_id']) : null;

// Get all companies that have products in the cart for this user
$sqlCompanies = "SELECT DISTINCT co.company_id, co.name
                 FROM cart c
                 JOIN products p ON c.product_id = p.product_id
                 JOIN companies co ON p.company_id = co.company_id
                 WHERE c.username = ?";
$stmt = $conn->prepare($sqlCompanies);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultCompanies = $stmt->get_result();
$companies_in_cart = $resultCompanies->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (!$selected_company_id && count($companies_in_cart) > 0) {
    $selected_company_id = $companies_in_cart[0]['company_id'];
}

// Get cart items for the selected company only
$cart_items = [];
if ($selected_company_id) {
    $sql = "SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.image_url, p.seller, co.name AS company_name
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            JOIN companies co ON p.company_id = co.company_id
            WHERE c.username = ? AND co.company_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $selected_company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Cart</title>
  <link rel="stylesheet" href="cart.css" />
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('cart-form');
      form.addEventListener('submit', function (e) {
        const checked = document.querySelectorAll('input[name="cart_ids[]"]:checked');
        if (checked.length === 0) {
          alert('Please select at least one item to proceed to checkout.');
          e.preventDefault();
        }
      });
    });
  </script>
</head>
<body>
  <button onclick="location.href='products.php'">← Back</button>
  <h1>Your Shopping Cart</h1>

  <?php if (count($companies_in_cart) > 1): ?>
    <form method="GET" action="cart.php">
      <label for="company_id">Select Company:</label>
      <select name="company_id" id="company_id" onchange="this.form.submit()">
        <?php foreach ($companies_in_cart as $company): ?>
          <option value="<?= $company['company_id'] ?>" <?= ($company['company_id'] == $selected_company_id) ? 'selected' : '' ?>>
            <?= htmlspecialchars($company['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  <?php endif; ?>

  <?php if (count($cart_items) > 0): ?>
    <form id="cart-form" method="POST" action="checkout.php">
      <input type="hidden" name="company_id" value="<?= $selected_company_id ?>">
      <div class="cart-items">
        <?php foreach ($cart_items as $item): ?>
          <div class="cart-item">
            <input type="checkbox" name="cart_ids[]" value="<?= $item['cart_id'] ?>" checked>
            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            <div class="cart-item-details">
              <h3><?= htmlspecialchars($item['name']) ?></h3>
              <p>Seller: <?= htmlspecialchars($item['company_name']) ?></p>
              <p>Price: ₱<?= number_format($item['price'], 2) ?></p>
              <p>Quantity: <?= intval($item['quantity']) ?></p>
              <p>Subtotal: ₱<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <input type="submit" value="Proceed to Checkout" class="checkout-btn">
    </form>
  <?php else: ?>
    <p>Your cart is empty for this company.</p>
    <p><a href="products.php">Continue Shopping</a></p>
  <?php endif; ?>
</body>
</html>
