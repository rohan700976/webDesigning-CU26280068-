<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 15
// Objective: Demonstrate protection against SQL injection by comparing
//            a vulnerable query with a prepared-statement version.
// File: q15.php
// ============================================================================

require_once 'db_connect.php';

// Get simulated or user input
$user_input = $_GET['payload'] ?? "' OR '1'='1";

// ----------------------------------------------------------------------------
// 1. VULNERABLE APPROACH: Direct String Concatenation
// ----------------------------------------------------------------------------
$vulnerable_sql = "SELECT id, name, email, role FROM users WHERE email = '" . $user_input . "'";

// ----------------------------------------------------------------------------
// 2. SECURE APPROACH: Parameterized Prepared Statement
// ----------------------------------------------------------------------------
$secure_sql_template = "SELECT id, name, email, role FROM users WHERE email = :email";

// Mock records to display if database is offline, or live execution
$vuln_results = [];
$vuln_error = "";
$secure_results = [];
$secure_error = "";

if ($db_connected && $pdo) {
    // Execute Vulnerable (for educational demo safely sandboxed)
    try {
        $vuln_stmt = $pdo->query($vulnerable_sql);
        $vuln_results = $vuln_stmt ? $vuln_stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        $vuln_error = $e->getMessage();
    }

    // Execute Secure Prepared Statement
    try {
        $secure_stmt = $pdo->prepare($secure_sql_template);
        $secure_stmt->execute([':email' => $user_input]);
        $secure_results = $secure_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $secure_error = $e->getMessage();
    }
} else {
    // Demonstration Simulation
    if (strpos($user_input, "' OR '1'='1") !== false || strpos($user_input, "' OR 1=1") !== false) {
        $vuln_results = [
            ['id' => 1, 'name' => 'Arjun Sharma', 'email' => 'arjun.sharma@example.com', 'role' => 'admin'],
            ['id' => 2, 'name' => 'Priya Patel', 'email' => 'priya.patel@example.com', 'role' => 'customer'],
            ['id' => 3, 'name' => 'Rohan Kumar', 'email' => 'rohan.kumar@example.com', 'role' => 'customer']
        ];
    } elseif ($user_input === "arjun.sharma@example.com") {
        $vuln_results = [
            ['id' => 1, 'name' => 'Arjun Sharma', 'email' => 'arjun.sharma@example.com', 'role' => 'admin']
        ];
        $secure_results = [
            ['id' => 1, 'name' => 'Arjun Sharma', 'email' => 'arjun.sharma@example.com', 'role' => 'admin']
        ];
    }
    // Secure results remain empty for injection strings because no user has email "' OR '1'='1"
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q15: SQL Injection Prevention Demonstration - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .comparison-col {
            border-radius: var(--border-radius);
            padding: 1.5rem;
            border: 1px solid var(--border-color);
        }
        .col-vulnerable {
            background: rgba(239, 68, 68, 0.05);
            border-color: rgba(239, 68, 68, 0.3);
        }
        .col-secure {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.3);
        }
        .payload-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-family: var(--font-mono);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }
        .payload-btn:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.15);
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q14.php">&larr; Q14: Image Upload</a>
            <a href="q15.php" style="color: var(--accent); font-weight: 700;">Q15: SQL Injection Demo</a>
            <a href="q16.php">Q16: XSS Prevention &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 15</div>
            <h1>SQL Injection: Vulnerability vs. Prepared Statements</h1>
            <p class="subtitle">Comparing catastrophic SQL concatenation against secure parameterized compilation side-by-side.</p>
        </div>

        <!-- Interactive Payload Tester -->
        <div class="card" style="margin-bottom: 2rem;">
            <form action="q15.php" method="GET">
                <div class="form-group">
                    <label for="payload">Test Input Value (Email Parameter)</label>
                    <div style="display: flex; gap: 0.75rem;">
                        <input type="text" id="payload" name="payload" class="form-control" 
                               value="<?= htmlspecialchars($user_input) ?>" style="font-family: var(--font-mono); font-size: 0.95rem;">
                        <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Execute Comparison</button>
                    </div>
                </div>

                <div style="margin-top: 0.75rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span style="font-size: 0.8rem; color: var(--text-muted);">Quick Presets:</span>
                    <a href="?payload=' OR '1'='1" class="payload-btn">Payload 1: ' OR '1'='1 (Tautology Bypass)</a>
                    <a href="?payload=admin' -- " class="payload-btn">Payload 2: admin' -- (Comment Truncation)</a>
                    <a href="?payload=arjun.sharma@example.com" class="payload-btn">Legitimate: arjun.sharma@example.com</a>
                </div>
            </form>
        </div>

        <!-- Side by Side Comparison Grid -->
        <div class="grid grid-2">
            <!-- Left: Vulnerable -->
            <div class="comparison-col col-vulnerable">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="color: #ef4444; margin: 0;">❌ Insecure (Concatenated)</h3>
                    <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444;">Vulnerable</span>
                </div>

                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Resulting Raw SQL String Sent to MySQL:</p>
                <div class="code-box" style="border-color: rgba(239, 68, 68, 0.3);">
