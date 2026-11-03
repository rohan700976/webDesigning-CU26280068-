<?php
require_once __DIR__ . '/db.php';

$pdo = getDBConnection();
$alert = "";

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id'] ?? 0);
    $newStatus = $_POST['new_status'] ?? 'Pending';

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = :s WHERE id = :id");
            $stmt->execute([':s' => $newStatus, ':id' => $orderId]);
            $alert = "✓ Order #{$orderId} status successfully updated to '{$newStatus}'!";
        } catch (PDOException $e) {
            $alert = "Update Error: " . $e->getMessage();
        }
    } else {
        // Update simulated session orders
        foreach ($_SESSION['orders'] as &$ord) {
            if ($ord['id'] == $orderId) {
                $ord['status'] = $newStatus;
                $alert = "✓ Order #{$orderId} status successfully updated to '{$newStatus}'!";
                break;
            }
        }
    }
}

// Fetch all orders
$ordersList = $_SESSION['orders'] ?? [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
        $dbOrders = $stmt->fetchAll();
        if (!empty($dbOrders)) {
            $ordersList = $dbOrders;
        }
    } catch (PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q9 - Admin Customer Orders &amp; Fulfillment</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        .orders-table th, .orders-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
            font-size: 14px;
        }
        .orders-table th { background: #f8fafc; font-weight: 700; color: #475569; }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 9:</strong> PHP script enabling administrators to view all orders and update fulfillment status (Pending, Shipped, Delivered)</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span> <span class="badge badge-warning" style="margin-left: 8px;">ADMIN</span></a>
            <ul class="nav-links">
                <li><a href="q8.php">Inventory Manager</a></li>
                <li><a href="q9.php" class="active">Orders Fulfillment</a></li>
                <li><a href="q21.php">Sales Reports</a></li>
                <li><a href="q1.php">Storefront &rarr;</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Customer Order Fulfillment Hub</h1>
            <p style="color: var(--gray-muted);">Inspect customer transaction data and update dispatch statuses in real time.</p>
        </header>

        <?php if (!empty($alert)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($alert); ?></div>
        <?php endif; ?>

        <div style="overflow-x: auto;">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Details</th>
                        <th>Shipping Destination</th>
                        <th>Total Amount</th>
                        <th>Current Status</th>
                        <th>Fulfillment Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ordersList)): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--gray-muted);">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($ordersList as $ord): 
                            $badgeClass = 'badge-warning';
                            if ($ord['status'] === 'Delivered') $badgeClass = 'badge-success';
                            if ($ord['status'] === 'Shipped') $badgeClass = 'badge-info';
                            if ($ord['status'] === 'Cancelled') $badgeClass = 'badge-danger';
                        ?>
                            <tr>
                                <td><strong>#<?php echo $ord['id']; ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($ord['customer_name'] ?? $ord['user'] ?? 'Customer'); ?></strong>
                                    <div style="font-size: 12px; color: var(--gray-muted);"><?php echo htmlspecialchars($ord['customer_email'] ?? 'customer@example.com'); ?></div>
                                </td>
                                <td style="max-width: 250px; font-size: 13px; color: #475569;">
                                    <?php echo htmlspecialchars($ord['shipping_address'] ?? $ord['address'] ?? 'COER Campus, Roorkee'); ?>
                                </td>
                                <td><strong>$<?php echo number_format($ord['total_amount'] ?? $ord['total'] ?? 0, 2); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($ord['status']); ?></span>
                                </td>
                                <td>
                                    <form method="POST" style="display: flex; gap: 6px;">
                                        <input type="hidden" name="update_status" value="1">
                                        <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                        <select name="new_status" class="form-control" style="padding: 4px 8px; font-size: 12.5px; width: auto;">
                                            <option value="Pending" <?php if ($ord['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                                            <option value="Shipped" <?php if ($ord['status'] === 'Shipped') echo 'selected'; ?>>Shipped</option>
                                            <option value="Delivered" <?php if ($ord['status'] === 'Delivered') echo 'selected'; ?>>Delivered</option>
                                            <option value="Cancelled" <?php if ($ord['status'] === 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary" style="padding: 4px 10px; font-size: 12px;">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 9: Orders Fulfillment).
    </footer>

</body>
</html>
