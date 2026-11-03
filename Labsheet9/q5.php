<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 5
// Objective: Create a protected dashboard page that redirects unauthenticated
//            visitors to the login page using session checks.
// File: q5.php
// ============================================================================

session_start();

// Strict Authentication Session Check
if (!isset($_SESSION['user_id'])) {
    // Visitor is unauthenticated! Redirect immediately to Login (q4.php)
    header("Location: q4.php?redirected=1");
    exit();
}

// User is authenticated
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$user_email = $_SESSION['user_email'] ?? 'N/A';
$user_role = $_SESSION['user_role'] ?? 'customer';
$logged_in_time = isset($_SESSION['logged_in_time']) ? date("Y-m-d H:i:s", $_SESSION['logged_in_time']) : 'Active Session';
$session_id = session_id();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q5: Protected Dashboard - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .user-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(168, 85, 247, 0.15));
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .user-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            margin-right: 1.25rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.25rem;
        }
        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-val {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-main);
            margin-top: 0.35rem;
        }
        .session-inspector {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 1rem;
            font-family: var(--font-mono);
            font-size: 0.85rem;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q4.php">&larr; Q4: Login</a>
            <a href="q5.php" style="color: var(--accent); font-weight: 700;">Q5: Dashboard</a>
            <a href="q6.php">Q6: Logout &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 5</div>
            <h1>Protected Member Dashboard</h1>
            <p class="subtitle">Enforcing session-based route security via PHP <code>$_SESSION['user_id']</code> validation.</p>
        </div>

        <div class="alert alert-success">
            <strong>Access Granted:</strong> Valid active session detected. Direct unauthenticated visits are automatically intercepted and redirected to <code>q4.php</code>.
        </div>

        <div class="user-banner">
            <div style="display: flex; align-items: center;">
                <div class="user-avatar">
                    <?= strtoupper(substr($user_name, 0, 1)) ?>
                </div>
                <div>
                    <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><?= htmlspecialchars($user_name) ?></h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($user_email) ?></p>
                    <span class="badge" style="background: <?= $user_role === 'admin' ? 'rgba(239, 68, 68, 0.2)' : 'rgba(99, 102, 241, 0.2)' ?>; color: <?= $user_role === 'admin' ? '#ef4444' : '#6366f1' ?>;">
                        Role: <?= strtoupper(htmlspecialchars($user_role)) ?>
                    </span>
                </div>
            </div>
            <div>
                <a href="q6.php" class="btn btn-danger">End Session &amp; Logout (Q6)</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">User ID</div>
                <div class="stat-val">#<?= htmlspecialchars((string)$user_id) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Session Lifetime</div>
                <div class="stat-val"><?= $logged_in_time ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Security Status</div>
                <div class="stat-val" style="color: #10b981;">Verified</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Quick Hub</div>
                <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem;">
                    <a href="q7.php" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Add Product</a>
                    <a href="q18.php" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Cart</a>
                </div>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="card">
                <h3>Live Session State ($_SESSION)</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                    Internal server-side session hash ID: <code><?= htmlspecialchars($session_id) ?></code>
                </p>
                <div class="session-inspector">
                    <pre><?= htmlspecialchars(print_r($_SESSION, true)) ?></pre>
                </div>
            </div>

            <div class="card">
                <h3>Session Protection Guard Code</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                    Standard secure guard placed at the top of every restricted PHP page:
                </p>
                <div class="code-box">
<span class="comment">// 1. Always start session first</span>
<span class="function">session_start</span>();

<span class="comment">// 2. Check if identity key exists in session</span>
<span class="keyword">if</span> (!<span class="keyword">isset</span>(<span class="keyword">$_SESSION</span>[<span class="string">'user_id'</span>])) {
    <span class="comment">// 3. Redirect unauthenticated visitors</span>
    <span class="function">header</span>(<span class="string">"Location: q4.php?redirected=1"</span>);
    <span class="keyword">exit</span>();
}

<span class="comment">// 4. Render protected content</span>
<span class="keyword">$user_id</span> = <span class="keyword">$_SESSION</span>[<span class="string">'user_id'</span>];
                </div>
            </div>
        </div>
    </div>
</body>
</html>
