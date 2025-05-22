<?php
session_start();
include('db_connect.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the user exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];  // For your username-based features
            
            // ** Set company_id in session for product/company association **
            $_SESSION['company_id'] = $user['company_id'];

            // Redirect based on role
            if ($user['role'] == 'Admin') {
                header('Location: adashboard.php');
                exit();
            } else {
                header('Location: useller.php');
                exit();
            }
        } else {
            $error_message = 'Incorrect password.';
        }
    } else {
        $error_message = 'User not found.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>B2B Platform Login</title>
  <link rel="stylesheet" href="llogin.css"/>
</head>
<body>
  <div class="container">
    <form method="POST" class="login-form">
      <h2>Business Login</h2>

      <?php if (isset($error_message)): ?>
        <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
      <?php endif; ?>

      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" placeholder="e.g. contact@abc.com" required />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required />

      <button type="submit">Log In</button>
      <p class="register-link">Don't have an account? <a href="register.php">Register here</a>.</p>
    </form>
  </div>
</body>
</html>
