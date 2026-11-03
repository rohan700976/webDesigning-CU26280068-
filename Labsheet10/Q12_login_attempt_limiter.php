<?php
// Q12: Login attempt limiter - lock account for 15 minutes after 5 failed attempts
// SQL:
//   ALTER TABLE users ADD COLUMN failed_attempts INT NOT NULL DEFAULT 0,
//                     ADD COLUMN locked_until DATETIME NULL;
require_once __DIR__ . '/../core/Database.php';

const MAX_ATTEMPTS = 5;
const LOCK_MINUTES = 15;

function attempt_login(PDO $db, string $email, string $password): array {
    $s = $db->prepare("SELECT * FROM users WHERE email = ?");
    $s->execute([$email]);
    $user = $s->fetch();

    if (!$user) { return ['ok' => false, 'msg' => 'Invalid email or password']; } // do not reveal which

    // 1. Is the account locked?
    if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
        $mins = ceil((strtotime($user['locked_until']) - time()) / 60);
        return ['ok' => false, 'msg' => "Account locked. Try again in $mins minute(s)."];
    }

    // 2. Verify password
    if (password_verify($password, $user['password'])) {
        $db->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?")
           ->execute([$user['id']]);
        return ['ok' => true, 'user' => $user];
    }

    // 3. Failed attempt
    $attempts = $user['failed_attempts'] + 1;
    if ($attempts >= MAX_ATTEMPTS) {
        $db->prepare("UPDATE users SET failed_attempts = 0, locked_until = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE id = ?")
           ->execute([LOCK_MINUTES, $user['id']]);
        return ['ok' => false, 'msg' => 'Too many failed attempts. Account locked for ' . LOCK_MINUTES . ' minutes.'];
    }
    $db->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?")->execute([$attempts, $user['id']]);
    return ['ok' => false, 'msg' => 'Invalid email or password. Attempts left: ' . (MAX_ATTEMPTS - $attempts)];
}

// ---- Usage ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db  = Database::getInstance()->getConnection();
    $res = attempt_login($db, $_POST['email'] ?? '', $_POST['password'] ?? '');
    echo $res['ok'] ? 'Login successful' : htmlspecialchars($res['msg']);
}
?>
<form method="POST">
  <input name="email" type="email" placeholder="Email" required>
  <input name="password" type="password" placeholder="Password" required>
  <button>Login</button>
</form>
