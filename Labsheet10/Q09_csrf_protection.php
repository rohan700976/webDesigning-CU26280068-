<?php
// Q9: CSRF protection - core/Csrf.php
session_start();

class Csrf {
    public static function token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    public static function field(): string {
        return '<input type="hidden" name="csrf_token" value="' . self::token() . '">';
    }
    public static function validate(?string $token): bool {
        return isset($_SESSION['csrf_token']) && is_string($token)
            && hash_equals($_SESSION['csrf_token'], $token);   // timing-safe compare
    }
    public static function verifyOrDie(): void {
        if (!self::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }
    }
}

// ---------- Handling POST ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyOrDie();
    echo "Form accepted for: " . htmlspecialchars($_POST['name'] ?? '');
    // optional: rotate token after successful use
    unset($_SESSION['csrf_token']);
    exit;
}
?>
<!-- ---------- Use in EVERY form ---------- -->
<form method="POST" action="">
    <?= Csrf::field() ?>
    <input type="text" name="name" placeholder="Name" required>
    <button type="submit">Submit</button>
</form>
