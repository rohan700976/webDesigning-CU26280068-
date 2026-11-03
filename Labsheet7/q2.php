<?php
require_once __DIR__ . '/db.php';

$message = "";
$messageType = "";

// Check if user clicked to execute the schema in MySQL
if (isset($_POST['execute_schema'])) {
    try {
        $pdoRoot = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $sql = file_get_contents(__DIR__ . '/q2.sql');
        $pdoRoot->exec($sql);
        $message = "✓ Success: Database 'bookstore_db' and tables ('books', 'users', 'orders', 'order_items', 'reviews', 'wishlist') created & seeded successfully!";
        $messageType = "alert-success";
    } catch (PDOException $e) {
        $message = "Notice / Error: " . htmlspecialchars($e->getMessage()) . " (Ensure MySQL is running in XAMPP or verify credentials in db.php)";
        $messageType = "alert-danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q2 - MySQL Database Schema for Bookstore</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .sql-viewer-box {
            background: #0f172a;
            color: #38bdf8;
            padding: 24px;
            border-radius: 12px;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 13.5px;
            line-height: 1.6;
            overflow-x: auto;
            max-height: 500px;
            border: 1px solid #334155;
            margin-bottom: 24px;
        }

        .schema-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .table-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .table-title {
            font-size: 16px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .column-list {
            list-style: none;
            font-size: 13px;
            color: #475569;
        }
        .column-list li {
            padding: 4px 0;
            border-bottom: 1px solid #f1f5f9;
            font-family: monospace;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 2:</strong> MySQL database schema for bookstore with tables for `books`, `users`, and `orders`</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <main class="container">
        <header style="margin-bottom: 30px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 8px;">MySQL Bookstore Database Schema</h1>
            <p style="color: var(--gray-muted);">Defines normalized tables with primary keys, foreign key constraints, indexes, and initial catalog seed data.</p>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Key Schema Tables Summary -->
        <section class="schema-cards">
            <div class="table-card">
                <div class="table-title">📄 Table: books</div>
                <ul class="column-list">
                    <li><strong>id:</strong> INT AUTO_INCREMENT [PK]</li>
                    <li><strong>title:</strong> VARCHAR(255) NOT NULL</li>
                    <li><strong>author:</strong> VARCHAR(150) NOT NULL</li>
                    <li><strong>price:</strong> DECIMAL(10,2) NOT NULL</li>
                    <li><strong>stock:</strong> INT DEFAULT 0</li>
                    <li><strong>image:</strong> VARCHAR(500)</li>
                    <li><strong>category:</strong> VARCHAR(50)</li>
                </ul>
            </div>

            <div class="table-card">
                <div class="table-title">👤 Table: users</div>
                <ul class="column-list">
                    <li><strong>id:</strong> INT AUTO_INCREMENT [PK]</li>
                    <li><strong>username:</strong> VARCHAR(50) UNIQUE</li>
                    <li><strong>email:</strong> VARCHAR(100) UNIQUE</li>
                    <li><strong>password:</strong> VARCHAR(255) [Hashed]</li>
                    <li><strong>full_name:</strong> VARCHAR(100)</li>
                    <li><strong>role:</strong> ENUM('customer', 'admin')</li>
                </ul>
            </div>

            <div class="table-card">
                <div class="table-title">📦 Table: orders &amp; order_items</div>
                <ul class="column-list">
                    <li><strong>id:</strong> INT AUTO_INCREMENT [PK]</li>
                    <li><strong>user_id:</strong> INT [FK -> users.id]</li>
                    <li><strong>total_amount:</strong> DECIMAL(10,2)</li>
                    <li><strong>shipping_address:</strong> TEXT</li>
                    <li><strong>status:</strong> ENUM(Pending, Shipped...)</li>
                    <li><strong>order_date:</strong> TIMESTAMP</li>
                </ul>
            </div>
        </section>

        <!-- Execute Schema Form -->
        <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px;">
            <h3 style="margin-bottom: 10px;">Execute / Deploy Database Schema to Local MySQL</h3>
            <p style="color: var(--gray-muted); font-size: 14px; margin-bottom: 16px;">
                Clicking the button below will connect to MySQL on <code>localhost</code> (user: <code>root</code>) and execute <code>q2.sql</code> to create the <code>bookstore_db</code> database and seed tables.
            </p>
            <form method="POST">
                <button type="submit" name="execute_schema" class="btn btn-primary">
                    ⚡ Execute &amp; Deploy Schema to MySQL
                </button>
                <a href="q2.sql" download class="btn btn-secondary" style="margin-left: 10px;">Download q2.sql</a>
            </form>
        </div>

        <h3 style="margin-bottom: 12px;">Full SQL Schema Definition (<code>q2.sql</code>):</h3>
        <pre class="sql-viewer-box"><?php echo htmlspecialchars(file_get_contents(__DIR__ . '/q2.sql')); ?></pre>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 2: MySQL Schema).
    </footer>

</body>
</html>
