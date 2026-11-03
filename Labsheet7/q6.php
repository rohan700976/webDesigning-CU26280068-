<?php
require_once __DIR__ . '/db.php';

$allBooks = getMockBooks();
$pdo = getDBConnection();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM books");
        $dbBooks = $stmt->fetchAll();
        foreach ($dbBooks as $dbB) {
            $allBooks[$dbB['id']] = $dbB;
        }
    } catch (PDOException $e) {}
}

$alertMessage = "";

// 1. Handle Add Item to Cart
if (isset($_GET['action']) && $_GET['action'] === 'add') {
    $bookId = intval($_GET['id'] ?? 0);
    $qty = max(1, intval($_GET['qty'] ?? 1));

    if (isset($allBooks[$bookId])) {
        if (!isset($_SESSION['cart'][$bookId])) {
            $_SESSION['cart'][$bookId] = $qty;
        } else {
            $_SESSION['cart'][$bookId] += $qty;
        }
        $alertMessage = "✓ Added " . htmlspecialchars($allBooks[$bookId]['title']) . " to your shopping cart!";
    }
}

// 2. Handle Update Quantity
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] ?? [] as $bId => $q) {
        $bId = intval($bId);
        $q = intval($q);
        if ($q <= 0) {
            unset($_SESSION['cart'][$bId]);
        } else {
            $_SESSION['cart'][$bId] = $q;
        }
    }
    $alertMessage = "✓ Cart quantities updated successfully!";
}

// 3. Handle Remove Item
if (isset($_GET['action']) && $_GET['action'] === 'remove') {
    $removeId = intval($_GET['id'] ?? 0);
    if (isset($_SESSION['cart'][$removeId])) {
        unset($_SESSION['cart'][$removeId]);
        $alertMessage = "Item removed from cart.";
    }
}

// 4. Handle Clear Entire Cart
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    $_SESSION['cart'] = [];
    $alertMessage = "Your shopping cart is now empty.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q6 - Session-Based Shopping Cart System</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }

        .cart-table th {
            background: #f8fafc;
            text-align: left;
            padding: 16px 20px;
            font-size: 13.5px;
            color: var(--gray-dark);
            border-bottom: 1px solid var(--border);
        }

        .cart-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 14.5px;
        }

        .cart-item-preview {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .cart-item-thumb {
            width: 55px;
            height: 75px;
            object-fit: cover;
            border-radius: 6px;
        }

        .cart-summary-box {
            max-width: 420px;
            margin-left: auto;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .summary-total {
            border-top: 2px solid #e2e8f0;
            padding-top: 14px;
            font-size: 20px;
            font-weight: 800;
            color: var(--dark);
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 6:</strong> Shopping cart system using PHP sessions (<code>$_SESSION['cart']</code>) to add, update &amp; remove items</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q3.php">Books</a></li>
                <li><a href="q6.php" class="active cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Your Shopping Cart</h1>
            <p style="color: var(--gray-muted);">Items preserved in <code>$_SESSION['cart']</code> array across browser requests.</p>
        </header>

        <?php if (!empty($alertMessage)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($alertMessage); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($_SESSION['cart'])): ?>
            <div style="background: white; padding: 60px 20px; text-align: center; border-radius: 12px; border: 1px dashed var(--border);">
                <div style="font-size: 48px; margin-bottom: 12px;">🛒</div>
                <h3 style="margin-bottom: 8px;">Your Shopping Cart is Empty</h3>
                <p style="color: var(--gray-muted); margin-bottom: 20px;">Explore our catalog to add top engineering and business books.</p>
                <a href="q3.php" class="btn btn-primary">Browse Available Books</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="update_cart" value="1">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Book Details</th>
                            <th>Unit Price</th>
                            <th style="width: 140px;">Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotalSum = 0;
                        foreach ($_SESSION['cart'] as $bId => $qty): 
                            if (!isset($allBooks[$bId])) continue;
                            $b = $allBooks[$bId];
                            $lineTotal = $b['price'] * $qty;
                            $subtotalSum += $lineTotal;
                        ?>
                            <tr>
                                <td>
                                    <div class="cart-item-preview">
                                        <img src="<?php echo htmlspecialchars($b['image']); ?>" alt="Cover" class="cart-item-thumb">
                                        <div>
                                            <strong><?php echo htmlspecialchars($b['title']); ?></strong>
                                            <p style="font-size: 13px; color: var(--gray-muted);">By <?php echo htmlspecialchars($b['author']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($b['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantities[<?php echo $bId; ?>]" value="<?php echo $qty; ?>" min="1" max="<?php echo $b['stock']; ?>" class="form-control" style="width: 80px; text-align: center;">
                                </td>
                                <td><strong>$<?php echo number_format($lineTotal, 2); ?></strong></td>
                                <td>
                                    <a href="q6.php?action=remove&id=<?php echo $bId; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <a href="q3.php" class="btn btn-secondary">&larr; Continue Shopping</a>
                        <a href="q6.php?action=clear" class="btn btn-secondary" onclick="return confirm('Clear entire cart?');" style="color: var(--danger); margin-left: 8px;">Clear Cart</a>
                    </div>
                    <button type="submit" class="btn btn-primary">🔄 Update Cart Quantities</button>
                </div>
            </form>

            <!-- Order Total Summary -->
            <?php 
                $tax = $subtotalSum * 0.05; // 5% sales tax
                $grandTotal = $subtotalSum + $tax;
            ?>
            <div class="cart-summary-box">
                <h3 style="margin-bottom: 16px; font-size: 18px;">Order Summary</h3>
                <div class="summary-row">
                    <span>Items Subtotal:</span>
                    <span>$<?php echo number_format($subtotalSum, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Estimated Tax (5%):</span>
                    <span>$<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="summary-row summary-total">
                    <span>Grand Total:</span>
                    <span style="color: var(--primary);">$<?php echo number_format($grandTotal, 2); ?></span>
                </div>
                <a href="q7.php" class="btn btn-primary" style="width: 100%; margin-top: 16px; padding: 14px; font-size: 15px;">
                    Proceed to Checkout &rarr;
                </a>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 6: Session Cart).
    </footer>

</body>
</html>
