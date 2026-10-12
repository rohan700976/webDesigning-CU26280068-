<?php
/**
 * Question 3:
 * Install and connect to the MySQL database server via command line or graphical interface (e.g., 
 * phpMyAdmin), creating a new database named `college_db`.
 */

// Database credentials
$host = "localhost";
$username = "root";
$password = ""; // Default password in XAMPP is empty

$message = "";
$statusClass = "";

try {
    // Connect to MySQL server without selecting database
    $conn = new mysqli($host, $username, $password);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // SQL query to create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS college_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        $message = "Successfully connected to MySQL! Database `college_db` is created and ready for use.";
        $statusClass = "success";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }

    $conn->close();
} catch (Exception $e) {
    $message = "Notice / Error: " . $e->getMessage() . "<br><small>If MySQL server is currently not running, start MySQL in XAMPP Control Panel.</small>";
    $statusClass = "warning";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 3 - Connect MySQL &amp; Create college_db</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; color: #1e293b; }
        .box { max-width: 750px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: 500; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        pre { background: #1e293b; color: #38bdf8; padding: 15px; border-radius: 6px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Question 3: MySQL Connection &amp; `college_db` Database Creation</h2>

        <div class="alert <?php echo $statusClass; ?>">
            <?php echo $message; ?>
        </div>

        <h3>1. Command-Line Instructions (MySQL CLI):</h3>
        <pre>-- Step 1: Login to MySQL CLI
mysql -u root -p

-- Step 2: Create database
CREATE DATABASE college_db;

-- Step 3: Verify created database
SHOW DATABASES;

-- Step 4: Use database
USE college_db;</pre>

        <h3>2. Graphical Interface (phpMyAdmin):</h3>
        <p>Navigate to <code>http://localhost/phpmyadmin</code> &rarr; Click <strong>Databases</strong> &rarr; Enter database name <code>college_db</code> &rarr; Click <strong>Create</strong>.</p>
    </div>
</body>
</html>
