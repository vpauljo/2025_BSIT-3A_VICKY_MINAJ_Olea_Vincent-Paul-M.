<?php
include 'db_connect.php';

// Retrieve filter and search inputs
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Base SQL query
$sql = "SELECT orders.order_id, companies.name AS business_name, orders.order_date, orders.status, orders.order_total
        FROM orders
        JOIN companies ON orders.buyer_company_id = companies.company_id";

// Append conditions based on filter and search inputs
$conditions = [];
$params = [];
$types = "";

if ($filter != 'all') {
    $conditions[] = "orders.status = ?";
    $params[] = $filter;
    $types .= "s";
}

if (!empty($search)) {
    $conditions[] = "companies.name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Orders - B2B E-commerce</title>
    <link rel="stylesheet" href="orders.css" />
    <script>
        // JavaScript to make the dropdown functional
        function applyFilter() {
            const filter = document.querySelector('select[name="filter"]').value;
            const search = document.querySelector('input[name="search"]').value;
            const urlParams = new URLSearchParams({ filter, search });
            window.location.href = `orders.php?${urlParams}`;
        }
    </script>
</head>
<body>
    <div class="orders-container">
        <h1>Manage Orders</h1>

        <!-- Filters Form -->
        <div class="filters">
            <form method="GET" action="orders.php" onsubmit="return false;">
                <select name="filter" onchange="applyFilter()">
                    <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All Orders</option>
                    <option value="pending" <?= $filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= $filter == 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="shipped" <?= $filter == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                </select>
                <input type="text" name="search" placeholder="Search Orders..." value="<?= htmlspecialchars($search) ?>" />
                <button type="submit" onclick="applyFilter()">Apply</button>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Business Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['order_id'] ?></td>
                            <td><?= htmlspecialchars($row['business_name']) ?></td>
                            <td><?= $row['order_date'] ?></td>
                            <td><span class="status <?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></span></td>
                            <td>₱<?= number_format($row['order_total'], 2) ?></td>
                            <td><a class="btn-view" href="order_details.php?order_id=<?= $row['order_id'] ?>">View</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No orders found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Back to Home Button -->
        <div class="back-to-home">
            <a href="useller.php" class="btn-back-home">Back to Home</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
