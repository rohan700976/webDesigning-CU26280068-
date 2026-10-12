<?php
/**
 * Question 8:
 * Write a PHP script to update an existing student's email address in the database based on their 
 * student ID.
 */

$message = "";
$statusClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $studentId = trim($_POST['student_id'] ?? '');
    $newEmail  = trim($_POST['new_email'] ?? '');

    if (!empty($studentId) && !empty($newEmail)) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=college_db;charset=utf8mb4", "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Prepared update query
            $stmt = $pdo->prepare("UPDATE students SET email = :email WHERE id = :id");
            $stmt->execute([
                ':email' => $newEmail,
                ':id'    => $studentId
            ]);

            if ($stmt->rowCount() > 0) {
                $message = "✓ Success: Student ID #$studentId email address successfully updated to '$newEmail'.";
                $statusClass = "success";
            } else {
                $message = "Notice: No changes made. (Either Student ID #$studentId does not exist or email was already '$newEmail').";
                $statusClass = "warning";
            }

        } catch (PDOException $e) {
            $message = "Database Notice / Error: " . $e->getMessage();
            $statusClass = "error";
        }
    } else {
        $message = "Please provide both Student ID and the New Email.";
        $statusClass = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 8 - Update Student Email</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 500px; margin: auto; background: white; padding: 25px 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .msg { padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px; font-size: 14px; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; padding: 9px; box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 4px; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Update Student Email Address</h2>

        <?php if (!empty($message)): ?>
            <div class="msg <?php echo $statusClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="student_id">Student ID (Target Record):</label>
                <input type="number" id="student_id" name="student_id" placeholder="e.g. 1" min="1" required>
            </div>

            <div class="form-group">
                <label for="new_email">New Email Address:</label>
                <input type="email" id="new_email" name="new_email" placeholder="e.g. updated.email@example.com" required>
            </div>

            <button type="submit">Update Record</button>
        </form>
    </div>
</body>
</html>
