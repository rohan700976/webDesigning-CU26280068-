<?php
// Lab Sheet 9 - Question 2: Database Connection with require_once & Error Handling
$pageTitle = "Q2 - Database Connection Module";

// Include the connection file
require_once 'db_connect.php';

$connectionInfo = [];
if ($db_connected && $pdo) {
    try {
        $serverVersion = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
        $driverName = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $connectionStatus = $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        $connectionInfo = [
            'status' => 'ONLINE',
            'driver' => $driverName,
            'version' => $serverVersion,
            'details' => $connectionStatus,
            'database' => $db_name
        ];
    } catch (Exception $e) {
        $connectionInfo = ['status' => 'ERROR', 'msg' => $e->getMessage()];
    }
} else {
    $connectionInfo = [
        'status' => 'OFFLINE_OR_PENDING',
        'msg' => $db_error_message ?: 'MySQL service is not running on localhost:3306 or webapp_db has not yet been imported.'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | COER University</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>COER University - Lab Sheet 9:</strong> Question 2 (PHP Database Connection with require_once)</span>
        <a href="index.php">&larr; Back to Lab Sheet 9 Index</a>
    </div>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand">
                <span class="brand-badge">LS9</span>
                <span>Database Connection</span>
            </a>
            <div class="nav-links">
                <a href="q1.php">Q1: Schema</a>
                <a href="q2.php" class="active">Q2: Connection</a>
                <a href="q3.php">Q3: Register</a>
                <a href="q4.php">Q4: Login</a>
            </div>
        </div>
    </nav>

    <main class="container">
        
        <div class="card">
            <div class="card-header">
                <div>
                    <span class="badge badge-primary">Backend Foundation</span>
                    <h1 class="card-title" style="margin-top: 6px;">2. PHP Database Connection with Error Handling</h1>
                </div>
                <span class="badge <?php echo $db_connected ? 'badge-success' : 'badge-warning'; ?>">
                    <?php echo $db_connected ? '● Connected' : '○ Standby / Demo Mode'; ?>
                </span>
            </div>
            <p style="color: var(--muted); font-size: 14.5px;">
                Implemented <code>db_connect.php</code> using PDO with UTF-8 character encoding, exception mode enabled (<code>ERRMODE_EXCEPTION</code>), and persistent prepared statements.
            </p>
        </div>

        <!-- CONNECTION DIAGNOSTICS CARD -->
        <div class="card">
            <h2 class="card-title" style="margin-bottom: 16px;">Connection Diagnostics</h2>
            
            <?php if ($db_connected): ?>
                <div class="alert alert-success">
                    <span>&#10004;</span>
                    <div>
                        <strong>Database Handshake Successful!</strong> Connected to MySQL database <code><?php echo htmlspecialchars($db_name); ?></code> via PDO.
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <tbody>
                            <tr>
                                <th>Driver Protocol</th>
                                <td><?php echo htmlspecialchars($connectionInfo['driver']); ?></td>
                            </tr>
                            <tr>
                                <th>Server Version</th>
                                <td><?php echo htmlspecialchars($connectionInfo['version']); ?></td>
                            </tr>
                            <tr>
                                <th>Target Database</th>
                                <td><code><?php echo htmlspecialchars($connectionInfo['database']); ?></code></td>
                            </tr>
                            <tr>
                                <th>Connection Status</th>
                                <td><?php echo htmlspecialchars($connectionInfo['details']); ?></td>
                            </tr>
                            <tr>
                                <th>Error Handling Mode</th>
                                <td><code>PDO::ERRMODE_EXCEPTION</code> (Automatic Rollbacks on Fatal Errors)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <span>&#9432;</span>
                    <div>
                        <strong>Connection Standby Notice:</strong><br>
                        <code><?php echo htmlspecialchars($connectionInfo['msg']); ?></code>
                        <div style="margin-top: 6px; font-size: 13px;">
                            To connect your live MySQL server: Start XAMPP/WAMP MySQL, create <code>webapp_db</code>, and import <code>q1.sql</code>. All pages in Lab Sheet 9 automatically fallback to resilient demo mode when MySQL is paused.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- CODE IMPLEMENTATION SNIPPET -->
        <div class="card">
            <h2 class="card-title">Implementation Pattern: <code>require_once 'db_connect.php';</code></h2>
            <pre class="code-box">
&lt;?php
// db_connect.php
$dsn = "mysql:host=localhost;dbname=webapp_db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, "root", "", $options);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database service temporarily unavailable. Please try again later.");
}
?&gt;

// Any other script (e.g. q3.php, q4.php)
&lt;?php
require_once 'db_connect.php';
// $pdo is now safely accessible across the page scope!
?&gt;
            </pre>
        </div>

    </main>

    <footer class="lab-footer">
        &copy; <?php echo date('Y'); ?> COER University &bull; Web Designing Lab Sheet 9 &bull; Question 2
    </footer>

</body>
</html>
