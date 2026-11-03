<?php
require_once __DIR__ . '/db.php';

$allBooks = getMockBooks();
$pdo = getDBConnection();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM books");
        foreach ($stmt->fetchAll() as $dbB) {
            $allBooks[$dbB['id']] = $dbB;
        }
    } catch (PDOException $e) {}
}

$alertMsg = "";
$alertType = "";

// Handle Stock Checked Addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stock_check_add'])) {
    $bookId = intval($_POST['book_id'] ?? 0);
    $requestedQty = intval($_POST['quantity'] ?? 1);

    if (isset($allBooks[$bookId])) {
        $book = $allBooks[$bookId];
        $availableStock = intval($book['stock']);
        $alreadyInCart = intval($_SESSION['cart'][$bookId] ?? 0);
        $totalProjected = $alreadyInCart + $requestedQty;

        // INVENTORY STOCK CHECK: Prevent exceeding inventory
        if ($availableStock <= 0) {
            $alertMsg = "✗ Out of Stock Error: '{$book['title']}' is currently out of stock (0 copies left).";
            $alertType = "alert-danger";
        } elseif ($totalProjected > $availableStock) {
            $canAddMore = max(0, $availableStock - $alreadyInCart);
            $alertMsg = "⚠️ Inventory Limit Exceeded: You requested {$requestedQty} copy(ies). You already have {$alreadyInCart} in your cart. Only {$availableStock} total copies exist in inventory. You can add at most {$canAddMore} more copy(ies).";
            $alertType = "alert-danger";
        } else {
            // Valid within available stock limits
            $_SESSION['cart'][$bookId] = $totalProjected;
            $alertMsg = "✓ Inventory Check Passed! Successfully added {$requestedQty} copies of '{$book['title']}' to your cart. (Current in cart: {$totalProjected} / Stock: {$availableStock})";
            $alertType = "alert-success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q13 - Inventory Stock Verification Check</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .stock-test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }
        .stock-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 13:</strong> Inventory stock check in PHP preventing users from adding more books to cart than available stock</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q13.php" class="active">Stock Verification</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Inventory Stock Guardian</h1>
            <p style="color: var(--gray-muted);">Validates requested quantity against <code>books.stock</code> and current <code>$_SESSION['cart']</code> before confirming item addition.</p>
        </header>

        <?php if (!empty($alertMsg)): ?>
            <div class="alert <?php echo $alertType; ?>" style="font-size: 14.5px; line-height: 1.6;">
                <?php echo htmlspecialchars($alertMsg); ?>
            </div>
        <?php endif; ?>

        <!-- Test Cards for Stock Check -->
        <section class="stock-test-grid">
            <?php foreach ($allBooks as $b): 
                $inCart = $_SESSION['cart'][$b['id']] ?? 0;
            ?>
                <div class="stock-card">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <span class="badge badge-info"><?php echo htmlspecialchars($b['category'] ?? 'General'); ?></span>
                            <span class="badge <?php echo $b['stock'] > 5 ? 'badge-success' : 'badge-danger'; ?>">
                                Available Stock: <?php echo $b['stock']; ?>
                            </span>
                        </div>
                        <h3 style="font-size: 17px; margin-bottom: 6px;"><?php echo htmlspecialchars($b['title']); ?></h3>
                        <p style="font-size: 13px; color: var(--gray-muted); margin-bottom: 12px;">By <?php echo htmlspecialchars($b['author']); ?></p>
                        
                        <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 16px; font-size: 13.5px;">
                            Currently in Your Cart: <strong><?php echo $inCart; ?> copy(ies)</strong>
                        </div>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="stock_check_add" value="1">
                        <input type="hidden" name="book_id" value="<?php echo $b['id']; ?>">

                        <div style="display: flex; gap: 10px; align-items: center;">
                            <label style="margin: 0; font-size: 13px;">Qty:</label>
                            <input type="number" name="quantity" value="1" min="1" max="50" class="form-control" style="width: 80px; text-align: center;">
                            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 10px;">
                                Validate &amp; Add
                            </button>
                        </div>
                        <p style="font-size: 11.5px; color: var(--gray-muted); margin-top: 6px;">
                            Tip: Try entering quantity > <?php echo $b['stock']; ?> to trigger stock rejection error!
                        </p>
                    </form>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 13: Inventory Stock Check).
    </footer>

</body>
</html>
