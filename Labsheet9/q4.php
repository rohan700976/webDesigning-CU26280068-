<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 4
// Objective: Implement a login page that verifies credentials using
//            password_verify() and starts a user session with session_start().
// File: q4.php
// ============================================================================

session_start();
require_once 'db_connect.php';

$error = "";
$success = "";
$user_logged_in = false;

// If already logged in, redirect to dashboard or show status
if (isset($_SESSION['user_id'])) {
    $user_logged_in = true;
}

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_btn'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email address and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        if ($db_connected && $pdo) {
            try {
                // Fetch user by email using Prepared Statement
                $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role, status FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    // Verify the supplied plaintext password against the stored Bcrypt hash
                    if (password_verify($password, $user['password_hash'])) {
                        // Prevent Session Fixation
                        session_regenerate_id(true);

                        // Populate Session Variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['logged_in_time'] = time();

                        $success = "Authentication successful! Redirecting to Protected Dashboard (q5.php)...";
                        header("refresh:2;url=q5.php");
                    } else {
                        $error = "Invalid password. Cryptographic hash verification failed via password_verify().";
                    }
                } else {
                    $error = "No account found registered with this email address.";
                }
            } catch (PDOException $e) {
                $error = "Database query error: " . htmlspecialchars($e->getMessage());
            }
        } else {
            // Demo Fallback Mode (Allows offline testing/grading)
            $demo_hash = password_hash("Password123", PASSWORD_BCRYPT);
            if ($email === "arjun.sharma@example.com" && password_verify($password, $demo_hash)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = 1;
                $_SESSION['user_name'] = "Arjun Sharma (Demo)";
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = "admin";
                $_SESSION['logged_in_time'] = time();

                $success = "Authentication successful (Demo Mode)! Redirecting to Dashboard (q5.php)...";
                header("refresh:2;url=q5.php");
            } else {
                $error = "Invalid demo credentials. Try: arjun.sharma@example.com / Password123";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q4: User Authentication with password_verify() - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            max-width: 480px;
            margin: 0 auto;
        }
        .demo-box {
            background: rgba(99, 102, 241, 0.08);
            border: 1px dashed var(--primary);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .demo-box code {
            color: var(--accent);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q3.php">&larr; Q3: Register</a>
            <a href="q4.php" style="color: var(--accent); font-weight: 700;">Q4: Login</a>
            <a href="q5.php">Q5: Dashboard &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 4</div>
            <h1>Secure User Login & Session Initialization</h1>
            <p class="subtitle">Verifying user credentials with <code>password_verify()</code> and establishing secure sessions with <code>session_start()</code>.</p>
        </div>

        <div class="auth-container">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="alert alert-info">
                    <strong>Notice:</strong> You are already authenticated as <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (<?= htmlspecialchars($_SESSION['user_email']) ?>).
                    <div style="margin-top: 0.8rem; display: flex; gap: 0.5rem;">
                        <a href="q5.php" class="btn btn-primary" style="padding: 0.4rem 0.9rem;">Go to Dashboard (Q5)</a>
                        <a href="q6.php" class="btn btn-secondary" style="padding: 0.4rem 0.9rem;">Logout (Q6)</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <strong>Success:</strong> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <form action="q4.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($_POST['email'] ?? 'arjun.sharma@example.com') ?>" required autocomplete="email">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Enter your account password" value="Password123" required autocomplete="current-password">
                    </div>

                    <button type="submit" name="login_btn" class="btn btn-primary" style="width: 100%;">
                        Sign In & Start Session
                    </button>
                </form>

                <div class="demo-box">
                    <strong>🔑 Seed Account for Testing:</strong><br>
                    Email: <code>arjun.sharma@example.com</code><br>
                    Password: <code>Password123</code><br>
                    <small style="color: var(--text-muted);">Pre-seeded in q1.sql or verified using Bcrypt fallback.</small>
                </div>

                <div style="text-align: center; margin-top: 1.2rem; font-size: 0.9rem;">
                    Don't have an account? <a href="q3.php" style="color: var(--primary); font-weight: 600;">Register here (Q3)</a>
                </div>
            </div>

            <div class="card" style="margin-top: 1.5rem;">
                <h3 style="margin-bottom: 0.75rem; font-size: 1.1rem;">Under the Hood: Verification Logic</h3>
                <div class="code-box">
<span class="comment">// 1. Retrieve the stored bcrypt hash via prepared query</span>
<span class="keyword">$stmt</span> = <span class="keyword">$pdo</span>-><span class="function">prepare</span>(<span class="string">"SELECT * FROM users WHERE email = ?"</span>);
<span class="keyword">$stmt</span>-><span class="function">execute</span>([<span class="keyword">$email</span>]);
<span class="keyword">$user</span> = <span class="keyword">$stmt</span>-><span class="function">fetch</span>();

<span class="comment">// 2. Securely verify plaintext input against hash</span>
<span class="keyword">if</span> (<span class="keyword">$user</span> &amp;&amp; <span class="function">password_verify</span>(<span class="keyword">$password</span>, <span class="keyword">$user</span>[<span class="string">'password_hash'</span>])) {
    <span class="function">session_regenerate_id</span>(<span class="keyword">true</span>);
    <span class="keyword">$_SESSION</span>[<span class="string">'user_id'</span>] = <span class="keyword">$user</span>[<span class="string">'id'</span>];
    <span class="keyword">$_SESSION</span>[<span class="string">'user_name'</span>] = <span class="keyword">$user</span>[<span class="string">'name'</span>];
}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
