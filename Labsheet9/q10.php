<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 10
// Objective: Implement record deletion with a JavaScript confirmation prompt
//            and a secure PHP DELETE handler.
// File: q10.php
// ============================================================================

session_start();
require_once 'db_connect.php';

// Generate Simple CSRF Token for Secure Deletion
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";
$delete_target_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle Secure POST Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $submitted_token = $_POST['csrf_token'] ?? '';
    $id_to_delete = intval($_POST['product_id'] ?? 0);

    // CSRF & Input Validation
    if (!hash_equals($_SESSION['csrf_token'], $submitted_token)) {
        $error = "CSRF Token Validation Failed! Request aborted.";
    } elseif ($id_to_delete <= 0) {
        $error = "Invalid product ID specified for deletion.";
    } else {
        if ($db_connected && $pdo) {
            try {
                // Prepared statement to securely delete
                $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$id_to_delete]);

                if ($stmt->rowCount() > 0) {
                    $success = "Product #{$id_to_delete} was permanently deleted from the database.";
                } else {
                    $error = "Record #{$id_to_delete} not found or already deleted.";
                }
            } catch (PDOException $e) {
                // Handle Foreign Key RESTRICT constraint if product exists in order_items
                if ($e->getCode() == '23000') {
                    $error = "Cannot delete Product #{$id_to_delete}: It is referenced in existing customer orders (Foreign Key Constraint).";
                } else {
                    $error = "Database Error: " . htmlspecialchars($e->getMessage());
                }
            }
        } else {
            // Simulated Deletion for Offline Demo
            $success = "Product #{$id_to_delete} deleted successfully (Demo Mode)! JavaScript confirmation and PHP handler verified.";
        }
    }
}

// Fetch current products for deletion demo
$products = [];
if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->query("SELECT id, name, category, price, stock FROM products ORDER BY id DESC LIMIT 10");
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        // Fallback below
    }
}

if (empty($products)) {
    $products = [
        ['id' => 1, 'name' => 'Dell XPS 15 Laptop', 'category' => 'Electronics', 'price' => '189999.00', 'stock' => 12],
        ['id' => 2, 'name' => 'Sony WH-1000XM5 Headphones', 'category' => 'Audio', 'price' => '29999.00', 'stock' => 28],
        ['id' => 3, 'name' => 'Apple Watch Ultra 2', 'category' => 'Wearables', 'price' => '89900.00', 'stock' => 5],
        ['id' => 4, 'name' => 'Nike Air Zoom Pegasus', 'category' => 'Footwear', 'price' => '10495.00', 'stock' => 0]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q10: JavaScript Confirm &amp; Secure PHP DELETE - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .delete-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 0.75rem;
            transition: background 0.2s ease;
        }
        .delete-card:hover {
            background: rgba(239, 68, 68, 0.05);
            border-color: rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q9.php">&larr; Q9: Edit Product</a>
            <a href="q10.php" style="color: var(--accent); font-weight: 700;">Q10: Delete Product</a>
            <a href="q11.php">Q11: Pagination &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 10</div>
            <h1>JavaScript Confirmation &amp; Secure PHP Deletion</h1>
            <p class="subtitle">Preventing accidental data loss with client-side <code>confirm()</code> prompts coupled with server-side prepared <code>DELETE</code> queries.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>Success:</strong> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-2">
            <div class="card">
                <h3 style="margin-bottom: 1rem;">Products Available for Deletion Test</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                    Clicking "Delete" triggers a native browser JavaScript modal. If you press Cancel, execution stops immediately without touching the database.
                </p>

                <?php foreach ($products as $item): ?>
                    <div class="delete-card">
                        <div>
                            <div style="font-weight: 600; color: var(--text-main);">
                                #<?= htmlspecialchars((string)$item['id']) ?> - <?= htmlspecialchars($item['name']) ?>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                <?= htmlspecialchars($item['category']) ?> &bull; ₹<?= number_format((float)$item['price'], 2) ?>
                            </div>
                        </div>

                        <!-- Secure Deletion Form with JavaScript Confirmation -->
                        <form action="q10.php" method="POST" onsubmit="return confirmDelete(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars((string)$item['id']) ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                🗑️ Delete Record
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div>
                <div class="card">
                    <h3>Implementation Architecture</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        <strong>1. Client-Side JavaScript Guard:</strong>
                    </p>
                    <div class="code-box">
<span class="function">function</span> <span class="function">confirmDelete</span>(id, name) {
    <span class="keyword">const</span> message = <span class="string">`Are you sure you want to PERMANENTLY delete product #${id}: "${name}"?\n\nThis action cannot be undone!`</span>;
    <span class="keyword">return</span> <span class="function">confirm</span>(message);
    <span class="comment">// Returns true to submit form, or false to halt</span>
}
                    </div>

                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 1rem 0 0.5rem;">
                        <strong>2. Server-Side PHP Prepared DELETE:</strong>
                    </p>
                    <div class="code-box">
<span class="keyword">if</span> (<span class="keyword">$_SERVER</span>[<span class="string">'REQUEST_METHOD'</span>] === <span class="string">'POST'</span>) {
    <span class="comment">// Verify CSRF token and execute prepared DELETE</span>
    <span class="keyword">$stmt</span> = <span class="keyword">$pdo</span>-><span class="function">prepare</span>(<span class="string">"DELETE FROM products WHERE id = ?"</span>);
    <span class="keyword">$stmt</span>-><span class="function">execute</span>([<span class="keyword">$id_to_delete</span>]);
}
                    </div>
                </div>

                <div class="card" style="margin-top: 1rem;">
                    <h4>Security Best Practices Applied</h4>
                    <ul style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem; padding-left: 1.2rem; line-height: 1.6;">
                        <li><strong>POST not GET:</strong> Never use <code>GET</code> links for destructive actions to prevent web crawler and pre-fetching accidental deletions.</li>
                        <li><strong>CSRF Token:</strong> Random token in session blocks cross-site forged requests.</li>
                        <li><strong>Prepared Statement:</strong> Eliminates SQL injection on the ID parameter.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, name) {
            const promptMsg = `⚠️ CONFIRM RECORD DELETION:\n\nAre you sure you want to permanently delete:\nProduct #${id}: "${name}"?\n\nClick [OK] to execute DELETE query or [Cancel] to abort.`;
            return confirm(promptMsg);
        }
    </script>
</body>
</html>
