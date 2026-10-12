<?php
/**
 * Question 6:
 * Write a PHP script to insert a new student record into the `students` table using prepared 
 * statements to prevent SQL injection.
 */

$message = "";
$statusClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $enrollDate = trim($_POST['enrollment_date'] ?? '');

    if (!empty($name) && !empty($email) && !empty($enrollDate)) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=college_db;charset=utf8mb4", "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Prepared Statement with bound parameters to prevent SQL injection
            $stmt = $pdo->prepare("INSERT INTO students (name, email, enrollment_date) VALUES (:name, :email, :enrollment_date)");
            
            // Execute with securely bound values
            $stmt->execute([
                ':name'            => $name,
                ':email'           => $email,
                ':enrollment_date' => $enrollDate
            ]);

            $message = "✓ Record inserted successfully for '$name' (ID: " . $pdo->lastInsertId() . ") using Prepared Statements!";
            $statusClass = "success";

        } catch (PDOException $e) {
            $message = "Database Notice / Error: " . $e->getMessage();
            $statusClass = "error";
        }
    } else {
        $message = "Please fill in all required fields.";
        $statusClass = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 6 - Insert Student Record with Prepared Statements</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 550px; margin: auto; background: white; padding: 25px 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .msg { padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px; font-size: 14px; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; padding: 9px; box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 4px; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Insert Student Record (Prepared Statements)</h2>
        <p style="color: #64748b; font-size: 13px;">Uses PDO parameter binding (<code>:name, :email, :enrollment_date</code>) to protect against SQL Injection attacks.</p>

        <?php if (!empty($message)): ?>
            <div class="msg <?php echo $statusClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="name">Student Full Name:</label>
                <input type="text" id="name" name="name" placeholder="e.g. Rahul Sharma" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" placeholder="e.g. rahul@example.com" required>
            </div>

            <div class="form-group">
                <label for="enrollment_date">Enrollment Date:</label>
                <input type="date" id="enrollment_date" name="enrollment_date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <button type="submit">Insert Record</button>
        </form>
    </div>
</body>
</html>
