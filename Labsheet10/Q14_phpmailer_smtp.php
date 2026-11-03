<?php
// Q14: Send registration confirmation & password-reset emails using PHPMailer (SMTP)
// Install:  composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function send_mail(string $to, string $toName, string $subject, string $htmlBody): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER') ?: 'your@gmail.com';
        $mail->Password   = getenv('SMTP_PASS') ?: 'your-16-char-app-password'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('no-reply@myproject.local', 'MyProject');
        $mail->addAddress($to, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mailer error: ' . $mail->ErrorInfo);
        return false;
    }
}

// ---- Registration confirmation ----
function send_registration_email(string $email, string $name, string $token): bool {
    $link = "https://myproject.local/confirm.php?token=" . urlencode($token);
    $html = "<h2>Welcome, " . htmlspecialchars($name) . "!</h2>
             <p>Please confirm your account: <a href='$link'>Confirm Email</a></p>";
    return send_mail($email, $name, 'Confirm your registration', $html);
}

// ---- Password reset ----
function send_reset_email(string $email, string $token): bool {
    $link = "https://myproject.local/forgot_password.php?action=reset&token=" . urlencode($token);
    $html = "<p>Click to reset your password (valid for 30 minutes):</p>
             <p><a href='$link'>Reset Password</a></p><p>If you did not request this, ignore this email.</p>";
    return send_mail($email, '', 'Password reset request', $html);
}

// Test
// var_dump(send_registration_email('student@example.com', 'Student', bin2hex(random_bytes(16))));
