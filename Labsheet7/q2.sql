-- =======================================================
-- COER University - Web Designing Lab Sheet 7
-- Question 2: MySQL Database Schema for Bookstore
-- Tables: books, users, orders, order_items, reviews, wishlist
-- =======================================================

CREATE DATABASE IF NOT EXISTS `bookstore_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `bookstore_db`;

-- 1. Table: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('customer', 'admin') DEFAULT 'customer',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Table: books
-- (id, title, author, price, stock, image, category, description)
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) DEFAULT 'General',
    `price` DECIMAL(10, 2) NOT NULL,
    `stock` INT NOT NULL DEFAULT 0,
    `image` VARCHAR(500) DEFAULT NULL,
    `rating` DECIMAL(2, 1) DEFAULT 5.0,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Table: orders
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_email` VARCHAR(100) NOT NULL,
    `shipping_address` TEXT NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('Pending', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    `payment_method` VARCHAR(50) DEFAULT 'Credit Card',
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Table: order_items
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `book_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10, 2) NOT NULL,
    `subtotal` DECIMAL(10, 2) NOT NULL,
    CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_items_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Table: reviews (User reviews and star ratings)
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `book_id` INT NOT NULL,
    `user_name` VARCHAR(100) NOT NULL,
    `rating` INT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reviews_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Table: wishlist (User personal saved books)
DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `book_id` INT NOT NULL,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user_book` (`user_id`, `book_id`),
    CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_wishlist_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =======================================================
-- SEED DATA INSERTION
-- =======================================================

-- Seed Administrator & Customer Users
-- Default passwords: 'password123' (hashed using BCRYPT)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'admin@bookvault.com', '$2y$10$eA3p8eF0g5o1mPqL8I.J4.gUv6T.7O5dYvN1N5KxQJv2v3n4o5p6q', 'Administrator', 'admin'),
('rohan_user', 'rohan@example.com', '$2y$10$eA3p8eF0g5o1mPqL8I.J4.gUv6T.7O5dYvN1N5KxQJv2v3n4o5p6q', 'Rohan Sharma', 'customer');

-- Seed Books
INSERT INTO `books` (`title`, `author`, `category`, `price`, `stock`, `image`, `rating`, `description`) VALUES
('Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'Technology', 29.99, 15, 'https://images.unsplash.com/photo-1532012164546-f432f2e3777f?auto=format&fit=crop&w=600&q=80', 4.8, 'Even bad code can function. But if code isn\'t clean, it can bring a development organization to its knees.'),
('The Pragmatic Programmer: Your Journey to Mastery', 'David Thomas & Andrew Hunt', 'Technology', 34.50, 10, 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80', 4.9, 'Filled with technical and architectural advice, this classic teaches software developers how to create efficient, maintainable software systems.'),
('Atomic Habits: An Easy & Proven Way to Build Good Habits', 'James Clear', 'Self-Help', 18.25, 25, 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=600&q=80', 4.9, 'No matter your goals, Atomic Habits offers a proven framework for improving every day with small changes that lead to remarkable results.'),
('Designing Data-Intensive Applications', 'Martin Kleppmann', 'Technology', 42.00, 8, 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=600&q=80', 4.9, 'The definitive guide to understanding storage engines, distributed consensus, data replication, and high-scale architectures.'),
('Deep Work: Rules for Focused Success in a Distracted World', 'Cal Newport', 'Business', 21.00, 12, 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=600&q=80', 4.7, 'Deep work is the ability to focus without distraction on a cognitively demanding task.'),
('The Psychology of Money', 'Morgan Housel', 'Business', 19.50, 20, 'https://images.unsplash.com/photo-1553729459-efe14ef6055d?auto=format&fit=crop&w=600&q=80', 4.8, 'Doing well with money isn\'t necessarily about what you know. It\'s about how you behave.');
