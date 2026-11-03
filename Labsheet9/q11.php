<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 11
// Objective: Add pagination to the product listing page using LIMIT and OFFSET,
//            with Previous/Next and numbered page links.
// File: q11.php
// ============================================================================

require_once 'db_connect.php';

// Pagination Parameters
$records_per_page = 3; // Kept at 3 so multiple pages are generated with sample data
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $records_per_page;

$total_records = 0;
$total_pages = 1;
$products = [];
$is_demo = false;

if ($db_connected && $pdo) {
    try {
        // 1. Get total record count for pagination calculation
        $count_stmt = $pdo->query("SELECT COUNT(*) FROM products");
        $total_records = (int)$count_stmt->fetchColumn();

        $total_pages = max(1, ceil($total_records / $records_per_page));

        // Ensure current page does not exceed total pages
        if ($current_page > $total_pages) {
            $current_page = $total_pages;
            $offset = ($current_page - 1) * $records_per_page;
        }

        // 2. Fetch records for the current page using LIMIT and OFFSET
        $sql = "SELECT id, name, description, category, price, stock 
                FROM products 
                ORDER BY id ASC 
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
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

// Fallback demo dataset with 8 items across 3 pages
if ($is_demo) {
    $seed_data = [
        ['id' => 1, 'name' => 'Dell XPS 15 Laptop', 'category' => 'Electronics', 'price' => '189999.00', 'stock' => 12],
        ['id' => 2, 'name' => 'Sony WH-1000XM5 Headphones', 'category' => 'Audio', 'price' => '29999.00', 'stock' => 28],
        ['id' => 3, 'name' => 'Apple Watch Ultra 2', 'category' => 'Wearables', 'price' => '89900.00', 'stock' => 5],
        ['id' => 4, 'name' => 'Nike Air Zoom Pegasus 40', 'category' => 'Footwear', 'price' => '10495.00', 'stock' => 15],
        ['id' => 5, 'name' => 'Logitech MX Master 3S Mouse', 'category' => 'Accessories', 'price' => '9495.00', 'stock' => 40],
        ['id' => 6, 'name' => 'LG 27-inch 4K UHD Monitor', 'category' => 'Electronics', 'price' => '32999.00', 'stock' => 8],
        ['id' => 7, 'name' => 'Keychron K2 Mechanical Keyboard', 'category' => 'Accessories', 'price' => '8499.00', 'stock' => 22],
        ['id' => 8, 'name' => 'Bose QuietComfort 45', 'category' => 'Audio', 'price' => '24990.00', 'stock' => 14]
    ];
    $total_records = count($seed_data);
    $total_pages = ceil($total_records / $records_per_page);
    $products = array_slice($seed_data, $offset, $records_per_page);
}

$start_record = ($total_records > 0) ? ($offset + 1) : 0;
$end_record = min($offset + $records_per_page, $total_records);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q11: Product Pagination (LIMIT &amp; OFFSET) - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .pagination-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .pagination-nav {
            display: flex;
            gap: 0.35rem;
        }
        .page-link {
            padding: 0.5rem 0.85rem;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .page-link:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: var(--primary);
            color: #fff;
        }
        .page-link.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            font-weight: 700;
        }
        .page-link.disabled {
            opacity: 0.35;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q10.php">&larr; Q10: Delete Product</a>
            <a href="q11.php" style="color: var(--accent); font-weight: 700;">Q11: Pagination</a>
            <a href="q12.php">Q12: Search &amp; Filter &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 11</div>
            <h1>Database Pagination with LIMIT &amp; OFFSET</h1>
            <p class="subtitle">Efficient database slicing using SQL <code>LIMIT :limit OFFSET :offset</code> with dynamic numeric pagination links.</p>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem;">No products found on this page.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars((string)$p['id']) ?></strong></td>
                                    <td style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($p['name']) ?></td>
                                    <td>
                                        <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: var(--primary);">
                                            <?= htmlspecialchars($p['category']) ?>
                                        </span>
                                    </td>
                                    <td style="font-weight: 700; color: #10b981;">₹<?= number_format((float)$p['price'], 2) ?></td>
                                    <td><?= htmlspecialchars((string)$p['stock']) ?> units</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination-bar">
            <div style="font-size: 0.9rem; color: var(--text-muted);">
                Showing <strong><?= $start_record ?></strong> to <strong><?= $end_record ?></strong> of <strong><?= $total_records ?></strong> products &bull; Page <?= $current_page ?> of <?= $total_pages ?>
            </div>

            <div class="pagination-nav">
                <!-- Previous Button -->
                <a href="?page=<?= $current_page - 1 ?>" 
                   class="page-link <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                    &laquo; Previous
                </a>

                <!-- Numbered Page Links -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="page-link <?= ($i === $current_page) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Next Button -->
                <a href="?page=<?= $current_page + 1 ?>" 
                   class="page-link <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                    Next &raquo;
                </a>
            </div>
        </div>

        <div class="grid grid-2" style="margin-top: 2rem;">
            <div class="card">
                <h3>Mathematical Formulation</h3>
                <div class="code-box">
<span class="comment">// 1. Determine parameters</span>
<span class="keyword">$records_per_page</span> = 3;
<span class="keyword">$page</span> = <span class="keyword">isset</span>(<span class="keyword">$_GET</span>[<span class="string">'page'</span>]) ? <span class="function">max</span>(1, <span class="function">intval</span>(<span class="keyword">$_GET</span>[<span class="string">'page'</span>])) : 1;

<span class="comment">// 2. Compute Offset</span>
<span class="keyword">$offset</span> = (<span class="keyword">$page</span> - 1) * <span class="keyword">$records_per_page</span>;

<span class="comment">// 3. Calculate total pages</span>
<span class="keyword">$total_pages</span> = <span class="function">ceil</span>(<span class="keyword">$total_records</span> / <span class="keyword">$records_per_page</span>);

<span class="comment">// 4. SQL Statement</span>
<span class="keyword">$sql</span> = <span class="string">"SELECT * FROM products ORDER BY id ASC LIMIT :limit OFFSET :offset"</span>;
                </div>
            </div>

            <div class="card">
                <h3>Why OFFSET Pagination?</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6;">
                    Without pagination, querying a database table with 100,000 products transfers megabytes of JSON/HTML in a single HTTP request, overloading memory and causing severe page latency.
                </p>
                <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; margin-top: 0.5rem;">
                    <code>LIMIT</code> caps the maximum records returned per slice, while <code>OFFSET</code> instructs MySQL to skip the preceding rows, delivering instant page rendering.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
