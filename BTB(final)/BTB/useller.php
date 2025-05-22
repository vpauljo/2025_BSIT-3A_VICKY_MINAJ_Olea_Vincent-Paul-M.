<?php
include 'db_connect.php'; // Include the database connection

// Fetch product count
$product_count_query = "SELECT COUNT(*) AS total FROM products";
$product_count_result = $conn->query($product_count_query);
$product_count = ($product_count_result->num_rows > 0) ? $product_count_result->fetch_assoc()['total'] : 0;

// Fetch order count (example for orders)
$order_count_query = "SELECT COUNT(*) AS total FROM orders WHERE status = 'Pending'";
$order_count_result = $conn->query($order_count_query);
$order_count = ($order_count_result->num_rows > 0) ? $order_count_result->fetch_assoc()['total'] : 0;

// Fetch invoice count (example for invoices)
$invoice_count_query = "SELECT COUNT(*) AS total FROM invoices WHERE paid = 0";
$invoice_count_result = $conn->query($invoice_count_query);
$invoice_count = ($invoice_count_result->num_rows > 0) ? $invoice_count_result->fetch_assoc()['total'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>B2B E-commerce Dashboard | User</title>
  <link rel="stylesheet" href="useller.css" />

  <!-- Font Awesome CDN for Cart Icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <!-- Sidebar Navigation -->
  <div class="sidebar">
    <h2>Welcome, User</h2>
    <p>Your Business Dashboard</p>
    <ul>
      <li><a href="useller.php" class="active">Home</a></li>
      <li><a href="orders.php">Orders</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="my_products.php">My Products</a></li>
      <li><a href="manage_orders.php">Customers</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <header style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background-color: #fff; border-bottom: 1px solid #ddd;">
      <h1 style="margin: 0;">VINEORIGIN B2B</h1>
      <a href="cart.php" title="Go to Cart" style="text-decoration: none; color: black; font-size: 24px;">
        <i class="fas fa-shopping-cart"></i>
      </a>
    </header>

    <section class="dashboard-overview">
      <div class="card">
        <h3>Orders</h3>
        <p><?php echo $order_count; ?> Active Orders</p>
        <a href="orders.php">View Orders</a>
      </div>
      <div class="card">
        <h3>Products</h3>
        <p><?php echo $product_count; ?> Products Listed</p>
        <a href="products.php">Manage Products</a>
      </div>
    </section>
  </div>
</body>
</html>
<?php $conn->close(); ?>
