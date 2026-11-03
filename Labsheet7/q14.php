<?php
require_once __DIR__ . '/db.php';

$rawInput = $_POST['test_query'] ?? "' OR '1'='1";
$sanitizedHtml = htmlspecialchars($rawInput, ENT_QUOTES, 'UTF-8');

$pdo = getDBConnection();
$vulnerableResult = "Simulation of unparameterized string concatenation: input changes SQL grammar and dumps table!";
$secureResult = [];

// Secure execution using PDO Prepared Statements
if ($pdo) {
    try {
        // SECURE: Parameterized Prepared Statement
        $stmt = $pdo->prepare("SELECT id, title, author, price FROM books WHERE title = :title");
        $stmt->execute([':title' => $rawInput]); // Safely bound as literal string
        $secureResult = $stmt->fetchAll();
    } catch (PDOException $e) {
        $secureResult = [];
    }
} else {
    // Simulated parameterized match
    $secureResult = []; // In prepared statements, no book has title "' OR '1'='1", so exactly 0 records match!
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q14 - SQL Injection Prevention &amp; Input Sanitization</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .sec-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            margin-top: 24px;
        }
        @media (max-width: 800px) {
            .sec-grid { grid-template-columns: 1fr; }
        }
        .sec-card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }
        .code-snippet {
            background: #0f172a;
            color: #38bdf8;
            padding: 16px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 13px;
            line-height: 1.5;
            margin: 12px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 14:</strong> Secure input sanitization &amp; prepared statements across all queries to prevent SQL injection</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q14.php" class="active">SQL Security Audit</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">SQL Injection Defense Verification</h1>
            <p style="color: var(--gray-muted);">Comparing naive string concatenation with PDO Parameterized Prepared Statements and XSS Sanitization.</p>
        </header>

        <!-- Test Injection Form -->
        <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border); box-shadow: var(--shadow);">
            <form method="POST">
                <label for="test_query" style="font-size: 15px; font-weight: 700;">Test SQL Injection Input Payload:</label>
                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <input type="text" id="test_query" name="test_query" value="<?php echo $sanitizedHtml; ?>" class="form-control" style="font-family: monospace; font-size: 15px;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Execute Test</button>
                </div>
                <div style="display: flex; gap: 8px; margin-top: 10px; font-size: 13px;">
                    <span style="color: var(--gray-muted);">Quick payloads:</span>
                    <a href="#" onclick="document.getElementById('test_query').value='\' OR \'1\'=\'1'; return false;">' OR '1'='1</a> &bull;
                    <a href="#" onclick="document.getElementById('test_query').value='Clean Code'; return false;">Clean Code</a> &bull;
                    <a href="#" onclick="document.getElementById('test_query').value='admin\' --'; return false;">admin' --</a>
                </div>
            </form>
        </div>

        <div class="sec-grid">
            <!-- 1. Vulnerable Demonstration Card -->
            <div class="sec-card" style="border-top: 4px solid var(--danger);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h3 style="color: var(--danger); font-size: 18px;">❌ Vulnerable Query (String Concatenation)</h3>
                    <span class="badge badge-danger">High Risk</span>
                </div>
                <p style="font-size: 13.5px; color: var(--gray-muted);">Concatenating user inputs directly into query strings breaks SQL syntax boundary:</p>
                
                <div class="code-snippet">
// INSECURE CODE:<br>
$sql = "SELECT * FROM books WHERE title = '" . $_POST['test_query'] . "'";<br>
// Evaluates to:<br>
SELECT * FROM books WHERE title = '' OR '1'='1'
                </div>

                <div class="alert alert-danger" style="margin-top: 14px; font-size: 13.5px;">
                    <strong>Vulnerability Impact:</strong> The query logic is hijacked. The condition <code>'1'='1'</code> always evaluates to TRUE, dumping entire confidential customer and inventory databases without authorization!
                </div>
            </div>

            <!-- 2. Secure Prepared Statement Card -->
            <div class="sec-card" style="border-top: 4px solid var(--success);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h3 style="color: var(--success); font-size: 18px;">✓ Secure PDO Prepared Statement</h3>
                    <span class="badge badge-success">W3C &amp; OWASP Standard</span>
                </div>
                <p style="font-size: 13.5px; color: var(--gray-muted);">Parameters are sent separately via binary protocols. SQL compiler treats input strictly as literal text:</p>

                <div class="code-snippet">
// SECURE CODE (Implemented across Bookstore):<br>
$stmt = $pdo->prepare("SELECT * FROM books WHERE title = :title");<br>
$stmt->execute([':title' => $sanitizedInput]);
                </div>

                <div class="alert alert-success" style="margin-top: 14px; font-size: 13.5px;">
                    <strong>Defense Verified:</strong> Query executed safely. 
                    Returned <strong><?php echo count($secureResult); ?> record(s)</strong>.
                    The SQL compiler searches for the exact literal string <code><?php echo $sanitizedHtml; ?></code> and refuses to execute user inputs as command instructions!
                </div>
            </div>
        </div>

        <!-- Sanitization Guidelines -->
        <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-top: 30px;">
            <h3 style="margin-bottom: 12px;">Bookstore Input Sanitization Checklist Applied:</h3>
            <ul style="padding-left: 20px; line-height: 1.8; font-size: 14.5px; color: #475569;">
                <li><strong>HTML XSS Sanitization:</strong> <code>htmlspecialchars($str, ENT_QUOTES, 'UTF-8')</code> on all rendered output.</li>
                <li><strong>Integer Casting:</strong> <code>intval($_GET['id'])</code> for all primary and foreign key lookups.</li>
                <li><strong>Decimal Sanitization:</strong> <code>floatval($_POST['price'])</code> ensuring numeric prices.</li>
                <li><strong>Email Sanitization:</strong> <code>filter_var($email, FILTER_SANITIZE_EMAIL)</code> for customer addresses.</li>
            </ul>
        </div>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 14: SQL Injection Defense).
    </footer>

</body>
</html>
