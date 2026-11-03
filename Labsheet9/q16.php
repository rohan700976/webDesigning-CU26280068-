<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 16
// Objective: Prevent Cross-Site Scripting (XSS) by escaping all user-generated
//            output using htmlspecialchars().
// File: q16.php
// ============================================================================

// Sample user-submitted payload
$raw_input = $_GET['comment'] ?? "<script>alert('XSS Attack: Session Cookie Hijacked!');</script><b>Welcome Visitor!</b>";

// Sanitize using htmlspecialchars with ENT_QUOTES and UTF-8
$escaped_output = htmlspecialchars($raw_input, ENT_QUOTES, 'UTF-8');

// Entity breakdown table
$entity_map = [
    '&'  => '&amp;',
    '<'  => '&lt;',
    '>'  => '&gt;',
    '"'  => '&quot;',
    "'"  => '&#039;'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q16: Prevent XSS with htmlspecialchars() - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .preview-pane {
            border-radius: var(--border-radius);
            padding: 1.25rem;
            min-height: 120px;
            background: #0f172a;
            border: 1px solid var(--border-color);
            margin-top: 0.75rem;
        }
        .payload-tag {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-family: var(--font-mono);
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }
        .payload-tag:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.15);
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q15.php">&larr; Q15: SQL Injection Demo</a>
            <a href="q16.php" style="color: var(--accent); font-weight: 700;">Q16: XSS Prevention</a>
            <a href="q17.php">Q17: Remember Me Cookies &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 16</div>
            <h1>Cross-Site Scripting (XSS) Prevention</h1>
            <p class="subtitle">Neutralizing client-side script injection attacks by sanitizing output with <code>htmlspecialchars(..., ENT_QUOTES, 'UTF-8')</code>.</p>
        </div>

        <div class="card" style="margin-bottom: 2rem;">
            <form action="q16.php" method="GET">
                <div class="form-group">
                    <label for="comment">User Input String (Comments, Profiles, Reviews)</label>
                    <textarea id="comment" name="comment" class="form-control" rows="2" style="font-family: var(--font-mono);"><?= htmlspecialchars($raw_input) ?></textarea>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                        <span style="font-size: 0.8rem; color: var(--text-muted);">Quick Payloads:</span>
                        <a href="?comment=<?= urlencode(\"<script>alert('Stealing document.cookie: ' + document.cookie)</script>\") ?>" class="payload-tag">Script Tag Alert</a>
                        <a href="?comment=<?= urlencode(\"<img src='invalid' onerror='alert(\\\"Img OnError XSS\\\")'>\") ?>" class="payload-tag">Image OnError Event</a>
                        <a href="?comment=<?= urlencode(\"Hello <b>Arjun</b>! <a href='javascript:alert(1)'>Click Me</a>\") ?>" class="payload-tag">Malicious Link</a>
                    </div>
                    <button type="submit" class="btn btn-primary">Test Sanitization</button>
                </div>
            </form>
        </div>

        <div class="grid grid-2">
            <!-- Left: Unescaped XSS Risk -->
            <div class="card" style="border-color: rgba(239, 68, 68, 0.4);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="color: #ef4444;">❌ Vulnerable: Raw Unescaped Output</h3>
                    <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444;">Insecure</span>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;">
                    Code: <code>&lt;?php echo $raw_input; ?&gt;</code>
                </p>

                <div class="preview-pane">
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;">Browser DOM Interpretation (Sandboxed Preview):</span>
                    <!-- Sandboxed iframe to safely isolate JavaScript execution -->
                    <iframe srcdoc="<?= htmlspecialchars($raw_input, ENT_QUOTES) ?>" 
                            sandbox="allow-scripts" 
                            style="width: 100%; height: 80px; border: 1px dashed rgba(239, 68, 68, 0.4); border-radius: 4px; background: #fff; color: #000;"></iframe>
                </div>

                <div style="margin-top: 1rem; font-size: 0.85rem; color: #fca5a5; line-height: 1.5;">
                    The browser's HTML parser treats tags like <code>&lt;script&gt;</code> or event handlers (<code>onerror</code>) as executable code, allowing attackers to steal session cookies, deface the website, or redirect users.
                </div>
            </div>

            <!-- Right: Protected Output -->
            <div class="card" style="border-color: rgba(16, 185, 129, 0.4);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="color: #10b981;">✅ Secure: Escaped with htmlspecialchars()</h3>
                    <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">Protected</span>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;">
                    Code: <code>&lt;?php echo htmlspecialchars($raw_input, ENT_QUOTES, 'UTF-8'); ?&gt;</code>
                </p>

                <div class="preview-pane">
                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;">Rendered Safely as Plain Text:</span>
                    <div style="color: #10b981; font-family: var(--font-mono); font-size: 0.9rem; word-break: break-all;">
                        <?= $escaped_output ?>
                    </div>
                </div>

                <div style="margin-top: 1rem; font-size: 0.85rem; color: #a7f3d0; line-height: 1.5;">
                    Special HTML characters are transformed into benign entity codes (e.g. <code>&amp;lt;</code>). The browser renders the visual characters without executing any script or creating DOM nodes.
                </div>
            </div>
        </div>

        <!-- Entity Conversion Reference Table -->
        <div class="card" style="margin-top: 2rem;">
            <h3>htmlspecialchars() Character Translation Matrix</h3>
            <div class="table-container" style="margin-top: 1rem;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Raw Character</th>
                            <th>Character Meaning</th>
                            <th>Safe HTML Entity</th>
                            <th>Why it must be escaped</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>&lt;</code></td>
                            <td>Less Than</td>
                            <td><code>&amp;lt;</code></td>
                            <td>Prevents opening new HTML tags like <code>&lt;script&gt;</code> or <code>&lt;iframe&gt;</code>.</td>
                        </tr>
                        <tr>
                            <td><code>&gt;</code></td>
                            <td>Greater Than</td>
                            <td><code>&amp;gt;</code></td>
                            <td>Prevents closing existing tags or attributes.</td>
                        </tr>
                        <tr>
                            <td><code>&amp;</code></td>
                            <td>Ampersand</td>
                            <td><code>&amp;amp;</code></td>
                            <td>Prevents ambiguous entity encoding and character set manipulation.</td>
                        </tr>
                        <tr>
                            <td><code>&quot;</code></td>
                            <td>Double Quote</td>
                            <td><code>&amp;quot;</code></td>
                            <td>Prevents breaking out of HTML attributes: <code>&lt;input value="..."&gt;</code>.</td>
                        </tr>
                        <tr>
                            <td><code>&#039;</code></td>
                            <td>Single Quote</td>
                            <td><code>&amp;#039;</code> (with <code>ENT_QUOTES</code>)</td>
                            <td>Prevents escaping single-quoted attributes and inline JavaScript handlers.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
