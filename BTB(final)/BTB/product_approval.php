<?php
include('db_connect.php');

// Fetch pending products
$sql = "SELECT * FROM products WHERE status = 'Pending'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Approval - Admin Dashboard</title>
  <link rel="stylesheet" href="product_approval.css"/>
</head>
<body>
  <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="adashboard.php">Dashboard</a></li>
      <li><a href="userapp.php">User Approvals</a></li>
      <li><a href="bl.php">Business Listings</a></li>
      <li><a href="product_approval.php">Products Approval</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>
  
  <div class="dashboard-container">
    <h2>Product Approval</h2>
    
    <table class="product-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Product Name</th>
          <th>Seller</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $productName = htmlspecialchars($row['name']);
                $seller = htmlspecialchars($row['seller']);
                $status = htmlspecialchars($row['status']);
                $image = !empty($row['image_url']) ? $row['image_url'] : 'https://via.placeholder.com/60';

                echo '<tr>';
                echo '<td><img src="' . $image . '" alt="' . $productName . '" width="60"></td>';
                echo '<td>' . $productName . '</td>';
                echo '<td>' . $seller . '</td>';
                echo '<td><span class="status pending">' . $status . '</span></td>';
                echo '<td>';
                echo '<form method="post" action="update_status.php" style="display:inline-block; margin-right: 5px;">';
                echo '<input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
                echo '<input type="hidden" name="action" value="approve">';
                echo '<button class="approve-btn" type="submit">Approve</button>';
                echo '</form>';
                echo '<form method="post" action="update_status.php" style="display:inline-block;">';
                echo '<input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
                echo '<input type="hidden" name="action" value="reject">';
                echo '<button class="reject-btn" type="submit">Reject</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">No products pending approval.</td></tr>';
        }

        $conn->close();
        ?>
      </tbody>
    </table>

  </div>
</body>
</html>
