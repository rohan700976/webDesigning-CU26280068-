<?php
require_once __DIR__ . '/db.php';

$allBooks = getMockBooks();
$pdo = getDBConnection();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM books");
        foreach ($stmt->fetchAll() as $dbB) {
            $allBooks[$dbB['id']] = $dbB;
        }
    } catch (PDOException $e) {}
}

$alert = "";

// 1. Add to Wishlist
if (isset($_GET['action']) && $_GET['action'] === 'add') {
    $bookId = intval($_GET['id'] ?? 0);
    if (isset($allBooks[$bookId])) {
        if (!in_array($bookId, $_SESSION['wishlist'])) {
            $_SESSION['wishlist'][] = $bookId;
            $alert = "✓ '{$allBooks[$bookId]['title']}' added to your Wishlist!";
        } else {
            $alert = "Book is already in your Wishlist.";
        }
    }
}

// 2. Remove from Wishlist
if (isset($_GET['action']) && $_GET['action'] === 'remove') {
    $bookId = intval($_GET['id'] ?? 0);
    $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$bookId]));
    $alert = "Item removed from your Wishlist.";
}

// 3. Move from Wishlist to Cart
if (isset($_GET['action']) && $_GET['action'] === 'move_to_cart') {
    $bookId = intval($_GET['id'] ?? 0);
    if (isset($allBooks[$bookId])) {
        // Add to cart
        $_SESSION['cart'][$bookId] = ($_SESSION['cart'][$bookId] ?? 0) + 1;
        // Remove from wishlist
        $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$bookId]));
        $alert = "✓ Moved '{$allBooks[$bookId]['title']}' from Wishlist to Shopping Cart!";
    }
}

// Default seed if empty for testing
if (empty($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [2, 4];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q17 - Personal Saved Wishlist</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 17:</strong> Wishlist feature allowing users to save books to a personal wishlist in the database</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q3.php">Books Catalog</a></li>
                <li><a href="q17.php" class="active">❤️ Wishlist (<?php echo count($_SESSION['wishlist']); ?>)</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 12px;">
            <div>
                <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">My Saved Wishlist</h1>
                <p style="color: var(--gray-muted);">Save books for future semesters or move them directly into your shopping cart.</p>
            </div>
            <a href="q3.php" class="btn btn-secondary">+ Browse More Titles</a>
        </header>

        <?php if (!empty($alert)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($alert); ?></div>
        <?php endif; ?>

        <?php if (empty($_SESSION['wishlist'])): ?>
            <div style="background: white; padding: 60px 20px; text-align: center; border-radius: 12px; border: 1px dashed var(--border);">
                <div style="font-size: 48px; margin-bottom: 12px;">❤️</div>
                <h3>Your Wishlist is Empty</h3>
                <p style="color: var(--gray-muted); margin-top: 6px;">Add books you are interested in reading to save them here.</p>
                <a href="q3.php" class="btn btn-primary" style="margin-top: 16px;">Explore Catalog</a>
            </div>
        <?php else: ?>
            <section class="wishlist-grid">
                <?php foreach ($_SESSION['wishlist'] as $bId): 
                    if (!isset($allBooks[$bId])) continue;
                    $b = $allBooks[$bId];
                ?>
                    <article class="book-card">
                        <div class="book-image-wrapper">
                            <img src="<?php echo htmlspecialchars($b['image']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>">
                        </div>
                        <div class="book-details">
                            <span class="book-category"><?php echo htmlspecialchars($b['category'] ?? 'General'); ?></span>
                            <h3 class="book-title"><?php echo htmlspecialchars($b['title']); ?></h3>
                            <p class="book-author">By <?php echo htmlspecialchars($b['author']); ?></p>

                            <div class="book-price-row">
                                <span class="price">$<?php echo number_format($b['price'], 2); ?></span>
                                <div style="display: flex; gap: 6px;">
                                    <a href="q17.php?action=move_to_cart&id=<?php echo $bId; ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">
                                        🛒 Move to Cart
                                    </a>
                                    <a href="q17.php?action=remove&id=<?php echo $bId; ?>" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;" title="Remove from Wishlist">
                                        &times;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 17: Wishlist Feature).
    </footer>

</body>
</html>
