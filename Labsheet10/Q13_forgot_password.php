<?php
// Q13: Forgot Password with unique, time-limited token stored in DB
// SQL:
// CREATE TABLE password_resets (
//   id INT AUTO_INCREMENT PRIMARY KEY,
//   email VARCHAR(255) NOT NULL,
//   token_hash CHAR(64) NOT NULL,
//   expires_at DATETIME NOT NULL,
//   used TINYINT(1) DEFAULT 0,
//   INDEX(token_hash)
// );
require_once __DIR__ . '/../core/Database.php';
$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'request';
$message = '';

// ---------- Step 1: request reset link ----------
if ($action === 'request' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if ($email) {
        $s = $db->prepare("SELECT id FROM users WHERE email = ?");
        $s->execute([$email]);
        if ($s->fetch()) {
            $token = bin2hex(random_bytes(32));
            $db->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            $db->prepare("INSERT INTO password_resets (email, token_hash, expires_at)
                          VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))")
               ->execute([$email, hash('sha256', $token)]);
            $link = "https://myproject.local/forgot_password.php?action=reset&token=$token";
            // send_mail($email, 'Reset your password', "Click: $link");   // see Q14
            error_log("Reset link for $email: $link");                    // dev only
        }
    }
    // Same message whether or not the email exists (prevents user enumeration)
    $message = 'If that email exists, a reset link has been sent.';
}

// ---------- Step 2: set new password ----------
if ($action === 'reset') {
    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    $s = $db->prepare("SELECT * FROM password_resets WHERE token_hash = ? AND used = 0 AND expires_at > NOW()");
    $s->execute([hash('sha256', $token)]);
    $row = $s->fetch();

    if (!$row) { exit('Invalid or expired token.'); }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pw = $_POST['password'] ?? '';
        if (strlen($pw) < 8) { $message = 'Password must be at least 8 characters.'; }
        else {
            $db->prepare("UPDATE users SET password = ? WHERE email = ?")
               ->execute([password_hash($pw, PASSWORD_DEFAULT), $row['email']]);
            $db->prepare("UPDATE password_resets SET used = 1 WHERE id = ?")->execute([$row['id']]);
            exit('Password updated. <a href="/login">Login</a>');
        }
    }
}
?>
<p><?= htmlspecialchars($message) ?></p>
<?php if ($action === 'request'): ?>
<form method="POST"><input type="email" name="email" placeholder="Your email" required><button>Send reset link</button></form>
<?php else: ?>
<form method="POST">
  <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
  <input type="password" name="password" placeholder="New password" required><button>Reset password</button>
</form>
<?php endif; ?>
