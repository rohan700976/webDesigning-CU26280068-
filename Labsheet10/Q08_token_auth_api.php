<?php
// Q8 (part 1): Token-based authentication for the API
// Extra SQL:
//   ALTER TABLE users ADD COLUMN api_token CHAR(64) NULL, ADD COLUMN token_expires DATETIME NULL;
// Files: public/api/login.php  and  public/api/auth.php (include at the top of products.php)

/* ---------------- public/api/login.php ---------------- */
function api_login(PDO $db): void {
    header('Content-Type: application/json');
    $in = json_decode(file_get_contents('php://input'), true) ?? [];
    $s = $db->prepare("SELECT id, password FROM users WHERE email = ?");
    $s->execute([$in['email'] ?? '']);
    $u = $s->fetch();
    if (!$u || !password_verify($in['password'] ?? '', $u['password'])) {
        http_response_code(401); echo json_encode(['error' => 'Invalid credentials']); return;
    }
    $token = bin2hex(random_bytes(32));                      // 64 hex chars
    $db->prepare("UPDATE users SET api_token = ?, token_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?")
       ->execute([hash('sha256', $token), $u['id']]);        // store only the hash
    echo json_encode(['token' => $token, 'expires_in' => 3600]);
}

/* ---------------- public/api/auth.php ---------------- */
function require_token(PDO $db): array {
    $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? (function_exists('getallheaders') ? (getallheaders()['Authorization'] ?? '') : '');
    if (!preg_match('/^Bearer\s+(\S+)$/', $hdr, $m)) {
        http_response_code(401); echo json_encode(['error' => 'Missing token']); exit;
    }
    $s = $db->prepare("SELECT id, email FROM users WHERE api_token = ? AND token_expires > NOW()");
    $s->execute([hash('sha256', $m[1])]);
    $user = $s->fetch();
    if (!$user) { http_response_code(401); echo json_encode(['error' => 'Invalid or expired token']); exit; }
    return $user;
}

/* In products.php, right after the header():
   $db = Database::getInstance()->getConnection();
   require_token($db);
   Apache note: add to .htaccess so Authorization header reaches PHP:
   RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
*/
