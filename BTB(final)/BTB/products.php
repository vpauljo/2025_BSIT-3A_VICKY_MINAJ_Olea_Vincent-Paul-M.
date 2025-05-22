<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT p.*, c.name AS company_name
        FROM products p 
        JOIN companies c ON p.company_id = c.company_id 
        WHERE p.status = 'Approved'";

$products_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Marketplace Products</title>
  <link rel="stylesheet" href="products.css">
</head>
<body>
  <div class="dashboard-container">
    <header>
      <div class="header-left">
        <button onclick="location.href='useller.php'">← Back</button>
        <h1>Marketplace Products</h1>
      </div>
      <div class="header-right">
        <a href="cart.php"><button>Cart</button></a>
      </div>
    </header>

    <section class="product-grid">
      <?php if ($products_result && $products_result->num_rows > 0): ?>
        <?php while ($product = $products_result->fetch_assoc()): ?>
          <div class="product-card">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p>Seller: <?= htmlspecialchars($product['company_name']) ?></p>
            <p>₱<?= number_format($product['price'], 2) ?></p>
            <form action="add_to_cart.php" method="POST">
              <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit">Add to Cart</button>
            </form>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No approved products available.</p>
      <?php endif; ?>
    </section>
  </div>
</body>
</html>
