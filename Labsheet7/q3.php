<?php
require_once __DIR__ . '/db.php';

$pdo = getDBConnection();
$books = [];
$dataSource = "Mock / Fallback Memory (Start MySQL in XAMPP to query live DB)";

if ($pdo) {
    try {
        // Query available books dynamically from MySQL database
        $stmt = $pdo->query("SELECT * FROM books WHERE stock > 0 ORDER BY id ASC");
        $dbBooks = $stmt->fetchAll();
        if (!empty($dbBooks)) {
            $books = $dbBooks;
            $dataSource = "Live MySQL Database ('bookstore_db.books')";
        } else {
            $books = getMockBooks();
        }
    } catch (PDOException $e) {
        $books = getMockBooks();
    }
} else {
    $books = getMockBooks();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q3 - Dynamic Books Grid from MySQL Database</title>
    <link rel="stylesheet" href="bookstore.css">
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 3:</strong> PHP script to fetch and display available books dynamically from MySQL database in a grid layout</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q3.php" class="active">Available Books</a></li>
                <li><a href="q10.php">Search</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 12px;">
            <div>
                <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Available Books Catalog</h1>
                <p style="color: var(--gray-muted);">Fetched dynamically using PHP PDO SQL Query: <code>SELECT * FROM books WHERE stock &gt; 0</code></p>
            </div>
            <span class="badge badge-info">Source: <?php echo htmlspecialchars($dataSource); ?></span>
        </header>

        <!-- Responsive Book Grid Layout -->
        <section class="book-grid">
            <?php foreach ($books as $b): ?>
                <article class="book-card">
                    <div class="book-image-wrapper">
                        <img src="<?php echo htmlspecialchars($b['image']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>">
                    </div>
                    <div class="book-details">
                        <span class="book-category"><?php echo htmlspecialchars($b['category'] ?? 'General'); ?></span>
                        <h3 class="book-title"><?php echo htmlspecialchars($b['title']); ?></h3>
                        <p class="book-author">Author: <?php echo htmlspecialchars($b['author']); ?></p>
                        
                        <div style="font-size: 13px; color: var(--success); font-weight: 600; margin-bottom: 12px;">
                            ✓ In Stock: <?php echo htmlspecialchars($b['stock']); ?> copies available
                        </div>

                        <div class="book-price-row">
                            <span class="price">$<?php echo number_format($b['price'], 2); ?></span>
                            <div style="display: flex; gap: 6px;">
                                <a href="q4.php?id=<?php echo $b['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">View Details</a>
                                <a href="q6.php?action=add&id=<?php echo $b['id']; ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 13px;">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 3: Fetch &amp; Display Books Grid).
    </footer>

</body>
</html>
