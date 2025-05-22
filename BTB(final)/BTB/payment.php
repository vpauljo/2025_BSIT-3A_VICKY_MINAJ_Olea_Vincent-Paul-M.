<?php
// Optional: Handle form submission (e.g., store payment data or redirect)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve submitted data (you can store this in the database if needed)
    $company = $_POST['company-name'];
    $address = $_POST['billing-address'];
    $gcashMobile = $_POST['gcash-mobile'];
    $amount = $_POST['payment-amount'];

    // Redirect to a success page (you can save to DB before this)
    header("Location: payment-success.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GCash Payment - B2B E-commerce</title>
  <link rel="stylesheet" href="payment.css" />
</head>
<body>
  <div class="payment-container">
    <h1>GCash Payment Information</h1>
    <form action="payment.php" method="POST" class="payment-form">
      <div class="form-group">
        <label for="company-name">Company Name</label>
        <input type="text" id="company-name" name="company-name" required />
      </div>

      <div class="form-group">
        <label for="billing-address">Billing Address</label>
        <input type="text" id="billing-address" name="billing-address" required />
      </div>

      <div class="form-group">
        <label for="gcash-mobile">GCash Mobile Number</label>
        <input type="text" id="gcash-mobile" name="gcash-mobile" placeholder="09XXXXXXXXX" required />
      </div>

      <div class="form-group">
        <label for="payment-amount">Amount</label>
        <input type="number" id="payment-amount" name="payment-amount" required />
      </div>

      <button type="submit" class="submit-btn">Submit GCash Payment</button>
    </form>
  </div>
</body>
</html>
