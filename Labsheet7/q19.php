<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q19 - Consistent External CSS Design System</title>
    <!-- Linking external bookstore CSS framework -->
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .token-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .token-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow);
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 19:</strong> Style entire bookstore application consistently using external CSS framework (<code>bookstore.css</code>)</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <!-- Reusable Consistent Navigation -->
    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q3.php">Books Catalog</a></li>
                <li><a href="q19.php" class="active">Design System</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 28px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">BookVault External Design System</h1>
            <p style="color: var(--gray-muted);">Reusable, tokenized CSS components ensuring visual and ergonomic consistency across all 21 bookstore modules via <code>bookstore.css</code>.</p>
        </header>

        <!-- Design Tokens Showcase -->
        <section class="token-grid">
            <div class="token-card">
                <h3 style="margin-bottom: 12px; font-size: 16px;">Color Palette &amp; Variables</h3>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <div style="background: #2563eb; color: white; padding: 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">--primary<br>#2563eb</div>
                    <div style="background: #16a34a; color: white; padding: 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">--success<br>#16a34a</div>
                    <div style="background: #f59e0b; color: white; padding: 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">--accent<br>#f59e0b</div>
                    <div style="background: #dc2626; color: white; padding: 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">--danger<br>#dc2626</div>
                </div>
            </div>

            <div class="token-card">
                <h3 style="margin-bottom: 12px; font-size: 16px;">Button Components</h3>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <button class="btn btn-primary">Primary</button>
                    <button class="btn btn-secondary">Secondary</button>
                    <button class="btn btn-success">Success</button>
                    <button class="btn btn-danger">Danger</button>
                </div>
            </div>

            <div class="token-card">
                <h3 style="margin-bottom: 12px; font-size: 16px;">Status Badges</h3>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <span class="badge badge-success">Delivered</span>
                    <span class="badge badge-warning">Pending</span>
                    <span class="badge badge-info">In Stock</span>
                    <span class="badge badge-danger">Out of Stock</span>
                </div>
            </div>
        </section>

        <!-- Alert Notification System -->
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 14px;">Notification Alerts</h2>
        <div class="alert alert-success">✓ Success Alert: Order placed and verified in transaction logs.</div>
        <div class="alert alert-info">ℹ Information Alert: 15 copies remaining in bookstore inventory.</div>
        <div class="alert alert-danger">⚠️ Danger Alert: Invalid credentials or payment rejection message.</div>

        <!-- Sample Book Card Layout Preview -->
        <h2 style="font-size: 20px; font-weight: 700; margin: 30px 0 14px;">Reusable Book Card Component</h2>
        <div style="max-width: 300px;">
            <article class="book-card">
                <div class="book-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80" alt="Book Demo">
                </div>
                <div class="book-details">
                    <span class="book-category">Technology</span>
                    <h3 class="book-title">The Pragmatic Programmer</h3>
                    <p class="book-author">By David Thomas &amp; Andrew Hunt</p>
                    <div class="book-price-row">
                        <span class="price">$34.50</span>
                        <a href="#" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">+ Add to Cart</a>
                    </div>
                </div>
            </article>
        </div>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 19: External CSS Framework).
    </footer>

</body>
</html>
