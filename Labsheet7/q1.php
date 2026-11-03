<?php
require_once __DIR__ . '/db.php';
$books = getMockBooks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q1 - Online Bookstore Homepage</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        /* Promotional Banner */
        .promo-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 50px 40px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3);
        }

        .promo-text {
            flex: 1 1 500px;
        }

        .promo-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .promo-title {
            font-size: 36px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .promo-desc {
            font-size: 16px;
            color: #dbeafe;
            margin-bottom: 24px;
            max-width: 580px;
        }

        /* Search Bar & Category Navigation */
        .search-category-section {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 40px;
            box-shadow: var(--shadow);
        }

        .search-form-row {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .search-input-field {
            flex: 1;
            padding: 12px 18px;
            font-size: 15px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s;
        }
        .search-input-field:focus {
            border-color: var(--primary);
        }

        .category-nav-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .category-pill {
            background: #f1f5f9;
            color: #475569;
            padding: 7px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .category-pill:hover,
        .category-pill.active {
            background: var(--primary);
            color: white;
        }

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .section-heading h2 {
            font-size: 24px;
            font-weight: 800;
            color: var(--dark);
        }

        .star-rating {
            color: #f59e0b;
            font-size: 13px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

    <!-- Question Context Banner -->
    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 1:</strong> Online bookstore homepage featuring featured books, search bar, category navigation &amp; promotional banner</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <!-- Bookstore Global Navigation -->
    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php" class="active">Home</a></li>
                <li><a href="q3.php">Books Catalog</a></li>
                <li><a href="q10.php">Search &amp; Filter</a></li>
                <li><a href="q17.php">Wishlist</a></li>
                <li><a href="q5.php">Account</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <!-- 1. Promotional Banner -->
        <section class="promo-banner">
            <div class="promo-text">
                <span class="promo-badge">🎉 Special Academic Semester Sale</span>
                <h1 class="promo-title">Expand Your Horizon with Award-Winning Titles</h1>
                <p class="promo-desc">Get up to 40% discount on Computer Science, Engineering, Business, and Personal Development bestsellers this week.</p>
                <a href="#featured-books" class="btn" style="background: white; color: #1e3a8a; font-weight: 700;">Explore Featured Titles</a>
            </div>
            <div style="font-size: 90px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));">📚</div>
        </section>

        <!-- 2. Search Bar & Category Navigation -->
        <section class="search-category-section">
            <form action="q10.php" method="GET" class="search-form-row">
                <input type="text" name="query" class="search-input-field" placeholder="Search by book title, author, or ISBN...">
                <button type="submit" class="btn btn-primary">Search Books</button>
            </form>

            <div class="category-nav-pills">
                <a href="q10.php" class="category-pill active">All Categories</a>
                <a href="q10.php?category=Technology" class="category-pill">Technology</a>
                <a href="q10.php?category=Self-Help" class="category-pill">Self-Help</a>
                <a href="q10.php?category=Business" class="category-pill">Business</a>
                <a href="q10.php?category=Science" class="category-pill">Science</a>
            </div>
        </section>

        <!-- 3. Featured Books Grid -->
        <section id="featured-books">
            <div class="section-heading">
                <h2>Featured Bestsellers</h2>
                <a href="q3.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">View All Books &rarr;</a>
            </div>

            <div class="book-grid">
                <?php foreach ($books as $b): ?>
                    <article class="book-card">
                        <div class="book-image-wrapper">
                            <img src="<?php echo htmlspecialchars($b['image']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>">
                        </div>
                        <div class="book-details">
                            <span class="book-category"><?php echo htmlspecialchars($b['category']); ?></span>
                            <h3 class="book-title"><?php echo htmlspecialchars($b['title']); ?></h3>
                            <p class="book-author">By <?php echo htmlspecialchars($b['author']); ?></p>
                            <div class="star-rating">★ ★ ★ ★ ★ <span style="color: #64748b; font-size: 12px;">(<?php echo $b['rating']; ?>)</span></div>
                            
                            <div class="book-price-row">
                                <span class="price">$<?php echo number_format($b['price'], 2); ?></span>
                                <div style="display: flex; gap: 6px;">
                                    <a href="q4.php?id=<?php echo $b['id']; ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Details</a>
                                    <a href="q6.php?action=add&id=<?php echo $b['id']; ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">+ Cart</a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 1: Online Bookstore Homepage).
    </footer>

</body>
</html>
