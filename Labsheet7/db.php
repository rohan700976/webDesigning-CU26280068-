<?php
/**
 * Shared Database Connection & Mock Fallback for Lab Sheet 7
 * Online Bookstore System
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookstore_db');

/**
 * Get Database Connection via PDO
 * @return PDO|null
 */
function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            // Return null if database server is not reachable
            $pdo = null;
        }
    }
    return $pdo;
}

/**
 * Standard Mock Books Data (used when local MySQL is not running or tables empty)
 */
function getMockBooks() {
    return [
        1 => [
            'id' => 1,
            'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
            'author' => 'Robert C. Martin',
            'category' => 'Technology',
            'price' => 29.99,
            'stock' => 15,
            'rating' => 4.8,
            'image' => 'https://images.unsplash.com/photo-1532012164546-f432f2e3777f?auto=format&fit=crop&w=600&q=80',
            'description' => 'Even bad code can function. But if code isn\'t clean, it can bring a development organization to its knees. A must-read for professional developers.'
        ],
        2 => [
            'id' => 2,
            'title' => 'The Pragmatic Programmer: Your Journey to Mastery',
            'author' => 'David Thomas & Andrew Hunt',
            'category' => 'Technology',
            'price' => 34.50,
            'stock' => 10,
            'rating' => 4.9,
            'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80',
            'description' => 'Filled with technical and architectural advice, this classic teaches software developers how to create efficient, maintainable software systems.'
        ],
        3 => [
            'id' => 3,
            'title' => 'Atomic Habits: An Easy & Proven Way to Build Good Habits',
            'author' => 'James Clear',
            'category' => 'Self-Help',
            'price' => 18.25,
            'stock' => 25,
            'rating' => 4.9,
            'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=600&q=80',
            'description' => 'No matter your goals, Atomic Habits offers a proven framework for improving every day with small changes that lead to remarkable results.'
        ],
        4 => [
            'id' => 4,
            'title' => 'Designing Data-Intensive Applications',
            'author' => 'Martin Kleppmann',
            'category' => 'Technology',
            'price' => 42.00,
            'stock' => 8,
            'rating' => 4.9,
            'image' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=600&q=80',
            'description' => 'The definitive guide to understanding storage engines, distributed consensus, data replication, and high-scale architectures.'
        ],
        5 => [
            'id' => 5,
            'title' => 'Deep Work: Rules for Focused Success in a Distracted World',
            'author' => 'Cal Newport',
            'category' => 'Business',
            'price' => 21.00,
            'stock' => 12,
            'rating' => 4.7,
            'image' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=600&q=80',
            'description' => 'Deep work is the ability to focus without distraction on a cognitively demanding task. Learn how to master this skill to thrive.'
        ],
        6 => [
            'id' => 6,
            'title' => 'The Psychology of Money',
            'author' => 'Morgan Housel',
            'category' => 'Business',
            'price' => 19.50,
            'stock' => 20,
            'rating' => 4.8,
            'image' => 'https://images.unsplash.com/photo-1553729459-efe14ef6055d?auto=format&fit=crop&w=600&q=80',
            'description' => 'Doing well with money isn\'t necessarily about what you know. It\'s about how you behave. Timeless lessons on wealth, greed, and happiness.'
        ]
    ];
}

/**
 * Initialize default session data if needed
 */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [
        [
            'id' => 1001,
            'user' => 'demo_student',
            'items_count' => 2,
            'total' => 64.49,
            'address' => 'COER Campus, Roorkee, UK',
            'status' => 'Delivered',
            'date' => '2026-02-15 14:30:00'
        ],
        [
            'id' => 1002,
            'user' => 'demo_student',
            'items_count' => 1,
            'total' => 34.50,
            'address' => 'Civil Lines, Haridwar, UK',
            'status' => 'Shipped',
            'date' => '2026-02-28 10:15:00'
        ]
    ];
}
