<?php
/**
 * Question 5:
 * Create a MySQL database table named `students` with columns (id, name, email, 
 * enrollment_date) using SQL queries executed via PHP script.
 */

$host = 'localhost';
$db   = 'college_db';
$user = 'root';
$pass = '';

$message = "";
$statusClass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // SQL query to create `students` table
    $createTableSQL = "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        enrollment_date DATE NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($createTableSQL);

    $message = "✓ Table `students` created successfully (or already exists) with columns: id, name, email, enrollment_date.";
    $statusClass = "success";

} catch (PDOException $e) {
    $message = "Notice / Error: " . $e->getMessage();
    $statusClass = "warning";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 5 - Create MySQL Table `students` via PHP</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 700px; margin: auto; background: white; padding: 25px 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .alert { padding: 15px; border-radius: 6px; font-weight: bold; margin-bottom: 20px; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        pre { background: #1e293b; color: #38bdf8; padding: 15px; border-radius: 6px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Question 5: Create MySQL `students` Table via PHP Script</h2>

        <div class="alert <?php echo $statusClass; ?>">
            <?php echo $message; ?>
        </div>

        <h3>Executed SQL Schema:</h3>
        <pre>CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    enrollment_date DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</pre>
    </div>
</body>
</html>
