<?php
/**
 * Question 4:
 * Write a PHP script to establish a secure database connection to MySQL using PDO (PHP Data 
 * Objects) and verify successful connection status.
 */

$host = 'localhost';
$db   = 'college_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Recommended secure PDO configuration options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw PDOException on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Native prepared statements
];

$statusMessage = "";
$statusClass = "";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $statusMessage = "✓ Connection Established Successfully! Connected to MySQL database [college_db] via PDO.";
    $statusClass = "success";
} catch (PDOException $e) {
    // Graceful error handling without exposing credentials
    $statusMessage = "✗ Database Connection Notice: " . $e->getMessage() . "<br><small>(Ensure Apache & MySQL are running in XAMPP/WAMP)</small>";
    $statusClass = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 4 - Secure MySQL PDO Connection</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .card { max-width: 680px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .msg { padding: 15px; border-radius: 6px; font-weight: bold; margin-bottom: 20px; font-size: 15px; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        pre { background: #1e293b; color: #38bdf8; padding: 15px; border-radius: 6px; font-size: 13px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Secure MySQL PDO Connection Verification</h2>

        <div class="msg <?php echo $statusClass; ?>">
            <?php echo $statusMessage; ?>
        </div>

        <h3>PDO Connection Script Architecture:</h3>
        <pre>&lt;?php
$dsn = "mysql:host=localhost;dbname=college_db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            =&gt; PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE =&gt; PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   =&gt; false,
];

try {
    $pdo = new PDO($dsn, "root", "", $options);
    echo "Connection successful!";
} catch (PDOException $e) {
    die("Connection failed: " . $e-&gt;getMessage());
}
?&gt;</pre>
    </div>
</body>
</html>
