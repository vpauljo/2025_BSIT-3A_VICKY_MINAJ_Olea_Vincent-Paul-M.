<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username']) ) {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE company_id = ? AND status = 'Approved'");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$my_products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Approved Products</title>
  <link rel="stylesheet" href="products.css" />
</head>
<body>
  <div class="dashboard-container">
    <header>
      <div class="header-left">
        <button onclick="location.href='useller.php'">← Back</button>
        <h1>My Products</h1>
      </div>
      <div class="header-right">
        <a href="add_products.php"><button>Add Product</button></a>
      </div>
    </header>

    <section class="product-grid">
      <?php if (!empty($my_products)): ?>
        <?php foreach ($my_products as $product): ?>
          <div class="product-card">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image" />
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p>₱<?= number_format($product['price'], 2) ?></p>
            <a class="edit-button" href="edit_product.php?id=<?= $product['product_id'] ?>">Edit</a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No approved products found for your account.</p>
      <?php endif; ?>
    </section>
  </div>
</body>
</html>
