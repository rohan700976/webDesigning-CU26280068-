<?php
/**
 * Question 20:
 * Configure PHP extensions (such as `mysqli`, `pdo_mysql`, `mbstring`, `curl`) in `php.ini` and verify 
 * their active status.
 */

$requiredExtensions = [
    'mysqli'     => 'MySQL Improved Extension for relational databases',
    'pdo_mysql'  => 'PDO MySQL Driver for secure abstraction and prepared statements',
    'mbstring'   => 'Multibyte String handling for internationalization and UTF-8',
    'curl'       => 'Client URL Library for making HTTP REST API requests',
    'openssl'    => 'OpenSSL encryption support for secure HTTPS communication',
    'gd'         => 'GD Graphics library for image manipulation and thumbnails'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 20 - Verify PHP Extensions in php.ini</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h2 { color: #0f172a; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px 14px; border: 1px solid #e2e8f0; text-align: left; }
        th { background: #1e293b; color: white; }
        .active { color: #166534; background: #dcfce7; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .inactive { color: #991b1b; background: #fee2e2; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        pre { background: #1e293b; color: #38bdf8; padding: 15px; border-radius: 6px; font-size: 13px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>PHP Extensions Status Verification</h2>
        <p>Checked dynamically using <code>extension_loaded()</code> against the active PHP runtime:</p>

        <table>
            <thead>
                <tr>
                    <th>Extension</th>
                    <th>Purpose / Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requiredExtensions as $ext => $desc): ?>
                    <?php $isLoaded = extension_loaded($ext); ?>
                    <tr>
                        <td><code>extension=<?php echo htmlspecialchars($ext); ?></code></td>
                        <td><?php echo htmlspecialchars($desc); ?></td>
                        <td>
                            <span class="<?php echo $isLoaded ? 'active' : 'inactive'; ?>">
                                <?php echo $isLoaded ? '✓ Enabled / Loaded' : '✗ Disabled / Missing'; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>How to Enable in <code>php.ini</code>:</h3>
        <pre>; In your active php.ini file, uncomment the extension lines (remove leading semicolon ;)
extension=mysqli
extension=pdo_mysql
extension=mbstring
extension=curl
extension=openssl
extension=gd</pre>
    </div>
</body>
</html>
