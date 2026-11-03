<?php
require_once __DIR__ . '/db.php';

$currentUser = $_SESSION['username'] ?? 'Rohan Sharma';
$pdo = getDBConnection();
$userOrders = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders ORDER BY id DESC");
        $stmt->execute();
        $userOrders = $stmt->fetchAll();
    } catch (PDOException $e) {
        $userOrders = [];
    }
}

if (empty($userOrders)) {
    $userOrders = $_SESSION['orders'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q12 - Customer Order History</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .order-history-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            transition: transform 0.2s ease;
        }
        .order-history-card:hover {
            transform: translateY(-2px);
        }
        .order-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 12:</strong> Customer order history page where logged-in users view past purchases &amp; status</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q3.php">Books</a></li>
                <li><a href="q12.php" class="active">Order History</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 28px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Past Purchase History</h1>
            <p style="color: var(--gray-muted);">Logged in as: <strong><?php echo htmlspecialchars($currentUser); ?></strong>. Review real-time delivery status and purchase summaries.</p>
        </header>

        <?php if (empty($userOrders)): ?>
            <div style="background: white; padding: 60px 20px; text-align: center; border-radius: 12px; border: 1px dashed var(--border);">
                <h3>No past orders recorded yet.</h3>
                <p style="color: var(--gray-muted); margin-top: 6px;">Place your first order through the bookstore storefront.</p>
                <a href="q1.php" class="btn btn-primary" style="margin-top: 16px;">Shop Now</a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach ($userOrders as $ord): 
                    $status = $ord['status'] ?? 'Pending';
                    $badgeClass = 'badge-warning';
                    if ($status === 'Delivered') $badgeClass = 'badge-success';
                    if ($status === 'Shipped') $badgeClass = 'badge-info';
                    if ($status === 'Cancelled') $badgeClass = 'badge-danger';
                ?>
                    <article class="order-history-card">
                        <div class="order-header-row">
                            <div>
                                <span style="font-size: 17px; font-weight: 800; color: var(--dark);">Order #<?php echo $ord['id']; ?></span>
                                <span style="color: var(--gray-muted); font-size: 13px; margin-left: 10px;">
                                    Placed on: <?php echo htmlspecialchars($ord['order_date'] ?? $ord['date'] ?? 'Recent'); ?>
                                </span>
                            </div>
                            <div>
                                <span class="badge <?php echo $badgeClass; ?>">● <?php echo htmlspecialchars($status); ?></span>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                            <div>
                                <p style="font-size: 14px; color: #475569; margin-bottom: 4px;">
                                    <strong>Shipping Address:</strong> <?php echo htmlspecialchars($ord['shipping_address'] ?? $ord['address'] ?? 'COER University Campus'); ?>
                                </p>
                                <p style="font-size: 13px; color: var(--gray-muted);">
                                    Recipient: <?php echo htmlspecialchars($ord['customer_name'] ?? $ord['user'] ?? $currentUser); ?>
                                </p>
                            </div>

                            <div style="text-align: right; display: flex; align-items: center; gap: 16px;">
                                <div>
                                    <span style="font-size: 12px; color: var(--gray-muted); display: block;">Total Paid</span>
                                    <strong style="font-size: 20px; color: var(--primary);">
                                        $<?php echo number_format($ord['total_amount'] ?? $ord['total'] ?? 0, 2); ?>
                                    </strong>
                                </div>
                                <a href="q16.php?order_id=<?php echo $ord['id']; ?>" class="btn btn-secondary" style="font-size: 13px; padding: 8px 14px;">
                                    📄 Invoice
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 12: Order History).
    </footer>

</body>
</html>
