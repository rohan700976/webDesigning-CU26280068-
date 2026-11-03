<?php
require_once __DIR__ . '/db.php';

// Retrieve and sanitize book ID passed via URL (GET)
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 1;
$pdo = getDBConnection();
$book = null;

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $bookId]);
        $book = $stmt->fetch();
    } catch (PDOException $e) {
        $book = null;
    }
}

// Fallback to mock data if DB not connected or record not found
if (!$book) {
    $mockBooks = getMockBooks();
    $book = $mockBooks[$bookId] ?? $mockBooks[1];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q4 - Book Detail Page (ID: <?php echo $book['id']; ?>)</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .book-detail-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 36px;
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 40px;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            .book-detail-card {
                grid-template-columns: 1fr;
            }
        }

        .book-cover-large {
            width: 100%;
            height: 420px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .book-cover-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .detail-meta-pill {
            display: inline-block;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .detail-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .detail-author {
            font-size: 16px;
            color: var(--gray-muted);
            margin-bottom: 18px;
        }

        .detail-price-box {
            display: flex;
            align-items: baseline;
            gap: 16px;
            margin-bottom: 24px;
            padding: 16px 20px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .detail-price {
            font-size: 32px;
            font-weight: 800;
            color: var(--dark);
        }

        .detail-stock {
            font-size: 14px;
            font-weight: 600;
            color: var(--success);
        }

        .detail-description {
            color: #475569;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .action-form-row {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .qty-input {
            width: 70px;
            padding: 10px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
        }

        .quick-switch {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
            align-items: center;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 4:</strong> Book detail page in PHP retrieving book information using ID passed via URL (<code>GET['id']</code>)</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q3.php">Books</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <!-- Quick Switcher to test different GET IDs -->
        <div class="quick-switch">
            <span style="font-size: 13px; font-weight: 600; color: var(--gray-muted);">Test other Book IDs via GET:</span>
            <a href="q4.php?id=1" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">ID 1</a>
            <a href="q4.php?id=2" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">ID 2</a>
            <a href="q4.php?id=3" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">ID 3</a>
            <a href="q4.php?id=4" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">ID 4</a>
            <a href="q4.php?id=5" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">ID 5</a>
            <a href="q4.php?id=6" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">ID 6</a>
        </div>

        <article class="book-detail-card">
            <!-- Large Book Cover -->
            <div class="book-cover-large">
                <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
            </div>

            <!-- Book Information Content -->
            <div>
                <span class="detail-meta-pill"><?php echo htmlspecialchars($book['category'] ?? 'General'); ?></span>
                <h1 class="detail-title"><?php echo htmlspecialchars($book['title']); ?></h1>
                <p class="detail-author">Written by <strong><?php echo htmlspecialchars($book['author']); ?></strong></p>

                <div class="detail-price-box">
                    <span class="detail-price">$<?php echo number_format($book['price'], 2); ?></span>
                    <span class="detail-stock">✓ Available in Stock (<?php echo htmlspecialchars($book['stock']); ?> remaining)</span>
                </div>

                <div class="detail-description">
                    <h3 style="font-size: 16px; margin-bottom: 8px; color: var(--dark);">Book Synopsis &amp; Overview:</h3>
                    <p><?php echo nl2br(htmlspecialchars($book['description'] ?? 'Detailed synopsis coming soon for this best-selling academic publication.')); ?></p>
                </div>

                <!-- Add to Cart Form -->
                <form action="q6.php" method="GET" class="action-form-row">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                    
                    <label for="qty" style="margin: 0; font-weight: 700;">Qty:</label>
                    <input type="number" id="qty" name="qty" value="1" min="1" max="<?php echo $book['stock']; ?>" class="qty-input">

                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                        🛒 Add to Shopping Cart
                    </button>

                    <a href="q17.php?action=add&id=<?php echo $book['id']; ?>" class="btn btn-secondary" style="padding: 12px 18px;">
                        ❤️ Save to Wishlist
                    </a>

                    <a href="q15.php?id=<?php echo $book['id']; ?>" class="btn btn-secondary" style="padding: 12px 18px;">
                        ⭐ Reviews &amp; Ratings
                    </a>
                </form>
            </div>
        </article>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 4: Book Detail Page).
    </footer>

</body>
</html>
