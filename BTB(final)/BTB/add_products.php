<?php
session_start();
include('db_connect.php');

// Check if user is logged in and company_id is set
if (!isset($_SESSION['username']) || !isset($_SESSION['company_id'])) {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = $conn->real_escape_string(trim($_POST['name']));
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);

    // Basic validation
    if (empty($name) || $price <= 0 || $stock_quantity < 0) {
        die("Invalid product details provided.");
    }

    // Insert product with the associated company_id and status 'Pending'
    $sql = "INSERT INTO products (name, price, stock_quantity, status, company_id) VALUES (?, ?, ?, 'Pending', ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sddi", $name, $price, $stock_quantity, $company_id);

    if ($stmt->execute()) {
        header("Location: products.php");
        exit();
    } else {
        die("Execution failed: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Product</title>
  <link rel="stylesheet" href="add_products.css" />
</head>
<body>
  <section class="add-product-section">
    <div class="form-container">
      <h1 class="section-title">Add Your Product</h1>
      <form action="add_products.php" method="POST" class="add-product-form">
        <div class="form-group">
          <label for="name">Product Name</label>
          <input type="text" name="name" id="name" required />
        </div>
        <div class="form-group">
          <label for="price">Price (₱)</label>
          <input type="number" name="price" id="price" step="0.01" min="0" required />
        </div>
        <div class="form-group">
          <label for="stock_quantity">Stock Quantity</label>
          <input type="number" name="stock_quantity" id="stock_quantity" min="0" required />
        </div>
        <button type="submit" class="submit-btn">Add Product</button>
      </form>
      <a href="products.php" class="back-link">← Back to Products</a>
    </div>
  </section>
</body>
</html>
