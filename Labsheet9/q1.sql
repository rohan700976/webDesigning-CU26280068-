-- ============================================================================
-- COER UNIVERSITY - LAB SHEET 9: QUESTION 1
-- Database Name: webapp_db
-- Tables: users, products, orders, order_items
-- Includes: Primary Keys, Foreign Keys, Constraints, and Seed Data
-- ============================================================================

-- Step 1: Create Database
CREATE DATABASE IF NOT EXISTS `webapp_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `webapp_db`;

-- Step 2: Disable foreign key checks for clean re-creation
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- Step 3: Create 'users' Table
-- Holds registered customer and administrator accounts
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,  -- Stored using password_hash()
    `role` ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 4: Create 'products' Table
-- Holds inventory catalog for e-commerce store
CREATE TABLE `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `category` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `stock` INT UNSIGNED NOT NULL DEFAULT 0,
    `image_path` VARCHAR(255) DEFAULT 'uploads/default_product.jpg',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_products_category` (`category`),
    INDEX `idx_products_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 5: Create 'orders' Table
-- Defines relationship with users via Foreign Key
CREATE TABLE `orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `order_status` ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'completed',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 6: Create 'order_items' Table (Normalized line items)
CREATE TABLE `order_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10, 2) NOT NULL,
    CONSTRAINT `fk_items_order` 
        FOREIGN KEY (`order_id`) 
        REFERENCES `orders` (`id`) 
        ON DELETE CASCADE,
    CONSTRAINT `fk_items_product` 
        FOREIGN KEY (`product_id`) 
        REFERENCES `products` (`id`) 
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 7: Insert Sample Seed Data for immediate testing across Lab Sheet 9
-- Default sample password is 'Password123' hashed with BCRYPT
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Rohan Sharma', 'rohan@example.com', '$2y$10$e8m4f9wP5R0W8F7bQ4hSmeZ5kL0M1N2O3P4Q5R6S7T8U9V0W1X2Y3', 'admin'),
(2, 'Aditi Verma', 'aditi@example.com', '$2y$10$e8m4f9wP5R0W8F7bQ4hSmeZ5kL0M1N2O3P4Q5R6S7T8U9V0W1X2Y3', 'customer'),
(3, 'Vikram Singh', 'vikram@example.com', '$2y$10$e8m4f9wP5R0W8F7bQ4hSmeZ5kL0M1N2O3P4Q5R6S7T8U9V0W1X2Y3', 'customer');

INSERT INTO `products` (`id`, `name`, `description`, `category`, `price`, `stock`, `image_path`) VALUES
(1, 'Mechanical Gaming Keyboard', 'RGB Backlit mechanical keyboard with tactile blue switches.', 'Electronics', 49.99, 25, 'uploads/keyboard.jpg'),
(2, 'Wireless Ergonomic Mouse', '2.4GHz precision wireless optical mouse with silent clicks.', 'Electronics', 24.50, 40, 'uploads/mouse.jpg'),
(3, 'Noise-Cancelling Headphones', 'Over-ear Bluetooth headphones with active noise cancellation.', 'Audio', 89.99, 15, 'uploads/headphones.jpg'),
(4, 'Stainless Steel Thermal Flask', 'Double-walled insulated 1-liter water bottle.', 'Home & Living', 18.00, 50, 'uploads/flask.jpg'),
(5, 'Smart Fitness Tracker Band', 'Heart-rate monitor with AMOLED color display and water resistance.', 'Wearables', 34.99, 30, 'uploads/band.jpg'),
(6, 'USB-C Fast Charging Hub', '7-in-1 multi-port adapter with 4K HDMI and 100W Power Delivery.', 'Accessories', 29.99, 35, 'uploads/hub.jpg');

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `order_status`) VALUES
(1, 1, 74.49, 'completed'),
(2, 2, 89.99, 'completed');

INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 49.99),
(1, 2, 1, 24.50),
(2, 3, 1, 89.99);
