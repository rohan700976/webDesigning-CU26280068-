<?php
require_once __DIR__ . '/db.php';

$message = "";
$messageType = "";

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    unset($_SESSION['role']);
    session_regenerate_id(true);
    $message = "You have been logged out securely.";
    $messageType = "alert-info";
}

// Handle User Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['reg_username'] ?? '');
    $email = trim($_POST['reg_email'] ?? '');
    $fullName = trim($_POST['reg_fullname'] ?? '');
    $password = $_POST['reg_password'] ?? '';

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Secure password hashing with cryptographic salt
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $pdo = getDBConnection();

        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (:u, :e, :p, :fn, 'customer')");
                $stmt->execute([
                    ':u' => $username,
                    ':e' => $email,
                    ':p' => $hashedPassword,
                    ':fn' => $fullName
                ]);
                $message = "✓ Account created successfully! You can now log in below.";
                $messageType = "alert-success";
            } catch (PDOException $e) {
                $message = "Registration Error: " . htmlspecialchars($e->getMessage());
                $messageType = "alert-danger";
            }
        } else {
            // Simulated memory registration for environments without active MySQL
            $_SESSION['demo_registered_users'][$username] = [
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'full_name' => $fullName,
                'role' => 'customer'
            ];
            $message = "✓ Simulated Account Registered for '{$username}'! (Password hashed with BCRYPT). You can now log in.";
            $messageType = "alert-success";
        }
    } else {
        $message = "Please fill in all registration fields.";
        $messageType = "alert-danger";
    }
}

// Handle User Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['login_username'] ?? '');
    $password = $_POST['login_password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $pdo = getDBConnection();
        $authenticatedUser = null;

        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
                $stmt->execute([':u' => $username]);
                $user = $stmt->fetch();
                if ($user && password_verify($password, $user['password'])) {
                    $authenticatedUser = $user;
                }
            } catch (PDOException $e) {
                // fall through
            }
        }

        // Check fallback demo users if DB is offline
        if (!$authenticatedUser) {
            if ($username === 'admin' && $password === 'password123') {
                $authenticatedUser = ['id' => 1, 'username' => 'admin', 'role' => 'admin', 'full_name' => 'Administrator'];
            } elseif (isset($_SESSION['demo_registered_users'][$username])) {
                $demoUser = $_SESSION['demo_registered_users'][$username];
                if (password_verify($password, $demoUser['password'])) {
                    $authenticatedUser = ['id' => 99, 'username' => $demoUser['username'], 'role' => 'customer', 'full_name' => $demoUser['full_name']];
                }
            }
        }

        if ($authenticatedUser) {
            // Secure Session Management: Regenerate Session ID to mitigate session fixation attacks
            session_regenerate_id(true);
            $_SESSION['user_id'] = $authenticatedUser['id'];
            $_SESSION['username'] = $authenticatedUser['username'];
            $_SESSION['role'] = $authenticatedUser['role'] ?? 'customer';

            $message = "✓ Authentication successful! Welcome, " . htmlspecialchars($authenticatedUser['username']) . ".";
            $messageType = "alert-success";
        } else {
            $message = "✗ Authentication failed: Invalid username or password. (Hint: username: <code>admin</code> / password: <code>password123</code>)";
            $messageType = "alert-danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q5 - User Registration &amp; Login with Password Hashing</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .auth-container {
            max-width: 900px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        @media (max-width: 768px) {
            .auth-container { grid-template-columns: 1fr; }
        }
        .auth-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 30px;
            box-shadow: var(--shadow);
        }
        .auth-card h2 {
            font-size: 20px;
            margin-bottom: 8px;
            color: var(--dark);
        }
        .auth-card p.sub {
            color: var(--gray-muted);
            font-size: 13.5px;
            margin-bottom: 20px;
        }
        .session-info-card {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 5:</strong> User registration and login in PHP with password hashing &amp; secure session management</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q3.php">Books</a></li>
                <li><a href="q5.php" class="active">Account</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">User Authentication &amp; Sessions</h1>
            <p style="color: var(--gray-muted);">Powered by <code>password_hash()</code>, <code>password_verify()</code>, and <code>session_regenerate_id(true)</code>.</p>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['username'])): ?>
            <!-- Active Logged In Session State -->
            <div class="session-info-card">
                <h3 style="color: #1e40af; margin-bottom: 8px;">🎉 Active Authenticated Session</h3>
                <p style="font-size: 15px; margin-bottom: 12px;">
                    Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
                    (Role: <span class="badge badge-info"><?php echo htmlspecialchars($_SESSION['role']); ?></span>) &bull;
                    Session ID: <code><?php echo session_id(); ?></code>
                </p>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="q12.php" class="btn btn-secondary">View Order History</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="q8.php" class="btn btn-primary">Open Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="q5.php?action=logout" class="btn btn-danger">Log Out Securely</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="auth-container">
            <!-- 1. User Registration Form -->
            <div class="auth-card">
                <h2>Create New Account</h2>
                <p class="sub">Passwords are secured with the BCRYPT hashing algorithm.</p>

                <form method="POST">
                    <input type="hidden" name="register" value="1">
                    
                    <div class="form-group">
                        <label for="reg_fullname">Full Name</label>
                        <input type="text" id="reg_fullname" name="reg_fullname" class="form-control" placeholder="e.g. Rohan Sharma" required>
                    </div>

                    <div class="form-group">
                        <label for="reg_username">Username</label>
                        <input type="text" id="reg_username" name="reg_username" class="form-control" placeholder="e.g. rohan_dev" required>
                    </div>

                    <div class="form-group">
                        <label for="reg_email">Email Address</label>
                        <input type="email" id="reg_email" name="reg_email" class="form-control" placeholder="user@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="reg_password">Password</label>
                        <input type="password" id="reg_password" name="reg_password" class="form-control" placeholder="Choose a strong password" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Register Account</button>
                </form>
            </div>

            <!-- 2. User Login Form -->
            <div class="auth-card">
                <h2>User Login</h2>
                <p class="sub">Verify credentials against hashed database records.</p>

                <form method="POST">
                    <input type="hidden" name="login" value="1">

                    <div class="form-group">
                        <label for="login_username">Username</label>
                        <input type="text" id="login_username" name="login_username" class="form-control" placeholder="Enter username" value="admin" required>
                    </div>

                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <input type="password" id="login_password" name="login_password" class="form-control" placeholder="Enter password" value="password123" required>
                    </div>

                    <p style="font-size: 12.5px; color: var(--gray-muted); margin-bottom: 16px;">
                        Demo Credentials: User: <code>admin</code> | Pass: <code>password123</code>
                    </p>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Log In</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 5: Registration &amp; Login).
    </footer>

</body>
</html>
