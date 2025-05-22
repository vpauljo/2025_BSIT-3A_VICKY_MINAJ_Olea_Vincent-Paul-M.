<?php
include 'db_connect.php';

// Fetch counts
$totalBusinessesQuery = "SELECT COUNT(*) AS total FROM businesses";
$pendingApprovalsQuery = "SELECT COUNT(*) AS pending FROM businesses WHERE status = 'Pending'";
$approvedBusinessesQuery = "SELECT COUNT(*) AS approved FROM businesses WHERE status = 'Approved'";
$rejectedBusinessesQuery = "SELECT COUNT(*) AS rejected FROM businesses WHERE status = 'Rejected'";

$totalBusinesses = $conn->query($totalBusinessesQuery)->fetch_assoc()['total'];
$pendingApprovals = $conn->query($pendingApprovalsQuery)->fetch_assoc()['pending'];
$approvedBusinesses = $conn->query($approvedBusinessesQuery)->fetch_assoc()['approved'];
$rejectedBusinesses = $conn->query($rejectedBusinessesQuery)->fetch_assoc()['rejected'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="adashboard.css" />
  <title>Admin Dashboard - Overview</title>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="dashboard.php" class="active">Dashboard</a></li>
      <li><a href="userapp.php">User Approvals</a></li>
      <li><a href="bl.php">Business Listings</a></li>
      <li><a href="product_approval.php">Products Approval</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <header>
      <h1>Dashboard Overview</h1>
    </header>

    <!-- Dashboard Cards Section -->
    <section class="dashboard-overview">
      <div class="card">
        <h3>Registered Businesses</h3>
        <p><?php echo $totalBusinesses; ?></p>
      </div>
      <div class="card">
        <h3>Pending Approvals</h3>
        <p><?php echo $pendingApprovals; ?></p>
      </div>
      <div class="card">
        <h3>Approved Businesses</h3>
        <p><?php echo $approvedBusinesses; ?></p>
      </div>
      <div class="card">
        <h3>Rejected Businesses</h3>
        <p><?php echo $rejectedBusinesses; ?></p>
      </div>
    </section>
  </div>
</body>
</html>
