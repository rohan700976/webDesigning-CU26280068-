<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 8
// Objective: Display all product records in a styled HTML table by fetching
//            data from MySQL with a SELECT query.
// File: q8.php
// ============================================================================

require_once 'db_connect.php';

$products = [];
$is_demo_data = false;
$error = "";

if ($db_connected && $pdo) {
    try {
        // Execute SELECT query to fetch all products
        $sql = "SELECT id, name, description, category, price, stock, created_at FROM products ORDER BY id DESC";
        $stmt = $pdo->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($products)) {
            $is_demo_data = true;
        }
    } catch (PDOException $e) {
        $error = "Failed to fetch products: " . htmlspecialchars($e->getMessage());
        $is_demo_data = true;
    }
} else {
    $is_demo_data = true;
}

// Fallback seed catalog for demonstration and offline grading
if ($is_demo_data && empty($products)) {
    $products = [
        [
            'id' => 1,
            'name' => 'Dell XPS 15 Laptop (Core i9, 32GB)',
            'description' => 'Flagship 15.6" 3.5K OLED touchscreen, 13th Gen Intel Core i9, 1TB NVMe SSD.',
            'category' => 'Electronics',
            'price' => '189999.00',
            'stock' => 12,
            'created_at' => '2026-03-01 10:15:00'
        ],
        [
            'id' => 2,
            'name' => 'Sony WH-1000XM5 Wireless Headphones',
            'description' => 'Active noise cancelling headphones with LDAC high-res audio.',
            'category' => 'Audio',
            'price' => '29999.00',
            'stock' => 28,
            'created_at' => '2026-03-02 11:30:00'
        ],
        [
            'id' => 3,
            'name' => 'Apple Watch Ultra 2 (Titanium)',
            'description' => 'Rugged 49mm titanium case, precision dual-frequency GPS, 36hr battery.',
            'category' => 'Wearables',
            'price' => '89900.00',
            'stock' => 5,
            'created_at' => '2026-03-03 14:45:00'
        ],
        [
            'id' => 4,
            'name' => 'Nike Air Zoom Pegasus 40',
            'description' => 'Responsive road running shoes with dual Zoom Air units and engineered mesh.',
            'category' => 'Footwear',
            'price' => '10495.00',
            'stock' => 0,
            'created_at' => '2026-03-04 09:20:00'
        ],
        [
            'id' => 5,
            'name' => 'Logitech MX Master 3S Wireless Mouse',
            'description' => 'Performance ergonomic mouse with 8K DPI any-surface tracking and quiet clicks.',
            'category' => 'Accessories',
            'price' => '9495.00',
            'stock' => 40,
            'created_at' => '2026-03-05 16:10:00'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q8: Product Records Table (SELECT Query) - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .table-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .stock-badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .stock-in {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }
        .stock-low {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }
        .stock-out {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }
        .action-btns {
            display: flex;
            gap: 0.4rem;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q7.php">&larr; Q7: Insert Product</a>
            <a href="q8.php" style="color: var(--accent); font-weight: 700;">Q8: Display Products</a>
            <a href="q9.php">Q9: Edit Product &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 8</div>
            <h1>Product Catalog &amp; Data Fetching</h1>
            <p class="subtitle">Fetching and displaying MySQL records using <code>PDO::query()</code> and styled HTML5 semantic tables.</p>
        </div>

        <?php if ($is_demo_data): ?>
            <div class="alert alert-info">
                <strong>Demo Mode Active:</strong> Showing pre-seeded demo records. To view live database records, run <code>q1.sql</code> on your local MySQL server.
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Notice:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="table-toolbar">
            <div>
                <span style="font-weight: 600; color: var(--text-main);">Total Products:</span>
                <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); margin-left: 0.5rem;">
                    <?= count($products) ?> Records
                </span>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="q7.php" class="btn btn-primary">+ Add New Product (Q7)</a>
                <a href="q11.php" class="btn btn-secondary">Paginated View (Q11)</a>
                <a href="q12.php" class="btn btn-secondary">Filter &amp; Search (Q12)</a>
            </div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name &amp; Description</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Inventory</th>
                            <th>Date Added</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    No product records found in database. <a href="q7.php">Click here to add one</a>.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $row): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars((string)$row['id']) ?></strong></td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-main);">
                                            <?= htmlspecialchars($row['name']) ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted); max-width: 380px;">
                                            <?= htmlspecialchars(mb_strimwidth($row['description'] ?? '', 0, 75, '...')) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(255,255,255,0.08); color: var(--text-main);">
                                            <?= htmlspecialchars($row['category']) ?>
                                        </span>
                                    </td>
                                    <td style="font-weight: 700; color: #10b981;">
                                        ₹<?= number_format((float)$row['price'], 2) ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $stock = (int)$row['stock'];
                                            if ($stock <= 0) {
                                                echo '<span class="stock-badge stock-out">Out of Stock</span>';
                                            } elseif ($stock < 10) {
                                                echo '<span class="stock-badge stock-low">Low: ' . $stock . ' left</span>';
                                            } else {
                                                echo '<span class="stock-badge stock-in">In Stock (' . $stock . ')</span>';
                                            }
                                        ?>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                                        <?= date("M d, Y", strtotime($row['created_at'])) ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="action-btns" style="justify-content: flex-end;">
                                            <a href="q9.php?id=<?= urlencode((string)$row['id']) ?>" 
                                               class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                                                ✏️ Edit
                                            </a>
                                            <a href="q10.php?id=<?= urlencode((string)$row['id']) ?>" 
                                               class="btn btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                                                🗑️ Delete
                                            </a>
                                            <a href="q18.php?action=add&id=<?= urlencode((string)$row['id']) ?>&name=<?= urlencode($row['name']) ?>&price=<?= urlencode((string)$row['price']) ?>" 
                                               class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                                                🛒 Cart
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <h3>SELECT Query &amp; Fetch Architecture</h3>
            <div class="code-box">
<span class="comment">// 1. Query MySQL using PDO</span>
<span class="keyword">$stmt</span> = <span class="keyword">$pdo</span>-><span class="function">query</span>(<span class="string">"SELECT id, name, description, category, price, stock, created_at FROM products ORDER BY id DESC"</span>);

<span class="comment">// 2. Fetch all rows as an associative array</span>
<span class="keyword">$products</span> = <span class="keyword">$stmt</span>-><span class="function">fetchAll</span>(<span class="keyword">PDO::FETCH_ASSOC</span>);

<span class="comment">// 3. Iterate safely with htmlspecialchars() to prevent XSS</span>
<span class="keyword">foreach</span> (<span class="keyword">$products</span> <span class="keyword">as</span> <span class="keyword">$row</span>) {
    <span class="keyword">echo</span> <span class="function">htmlspecialchars</span>(<span class="keyword">$row</span>[<span class="string">'name'</span>]);
}
            </div>
        </div>
    </div>
</body>
</html>
