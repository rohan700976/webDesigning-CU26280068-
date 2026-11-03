<?php
// Lab Sheet 9 - Question 3: User Registration with password_hash()
$pageTitle = "Q3 - User Registration & Secure Password Hashing";

require_once 'db_connect.php';

$errors = [];
$successMessage = "";
$name = "";
$email = "";
$debugHash = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Sanitize & trim inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // 2. Server-side Validations
    if (empty($name) || strlen($name) < 3) {
        $errors[] = "Full Name must be at least 3 characters long.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Password confirmation does not match.";
    }

    // 3. Database Duplicate Check & Insertion if no errors
    if (empty($errors)) {
        // Securely hash the password using standard Bcrypt
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $debugHash = $hashedPassword;

        if ($db_connected && $pdo) {
            try {
                // Check if email already exists
                $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                $stmtCheck->execute([$email]);
                
                if ($stmtCheck->fetch()) {
                    $errors[] = "An account with this email address already exists. Please login.";
                } else {
                    // Insert new user
                    $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
                    $stmtInsert->execute([$name, $email, $hashedPassword]);
                    $successMessage = "Account registered successfully for {$name}! Password securely encrypted with Bcrypt.";
                    $name = "";
                    $email = "";
                }
            } catch (PDOException $e) {
                $errors[] = "Database insertion error: " . $e->getMessage();
            }
        } else {
            // Simulated Success Mode (When running without active MySQL)
            $successMessage = "Demo Mode: Account successfully validated! In production, the password hash would be saved to MySQL.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | COER University</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 580px;
            margin: 0 auto;
        }
        .hash-preview-box {
            background: #0f172a;
            color: #38bdf8;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-family: var(--font-mono);
            font-size: 12.5px;
            word-break: break-all;
            margin-top: 14px;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>COER University - Lab Sheet 9:</strong> Question 3 (User Registration &amp; password_hash)</span>
        <a href="index.php">&larr; Back to Lab Sheet 9 Index</a>
    </div>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand">
                <span class="brand-badge">LS9</span>
                <span>User Registration</span>
            </a>
            <div class="nav-links">
                <a href="q2.php">Q2: DB Connect</a>
                <a href="q3.php" class="active">Q3: Register</a>
                <a href="q4.php">Q4: Login</a>
                <a href="q5.php">Q5: Dashboard</a>
            </div>
        </div>
    </nav>

    <main class="container">
        
        <div class="form-container">
            <div class="card">
                <div class="card-header">
                    <div>
                        <span class="badge badge-primary">Authentication</span>
                        <h1 class="card-title" style="margin-top: 6px;">3. User Registration Form</h1>
                    </div>
                    <span class="badge badge-dark">Bcrypt / Argon2</span>
                </div>

                <!-- Display Errors -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <div>
                            <strong>Registration Errors:</strong>
                            <ul style="margin-left: 20px; margin-top: 6px; font-size: 13.5px;">
                                <?php foreach ($errors as $err): ?>
                                    <li><?php echo htmlspecialchars($err); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Display Success Message -->
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success">
                        <span>&#10004;</span>
                        <div>
                            <strong>Success!</strong> <?php echo htmlspecialchars($successMessage); ?>
                            <div style="margin-top: 8px;">
                                <a href="q4.php" class="btn btn-primary btn-sm">Proceed to Login &rarr;</a>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($debugHash)): ?>
                        <div style="font-size: 13px; color: var(--muted); margin-bottom: 20px;">
                            <strong>Cryptographic Output (password_hash):</strong>
                            <div class="hash-preview-box"><?php echo htmlspecialchars($debugHash); ?></div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Registration Form -->
                <form action="q3.php" method="POST" novalidate>
                    
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Rohan Sharma" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="e.g. rohan@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
                        Create Account &rarr;
                    </button>

                    <div style="text-align: center; margin-top: 18px; font-size: 13.5px; color: var(--muted);">
                        Already registered? <a href="q4.php" style="color: var(--primary); font-weight: 600;">Sign in here</a>
                    </div>
                </form>
            </div>

            <!-- SECURITY CONCEPT CARD -->
            <div class="card">
                <h3 style="font-size: 16px; margin-bottom: 8px;">Security Implementation</h3>
                <p style="font-size: 13.5px; color: var(--muted); line-height: 1.6;">
                    Never store plain-text passwords or obsolete hashes (like MD5 / SHA1). PHP's <code>password_hash()</code> automatically incorporates a cryptographically secure random salt and uses the computationally intensive Bcrypt algorithm, rendering rainbow table attacks ineffective.
                </p>
            </div>
        </div>

    </main>

    <footer class="lab-footer">
        &copy; <?php echo date('Y'); ?> COER University &bull; Web Designing Lab Sheet 9 &bull; Question 3
    </footer>

</body>
</html>
