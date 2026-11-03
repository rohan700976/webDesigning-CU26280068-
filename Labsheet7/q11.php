<?php
require_once __DIR__ . '/db.php';

$pdo = getDBConnection();
$perPage = 3; // 3 books per page to cleanly demonstrate pagination
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;

$books = [];
$totalBooks = 0;

if ($pdo) {
    try {
        // Count total books
        $countStmt = $pdo->query("SELECT COUNT(*) FROM books");
        $totalBooks = intval($countStmt->fetchColumn());

        // Fetch limited books with LIMIT and OFFSET
        $stmt = $pdo->prepare("SELECT * FROM books ORDER BY id ASC LIMIT :lim OFFSET :off");
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $books = $stmt->fetchAll();
    } catch (PDOException $e) {
        $books = [];
    }
}

// Fallback for memory data
if (empty($books) && !$pdo) {
    $all = array_values(getMockBooks());
    $totalBooks = count($all);
    $books = array_slice($all, $offset, $perPage);
}

$totalPages = max(1, ceil($totalBooks / $perPage));
if ($currentPage > $totalPages) $currentPage = $totalPages;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q11 - Book Catalog Pagination in PHP</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .pagination-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 40px;
        }
        .page-link {
            padding: 9px 16px;
            border-radius: 8px;
            background: white;
            border: 1px solid var(--border);
            color: var(--dark);
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .page-link:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .page-link.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .page-link.disabled {
            opacity: 0.5;
            pointer-events: none;
            background: #f1f5f9;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 11:</strong> Pagination in PHP to display limited books per page with page number links</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q11.php" class="active">Pagination Demo</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 12px;">
            <div>
                <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Paginated Books Catalog</h1>
                <p style="color: var(--gray-muted);">Displaying <?php echo $perPage; ?> books per page. Current Page: <strong><?php echo $currentPage; ?> of <?php echo $totalPages; ?></strong> (Total: <?php echo $totalBooks; ?> items).</p>
            </div>
            <span class="badge badge-info">SQL: LIMIT <?php echo $perPage; ?> OFFSET <?php echo $offset; ?></span>
        </header>

        <!-- Book Grid -->
        <section class="book-grid">
            <?php foreach ($books as $b): ?>
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
                            <a href="q4.php?id=<?php echo $b['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Details</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <!-- Pagination Controls -->
        <nav class="pagination-nav">
            <!-- Previous Button -->
            <a href="q11.php?page=<?php echo $currentPage - 1; ?>" class="page-link <?php if ($currentPage <= 1) echo 'disabled'; ?>">&larr; Previous</a>

            <!-- Numeric Page Links -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="q11.php?page=<?php echo $i; ?>" class="page-link <?php if ($i == $currentPage) echo 'active'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <!-- Next Button -->
            <a href="q11.php?page=<?php echo $currentPage + 1; ?>" class="page-link <?php if ($currentPage >= $totalPages) echo 'disabled'; ?>">Next &rarr;</a>
        </nav>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 11: Pagination).
    </footer>

</body>
</html>
