<?php
require_once __DIR__ . '/db.php';

$searchQuery = trim($_GET['query'] ?? '');
$selectedCategory = trim($_GET['category'] ?? '');
$sort = $_GET['sort'] ?? 'title_asc';

$pdo = getDBConnection();
$filteredBooks = [];

if ($pdo) {
    try {
        $sql = "SELECT * FROM books WHERE 1=1";
        $params = [];

        if (!empty($searchQuery)) {
            $sql .= " AND (title LIKE :q OR author LIKE :q)";
            $params[':q'] = "%" . $searchQuery . "%";
        }

        if (!empty($selectedCategory) && $selectedCategory !== 'All') {
            $sql .= " AND category = :cat";
            $params[':cat'] = $selectedCategory;
        }

        if ($sort === 'price_asc') {
            $sql .= " ORDER BY price ASC";
        } elseif ($sort === 'price_desc') {
            $sql .= " ORDER BY price DESC";
        } else {
            $sql .= " ORDER BY title ASC";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $filteredBooks = $stmt->fetchAll();
    } catch (PDOException $e) {
        $filteredBooks = [];
    }
}

// Fallback search algorithm for mock memory data
if (empty($filteredBooks) && !$pdo) {
    $all = getMockBooks();
    foreach ($all as $b) {
        $matchesQuery = empty($searchQuery) || 
                        stripos($b['title'], $searchQuery) !== false || 
                        stripos($b['author'], $searchQuery) !== false;
        
        $matchesCategory = empty($selectedCategory) || $selectedCategory === 'All' || 
                           strcasecmp($b['category'], $selectedCategory) === 0;

        if ($matchesQuery && $matchesCategory) {
            $filteredBooks[] = $b;
        }
    }

    if ($sort === 'price_asc') {
        usort($filteredBooks, fn($a, $b) => $a['price'] <=> $b['price']);
    } elseif ($sort === 'price_desc') {
        usort($filteredBooks, fn($a, $b) => $b['price'] <=> $a['price']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q10 - Search &amp; Filter Books Feature in PHP</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .filter-bar-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }
        .filter-form-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 14px;
            align-items: flex-end;
        }
        @media (max-width: 800px) {
            .filter-form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 10:</strong> Search &amp; filter feature in PHP allowing users to search books by title, author, or category</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q3.php">Books Catalog</a></li>
                <li><a href="q10.php" class="active">Search &amp; Filter</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Multi-Criteria Book Search</h1>
            <p style="color: var(--gray-muted);">Filter bookstore database dynamically using PHP prepared statements and SQL <code>LIKE</code> operators.</p>
        </header>

        <!-- Search & Filter Controls Form -->
        <section class="filter-bar-card">
            <form method="GET" class="filter-form-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Keywords (Title or Author)</label>
                    <input type="text" name="query" value="<?php echo htmlspecialchars($searchQuery); ?>" class="form-control" placeholder="e.g. 'Clean Code', 'Robert', 'Habits'...">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Category Filter</label>
                    <select name="category" class="form-control">
                        <option value="All" <?php if ($selectedCategory === 'All' || empty($selectedCategory)) echo 'selected'; ?>>All Categories</option>
                        <option value="Technology" <?php if ($selectedCategory === 'Technology') echo 'selected'; ?>>Technology</option>
                        <option value="Business" <?php if ($selectedCategory === 'Business') echo 'selected'; ?>>Business</option>
                        <option value="Self-Help" <?php if ($selectedCategory === 'Self-Help') echo 'selected'; ?>>Self-Help</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Sort By</label>
                    <select name="sort" class="form-control">
                        <option value="title_asc" <?php if ($sort === 'title_asc') echo 'selected'; ?>>Title (A-Z)</option>
                        <option value="price_asc" <?php if ($sort === 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php if ($sort === 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
                    <a href="q10.php" class="btn btn-secondary" style="height: 42px;">Reset</a>
                </div>
            </form>
        </section>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 700;">Matching Results</h2>
            <span class="badge badge-info">Found <?php echo count($filteredBooks); ?> Books</span>
        </div>

        <!-- Filtered Books Grid -->
        <?php if (empty($filteredBooks)): ?>
            <div style="background: white; padding: 50px 20px; text-align: center; border-radius: 12px; border: 1px dashed var(--border);">
                <h3>No books found matching your search query.</h3>
                <p style="color: var(--gray-muted); margin-top: 6px;">Try clearing filters or searching with general terms.</p>
            </div>
        <?php else: ?>
            <section class="book-grid">
                <?php foreach ($filteredBooks as $b): ?>
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
                                <a href="q4.php?id=<?php echo $b['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Details &rarr;</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 10: Search &amp; Filter Books).
    </footer>

</body>
</html>
