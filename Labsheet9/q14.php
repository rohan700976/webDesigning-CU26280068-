<?php
// ============================================================================
// COER UNIVERSITY - LAB SHEET 9 | QUESTION 14
// Objective: Build an image upload feature for product photos with validation
//            of file type, size, and unique file naming in PHP.
// File: q14.php
// ============================================================================

$upload_dir = __DIR__ . '/uploads';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$error = "";
$success = "";
$uploaded_file_info = null;

// Allowed MIME types and extensions
$allowed_mimes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif'
];
$max_file_size = 2 * 1024 * 1024; // 2 Megabytes

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_btn'])) {
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Please choose an image file to upload.";
    } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $error = "File upload failed with error code: " . $_FILES['photo']['error'];
    } else {
        $file = $_FILES['photo'];

        // 1. Validate File Size
        if ($file['size'] > $max_file_size) {
            $error = "File exceeds the 2MB maximum size limit (Current: " . round($file['size'] / 1024 / 1024, 2) . " MB).";
        } else {
            // 2. Validate MIME Type securely using finfo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!array_key_exists($mime_type, $allowed_mimes)) {
                $error = "Invalid image format ($mime_type). Only JPEG, PNG, WEBP, and GIF are allowed.";
            } else {
                // 3. Generate a Cryptographically Unique File Name
                $ext = $allowed_mimes[$mime_type];
                $unique_name = 'prod_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
                $destination = $upload_dir . '/' . $unique_name;

                // 4. Move uploaded temporary file
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $success = "Image uploaded and verified successfully!";
                    $uploaded_file_info = [
                        'original_name' => $file['name'],
                        'stored_name'   => $unique_name,
                        'file_size'     => round($file['size'] / 1024, 2) . " KB",
                        'mime_type'     => $mime_type,
                        'relative_url'  => 'uploads/' . $unique_name
                    ];
                } else {
                    $error = "Failed to save file to uploads directory. Check folder permissions.";
                }
            }
        }
    }
}

// Read existing uploads for preview
$existing_images = [];
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..' && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $f)) {
            $existing_images[] = $f;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q14: Secure Image Upload &amp; Validation - Lab Sheet 9</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .upload-dropzone {
            border: 2px dashed var(--border-color);
            border-radius: var(--border-radius);
            padding: 2.5rem 1.5rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.02);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .upload-dropzone:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }
        .preview-box {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .preview-img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="index.php" class="brand">🚀 LabSheet 9 Portal</a>
        <div class="nav-links">
            <a href="q13.php">&larr; Q13: Column Sorting</a>
            <a href="q14.php" style="color: var(--accent); font-weight: 700;">Q14: Image Upload</a>
            <a href="q15.php">Q15: SQL Injection Demo &rarr;</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="badge">Lab Sheet 9 &bull; Question 14</div>
            <h1>Secure Product Image Upload Pipeline</h1>
            <p class="subtitle">Validating MIME types with <code>finfo</code>, enforcing 2MB file limits, and generating unique non-colliding names with PHP.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Upload Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>Upload Succeeded:</strong> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($uploaded_file_info): ?>
            <div class="preview-box" style="margin-bottom: 2rem;">
                <img src="<?= htmlspecialchars($uploaded_file_info['relative_url']) ?>" alt="Uploaded Product Photo" class="preview-img">
                <div>
                    <h3 style="margin-bottom: 0.5rem; color: #10b981;">File Successfully Processed</h3>
                    <ul style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.6; list-style: none;">
                        <li><strong>Original Client Name:</strong> <code><?= htmlspecialchars($uploaded_file_info['original_name']) ?></code></li>
                        <li><strong>Safe Unique Server Name:</strong> <code><?= htmlspecialchars($uploaded_file_info['stored_name']) ?></code></li>
                        <li><strong>Verified MIME Content:</strong> <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981;"><?= htmlspecialchars($uploaded_file_info['mime_type']) ?></span></li>
                        <li><strong>Stored Size:</strong> <?= htmlspecialchars($uploaded_file_info['file_size']) ?></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-2">
            <div class="card">
                <h3 style="margin-bottom: 1rem;">Upload Product Photo</h3>
                <form action="q14.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="photo">Select Image (Max 2MB: JPG, PNG, WEBP)</label>
                        <div class="upload-dropzone" onclick="document.getElementById('photo').click();">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📸</div>
                            <p style="font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;">Click to browse image</p>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Strict server-side content-type inspection</span>
                        </div>
                        <input type="file" id="photo" name="photo" style="display: none;" accept="image/jpeg,image/png,image/webp,image/gif" onchange="updateFileName(this)">
                        <div id="file-name-display" style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--accent); font-weight: 600;"></div>
                    </div>

                    <button type="submit" name="upload_btn" class="btn btn-primary" style="width: 100%;">
                        🚀 Upload &amp; Verify Image
                    </button>
                </form>
            </div>

            <div>
                <div class="card">
                    <h3>3-Tier Upload Security Standard</h3>
                    <div class="code-box">
<span class="comment">// 1. Size Validation (Max 2MB)</span>
<span class="keyword">if</span> (<span class="keyword">$_FILES</span>[<span class="string">'photo'</span>][<span class="string">'size'</span>] > 2 * 1024 * 1024) {
    <span class="keyword">throw new Exception</span>(<span class="string">"File size exceeds 2MB limit"</span>);
}

<span class="comment">// 2. Magic-Byte MIME Inspection (finfo)</span>
<span class="keyword">$finfo</span> = <span class="function">finfo_open</span>(<span class="keyword">FILEINFO_MIME_TYPE</span>);
<span class="keyword">$mime</span> = <span class="function">finfo_file</span>(<span class="keyword">$finfo</span>, <span class="keyword">$_FILES</span>[<span class="string">'photo'</span>][<span class="string">'tmp_name'</span>]);
<span class="keyword">if</span> (!<span class="function">in_array</span>(<span class="keyword">$mime</span>, [<span class="string">'image/jpeg'</span>, <span class="string">'image/png'</span>, <span class="string">'image/webp'</span>])) {
    <span class="keyword">throw new Exception</span>(<span class="string">"Invalid binary MIME signature"</span>);
}

<span class="comment">// 3. Unique Naming (Avoid Directory Traversal)</span>
<span class="keyword">$unique_name</span> = <span class="string">'prod_'</span> . <span class="function">bin2hex</span>(<span class="function">random_bytes</span>(8)) . <span class="string">'.'</span> . <span class="keyword">$ext</span>;
<span class="function">move_uploaded_file</span>(<span class="keyword">$_FILES</span>[<span class="string">'photo'</span>][<span class="string">'tmp_name'</span>], <span class="string">"uploads/"</span> . <span class="keyword">$unique_name</span>);
                    </div>
                </div>

                <div class="card" style="margin-top: 1rem;">
                    <h4>Why Never Trust <code>$_FILES['photo']['name']</code>?</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-top: 0.5rem;">
                        Attackers can send names like <code>../../shell.php</code> or overwrite critical server files. Generating random cryptographic names completely neutralizes directory traversal and file collision vulnerabilities.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const display = document.getElementById('file-name-display');
            if (input.files && input.files[0]) {
                display.textContent = 'Selected: ' + input.files[0].name + ' (' + Math.round(input.files[0].size / 1024) + ' KB)';
            }
        }
    </script>
</body>
</html>
