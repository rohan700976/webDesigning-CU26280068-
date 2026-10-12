<?php
/**
 * Question 15:
 * Write a PHP script demonstrating session management (`session_start()`, `$_SESSION`) to 
 * maintain user login state across multiple pages.
 */

// Step 1: Initialize PHP session
session_start();

// Handle login submission
if (isset($_POST['login'])) {
    $user = trim($_POST['username'] ?? '');
    if (!empty($user)) {
        $_SESSION['authenticated_user'] = $user;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
    }
}

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Unset all session variables
    $_SESSION = [];
    // Destroy session cookie if set
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    // Destroy session
    session_destroy();
    header("Location: q15.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 15 - PHP Session Management</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 550px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .user-panel { background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 20px; border-radius: 6px; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; }
        .btn-logout { background: #ef4444; color: white; margin-top: 15px; }
        .btn-login { background: #2563eb; color: white; width: 100%; padding: 10px; }
        input[type="text"] { width: 100%; padding: 10px; margin: 10px 0 15px 0; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="box">
        <h2>PHP Session Management Demo</h2>

        <?php if (isset($_SESSION['authenticated_user'])): ?>
            <!-- Authenticated State -->
            <div class="user-panel">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['authenticated_user']); ?>!</h3>
                <p>Session ID: <code><?php echo session_id(); ?></code></p>
                <p>Login Timestamp: <strong><?php echo $_SESSION['login_time']; ?></strong></p>
                <p>Your session is active and preserved across page reloads.</p>
                
                <a href="?action=logout" class="btn btn-logout">Log Out of Session</a>
            </div>
        <?php else: ?>
            <!-- Login Form -->
            <p>No active session found. Log in below to create an authenticated session.</p>
            <form action="" method="POST">
                <label for="username" style="font-weight: bold;">Enter Username:</label>
                <input type="text" id="username" name="username" placeholder="e.g. Aarav" required>
                <button type="submit" name="login" class="btn btn-login">Start Session &amp; Log In</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