<span class="comment">// String interpolation breaks boundary</span>
<span class="keyword">$sql</span> = <span class="string">"SELECT * FROM users WHERE email = '<?= htmlspecialchars($user_input) ?>'"</span>;
                </div>

                <div style="margin-top: 1.25rem;">
                    <strong>Database Output (Records Exposed):</strong>
                    <div style="margin-top: 0.5rem; background: #0f172a; padding: 0.75rem; border-radius: 6px; font-family: var(--font-mono); font-size: 0.8rem; max-height: 180px; overflow-y: auto;">
                        <?php if (!empty($vuln_error)): ?>
                            <span style="color: #ef4444;">Syntax Error: <?= htmlspecialchars($vuln_error) ?></span>
                        <?php elseif (empty($vuln_results)): ?>
                            <span style="color: var(--text-muted);">0 records returned.</span>
                        <?php else: ?>
                            <span style="color: #ef4444; font-weight: bold;">⚠️ <?= count($vuln_results) ?> Record(s) Leaked! (Bypass Successful)</span><br><br>
                            <?php foreach ($vuln_results as $u): ?>
                                <div>&bull; #<?= htmlspecialchars((string)$u['id']) ?> | <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>) - <?= htmlspecialchars($u['role']) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="margin-top: 1rem; font-size: 0.85rem; color: #fca5a5; line-height: 1.5;">
                    The unescaped single quote <code>'</code> closes the string literal, and <code>OR '1'='1</code> turns the entire WHERE clause condition into an unconditional <strong>TRUE</strong>, dumping the entire table.
                </div>
            </div>

            <!-- Right: Secure -->
            <div class="comparison-col col-secure">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="color: #10b981; margin: 0;">✅ Secure (Prepared Statement)</h3>
                    <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">Protected</span>
                </div>

                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Compiled AST SQL Template + Parameter:</p>
                <div class="code-box" style="border-color: rgba(16, 185, 129, 0.3);">
<span class="keyword">$stmt</span> = <span class="keyword">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM users WHERE email = :email"</span>);
<span class="keyword">$stmt</span>-><span class="function">execute</span>([<span class="string">':email'</span> => <span class="keyword">$user_input</span>]);
                </div>

                <div style="margin-top: 1.25rem;">
                    <strong>Database Output (Strict Evaluation):</strong>
                    <div style="margin-top: 0.5rem; background: #0f172a; padding: 0.75rem; border-radius: 6px; font-family: var(--font-mono); font-size: 0.8rem; max-height: 180px; overflow-y: auto;">
                        <?php if (empty($secure_results)): ?>
                            <span style="color: #10b981; font-weight: 600;">🛡️ 0 Records Returned. (Neutralized!)</span><br>
                            <span style="color: var(--text-muted); font-size: 0.75rem;">Database literally looked for an email matching string: "<?= htmlspecialchars($user_input) ?>"</span>
                        <?php else: ?>
                            <span style="color: #10b981; font-weight: bold;">✅ Legitimate User Match (1 Record):</span><br><br>
                            <?php foreach ($secure_results as $u): ?>
                                <div>&bull; #<?= htmlspecialchars((string)$u['id']) ?> | <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>) - <?= htmlspecialchars($u['role']) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="margin-top: 1rem; font-size: 0.85rem; color: #a7f3d0; line-height: 1.5;">
                    The database engine parses and compiles the query abstract syntax tree (AST) <strong>before</strong> receiving parameters. The input payload is treated strictly as data bytes, never as executable code.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
