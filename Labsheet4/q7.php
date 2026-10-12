<?php
/**
 * Question 7:
 * Develop a PHP script to retrieve and display all records from the `students` table inside a styled 
 * HTML table.
 */

$students = [];
$errorMsg = "";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=college_db;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Query to retrieve all records
    $stmt = $pdo->query("SELECT id, name, email, enrollment_date FROM students ORDER BY id ASC");
    $students = $stmt->fetchAll();

} catch (PDOException $e) {
    $errorMsg = "Database Notice: " . $e->getMessage() . " (Showing sample records for visual preview if MySQL is inactive)";
    
    // Sample fallback data for demonstration
    $students = [
        ['id' => 1, 'name' => 'Aarav Sharma', 'email' => 'aarav.sharma@example.com', 'enrollment_date' => '2026-08-01'],
        ['id' => 2, 'name' => 'Priya Verma', 'email' => 'priya.verma@example.com', 'enrollment_date' => '2026-08-05'],
        ['id' => 3, 'name' => 'Rohan Patel', 'email' => 'rohan.patel@example.com', 'enrollment_date' => '2026-08-10']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 7 - Display Students in Styled Table</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h2 { color: #0f172a; margin-bottom: 8px; }
        .sub { color: #64748b; font-size: 14px; margin-bottom: 20px; }
        .alert { background: #fef3c7; color: #92400e; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #1e293b; color: white; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr:nth-child(even) { background-color: #f8fafc; }
        tbody tr:hover { background-color: #e0f2fe; }
        .badge { background: #dbeafe; color: #1d4ed8; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Students Directory</h2>
        <p class="sub">Dynamic data retrieved from MySQL table <code>students</code></p>

        <?php if (!empty($errorMsg)): ?>
            <div class="alert"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Enrollment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $row): ?>
                        <tr>
                            <td><span class="badge">#<?php echo htmlspecialchars($row['id']); ?></span></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['enrollment_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b;">No records found in students table.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
