<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die('You must be logged in to add to cart.');
}

$username = $_SESSION['username'];

// Check if product_id is passed
if (!isset($_POST['product_id'])) {
    die('Product ID is required.');
}

$product_id = intval($_POST['product_id']);
$quantity = 1; // Default quantity, you can allow dynamic if needed

// Check if user exists (important due to foreign key constraint)
$user_check = $conn->prepare("SELECT username FROM users WHERE username = ?");
$user_check->bind_param("s", $username);
$user_check->execute();
$user_result = $user_check->get_result();

if ($user_result->num_rows === 0) {
    die("User does not exist in the database.");
}

// Check if product exists
$product_check = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
$product_check->bind_param("i", $product_id);
$product_check->execute();
$product_result = $product_check->get_result();

if ($product_result->num_rows === 0) {
    die("Product does not exist.");
}

// Check if the product is already in the user's cart
$check_cart = $conn->prepare("SELECT quantity FROM cart WHERE username = ? AND product_id = ?");
$check_cart->bind_param("si", $username, $product_id);
$check_cart->execute();
$cart_result = $check_cart->get_result();

if ($cart_result->num_rows > 0) {
    // Update quantity if already in cart
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE username = ? AND product_id = ?");
    $stmt->bind_param("si", $username, $product_id);
    $stmt->execute();
} else {
    // Insert new item
    $stmt = $conn->prepare("INSERT INTO cart (username, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $username, $product_id, $quantity);
    $stmt->execute();
}

$stmt->close();
$conn->close();

// Redirect to cart
header("Location: cart.php");
exit();
?>
