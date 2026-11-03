<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 12
// Objective: Create a search and filter feature that retrieves products by
//            name, category, or price range using LIKE and BETWEEN clauses.
// File: q12.php
// ============================================================================

require_once 'db_connect.php';

// Retrieve filter criteria from GET request
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$min_price = trim($_GET['min_price'] ?? '');
$max_price = trim($_GET['max_price'] ?? '');

$products = [];
$generated_sql = "";
$is_demo = false;

// Base query
$sql = "SELECT id, name, description, category, price, stock FROM products WHERE 1=1";
$params = [];

// 1. Name search with LIKE %term%
if (!empty($search)) {
    $sql .= " AND (name LIKE :search OR description LIKE :search_desc)";
    $params[':search'] = '%' . $search . '%';
    $params[':search_desc'] = '%' . $search . '%';
}

// 2. Category filter
if (!empty($category) && $category !== 'All') {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}

// 3. Price range filter with BETWEEN clause
if ($min_price !== '' && $max_price !== '' && is_numeric($min_price) && is_numeric($max_price)) {
    $sql .= " AND price BETWEEN :min_price AND :max_price";
    $params[':min_price'] = floatval($min_price);
    $params[':max_price'] = floatval($max_price);
}

$sql .= " ORDER BY price ASC";
$generated_sql = $sql;

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $is_demo = true;
    }
} else {
    $is_demo = true;
}

// Demo fallback data filtering
if ($is_demo) {
    $all_demo = [
        ['id' => 1, 'name' => 'Dell XPS 15 Laptop', 'category' => 'Electronics', 'price' => 189999.00, 'stock' => 12, 'description' => 'Flagship OLED laptop'],
        ['id' => 2, 'name' => 'Sony WH-1000XM5 Headphones', 'category' => 'Audio', 'price' => 29999.00, 'stock' => 28, 'description' => 'Noise cancelling headphones'],
        ['id' => 3, 'name' => 'Apple Watch Ultra 2', 'category' => 'Wearables', 'price' => 89900.00, 'stock' => 5, 'description' => 'Rugged titanium smartwatch'],
        ['id' => 4, 'name' => 'Nike Air Zoom Pegasus 40', 'category' => 'Footwear', 'price' => 10495.00, 'stock' => 15, 'description' => 'Road running shoes'],
        ['id' => 5, 'name' => 'Logitech MX Master 3S Mouse', 'category' => 'Accessories', 'price' => 9495.00, 'stock' => 40, 'description' => 'Ergonomic precision mouse'],
        ['id' => 6, 'name' => 'LG 27-inch 4K UHD Monitor', 'category' => 'Electronics', 'price' => 32999.00, 'stock' => 8, 'description' => 'Crisp 4K display']
    ];

    $products = array_filter($all_demo, function($item) use ($search, $category, $min_price, $max_price) {
        if (!empty($search) && stripos($item['name'], $search) === false && stripos($item['description'], $search) === false) {
            return false;
        }
        if (!empty($category) && $category !== 'All' && strcasecmp($item['category'], $category) !== 0) {
            return false;
        }
        if ($min_price !== '' && is_numeric($min_price) && $item['price'] < floatval($min_price)) {
            return false;
        }
        if ($max_price !== '' && is_numeric($max_price) && $item['price'] > floatval($max_price)) {
            return false;
        }
        return true;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q12: Search &amp; Filter (LIKE &amp; BETWEEN) - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .filter-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr 1fr auto;
            gap: 1rem;
            align-items: flex-end;
        }
        @media (max-width: 900px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q11.php">&larr; Q11: Pagination</a>
            <a href="q12.php" style="color: var(--accent); font-weight: 700;">Q12: Search &amp; Filter</a>
            <a href="q13.php">Q13: Column Sorting &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 12</div>
            <h1>Multi-Clause Search &amp; Range Filtering</h1>
            <p class="subtitle">Querying MySQL with dynamic <code>LIKE</code> substring patterns and numerical <code>BETWEEN</code> price intervals.</p>
        </div>

        <!-- Filter Form -->
        <div class="filter-panel">
            <form action="q12.php" method="GET">
                <div class="filter-grid">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="search">Product Search (LIKE)</label>
                        <input type="text" id="search" name="search" class="form-control" 
                               placeholder="e.g. Sony, Laptop, Shoes..." value="<?= htmlspecialchars($search) ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control">
                            <option value="All">All Categories</option>
                            <?php
                            $cats = ['Electronics', 'Audio', 'Footwear', 'Wearables', 'Accessories'];
                            foreach ($cats as $c) {
                                $selected = ($category === $c) ? 'selected' : '';
                                echo "<option value=\"$c\" $selected>$c</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="min_price">Min Price (₹)</label>
                        <input type="number" id="min_price" name="min_price" class="form-control" 
                               placeholder="0" value="<?= htmlspecialchars($min_price) ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="max_price">Max Price (₹)</label>
                        <input type="number" id="max_price" name="max_price" class="form-control" 
                               placeholder="100000" value="<?= htmlspecialchars($max_price) ?>">
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="height: 44px;">Filter</button>
                        <a href="q12.php" class="btn btn-secondary" style="height: 44px; display: inline-flex; align-items: center;">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Filter Query Inspector -->
        <div class="card" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">
                    Constructed Dynamic SQL Query:
                </span>
                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                    <?= count($products) ?> Matches Found
                </span>
            </div>
            <div style="font-family: var(--font-mono); font-size: 0.85rem; color: #a5b4fc; background: #0f172a; padding: 0.75rem; border-radius: 6px; overflow-x: auto;">
                <?= htmlspecialchars($generated_sql) ?>
            </div>
        </div>

        <!-- Results Table -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price (₹)</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                    🔍 No products matched your criteria. Try widening your price range or search query.
                                </td>
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
                                    <td><?= htmlspecialchars((string)$p['stock']) ?> in stock</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
