<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 9
// Objective: Implement an edit form that pre-fills existing record data and
//            updates it in the database using an UPDATE query.
// File: q9.php
// ============================================================================

require_once 'db_connect.php';

$error = "";
$success = "";
$product_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 1);
$product = null;

// Demo placeholder record if DB is offline or record not found
$demo_product = [
    'id' => $product_id,
    'name' => 'Dell XPS 15 Laptop (Core i9, 32GB)',
    'description' => 'Flagship 15.6" 3.5K OLED touchscreen, 13th Gen Intel Core i9, 1TB NVMe SSD.',
    'category' => 'Electronics',
    'price' => '189999.00',
    'stock' => 12
];

// Handle UPDATE Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validation
    if ($id <= 0) {
        $error = "Invalid product ID.";
    } elseif (empty($name)) {
        $error = "Product name cannot be empty.";
    } elseif (empty($category)) {
        $error = "Category is required.";
    } elseif (!is_numeric($price) || floatval($price) <= 0) {
        $error = "Valid positive price is required.";
    } elseif (!is_numeric($stock) || intval($stock) < 0) {
        $error = "Stock must be zero or a positive number.";
    } else {
        if ($db_connected && $pdo) {
            try {
                $sql = "UPDATE products 
                        SET name = :name, 
                            description = :description, 
                            category = :category, 
                            price = :price, 
                            stock = :stock 
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->bindValue(':description', $description, PDO::PARAM_STR);
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
                $stmt->bindValue(':price', number_format((float)$price, 2, '.', ''), PDO::PARAM_STR);
                $stmt->bindValue(':stock', (int)$stock, PDO::PARAM_INT);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $success = "Product #{$id} updated successfully in the database via parameterized UPDATE query!";
                } else {
                    $error = "Failed to update record.";
                }
            } catch (PDOException $e) {
                $error = "Database Error: " . htmlspecialchars($e->getMessage());
            }
        } else {
            // Simulated update response for offline demo
            $success = "Product #{$id} updated successfully (Demo Mode)! Pre-fill and UPDATE logic validated.";
            $demo_product = [
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'category' => $category,
                'price' => $price,
                'stock' => $stock
            ];
        }
    }
}

// Fetch existing record data to PRE-FILL form
if ($db_connected && $pdo && $product_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Query Error: " . htmlspecialchars($e->getMessage());
    }
}

// Fallback to demo record if not found in DB
if (!$product) {
    $product = $demo_product;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q9: Edit Product Record (UPDATE Query) - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q8.php">&larr; Q8: Display Catalog</a>
            <a href="q9.php" style="color: var(--accent); font-weight: 700;">Q9: Edit Product</a>
            <a href="q10.php">Q10: Delete Product &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 9</div>
            <h1>Edit Record &amp; Dynamic Pre-Filling</h1>
            <p class="subtitle">Fetching existing product data by Primary Key, pre-filling input fields, and executing an <code>UPDATE</code> query.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>Updated:</strong> <?= htmlspecialchars($success) ?>
                <div style="margin-top: 0.6rem;">
                    <a href="q8.php" class="btn btn-primary" style="padding: 0.35rem 0.8rem;">Return to Product Table (Q8) &rarr;</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-2">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h3 style="margin: 0;">Editing Product #<?= htmlspecialchars((string)$product['id']) ?></h3>
                    <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">Pre-Filled Form</span>
                </div>

                <form action="q9.php" method="POST">
                    <!-- Hidden ID input -->
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)$product['id']) ?>">

                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" class="form-control" 
                               value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                    </div>

                    <div class="grid grid-2" style="gap: 1rem;">
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" class="form-control" required>
                                <?php
                                $categories = ['Electronics', 'Audio', 'Footwear', 'Wearables', 'Accessories', 'Appliances'];
                                foreach ($categories as $cat) {
                                    $selected = ($product['category'] ?? '') === $cat ? 'selected' : '';
                                    echo "<option value=\"$cat\" $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Price (₹) *</label>
                            <input type="number" step="0.01" id="price" name="price" class="form-control" 
                                   value="<?= htmlspecialchars((string)($product['price'] ?? '0.00')) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stock">Inventory Stock *</label>
                        <input type="number" id="stock" name="stock" class="form-control" 
                               value="<?= htmlspecialchars((string)($product['stock'] ?? 0)) ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Product Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <div style="display: flex; gap: 0.75rem;">
                        <button type="submit" name="update_product" class="btn btn-primary" style="flex: 2;">
                            💾 Save &amp; Execute UPDATE Query
                        </button>
                        <a href="q8.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
                    </div>
                </form>
            </div>

            <div>
                <div class="card">
                    <h3 style="margin-bottom: 0.75rem;">Two-Step Pre-Fill &amp; UPDATE Workflow</h3>
                    <div class="code-box">
<span class="comment">// STEP 1: Fetch existing values by ID</span>
<span class="keyword">$stmt</span> = <span class="keyword">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM products WHERE id = ?"</span>);
<span class="keyword">$stmt</span>-><span class="function">execute</span>([<span class="keyword">$id</span>]);
<span class="keyword">$product</span> = <span class="keyword">$stmt</span>-><span class="function">fetch</span>();

<span class="comment">// STEP 2: Pre-fill input value attribute</span>
<span class="comment">&lt;input value="&lt;?= htmlspecialchars($product['name']) ?&gt;"&gt;</span>

<span class="comment">// STEP 3: Handle POST submission and execute UPDATE</span>
<span class="keyword">$sql</span> = <span class="string">"UPDATE products SET 
        name = :name, 
        price = :price, 
        stock = :stock 
        WHERE id = :id"</span>;
<span class="keyword">$updateStmt</span> = <span class="keyword">$pdo</span>-><span class="function">prepare</span>(<span class="keyword">$sql</span>);
<span class="keyword">$updateStmt</span>-><span class="function">execute</span>([...]);
                    </div>
                </div>

                <div class="card" style="margin-top: 1rem;">
                    <h4>Why Pre-filling Matters</h4>
                    <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.6; margin-top: 0.5rem;">
                        Pre-filling ensures that the administrator only changes the fields they intend to modify, without having to re-type the entire product specification. Escaping with <code>htmlspecialchars()</code> prevents input injection.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
