<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 17
// Objective: Use cookies to implement a "Remember Me" option on the login form
//            and display a personalized welcome message.
// File: q17.php
// ============================================================================

session_start();

$cookie_name = "remember_user";
$cookie_disp_name = "remember_name";

$message = "";
$message_type = "info";

// Handle "Forget Me" action to remove cookie
if (isset($_GET['action']) && $_GET['action'] === 'forget') {
    setcookie($cookie_name, '', time() - 3600, "/");
    setcookie($cookie_disp_name, '', time() - 3600, "/");
    unset($_COOKIE[$cookie_name]);
    unset($_COOKIE[$cookie_disp_name]);
    $message = "Your remember cookie has been deleted from your browser.";
    $message_type = "success";
}

// Handle Login with "Remember Me"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_btn'])) {
    $email = trim($_POST['email'] ?? '');
    $remember = isset($_POST['remember_me']);

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Derive clean name from email
        $parts = explode('@', $email);
        $display_name = ucwords(str_replace('.', ' ', $parts[0]));

        if ($remember) {
            // Set Cookie for 30 Days with HttpOnly flag
            $expiry = time() + (30 * 24 * 60 * 60);
            setcookie($cookie_name, $email, $expiry, "/", "", false, true);
            setcookie($cookie_disp_name, $display_name, $expiry, "/", "", false, true);
            $_COOKIE[$cookie_name] = $email;
            $_COOKIE[$cookie_disp_name] = $display_name;
            $message = "Login successful! 'Remember Me' cookie stored for 30 days.";
        } else {
            // Clear existing remember cookie if unticked
            setcookie($cookie_name, '', time() - 3600, "/");
            setcookie($cookie_disp_name, '', time() - 3600, "/");
            unset($_COOKIE[$cookie_name]);
            unset($_COOKIE[$cookie_disp_name]);
            $message = "Login successful! Remember Me was not selected.";
        }
        $message_type = "success";
    } else {
        $message = "Please enter a valid email address.";
        $message_type = "error";
    }
}

// Check for remembered user cookie
$has_cookie = isset($_COOKIE[$cookie_name]);
$saved_email = $has_cookie ? $_COOKIE[$cookie_name] : '';
$saved_name = isset($_COOKIE[$cookie_disp_name]) ? $_COOKIE[$cookie_disp_name] : 'Returning User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q17: "Remember Me" Cookie Implementation - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-box {
            max-width: 460px;
            margin: 0 auto;
        }
        .welcome-banner {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(99, 102, 241, 0.15));
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            user-select: none;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .remember-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q16.php">&larr; Q16: XSS Prevention</a>
            <a href="q17.php" style="color: var(--accent); font-weight: 700;">Q17: Remember Me</a>
            <a href="q18.php">Q18: Shopping Cart &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 17</div>
            <h1>Persistent "Remember Me" Cookie Authentication</h1>
            <p class="subtitle">Persisting user identity across browser restarts with PHP <code>setcookie()</code> and rendering personalized greeting banners.</p>
        </div>

        <div class="login-box">
            <!-- Personalized Welcome Banner if Cookie Exists -->
            <?php if ($has_cookie): ?>
                <div class="welcome-banner">
                    <div>
                        <div style="font-weight: 700; color: #10b981; font-size: 1.1rem;">
                            👋 Welcome Back, <?= htmlspecialchars($saved_name) ?>!
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                            Recognized via HTTP cookie (<code><?= htmlspecialchars($saved_email) ?></code>)
                        </div>
                    </div>
                    <a href="q17.php?action=forget" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                        Forget Me
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <form action="q17.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($saved_email ?: 'arjun.sharma@example.com') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" 
                               value="Secret123" required>
                    </div>

                    <div class="form-group">
                        <label class="remember-checkbox">
                            <input type="checkbox" name="remember_me" id="remember_me" <?= $has_cookie ? 'checked' : '' ?>>
                            <span>Remember my login on this device for 30 days</span>
                        </label>
                    </div>

                    <button type="submit" name="login_btn" class="btn btn-primary" style="width: 100%;">
                        Sign In &amp; Save Preference
                    </button>
                </form>
            </div>

            <!-- Cookie Architecture Card -->
            <div class="card" style="margin-top: 1.5rem;">
                <h3>Cookie Syntax &amp; Security Attributes</h3>
                <div class="code-box">
<span class="comment">// 30-Day Cookie Expiry Calculation</span>
<span class="keyword">$expiry</span> = <span class="function">time</span>() + (30 * 24 * 60 * 60);

<span class="comment">// Set-Cookie: remember_user; HttpOnly; SameSite=Lax</span>
<span class="function">setcookie</span>(
    <span class="string">'remember_user'</span>,  <span class="comment">// Cookie Name</span>
    <span class="keyword">$email</span>,          <span class="comment">// Value</span>
    <span class="keyword">$expiry</span>,         <span class="comment">// Expiry Timestamp</span>
    <span class="string">'/'</span>,             <span class="comment">// Path (Entire Domain)</span>
    <span class="string">''</span>,              <span class="comment">// Domain</span>
    <span class="keyword">false</span>,           <span class="comment">// Secure (true in HTTPS)</span>
    <span class="keyword">true</span>             <span class="comment">// HttpOnly (Prevents XSS theft)</span>
);
                </div>
            </div>
        </div>
    </div>
</body>
</html>
