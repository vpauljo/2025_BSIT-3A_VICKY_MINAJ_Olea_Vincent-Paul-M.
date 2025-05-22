<?php
include('db_connect.php');

$registrationSuccess = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company = $_POST['company'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $conn->begin_transaction();

        try {
            // Check if company already exists
            $cmpStmt = $conn->prepare("SELECT company_id FROM companies WHERE name = ?");
            $cmpStmt->bind_param("s", $company);
            $cmpStmt->execute();
            $cmpStmt->bind_result($companyId);
            $cmpStmt->fetch();
            $cmpStmt->close();

            // Insert company if not found
            if (!$companyId) {
                $cmpIns = $conn->prepare(
                    "INSERT INTO companies (name, email, phone) VALUES (?, ?, ?)"
                );
                $cmpIns->bind_param("sss", $company, $email, $phone);
                $cmpIns->execute();
                $companyId = $cmpIns->insert_id;
                $cmpIns->close();
            }

            // Insert user with 'Pending' status
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $usrIns = $conn->prepare(
                "INSERT INTO users (company_name, email, phone, password, status, role, company_id)
                 VALUES (?, ?, ?, ?, 'Pending', 'User', ?)"
            );
            $usrIns->bind_param("ssssi", $company, $email, $phone, $hashedPassword, $companyId);
            $usrIns->execute();
            $usrIns->close();

            $conn->commit();
            $registrationSuccess = true;
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Registration failed: " . $e->getMessage();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>B2B Platform Registration</title>
  <link rel="stylesheet" href="register.css"/>
</head>
<body>
  <div class="container">
    <form class="register-form" method="POST" action="register.php">
      <h2>Register Your Business</h2>

      <?php if ($registrationSuccess): ?>
        <p style="color: green;">✅ Registration successful! <a href="llogin.php">Log in</a></p>
      <?php elseif ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <label for="company">Company Name</label>
      <input type="text" id="company" name="company" placeholder="e.g. ABC Trading Co." required />

      <label for="email">Business Email</label>
      <input type="email" id="email" name="email" placeholder="e.g. contact@abc.com" required />

      <label for="phone">Phone Number</label>
      <input type="tel" id="phone" name="phone" placeholder="e.g. +1234567890" required />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter password" required />

      <label for="confirm-password">Confirm Password</label>
      <input type="password" id="confirm-password" name="confirm-password" placeholder="Re-enter password" required />

      <button type="submit">Create Account</button>
      <p class="login-link">Already have an account? <a href="llogin.php">Log in here</a>.</p>
    </form>
  </div>
</body>
</html>
