<?php
/**
 * Question 11:
 * Write a PHP script that checks Apache server environment variables and server software details 
 * using `$_SERVER` superglobal.
 */

$serverDetails = [
    'Server Software'       => $_SERVER['SERVER_SOFTWARE'] ?? 'Not set',
    'Server Name / Host'    => $_SERVER['SERVER_NAME'] ?? 'localhost',
    'Server Protocol'       => $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1',
    'Server Port'           => $_SERVER['SERVER_PORT'] ?? '80',
    'Request Method'        => $_SERVER['REQUEST_METHOD'] ?? 'GET',
    'Document Root'         => $_SERVER['DOCUMENT_ROOT'] ?? 'Not set',
    'Script Name / Path'    => $_SERVER['SCRIPT_NAME'] ?? __FILE__,
    'Gateway Interface'     => $_SERVER['GATEWAY_INTERFACE'] ?? 'CGI/1.1',
    'User Agent'            => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
    'Client IP Address'     => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
    'HTTP Host Header'      => $_SERVER['HTTP_HOST'] ?? 'localhost',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 11 - $_SERVER Environment Inspector</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 850px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h2 { color: #0f172a; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border: 1px solid #e2e8f0; text-align: left; }
        th { background: #1e293b; color: white; width: 35%; }
        td { font-family: monospace; color: #0284c7; word-break: break-all; }
        tr:nth-child(even) { background-color: #f8fafc; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Apache Server &amp; Environment Inspection (<code>$_SERVER</code>)</h2>
        <table>
            <thead>
                <tr>
                    <th>Environment Variable</th>
                    <th>Value / Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($serverDetails as $key => $val): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($key); ?></strong></td>
                        <td><?php echo htmlspecialchars($val); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
