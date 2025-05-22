<?php
/*-----------------------------------------------------------
  bl.php  –  Business-Listings page for the admin dashboard
-----------------------------------------------------------*/
include 'db_connect.php';

/*
|------------------------------------------------------------------
|  Get all businesses + their owners
|  (1) users.role  = 'User'     → skip admin accounts
|  (2) users.status             → Approved / Pending / Rejected
|------------------------------------------------------------------
*/
$sql = "
SELECT
    c.company_id                AS id,
    c.name                      AS business_name,
    u.company_name              AS owner_name,
    u.email,
    DATE(u.created_at)          AS registration_date,
    u.status
FROM   companies c
JOIN   users u        ON u.company_id = c.company_id
WHERE  u.role = 'User'
ORDER  BY u.created_at DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="bl.css" />
  <title>Admin Dashboard – Business Listings</title>
</head>
<body>
  <!-- ==========  SIDE BAR  ========== -->
  <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="adashboard.php">Dashboard</a></li>
      <li><a href="userapp.php">User Approvals</a></li>
      <li><a href="bl.php" class="active">Business Listings</a></li>
      <li><a href="product_approval.php">Products Approval</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <!-- ==========  MAIN CONTENT  ========== -->
  <div class="main-content">
    <header>
      <h1>Business Listings</h1>
    </header>

    <section class="business-listing">
      <label for="status-filter">Filter by Status:</label>
      <select id="status-filter" onchange="filterTable()">
        <option value="all">All</option>
        <option value="Approved">Approved</option>
        <option value="Pending">Pending</option>
        <option value="Rejected">Rejected</option>
      </select>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Business Name</th>
            <th>Owner</th>
            <th>Email</th>
            <th>Registration&nbsp;Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="business-table">
          <?php
          if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['id']) . "</td>";
              echo "<td>" . htmlspecialchars($row['business_name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['owner_name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['email']) . "</td>";
              echo "<td>" . htmlspecialchars($row['registration_date']) . "</td>";
              echo "<td class='status'>" . htmlspecialchars($row['status']) . "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='6'>No businesses found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>
  </div>

  <!-- ==========  FILTER SCRIPT  ========== -->
  <script>
    function filterTable () {
      const filterValue = document.getElementById('status-filter').value;
      const rows        = document.querySelectorAll('#business-table tr');

      rows.forEach(row => {
        const status = row.querySelector('.status').textContent.trim();
        row.style.display = (filterValue === 'all' || status === filterValue)
                            ? ''
                            : 'none';
      });
    }
  </script>
</body>
</html>
<?php
$conn->close();
?>
