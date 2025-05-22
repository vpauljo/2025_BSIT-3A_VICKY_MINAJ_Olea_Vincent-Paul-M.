<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// First, fetch the company_id associated with this user
// Assuming you have a users or buyer_seller table to get company_id from username
// If company_id is stored in session, you can also use $_SESSION['company_id']
$company_id = null;

// Example: get company_id from users table (adjust table/column names as needed)
$stmt = $conn->prepare("SELECT company_id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($company_id);
$stmt->fetch();
$stmt->close();

if (!$company_id) {
    die("No company associated with your account.");
}

// Get product ID from GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = intval($_GET['id']);

// Fetch product to edit, verify it belongs to this user's company and is approved
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ? AND company_id = ? AND status = 'Approved'");
$stmt->bind_param("ii", $product_id, $company_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Product not found or you don't have permission to edit it.");
}

$product = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    // Optional: you can also allow editing image_url if you have that in form

    $update_stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock_quantity = ? WHERE product_id = ? AND company_id = ?");
    $update_stmt->bind_param("sdiii", $name, $price, $stock_quantity, $product_id, $company_id);

    if ($update_stmt->execute()) {
        $update_stmt->close();
        header("Location: my_products.php");
        exit;
    } else {
        $error = "Failed to update product: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Product</title>
    <link rel="stylesheet" href="add_products.css" />
</head>
<body>
    <section class="edit-product-section">
        <div class="form-container">
            <h1 class="section-title">Edit Product</h1>

            <?php if (!empty($error)): ?>
                <p style="color:red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form action="edit_product.php?id=<?= $product_id ?>" method="POST" class="edit-product-form">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="<?= htmlspecialchars($product['name']) ?>"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="price">Price (₱)</label>
                    <input
                        type="number"
                        name="price"
                        id="price"
                        value="<?= htmlspecialchars($product['price']) ?>"
                        step="0.01"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="stock_quantity">Stock Quantity</label>
                    <input
                        type="number"
                        name="stock_quantity"
                        id="stock_quantity"
                        value="<?= htmlspecialchars($product['stock_quantity']) ?>"
                        required
                    />
                </div>
                <button type="submit" class="submit-btn">Update Product</button>
            </form>

            <a href="my_products.php" class="back-link">← Back to My Products</a>
        </div>
    </section>
</body>
</html>
