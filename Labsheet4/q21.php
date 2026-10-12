<?php
/**
 * Question 21:
 * Write a PHP script utilizing `password_hash()` and `password_verify()` to securely store and 
 * authenticate hashed user passwords in MySQL.
 */

$hashResult = "";
$verifyResult = "";
$statusClass = "";

// Scenario 1: Hash a new password
if (isset($_POST['hash_password'])) {
    $rawPassword = $_POST['new_pass'] ?? '';
    if (!empty($rawPassword)) {
        // Generating secure Bcrypt / Argon2 hash with strong cryptographic salt
        $hashResult = password_hash($rawPassword, PASSWORD_DEFAULT);
    }
}

// Scenario 2: Verify password against an existing hash
if (isset($_POST['verify_password'])) {
    $inputPass = $_POST['check_pass'] ?? '';
    $storedHash = $_POST['stored_hash'] ?? '';

    if (!empty($inputPass) && !empty($storedHash)) {
        // Secure constant-time hash verification
        if (password_verify($inputPass, $storedHash)) {
            $verifyResult = "✓ Authentication SUCCESSFUL! Password matches the stored hash perfectly.";
            $statusClass = "success";
        } else {
            $verifyResult = "✗ Authentication FAILED! The entered password does not match the hash.";
            $statusClass = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 21 - password_hash() and password_verify()</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 650px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h2 { color: #0f172a; margin-bottom: 12px; }
        .section { margin-bottom: 30px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 8px 0 12px 0; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .hash-display { background: #1e293b; color: #38bdf8; padding: 12px; border-radius: 6px; font-family: monospace; word-break: break-all; margin-top: 10px; font-size: 13px; }
        .msg { padding: 12px; border-radius: 6px; font-weight: bold; margin-top: 15px; font-size: 14px; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Secure Password Hashing &amp; Verification</h2>
        <p style="color: #64748b; font-size: 13px;">Using PHP's native <code>password_hash()</code> and <code>password_verify()</code> APIs.</p>

        <!-- 1. Password Hashing Section -->
        <div class="section">
            <h3>1. Generate Secure Password Hash</h3>
            <form action="" method="POST">
                <label for="new_pass">Enter Plaintext Password to Hash:</label>
                <input type="text" id="new_pass" name="new_pass" value="Student@2026" required>
                <button type="submit" name="hash_password">Generate password_hash()</button>
            </form>

            <?php if (!empty($hashResult)): ?>
                <div class="hash-display">
                    <strong>Generated Hash (Bcrypt):</strong><br>
                    <?php echo htmlspecialchars($hashResult); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. Password Verification Section -->
        <div class="section" style="border: none; padding-bottom: 0;">
            <h3>2. Verify Password Against Stored Hash</h3>
            <form action="" method="POST">
                <label for="check_pass">Enter Password Attempt:</label>
                <input type="password" id="check_pass" name="check_pass" placeholder="Enter password to verify" required>

                <label for="stored_hash">Stored Database Hash:</label>
                <input type="text" id="stored_hash" name="stored_hash" 
                       value="<?php echo !empty($hashResult) ? htmlspecialchars($hashResult) : '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; ?>" required>

                <button type="submit" name="verify_password">Verify with password_verify()</button>
            </form>

            <?php if (!empty($verifyResult)): ?>
                <div class="msg <?php echo $statusClass; ?>"><?php echo $verifyResult; ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
