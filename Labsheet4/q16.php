<?php
/**
 * Question 16:
 * Implement cookie management in PHP (`setcookie()`) to store and retrieve user preferences like 
 * theme color.
 */

// Handle theme selection and setcookie()
if (isset($_POST['theme'])) {
    $selectedTheme = $_POST['theme'];
    
    // Set cookie expiring in 30 days (86400 * 30 seconds)
    setcookie("user_theme_preference", $selectedTheme, time() + (86400 * 30), "/");
    
    // Redirect to self so cookie is available immediately in $_COOKIE
    header("Location: q16.php");
    exit();
}

// Retrieve cookie value (default to 'light' if not set)
$currentTheme = $_COOKIE['user_theme_preference'] ?? 'light';

// Style mapping based on cookie
$themeStyles = [
    'light' => ['bg' => '#f8fafc', 'card' => '#ffffff', 'text' => '#1e293b'],
    'dark'  => ['bg' => '#0f172a', 'card' => '#1e293b', 'text' => '#f8fafc'],
    'blue'  => ['bg' => '#eff6ff', 'card' => '#dbeafe', 'text' => '#1e3a8a'],
];

$activeStyle = $themeStyles[$currentTheme] ?? $themeStyles['light'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 16 - Cookie Management (Theme Preference)</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: <?php echo $activeStyle['bg']; ?>;
            color: <?php echo $activeStyle['text']; ?>;
            padding: 30px;
            transition: all 0.3s ease;
        }
        .box {
            max-width: 520px;
            margin: auto;
            background: <?php echo $activeStyle['card']; ?>;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 { margin-bottom: 12px; }
        .theme-select { width: 100%; padding: 10px; margin: 15px 0; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 15px; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; background: #e2e8f0; color: #0f172a; font-weight: bold; font-size: 13px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>PHP Cookie Management</h2>
        <p>Current Active Theme: <span class="badge"><?php echo strtoupper($currentTheme); ?> THEME</span></p>
        <p style="font-size: 14px;">Stored in Cookie: <code>$_COOKIE['user_theme_preference']</code></p>

        <form action="" method="POST">
            <label for="theme" style="font-weight: bold;">Select Theme Preference:</label>
            <select name="theme" id="theme" class="theme-select">
                <option value="light" <?php if ($currentTheme == 'light') echo 'selected'; ?>>Light Theme</option>
                <option value="dark" <?php if ($currentTheme == 'dark') echo 'selected'; ?>>Dark Theme</option>
                <option value="blue" <?php if ($currentTheme == 'blue') echo 'selected'; ?>>Blue Theme</option>
            </select>
            <button type="submit">Save Preference in Cookie</button>
        </form>
    </div>
</body>
</html>
