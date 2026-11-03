<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 13
// Objective: Implement column sorting (ascending/descending) on the product
//            table by clicking the table headers.
// File: q13.php
// ============================================================================

require_once 'db_connect.php';

// Strict Whitelist Validation to Prevent SQL Injection on ORDER BY
$allowed_columns = [
    'id'       => 'ID',
    'name'     => 'Product Name',
    'category' => 'Category',
    'price'    => 'Price',
    'stock'    => 'Stock'
];

$sort = $_GET['sort'] ?? 'id';
if (!array_key_exists($sort, $allowed_columns)) {
    $sort = 'id';
}

$order = strtoupper($_GET['order'] ?? 'ASC');
if ($order !== 'ASC' && $order !== 'DESC') {
    $order = 'ASC';
}

// Function to generate toggle URL and sort indicators
function sort_link($column, $title, $current_sort, $current_order) {
    $new_order = ($current_sort === $column && $current_order === 'ASC') ? 'DESC' : 'ASC';
    $arrow = '';
    $active_class = '';

    if ($current_sort === $column) {
        $arrow = ($current_order === 'ASC') ? ' ▲' : ' ▼';
        $active_class = 'style="color: var(--accent); font-weight: 700;"';
    }

    $url = "?sort=" . urlencode($column) . "&order=" . urlencode($new_order);
    return "<a href=\"{$url}\" class=\"sort-header\" {$active_class}>{$title}{$arrow}</a>";
}

$products = [];
$is_demo = false;

if ($db_connected && $pdo) {
    try {
        // Query using whitelisted column and verified ASC/DESC token
        $sql = "SELECT id, name, category, price, stock FROM products ORDER BY {$sort} {$order}";
        $stmt = $pdo->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($products)) {
            $is_demo = true;
        }
    } catch (PDOException $e) {
        $is_demo = true;
    }
} else {
    $is_demo = true;
}

if ($is_demo) {
    $products = [
        ['id' => 1, 'name' => 'Dell XPS 15 Laptop', 'category' => 'Electronics', 'price' => 189999.00, 'stock' => 12],
        ['id' => 2, 'name' => 'Sony WH-1000XM5 Headphones', 'category' => 'Audio', 'price' => 29999.00, 'stock' => 28],
        ['id' => 3, 'name' => 'Apple Watch Ultra 2', 'category' => 'Wearables', 'price' => 89900.00, 'stock' => 5],
        ['id' => 4, 'name' => 'Nike Air Zoom Pegasus 40', 'category' => 'Footwear', 'price' => 10495.00, 'stock' => 15],
        ['id' => 5, 'name' => 'Logitech MX Master 3S Mouse', 'category' => 'Accessories', 'price' => 9495.00, 'stock' => 40],
        ['id' => 6, 'name' => 'LG 27-inch 4K UHD Monitor', 'category' => 'Electronics', 'price' => 32999.00, 'stock' => 8]
    ];

    // In-memory sorting for demo simulation
    usort($products, function($a, $b) use ($sort, $order) {
        $valA = $a[$sort];
        $valB = $b[$sort];
        if (is_numeric($valA) && is_numeric($valB)) {
            $cmp = $valA <=> $valB;
        } else {
            $cmp = strcasecmp((string)$valA, (string)$valB);
        }
        return ($order === 'DESC') ? -$cmp : $cmp;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q13: Dynamic Column Sorting (ASC/DESC) - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .sort-header {
            color: var(--text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: color 0.2s ease;
        }
        .sort-header:hover {
            color: var(--primary);
        }
        .status-pill {
            background: rgba(99, 102, 241, 0.15);
            color: var(--primary);
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q12.php">&larr; Q12: Search &amp; Filter</a>
            <a href="q13.php" style="color: var(--accent); font-weight: 700;">Q13: Column Sorting</a>
            <a href="q14.php">Q14: Image Upload &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 13</div>
            <h1>Interactive Column Sorting (ASC / DESC)</h1>
            <p class="subtitle">Click any table column header to toggle ascending or descending sorting with SQL whitelist validation.</p>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div class="status-pill">
                Active Sort: <strong><?= htmlspecialchars($allowed_columns[$sort]) ?></strong> (<?= htmlspecialchars($order) ?>)
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                💡 Tip: Click the header again to reverse the sort direction.
            </div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?= sort_link('id', 'ID', $sort, $order) ?></th>
                            <th><?= sort_link('name', 'Product Name', $sort, $order) ?></th>
                            <th><?= sort_link('category', 'Category', $sort, $order) ?></th>
                            <th><?= sort_link('price', 'Price (₹)', $sort, $order) ?></th>
                            <th><?= sort_link('stock', 'Inventory Stock', $sort, $order) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $row): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars((string)$row['id']) ?></strong></td>
                                <td style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($row['name']) ?></td>
                                <td>
                                    <span class="badge" style="background: rgba(255,255,255,0.08); color: var(--text-main);">
                                        <?= htmlspecialchars($row['category']) ?>
                                    </span>
                                </td>
                                <td style="font-weight: 700; color: #10b981;">₹<?= number_format((float)$row['price'], 2) ?></td>
                                <td><?= htmlspecialchars((string)$row['stock']) ?> units</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-2" style="margin-top: 2rem;">
            <div class="card">
                <h3>Whitelist Protection Principle</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                    <strong>Security Warning:</strong> Prepared statements cannot bind column names or <code>ASC/DESC</code> keywords. Therefore, an explicit whitelist array is strictly mandatory:
                </p>
                <div class="code-box">
<span class="comment">// 1. Define allowed columns whitelist</span>
<span class="keyword">$allowed</span> = [<span class="string">'id'</span>, <span class="string">'name'</span>, <span class="string">'category'</span>, <span class="string">'price'</span>, <span class="string">'stock'</span>];

<span class="comment">// 2. Fall back to 'id' if invalid or manipulated</span>
<span class="keyword">$sort</span> = <span class="function">in_array</span>(<span class="keyword">$_GET</span>[<span class="string">'sort'</span>], <span class="keyword">$allowed</span>) ? <span class="keyword">$_GET</span>[<span class="string">'sort'</span>] : <span class="string">'id'</span>;

<span class="comment">// 3. Enforce strictly ASC or DESC</span>
<span class="keyword">$order</span> = (<span class="function">strtoupper</span>(<span class="keyword">$_GET</span>[<span class="string">'order'</span>]) === <span class="string">'DESC'</span>) ? <span class="string">'DESC'</span> : <span class="string">'ASC'</span>;

<span class="comment">// 4. Safe interpolated query</span>
<span class="keyword">$sql</span> = <span class="string">"SELECT * FROM products ORDER BY {$sort} {$order}"</span>;
                </div>
            </div>

            <div class="card">
                <h3>Bidirectional Toggle Logic</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
                    When generating table header links, the script checks if the header being rendered is already active:
                </p>
                <ul style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem; padding-left: 1.2rem; line-height: 1.6;">
                    <li>If <strong>Active</strong> &amp; currently <strong>ASC</strong> &rarr; next click sends <strong>DESC</strong> (▼).</li>
                    <li>If <strong>Active</strong> &amp; currently <strong>DESC</strong> &rarr; next click sends <strong>ASC</strong> (▲).</li>
                    <li>If <strong>Inactive</strong> &rarr; default to <strong>ASC</strong>.</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
