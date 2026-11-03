<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 6
// Objective: Implement a secure logout script that destroys the session,
//            clears session variables, and removes the session cookie.
// File: q6.php
// ============================================================================

session_start();

$was_logged_in = isset($_SESSION['user_id']);
$previous_user = $_SESSION['user_name'] ?? 'User';

// ----------------------------------------------------------------------------
// SECURE LOGOUT STEP 1: Clear all Session Variables in Memory
// ----------------------------------------------------------------------------
$_SESSION = array();

// ----------------------------------------------------------------------------
// SECURE LOGOUT STEP 2: Invalidate & Delete the Session Cookie in Client Browser
// ----------------------------------------------------------------------------
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000, // Expire timestamp far into the past
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ----------------------------------------------------------------------------
// SECURE LOGOUT STEP 3: Destroy Server-Side Session Storage
// ----------------------------------------------------------------------------
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q6: Secure Session Destruction (Logout) - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .logout-box {
            max-width: 600px;
            margin: 2rem auto;
            text-align: center;
        }
        .step-list {
            text-align: left;
            margin: 1.5rem 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .step-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .step-icon {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            font-weight: 700;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q5.php">&larr; Q5: Dashboard</a>
            <a href="q6.php" style="color: var(--accent); font-weight: 700;">Q6: Logout</a>
            <a href="q7.php">Q7: Insert Product &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 6</div>
            <h1>Secure Logout &amp; Cookie Invalidation</h1>
            <p class="subtitle">Complete 3-stage PHP session termination conforming to OWASP security guidelines.</p>
        </div>

        <div class="logout-box">
            <div class="card">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">🔒</div>
                <h2 style="color: #10b981; margin-bottom: 0.5rem;">You Have Been Logged Out</h2>
                <p style="color: var(--text-muted);">
                    <?php if ($was_logged_in): ?>
                        Session for <strong><?= htmlspecialchars($previous_user) ?></strong> has been completely destroyed.
                    <?php else: ?>
                        No active session was present or session has already expired.
                    <?php endif; ?>
                </p>

                <div class="step-list">
                    <div class="step-item">
                        <div class="step-icon">1</div>
                        <div>
                            <strong>Reset Global Array:</strong> <code>$_SESSION = array();</code>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                                Clears in-memory keys so downstream logic in the current request cannot read stale session data.
                            </p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-icon">2</div>
                        <div>
                            <strong>Expire Browser Cookie:</strong> <code>setcookie(session_name(), '', time() - 42000, ...)</code>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                                Forces the browser client to purge the <code>PHPSESSID</code> cookie header immediately.
                            </p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-icon">3</div>
                        <div>
                            <strong>Destroy Server Session File:</strong> <code>session_destroy();</code>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                                Completely removes the temporary session file/record from the server's disk storage.
                            </p>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem;">
                    <a href="q4.php" class="btn btn-primary">Sign In Again (Q4)</a>
                    <a href="q5.php" class="btn btn-secondary">Test Accessing Protected Dashboard (Q5)</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
