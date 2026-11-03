<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9: DATABASE CONNECTION MODULE
// File: db_connect.php
// Uses PDO (PHP Data Objects) with comprehensive error handling & try/catch
// Included across pages via: require_once 'db_connect.php';
// ============================================================================

// Database Credentials (Standard Local XAMPP/WAMP Defaults)
$db_host = "localhost";
$db_name = "webapp_db";
$db_user = "root";
$db_pass = "";
$db_port = "3306";
$charset = "utf8mb4";

$pdo = null;
$db_connected = false;
$db_error_message = "";

// Data Source Name (DSN)
$dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on SQL errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use genuine server-side prepared statements
    PDO::ATTR_TIMEOUT            => 3                       // 3-second connection timeout
];

try {
    // Attempt PDO Connection
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    $db_connected = true;
} catch (PDOException $e) {
    // Graceful error handling (Log error without exposing credentials)
    $db_connected = false;
    $db_error_message = $e->getMessage();
    error_log("Database Connection Error: " . $e->getMessage());
}

/**
 * Helper function to ensure database is available
 */
function check_db_connection() {
    global $db_connected, $db_error_message;
    if (!$db_connected) {
        return false;
    }
    return true;
}
?>
