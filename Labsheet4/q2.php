<?php
/**
 * Question 2:
 * Configure the `php.ini` file to enable error reporting (`display_errors = On`) and verify settings 
 * using a `phpinfo()` script.
 * 
 * Recommended php.ini Configuration Settings:
 * -------------------------------------------
 * display_errors = On
 * display_startup_errors = On
 * error_reporting = E_ALL
 * log_errors = On
 * error_log = "C:/xampp/apache/logs/php_error.log"
 */

// Enable dynamic runtime error reporting for this script verification
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 2 - php.ini Error Reporting &amp; phpinfo()</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 25px; color: #1e293b; }
        .box { max-width: 850px; margin: auto; background: white; padding: 25px 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
        .info-card { background: #f1f5f9; padding: 15px; border-radius: 6px; border-left: 4px solid #2563eb; }
        .info-card strong { display: block; color: #475569; font-size: 13px; margin-bottom: 4px; }
        .info-card span { font-size: 16px; font-weight: bold; color: #0f172a; font-family: monospace; }
        .code-box { background: #1e293b; color: #38bdf8; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 13px; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Question 2: Verify Error Reporting &amp; PHP Configuration</h2>
        <p>Current PHP Configuration Directives dynamically read from the active environment:</p>

        <div class="grid">
            <div class="info-card">
                <strong>display_errors:</strong>
                <span><?php echo ini_get('display_errors') ? 'On (Enabled)' : 'Off (Disabled)'; ?></span>
            </div>
            <div class="info-card">
                <strong>error_reporting level:</strong>
                <span><?php echo error_reporting(); ?> (E_ALL = <?php echo E_ALL; ?>)</span>
            </div>
            <div class="info-card">
                <strong>Loaded php.ini File:</strong>
                <span><?php echo php_ini_loaded_file() ?: 'Default CLI Configuration'; ?></span>
            </div>
            <div class="info-card">
                <strong>PHP Version:</strong>
                <span><?php echo phpversion(); ?></span>
            </div>
        </div>

        <h3>php.ini Required Changes:</h3>
        <div class="code-box">
; In php.ini file:
display_errors = On
display_startup_errors = On
error_reporting = E_ALL
        </div>

        <form method="GET" action="" style="margin-top: 15px;">
            <input type="hidden" name="show_phpinfo" value="1">
            <button type="submit">Show Full phpinfo() Page</button>
        </form>
    </div>

    <?php
    if (isset($_GET['show_phpinfo']) && $_GET['show_phpinfo'] == '1') {
        echo "<hr><h2 style='text-align:center;'>phpinfo() Output</h2>";
        phpinfo();
    }
    ?>
</body>
</html>
