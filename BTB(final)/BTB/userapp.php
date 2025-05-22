<?php 
include 'db_connect.php';

// Fetch all users who need approval (status = 'Pending')
$sql = "
SELECT u.id,
       COALESCE(c.name, u.company_name) AS business_name,
       u.company_name AS owner_name,
       u.email,
       u.status
FROM users u
LEFT JOIN companies c ON u.company_id = c.company_id
WHERE u.status = 'Pending'
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="userapp.css" />
  <title>Admin Dashboard</title> 
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
  
  <div class="main-content">
    <header>
      <h1>User Approval</h1>
    </header>
    
    <section class="dashboard-content">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Business Name</th>
            <th>Email</th>
            <th>Contact No.</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['business_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['owner_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>";
                    echo "<a href='approve_user.php?id=" . $row['id'] . "' class='approve'>Approve</a> ";
                    echo "<a href='reject_user.php?id=" . $row['id'] . "' class='reject'>Reject</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No users found for approval</td></tr>";
            }
          ?>
        </tbody>
      </table>
    </section>
  </div>
</body>
</html>

<?php $conn->close(); ?>
