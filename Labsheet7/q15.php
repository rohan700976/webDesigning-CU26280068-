<?php
require_once __DIR__ . '/db.php';

$bookId = isset($_GET['id']) ? intval($_GET['id']) : 1;
$allBooks = getMockBooks();
$book = $allBooks[$bookId] ?? $allBooks[1];

$pdo = getDBConnection();
$alert = "";

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $userName = trim($_POST['user_name'] ?? ($_SESSION['username'] ?? 'Anonymous'));
    $rating = intval($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');

    if (!empty($userName) && !empty($comment) && $rating >= 1 && $rating <= 5) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO reviews (book_id, user_name, rating, comment) VALUES (:bid, :un, :r, :c)");
                $stmt->execute([
                    ':bid' => $bookId,
                    ':un' => $userName,
                    ':r' => $rating,
                    ':c' => $comment
                ]);
                $alert = "✓ Thank you! Your {$rating}-star review has been published.";
            } catch (PDOException $e) {
                $alert = "Error: " . $e->getMessage();
            }
        } else {
            $_SESSION['book_reviews'][$bookId][] = [
                'user_name' => $userName,
                'rating' => $rating,
                'comment' => $comment,
                'created_at' => date('Y-m-d H:i')
            ];
            $alert = "✓ Thank you! Your {$rating}-star review has been published.";
        }
    }
}

// Fetch Reviews for this book
$reviews = [
    [
        'user_name' => 'Aarav Patel',
        'rating' => 5,
        'comment' => 'Transformed the way I write object-oriented code. A timeless guide that every engineering student must read!',
        'created_at' => '2026-02-10 11:20'
    ],
    [
        'user_name' => 'Sneha Gupta',
        'rating' => 5,
        'comment' => 'Concise, practical, and highly applicable in production software systems. Excellent condition and fast shipping.',
        'created_at' => '2026-02-18 16:45'
    ]
];

if (!empty($_SESSION['book_reviews'][$bookId])) {
    $reviews = array_merge($reviews, $_SESSION['book_reviews'][$bookId]);
}

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM reviews WHERE book_id = :bid ORDER BY id DESC");
        $stmt->execute([':bid' => $bookId]);
        $dbReviews = $stmt->fetchAll();
        if (!empty($dbReviews)) {
            $reviews = $dbReviews;
        }
    } catch (PDOException $e) {}
}

// Calculate Average Rating
$totalRating = 0;
foreach ($reviews as $r) {
    $totalRating += $r['rating'];
}
$avgRating = count($reviews) > 0 ? round($totalRating / count($reviews), 1) : 5.0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q15 - User Review &amp; Rating System</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .review-layout {
            display: grid;
            grid-template-columns: 1fr 1.6fr;
            gap: 36px;
        }
        @media (max-width: 800px) {
            .review-layout { grid-template-columns: 1fr; }
        }
        .review-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: var(--shadow);
        }
        .stars-gold {
            color: #f59e0b;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 15:</strong> User review and rating system where registered users leave star ratings and comments</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q4.php?id=<?php echo $bookId; ?>">&larr; Back to Book Details</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <!-- Book Header Banner -->
        <div style="background: white; border: 1px solid var(--border); border-radius: 14px; padding: 24px; display: flex; align-items: center; gap: 24px; margin-bottom: 30px;">
            <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="Cover" style="width: 80px; height: 110px; object-fit: cover; border-radius: 8px;">
            <div>
                <span class="badge badge-info"><?php echo htmlspecialchars($book['category']); ?></span>
                <h1 style="font-size: 24px; margin: 6px 0;"><?php echo htmlspecialchars($book['title']); ?></h1>
                <p style="color: var(--gray-muted); font-size: 14px;">By <?php echo htmlspecialchars($book['author']); ?></p>
                <div class="stars-gold" style="margin-top: 6px;">
                    ★★★★★ <strong style="color: var(--dark); margin-left: 6px;"><?php echo $avgRating; ?> / 5.0</strong>
                    <span style="color: var(--gray-muted); font-size: 13px;">(Based on <?php echo count($reviews); ?> customer reviews)</span>
                </div>
            </div>
        </div>

        <?php if (!empty($alert)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($alert); ?></div>
        <?php endif; ?>

        <div class="review-layout">
            <!-- 1. Submit Review Form -->
            <div style="background: white; border: 1px solid var(--border); border-radius: 14px; padding: 28px; height: fit-content; box-shadow: var(--shadow);">
                <h2 style="font-size: 18px; margin-bottom: 16px;">Write a Customer Review</h2>
                
                <form method="POST">
                    <input type="hidden" name="submit_review" value="1">

                    <div class="form-group">
                        <label for="user_name">Your Name / Reviewer</label>
                        <input type="text" id="user_name" name="user_name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['username'] ?? 'Rohan Sharma'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="rating">Star Rating</label>
                        <select id="rating" name="rating" class="form-control" style="font-size: 15px;">
                            <option value="5">★★★★★ 5 Stars (Exceptional)</option>
                            <option value="4">★★★★☆ 4 Stars (Very Good)</option>
                            <option value="3">★★★☆☆ 3 Stars (Average)</option>
                            <option value="2">★★☆☆☆ 2 Stars (Needs Improvement)</option>
                            <option value="1">★☆☆☆☆ 1 Star (Disappointing)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comment">Your Detailed Feedback &amp; Review</label>
                        <textarea id="comment" name="comment" rows="4" class="form-control" placeholder="What did you think of the concepts, explanations, and structure?" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Post Review</button>
                </form>
            </div>

            <!-- 2. Reviews Feed -->
            <div>
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 16px;">Verified Customer Reviews</h2>

                <?php if (empty($reviews)): ?>
                    <p style="color: var(--gray-muted);">No reviews posted yet. Be the first to review this book!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <article class="review-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <strong><?php echo htmlspecialchars($rev['user_name']); ?></strong>
                                <span style="font-size: 12px; color: var(--gray-muted);"><?php echo htmlspecialchars($rev['created_at'] ?? 'Verified Buyer'); ?></span>
                            </div>
                            <div class="stars-gold" style="margin-bottom: 10px;">
                                <?php echo str_repeat("★", $rev['rating']) . str_repeat("☆", 5 - $rev['rating']); ?>
                            </div>
                            <p style="font-size: 14.5px; color: #475569; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($rev['comment'])); ?>
                            </p>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 15: Review &amp; Rating System).
    </footer>

</body>
</html>
