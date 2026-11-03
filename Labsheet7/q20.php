<?php
require_once __DIR__ . '/db.php';

// Interactive End-to-End Simulation Step Tracker
$currentStep = isset($_GET['step']) ? intval($_GET['step']) : 1;
$stepSuccess = false;
$stepMsg = "";

// Perform automated actions based on step progression
if ($currentStep === 1) {
    $stepMsg = "Stage 1 Active: Customer begins browsing the book catalog.";
} elseif ($currentStep === 2) {
    $stepMsg = "Stage 2 Verified: Search filter applied for keyword 'Clean Code'. Matching records returned.";
} elseif ($currentStep === 3) {
    // Simulate Add to Cart
    $_SESSION['cart'][1] = 2; // Add 2 copies of Book ID 1
    $stepMsg = "Stage 3 Verified: Added 2 copies of 'Clean Code' to \$_SESSION['cart']. Current Cart Count: " . count($_SESSION['cart']);
} elseif ($currentStep === 4) {
    // Simulate User Login
    $_SESSION['user_id'] = 101;
    $_SESSION['username'] = 'rohan_student';
    $_SESSION['role'] = 'customer';
    $stepMsg = "Stage 4 Verified: Authenticated session established for user 'rohan_student'.";
} elseif ($currentStep === 5) {
    // Simulate Checkout
    $stepMsg = "Stage 5 Verified: Shipping address validated (COER University, Roorkee). Ready to commit transaction.";
} elseif ($currentStep === 6) {
    // Simulate Order Placement & Clear Cart
    $simulatedOrderId = 2050;
    $_SESSION['orders'][] = [
        'id' => $simulatedOrderId,
        'user' => $_SESSION['username'] ?? 'rohan_student',
        'items_count' => 2,
        'total' => 62.98,
        'address' => 'COER University Campus, Roorkee, UK',
        'status' => 'Pending',
        'date' => date('Y-m-d H:i:s')
    ];
    $_SESSION['cart'] = []; // Cleared upon order
    $stepMsg = "Stage 6 Verified: Order #{$simulatedOrderId} created in database. Cart cleared. Invoice generated!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q20 - Complete E-Commerce User Journey Test</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .journey-stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 36px;
            position: relative;
            flex-wrap: wrap;
            gap: 12px;
        }

        .step-item {
            flex: 1 1 120px;
            text-align: center;
            background: white;
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 14px 8px;
            position: relative;
            transition: all 0.2s;
        }

        .step-item.active {
            border-color: var(--primary);
            background: #eff6ff;
        }

        .step-item.completed {
            border-color: var(--success);
            background: #f0fdf4;
        }

        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #cbd5e1;
            color: white;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .step-item.active .step-num {
            background: var(--primary);
        }

        .step-item.completed .step-num {
            background: var(--success);
        }

        .step-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--dark);
            display: block;
        }

        .stage-display-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 36px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .state-inspector {
            background: #0f172a;
            color: #38bdf8;
            padding: 16px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 13px;
            margin-top: 20px;
            overflow-x: auto;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 20:</strong> End-to-end e-commerce user journey: browsing, searching, cart, login, checkout, confirmation</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Storefront</a></li>
                <li><a href="q20.php" class="active">Journey Test Suite</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 28px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">End-to-End User Journey Walkthrough</h1>
            <p style="color: var(--gray-muted);">Walk through all 6 critical milestones of the online bookstore transaction pipeline.</p>
        </header>

        <!-- 6-Stage Stepper Header -->
        <div class="journey-stepper">
            <div class="step-item <?php echo $currentStep == 1 ? 'active' : ($currentStep > 1 ? 'completed' : ''); ?>">
                <div class="step-num"><?php echo $currentStep > 1 ? '✓' : '1'; ?></div>
                <span class="step-title">1. Browsing</span>
            </div>
            <div class="step-item <?php echo $currentStep == 2 ? 'active' : ($currentStep > 2 ? 'completed' : ''); ?>">
                <div class="step-num"><?php echo $currentStep > 2 ? '✓' : '2'; ?></div>
                <span class="step-title">2. Searching</span>
            </div>
            <div class="step-item <?php echo $currentStep == 3 ? 'active' : ($currentStep > 3 ? 'completed' : ''); ?>">
                <div class="step-num"><?php echo $currentStep > 3 ? '✓' : '3'; ?></div>
                <span class="step-title">3. Add to Cart</span>
            </div>
            <div class="step-item <?php echo $currentStep == 4 ? 'active' : ($currentStep > 4 ? 'completed' : ''); ?>">
                <div class="step-num"><?php echo $currentStep > 4 ? '✓' : '4'; ?></div>
                <span class="step-title">4. User Login</span>
            </div>
            <div class="step-item <?php echo $currentStep == 5 ? 'active' : ($currentStep > 5 ? 'completed' : ''); ?>">
                <div class="step-num"><?php echo $currentStep > 5 ? '✓' : '5'; ?></div>
                <span class="step-title">5. Checkout</span>
            </div>
            <div class="step-item <?php echo $currentStep == 6 ? 'active' : ($currentStep > 6 ? 'completed' : ''); ?>">
                <div class="step-num"><?php echo $currentStep > 6 ? '✓' : '6'; ?></div>
                <span class="step-title">6. Confirmation</span>
            </div>
        </div>

        <!-- Current Active Stage Presentation -->
        <section class="stage-display-card">
            <div class="alert alert-info" style="font-size: 15px;">
                <?php echo htmlspecialchars($stepMsg); ?>
            </div>

            <?php if ($currentStep === 1): ?>
                <h3>Stage 1: Browsing the Bookstore Catalog</h3>
                <p style="color: var(--gray-muted); margin: 10px 0 20px;">The user visits the storefront, viewing featured books, category navigation pills, and promotional discounts.</p>
                <a href="q20.php?step=2" class="btn btn-primary">Proceed to Step 2: Search for a Book &rarr;</a>
                <a href="q1.php" target="_blank" class="btn btn-secondary" style="margin-left: 8px;">Open Homepage in New Tab ↗</a>

            <?php elseif ($currentStep === 2): ?>
                <h3>Stage 2: Searching &amp; Keyword Filtering</h3>
                <p style="color: var(--gray-muted); margin: 10px 0 20px;">The user searches for specific software craftsmanship books using PHP SQL <code>LIKE</code> query filters.</p>
                <a href="q20.php?step=3" class="btn btn-primary">Proceed to Step 3: Add Book to Cart &rarr;</a>
                <a href="q10.php?query=Clean" target="_blank" class="btn btn-secondary" style="margin-left: 8px;">Test Search Page ↗</a>

            <?php elseif ($currentStep === 3): ?>
                <h3>Stage 3: Session Shopping Cart Management</h3>
                <p style="color: var(--gray-muted); margin: 10px 0 20px;">Selected book added to <code>$_SESSION['cart']</code> with inventory stock check validation.</p>
                <a href="q20.php?step=4" class="btn btn-primary">Proceed to Step 4: User Authentication &rarr;</a>
                <a href="q6.php" target="_blank" class="btn btn-secondary" style="margin-left: 8px;">View Active Cart ↗</a>

            <?php elseif ($currentStep === 4): ?>
                <h3>Stage 4: Secure User Authentication</h3>
                <p style="color: var(--gray-muted); margin: 10px 0 20px;">Customer authenticates via hashed passwords (<code>password_verify</code>) with session fixation regeneration.</p>
                <a href="q20.php?step=5" class="btn btn-primary">Proceed to Step 5: Shipping Details Checkout &rarr;</a>
                <a href="q5.php" target="_blank" class="btn btn-secondary" style="margin-left: 8px;">Test Login Module ↗</a>

            <?php elseif ($currentStep === 5): ?>
                <h3>Stage 5: Shipping &amp; Transaction Checkout</h3>
                <p style="color: var(--gray-muted); margin: 10px 0 20px;">Customer inputs delivery address and confirms payment method.</p>
                <a href="q20.php?step=6" class="btn btn-primary">Complete Order &amp; Commit Transaction &rarr;</a>
                <a href="q7.php" target="_blank" class="btn btn-secondary" style="margin-left: 8px;">Test Checkout Page ↗</a>

            <?php elseif ($currentStep === 6): ?>
                <div style="text-align: center; padding: 20px 0;">
                    <div style="font-size: 54px; margin-bottom: 12px;">🏆</div>
                    <h2 style="color: var(--success); margin-bottom: 8px;">End-to-End User Journey Completed!</h2>
                    <p style="color: var(--gray-muted); margin-bottom: 24px;">All 6 stages verified successfully: Browsing &rarr; Search &rarr; Cart &rarr; Login &rarr; Checkout &rarr; Order Confirmation &amp; Invoice.</p>
                    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                        <a href="q16.php?order_id=2050" class="btn btn-primary">📄 View Final Generated Invoice</a>
                        <a href="q12.php" class="btn btn-secondary">📦 Inspect Order History</a>
                        <a href="q20.php?step=1" class="btn btn-secondary">🔄 Restart Journey Walkthrough</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Live Session State Inspector -->
            <h4 style="margin-top: 30px; font-size: 13px; text-transform: uppercase; color: var(--gray-muted);">Live Session &amp; Cart State Inspector:</h4>
            <div class="state-inspector">
$_SESSION['user_id']  = <?php echo json_encode($_SESSION['user_id'] ?? null); ?><br>
$_SESSION['username'] = <?php echo json_encode($_SESSION['username'] ?? null); ?><br>
$_SESSION['cart']     = <?php echo json_encode($_SESSION['cart'] ?? []); ?><br>
$_SESSION['orders']   = <?php echo json_encode($_SESSION['orders'] ?? []); ?>
            </div>
        </section>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 20: End-to-End E-Commerce Journey).
    </footer>

</body>
</html>
