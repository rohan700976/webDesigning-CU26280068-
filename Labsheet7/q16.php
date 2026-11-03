<?php
require_once __DIR__ . '/db.php';

$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 1001;
$allBooks = getMockBooks();
$pdo = getDBConnection();
$order = null;
$orderItems = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch();

        if ($order) {
            $itemStmt = $pdo->prepare("SELECT oi.*, b.title, b.author FROM order_items oi JOIN books b ON oi.book_id = b.id WHERE oi.order_id = :id");
            $itemStmt->execute([':id' => $orderId]);
            $orderItems = $itemStmt->fetchAll();
        }
    } catch (PDOException $e) {}
}

// Fallback simulated order if DB is offline or record not found
if (!$order) {
    $order = [
        'id' => $orderId,
        'customer_name' => 'Rohan Sharma',
        'customer_email' => 'rohan@example.com',
        'shipping_address' => 'COER University Campus, 7th Km Haridwar Road, Roorkee, UK - 247667',
        'total_amount' => 64.49,
        'payment_method' => 'Credit / Debit Card (Online)',
        'order_date' => date('Y-m-d H:i:s'),
        'status' => 'Confirmed & Paid'
    ];
    $orderItems = [
        [
            'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
            'author' => 'Robert C. Martin',
            'quantity' => 1,
            'unit_price' => 29.99,
            'subtotal' => 29.99
        ],
        [
            'title' => 'The Pragmatic Programmer: Your Journey to Mastery',
            'author' => 'David Thomas & Andrew Hunt',
            'quantity' => 1,
            'unit_price' => 34.50,
            'subtotal' => 34.50
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q16 - Invoice Summary #INV-<?php echo $order['id']; ?></title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .invoice-box {
            max-width: 820px;
            margin: 30px auto;
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            padding: 48px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 24px;
            margin-bottom: 28px;
        }

        .invoice-title-block h1 {
            font-size: 28px;
            font-weight: 800;
            color: #1e3a8a;
        }

        .invoice-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 30px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .invoice-table th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
            text-align: left;
            font-size: 13.5px;
            color: #475569;
        }

        .invoice-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .invoice-summary-totals {
            width: 320px;
            margin-left: auto;
            margin-bottom: 30px;
        }

        .totals-line {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
        }

        .grand-line {
            border-top: 2px solid #0f172a;
            padding-top: 10px;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }

        @media print {
            .lab-header-banner, .bookstore-nav, .no-print, footer {
                display: none !important;
            }
            body { background: white; }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="lab-header-banner no-print">
        <span><strong>Lab Sheet 7 - Question 16:</strong> PHP script generating invoice summary page after successful order placement</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav no-print">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q12.php">Order History</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <!-- Top Toolbar (Screen only) -->
        <div class="no-print" style="max-width: 820px; margin: 0 auto 16px; display: flex; justify-content: space-between; align-items: center;">
            <a href="q12.php" class="btn btn-secondary">&larr; Back to Orders</a>
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Print Official Invoice (Ctrl + P)
            </button>
        </div>

        <!-- Printable Invoice Container -->
        <div class="invoice-box">
            <div class="invoice-header">
                <div class="invoice-title-block">
                    <h1>TAX INVOICE</h1>
                    <p style="color: var(--gray-muted); font-size: 14px;">Invoice No: <strong>#INV-2026-<?php echo $order['id']; ?></strong></p>
                    <p style="color: var(--gray-muted); font-size: 14px;">Order Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
                </div>
                <div style="text-align: right;">
                    <h2 style="color: var(--primary); font-size: 22px; font-weight: 800;">BookVault Ltd.</h2>
                    <p style="font-size: 13px; color: #475569;">COER University Academic Press</p>
                    <p style="font-size: 13px; color: #475569;">Roorkee, Uttarakhand - 247667</p>
                    <p style="font-size: 13px; color: #475569;">GSTIN: 05AAACB1234F1Z5</p>
                </div>
            </div>

            <div class="invoice-meta-grid">
                <div>
                    <h4 style="font-size: 13px; text-transform: uppercase; color: var(--gray-muted); margin-bottom: 6px;">Billed &amp; Shipped To:</h4>
                    <p style="font-weight: 700; font-size: 15px; color: var(--dark);"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p style="font-size: 13.5px; color: #475569;"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    <p style="font-size: 13.5px; color: #475569;">Email: <?php echo htmlspecialchars($order['customer_email']); ?></p>
                </div>
                <div style="text-align: right;">
                    <h4 style="font-size: 13px; text-transform: uppercase; color: var(--gray-muted); margin-bottom: 6px;">Payment Information:</h4>
                    <p style="font-size: 14px;">Method: <strong><?php echo htmlspecialchars($order['payment_method'] ?? 'Prepaid'); ?></strong></p>
                    <p style="font-size: 14px;">Fulfillment Status: <span class="badge badge-success">✓ <?php echo htmlspecialchars($order['status']); ?></span></p>
                </div>
            </div>

            <!-- Itemized Products Table -->
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Book Description</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Unit Price</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $subtotal = 0;
                    $itemNum = 1;
                    foreach ($orderItems as $item): 
                        $amt = $item['subtotal'] ?? ($item['unit_price'] * $item['quantity']);
                        $subtotal += $amt;
                    ?>
                        <tr>
                            <td><?php echo $itemNum++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                <?php if (!empty($item['author'])): ?>
                                    <div style="font-size: 12px; color: var(--gray-muted);">Author: <?php echo htmlspecialchars($item['author']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                            <td style="text-align: right;">$<?php echo number_format($item['unit_price'], 2); ?></td>
                            <td style="text-align: right;"><strong>$<?php echo number_format($amt, 2); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totals Calculation -->
            <?php 
                $tax = $subtotal * 0.05;
                $grandTotal = $subtotal + $tax;
            ?>
            <div class="invoice-summary-totals">
                <div class="totals-line">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="totals-line">
                    <span>Applicable Tax (GST 5%):</span>
                    <span>$<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="totals-line">
                    <span>Shipping Charges:</span>
                    <span style="color: var(--success); font-weight: 700;">FREE</span>
                </div>
                <div class="totals-line grand-line">
                    <span>Total Amount:</span>
                    <span>$<?php echo number_format($grandTotal, 2); ?></span>
                </div>
            </div>

            <div style="border-top: 1px solid #e2e8f0; padding-top: 20px; font-size: 12px; color: var(--gray-muted); text-align: center;">
                This is a computer-generated tax invoice verified under COER University Web Engineering Laboratory.
            </div>
        </div>
    </main>

    <footer class="bookstore-footer no-print">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 16: Invoice Summary Page).
    </footer>

</body>
</html>
