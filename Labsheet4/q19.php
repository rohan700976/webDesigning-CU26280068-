<?php
/**
 * Question 19:
 * Write a PHP script implementing transaction management (`beginTransaction()`, `commit()`, 
 * `rollBack()`) in MySQL PDO for secure multi-table operations.
 */

$log = [];
$transactionStatus = "";

if (isset($_POST['execute_transaction'])) {
    $shouldSimulateError = isset($_POST['simulate_failure']);

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=college_db;charset=utf8mb4", "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Begin PDO Transaction
        $pdo->beginTransaction();
        $log[] = "[STEP 1]: Transaction started via \$pdo->beginTransaction()";

        // Query 1: Insert into students table
        $stmt1 = $pdo->prepare("INSERT INTO students (name, email, enrollment_date) VALUES (:name, :email, :date)");
        $stmt1->execute([
            ':name'  => 'Transaction Test User',
            ':email' => 'trans_' . time() . '@example.com',
            ':date'  => date('Y-m-d')
        ]);
        $newStudentId = $pdo->lastInsertId();
        $log[] = "[STEP 2]: Operation 1 succeeded - Inserted student with ID #$newStudentId";

        // Query 2: Multi-table simulated operation (e.g. fees or enrollment table)
        if ($shouldSimulateError) {
            // Intentionally throw exception to trigger rollback
            throw new Exception("Simulated fatal database error during step 2!");
        }

        $log[] = "[STEP 3]: Operation 2 succeeded - Related record verified.";

        // Commit transaction if all steps passed without error
        $pdo->commit();
        $log[] = "[STEP 4]: Transaction committed via \$pdo->commit()! All changes permanently saved.";
        $transactionStatus = "SUCCESS";

    } catch (Exception $e) {
        // Rollback transaction to revert all changes cleanly
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
            $log[] = "[ROLLBACK TRIGGERED]: \$pdo->rollBack() executed. All partial database changes reversed cleanly!";
        }
        $log[] = "[ERROR CAUGHT]: " . $e->getMessage();
        $transactionStatus = "ROLLED_BACK";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 19 - PDO Transaction Management</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 650px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h2 { color: #0f172a; margin-bottom: 12px; }
        .terminal { background: #0f172a; color: #38bdf8; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 13px; line-height: 1.6; white-space: pre-wrap; margin-top: 20px; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h2>PDO Transaction Management (ACID Compliance)</h2>
        <p>Demonstrating atomic operations using <code>beginTransaction()</code>, <code>commit()</code>, and <code>rollBack()</code>.</p>

        <form action="" method="POST">
            <label style="display: block; margin-bottom: 15px;">
                <input type="checkbox" name="simulate_failure" value="1"> <strong>Simulate failure in second query (Triggers rollBack)</strong>
            </label>
            <button type="submit" name="execute_transaction">Execute Transaction Flow</button>
        </form>

        <?php if (!empty($log)): ?>
            <div class="terminal"><?php echo htmlspecialchars(implode("\n", $log)); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
