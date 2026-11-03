<?php
require_once __DIR__ . '/db.php';

$pdo = getDBConnection();
$alert = "";

// 1. Handle Add New Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category = trim($_POST['category'] ?? 'General');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $image = trim($_POST['image'] ?? 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80');
    $desc = trim($_POST['description'] ?? '');

    if (!empty($title) && !empty($author) && $price > 0) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO books (title, author, category, price, stock, image, description) VALUES (:t, :a, :c, :p, :s, :i, :d)");
                $stmt->execute([
                    ':t' => $title,
                    ':a' => $author,
                    ':c' => $category,
                    ':p' => $price,
                    ':s' => $stock,
                    ':i' => $image,
                    ':d' => $desc
                ]);
                $alert = "✓ Book '{$title}' added to database successfully!";
            } catch (PDOException $e) {
                $alert = "Database Error: " . $e->getMessage();
            }
        } else {
            // Memory mock addition
            $newId = count($_SESSION['demo_books'] ?? []) + 10;
            $_SESSION['demo_books'][$newId] = [
                'id' => $newId,
                'title' => $title,
                'author' => $author,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                'image' => $image,
                'description' => $desc
            ];
            $alert = "✓ Book '{$title}' added to inventory!";
        }
    }
}

// 2. Handle Update Price & Stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_inventory'])) {
    $bookId = intval($_POST['book_id'] ?? 0);
    $newPrice = floatval($_POST['new_price'] ?? 0);
    $newStock = intval($_POST['new_stock'] ?? 0);

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE books SET price = :p, stock = :s WHERE id = :id");
            $stmt->execute([':p' => $newPrice, ':s' => $newStock, ':id' => $bookId]);
            $alert = "✓ Inventory updated for Book ID #{$bookId} (Price: \${$newPrice}, Stock: {$newStock})";
        } catch (PDOException $e) {
            $alert = "Update Error: " . $e->getMessage();
        }
    } else {
        $alert = "✓ Inventory updated for Book ID #{$bookId} (Price: \${$newPrice}, Stock: {$newStock})";
    }
}

// Fetch all books for display in table
$booksList = getMockBooks();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC");
        $dbData = $stmt->fetchAll();
        if (!empty($dbData)) {
            $booksList = $dbData;
        }
    } catch (PDOException $e) {}
}
if (!empty($_SESSION['demo_books'])) {
    foreach ($_SESSION['demo_books'] as $id => $b) {
        $booksList[$id] = $b;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q8 - Bookstore Admin Dashboard</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }
        @media (max-width: 900px) {
            .admin-grid { grid-template-columns: 1fr; }
        }
        .admin-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .admin-table th, .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }
        .admin-table th { background: #f8fafc; font-weight: 700; color: #475569; }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 8:</strong> Admin dashboard in PHP allowing administrators to add new books, update prices &amp; manage inventory</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span> <span class="badge badge-warning" style="margin-left: 8px;">ADMIN</span></a>
            <ul class="nav-links">
                <li><a href="q8.php" class="active">Inventory Manager</a></li>
                <li><a href="q9.php">Orders Manager</a></li>
                <li><a href="q21.php">Sales Reports</a></li>
                <li><a href="q1.php">Storefront &rarr;</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Bookstore Administration &amp; Inventory</h1>
            <p style="color: var(--gray-muted);">Directly control database inventory stock, adjust unit pricing, and publish new titles.</p>
        </header>

        <?php if (!empty($alert)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($alert); ?></div>
        <?php endif; ?>

        <div class="admin-grid">
            <!-- Add New Book Form -->
            <div class="admin-card">
                <h2 style="font-size: 18px; margin-bottom: 16px;">Add New Book</h2>
                <form method="POST">
                    <input type="hidden" name="add_book" value="1">
                    
                    <div class="form-group">
                        <label>Book Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. System Design Interview" required>
                    </div>

                    <div class="form-group">
                        <label>Author *</label>
                        <input type="text" name="author" class="form-control" placeholder="e.g. Alex Xu" required>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control">
                            <option value="Technology">Technology</option>
                            <option value="Business">Business</option>
                            <option value="Self-Help">Self-Help</option>
                            <option value="Science">Science</option>
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label>Price ($) *</label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="29.99" required>
                        </div>
                        <div class="form-group">
                            <label>Initial Stock *</label>
                            <input type="number" name="stock" class="form-control" placeholder="10" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="text" name="image" class="form-control" value="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Book overview..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">+ Publish New Book</button>
                </form>
            </div>

            <!-- Manage Existing Books Table -->
            <div class="admin-card" style="overflow-x: auto;">
                <h2 style="font-size: 18px; margin-bottom: 16px;">Manage Inventory &amp; Prices</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Category</th>
                            <th style="width: 110px;">Price ($)</th>
                            <th style="width: 90px;">Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($booksList as $b): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($b['title']); ?></strong>
                                    <div style="font-size: 12px; color: var(--gray-muted);">By <?php echo htmlspecialchars($b['author']); ?> (ID: #<?php echo $b['id']; ?>)</div>
                                </td>
                                <td><span class="badge badge-info"><?php echo htmlspecialchars($b['category'] ?? 'General'); ?></span></td>
                                
                                <form method="POST">
                                    <input type="hidden" name="update_inventory" value="1">
                                    <input type="hidden" name="book_id" value="<?php echo $b['id']; ?>">
                                    <td>
                                        <input type="number" step="0.01" name="new_price" value="<?php echo $b['price']; ?>" class="form-control" style="padding: 6px; font-size: 13px;">
                                    </td>
                                    <td>
                                        <input type="number" name="new_stock" value="<?php echo $b['stock']; ?>" class="form-control" style="padding: 6px; font-size: 13px;">
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-primary" style="padding: 6px 10px; font-size: 12px;">Update</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 8: Admin Dashboard).
    </footer>

</body>
</html>
