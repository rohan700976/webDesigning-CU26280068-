<?php
// Q10: Secure session configuration + session regeneration after login
// Put this in config/session.php and include BEFORE any output.

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,      // only sent over HTTPS (set true in production)
    'httponly' => true,          // JS cannot read cookie
    'samesite' => 'Strict',      // or 'Lax'
]);
session_name('MYPROJECTSESSID');
session_start();

// Idle timeout (30 minutes)
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
    session_unset(); session_destroy(); session_start();
}
$_SESSION['last_activity'] = time();

// ---------- After successful login ----------
function login_user(array $user): void {
    session_regenerate_id(true);               // prevents session fixation; deletes old session
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['email']    = $user['email'];
    $_SESSION['ua_hash']  = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
}

// ---------- Logout ----------
function logout_user(): void {
    $_SESSION = [];
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    session_destroy();
}
