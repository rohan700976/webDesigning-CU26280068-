<?php
// Lab Sheet 9 - Question 1: Database Design for webapp_db
$pageTitle = "Q1 - Database Schema Design (webapp_db)";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | COER University</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .schema-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .table-schema-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .table-schema-header {
            background: #0f172a;
            color: #ffffff;
            padding: 12px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: var(--font-mono);
            font-size: 14px;
        }
        .schema-col-list {
            padding: 12px 18px;
            list-style: none;
            font-size: 13.5px;
        }
        .schema-col-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
        }
        .schema-col-list li:last-child {
            border-bottom: none;
        }
        .col-key {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .pk { background: #fef3c7; color: #b45309; }
        .fk { background: #e0e7ff; color: #4338ca; }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>COER University - Lab Sheet 9:</strong> Question 1 (Database Schema Design)</span>
        <a href="index.php">&larr; Back to Lab Sheet 9 Index</a>
    </div>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand">
                <span class="brand-badge">LS9</span>
                <span>webapp_db Schema</span>
            </a>
            <div class="nav-links">
                <a href="q1.php" class="active">Q1: Schema</a>
                <a href="q2.php">Q2: Connection</a>
                <a href="q3.php">Q3: Register</a>
                <a href="q4.php">Q4: Login</a>
            </div>
        </div>
    </nav>

    <main class="container">
        
        <div class="card">
            <div class="card-header">
                <div>
                    <span class="badge badge-primary">MySQL Architecture</span>
                    <h1 class="card-title" style="margin-top: 6px;">1. Design MySQL Database: <code>webapp_db</code></h1>
                </div>
                <a href="q1.sql" download class="btn btn-primary btn-sm">Download q1.sql</a>
            </div>
            <p style="color: var(--muted); font-size: 14.5px;">
                Designed relational database schema <code>webapp_db</code> incorporating normalized tables (<code>users</code>, <code>products</code>, <code>orders</code>, and <code>order_items</code>) with primary keys, foreign key constraints (CASCADE / RESTRICT), and indexing for fast query execution.
            </p>
        </div>

        <!-- VISUAL SCHEMA ARCHITECTURE -->
        <div class="schema-grid">
            
            <!-- USERS TABLE -->
            <div class="table-schema-card">
                <div class="table-schema-header">
                    <span>&#128100; Table: users</span>
                    <span class="badge badge-success" style="font-size: 10px;">InnoDB</span>
                </div>
                <ul class="schema-col-list">
                    <li>
                        <strong>id</strong>
                        <span><span class="col-key pk">PK</span> INT UNSIGNED AI</span>
                    </li>
                    <li>
                        <span>name</span>
                        <span style="color: var(--muted);">VARCHAR(100) NOT NULL</span>
                    </li>
                    <li>
                        <span>email</span>
                        <span style="color: var(--muted);">VARCHAR(150) UNIQUE</span>
                    </li>
                    <li>
                        <span>password</span>
                        <span style="color: var(--muted);">VARCHAR(255) (Bcrypt)</span>
                    </li>
                    <li>
                        <span>role</span>
                        <span style="color: var(--muted);">ENUM('admin','customer')</span>
                    </li>
                    <li>
                        <span>created_at</span>
                        <span style="color: var(--muted);">TIMESTAMP DEFAULT NOW</span>
                    </li>
                </ul>
            </div>

            <!-- PRODUCTS TABLE -->
            <div class="table-schema-card">
                <div class="table-schema-header">
                    <span>&#128230; Table: products</span>
                    <span class="badge badge-success" style="font-size: 10px;">InnoDB</span>
                </div>
                <ul class="schema-col-list">
                    <li>
                        <strong>id</strong>
                        <span><span class="col-key pk">PK</span> INT UNSIGNED AI</span>
                    </li>
                    <li>
                        <span>name</span>
                        <span style="color: var(--muted);">VARCHAR(150) NOT NULL</span>
                    </li>
                    <li>
                        <span>description</span>
                        <span style="color: var(--muted);">TEXT NULL</span>
                    </li>
                    <li>
                        <span>category</span>
                        <span style="color: var(--muted);">VARCHAR(100) INDEX</span>
                    </li>
                    <li>
                        <span>price</span>
                        <span style="color: var(--muted);">DECIMAL(10,2) NOT NULL</span>
                    </li>
                    <li>
                        <span>stock</span>
                        <span style="color: var(--muted);">INT UNSIGNED (DEFAULT 0)</span>
                    </li>
                    <li>
                        <span>image_path</span>
                        <span style="color: var(--muted);">VARCHAR(255)</span>
                    </li>
                </ul>
            </div>

            <!-- ORDERS TABLE -->
            <div class="table-schema-card">
                <div class="table-schema-header">
                    <span>&#128203; Table: orders</span>
                    <span class="badge badge-success" style="font-size: 10px;">InnoDB</span>
                </div>
                <ul class="schema-col-list">
                    <li>
                        <strong>id</strong>
                        <span><span class="col-key pk">PK</span> INT UNSIGNED AI</span>
                    </li>
                    <li>
                        <strong>user_id</strong>
                        <span><span class="col-key fk">FK</span> INT &rarr; users(id)</span>
                    </li>
                    <li>
                        <span>total_amount</span>
                        <span style="color: var(--muted);">DECIMAL(10,2) NOT NULL</span>
                    </li>
                    <li>
                        <span>order_status</span>
                        <span style="color: var(--muted);">ENUM('pending','completed')</span>
                    </li>
                    <li>
                        <span>created_at</span>
                        <span style="color: var(--muted);">TIMESTAMP DEFAULT NOW</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- SQL CODE DISPLAY -->
        <div class="card" style="margin-top: 30px;">
            <div class="card-header">
                <h2 class="card-title">Complete DDL SQL Script (q1.sql)</h2>
                <button class="btn btn-secondary btn-sm" onclick="copySql()">Copy SQL</button>
            </div>
            <pre class="code-box" id="sqlBox">
CREATE DATABASE IF NOT EXISTS `webapp_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `webapp_db`;

-- USERS TABLE
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PRODUCTS TABLE
CREATE TABLE `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `category` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `stock` INT UNSIGNED NOT NULL DEFAULT 0,
    `image_path` VARCHAR(255) DEFAULT 'uploads/default_product.jpg',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ORDERS TABLE WITH FOREIGN KEY
CREATE TABLE `orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `order_status` ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'completed',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            </pre>
        </div>

    </main>

    <footer class="lab-footer">
        &copy; <?php echo date('Y'); ?> COER University &bull; Web Designing Lab Sheet 9 &bull; Question 1
    </footer>

    <script>
        function copySql() {
            const text = document.getElementById('sqlBox').innerText;
            navigator.clipboard.writeText(text);
            alert('SQL Script copied to clipboard!');
        }
    </script>
</body>
</html>
