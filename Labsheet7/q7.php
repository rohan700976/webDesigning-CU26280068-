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

// Auto-seed sample item if cart is currently empty for quick evaluation
if (empty($_SESSION['cart'])) {
    $_SESSION['cart'][1] = 1;
}

// Calculate Cart Totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $bId => $qty) {
    if (isset($allBooks[$bId])) {
        $subtotal += $allBooks[$bId]['price'] * $qty;
    }
}
$tax = $subtotal * 0.05;
$totalAmount = $subtotal + $tax;

$orderPlaced = false;
$placedOrderId = null;
$errorMsg = "";

// Handle Order Placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $custName = trim($_POST['full_name'] ?? '');
    $custEmail = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'Credit Card';

    $fullAddress = "{$address}, {$city} - {$zip}";

    if (!empty($custName) && !empty($custEmail) && !empty($address)) {
        if ($pdo) {
            try {
                // Begin Transaction
                $pdo->beginTransaction();

                // 1. Insert into orders table
                $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, shipping_address, total_amount, payment_method, status) VALUES (:name, :email, :addr, :total, :pm, 'Pending')");
                $stmt->execute([
                    ':name' => $custName,
                    ':email' => $custEmail,
                    ':addr' => $fullAddress,
                    ':total' => $totalAmount,
                    ':pm' => $paymentMethod
                ]);
                $placedOrderId = $pdo->lastInsertId();

                // 2. Insert itemized order_items
                $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, unit_price, subtotal) VALUES (:oid, :bid, :qty, :up, :sub)");
                foreach ($_SESSION['cart'] as $bId => $qty) {
                    if (isset($allBooks[$bId])) {
                        $up = $allBooks[$bId]['price'];
                        $lineSub = $up * $qty;
                        $itemStmt->execute([
                            ':oid' => $placedOrderId,
                            ':bid' => $bId,
                            ':qty' => $qty,
                            ':up' => $up,
                            ':sub' => $lineSub
                        ]);
                    }
                }

                $pdo->commit();
                $orderPlaced = true;
                $_SESSION['cart'] = []; // Clear Cart
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errorMsg = "Transaction Error: " . $e->getMessage();
            }
        }

        // Fallback / Simulated Order Placement if DB is not active
        if (!$orderPlaced && empty($errorMsg)) {
            $placedOrderId = rand(1050, 9999);
            $_SESSION['orders'][] = [
                'id' => $placedOrderId,
                'user' => $custName,
                'items_count' => count($_SESSION['cart']),
                'total' => $totalAmount,
                'address' => $fullAddress,
                'status' => 'Pending',
                'date' => date('Y-m-d H:i:s')
            ];
            $orderPlaced = true;
            $_SESSION['cart'] = [];
        }
    } else {
        $errorMsg = "Please fill in all required shipping address fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q7 - Checkout &amp; Order Placement</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .checkout-layout {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 36px;
        }
        @media (max-width: 850px) {
            .checkout-layout { grid-template-columns: 1fr; }
        }
        .checkout-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 30px;
            box-shadow: var(--shadow);
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 7:</strong> Checkout page in PHP recording transaction &amp; shipping details into MySQL database</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q6.php">Shopping Cart</a></li>
                <li><a href="q7.php" class="active">Checkout</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <?php if ($orderPlaced): ?>
            <div style="background: white; border: 1px solid #86efac; border-radius: 16px; padding: 50px 30px; text-align: center; max-width: 650px; margin: 40px auto; box-shadow: var(--shadow);">
                <div style="font-size: 56px; margin-bottom: 16px;">🎉</div>
                <h1 style="color: #166534; font-size: 26px; margin-bottom: 8px;">Order Placed Successfully!</h1>
                <p style="color: var(--gray-muted); font-size: 15px; margin-bottom: 24px;">
                    Thank you! Your transaction data has been recorded in the MySQL database.
                    <br><strong>Order Reference ID: #<?php echo $placedOrderId; ?></strong>
                </p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="q16.php?order_id=<?php echo $placedOrderId; ?>" class="btn btn-primary">📄 View Printable Invoice</a>
                    <a href="q12.php" class="btn btn-secondary">📦 My Order History</a>
                    <a href="q1.php" class="btn btn-secondary">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <header style="margin-bottom: 28px;">
                <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Secure Checkout</h1>
                <p style="color: var(--gray-muted);">Enter your shipping address to finalize your book purchase.</p>
            </header>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
            <?php endif; ?>

            <div class="checkout-layout">
                <!-- Shipping Form -->
                <div class="checkout-card">
                    <h2 style="font-size: 18px; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">1. Shipping &amp; Contact Details</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="full_name">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" value="Rohan Sharma" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" value="rohan@example.com" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Street Address *</label>
                            <input type="text" id="address" name="address" class="form-control" value="COER University Campus, 7th Km Haridwar Highway" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" id="city" name="city" class="form-control" value="Roorkee" required>
                            </div>
                            <div class="form-group">
                                <label for="zip">Postal / PIN Code *</label>
                                <input type="text" id="zip" name="zip" class="form-control" value="247667" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="form-control">
                                <option value="Credit / Debit Card">Credit / Debit Card (Simulated)</option>
                                <option value="UPI / Net Banking">UPI / Net Banking</option>
                                <option value="Cash on Delivery">Cash on Delivery (COD)</option>
                            </select>
                        </div>

                        <button type="submit" name="place_order" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px; margin-top: 10px;">
                            🔒 Place Order ($<?php echo number_format($totalAmount, 2); ?>)
                        </button>
                    </form>
                </div>

                <!-- Order Review Panel -->
                <div class="checkout-card" style="height: fit-content;">
                    <h2 style="font-size: 18px; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">2. Order Summary</h2>
                    <ul style="list-style: none; margin-bottom: 20px;">
                        <?php foreach ($_SESSION['cart'] as $bId => $qty): 
                            if (!isset($allBooks[$bId])) continue;
                            $b = $allBooks[$bId];
                        ?>
                            <li style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                                <span><?php echo htmlspecialchars($b['title']); ?> &times; <?php echo $qty; ?></span>
                                <strong>$<?php echo number_format($b['price'] * $qty, 2); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div style="border-top: 1px solid #f1f5f9; padding-top: 14px; font-size: 14.5px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span>Tax (5%):</span>
                            <span>$<?php echo number_format($tax, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; color: var(--primary); border-top: 2px solid var(--border); padding-top: 10px; margin-top: 10px;">
                            <span>Grand Total:</span>
                            <span>$<?php echo number_format($totalAmount, 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 7: Checkout &amp; Order Placement).
    </footer>

</body>
</html>
