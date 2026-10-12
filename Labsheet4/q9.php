<?php
/**
 * Question 9:
 * Implement a PHP script to delete a specific student record from the database using a URL 
 * parameter (`GET` request).
 */

$message = "";
$statusClass = "";

// Check if 'delete_id' GET parameter exists in URL (e.g. q9.php?delete_id=3)
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);

    if ($deleteId > 0) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=college_db;charset=utf8mb4", "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Prepared DELETE query to prevent SQL injection
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
            $stmt->execute([':id' => $deleteId]);

            if ($stmt->rowCount() > 0) {
                $message = "✓ Success: Student record with ID #$deleteId has been permanently deleted.";
                $statusClass = "success";
            } else {
                $message = "Notice: No student record found with ID #$deleteId to delete.";
                $statusClass = "warning";
            }
        } catch (PDOException $e) {
            $message = "Database Notice / Error: " . $e->getMessage();
            $statusClass = "error";
        }
    } else {
        $message = "Invalid Student ID provided in URL.";
        $statusClass = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 9 - Delete Record via GET Parameter</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 650px; margin: auto; background: white; padding: 25px 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .msg { padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px; font-size: 14px; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border: 1px solid #e2e8f0; text-align: left; }
        th { background: #1e293b; color: white; font-size: 13px; }
        .btn-delete { background: #ef4444; color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 13px; font-weight: bold; }
        .btn-delete:hover { background: #dc2626; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Delete Student Record (GET Parameter)</h2>
        <p style="color: #64748b; font-size: 13px;">Demonstrating record deletion using URL parameter <code>?delete_id=&lt;id&gt;</code>.</p>

        <?php if (!empty($message)): ?>
            <div class="msg <?php echo $statusClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <h3>Simulated Records with Delete Action Links:</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Action Link (GET URL)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Aarav Sharma</td>
                    <td><a href="?delete_id=1" class="btn-delete" onclick="return confirm('Delete student #1?')">Delete (ID: 1)</a></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Priya Verma</td>
                    <td><a href="?delete_id=2" class="btn-delete" onclick="return confirm('Delete student #2?')">Delete (ID: 2)</a></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Rohan Patel</td>
                    <td><a href="?delete_id=3" class="btn-delete" onclick="return confirm('Delete student #3?')">Delete (ID: 3)</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
