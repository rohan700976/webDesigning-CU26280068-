<?php
/**
 * Question 13:
 * Write a PHP script that handles file uploads (`$_FILES`), validates file size and extension, and 
 * stores images in an Apache server directory.
 */

$message = "";
$statusClass = "";
$uploadedFilePath = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["user_image"])) {
    $file = $_FILES["user_image"];

    $fileName = $file["name"];
    $fileTmp  = $file["tmp_name"];
    $fileSize = $file["size"];
    $fileErr  = $file["error"];

    $allowedExts = ["jpg", "jpeg", "png", "gif", "webp"];
    $maxSize = 2 * 1024 * 1024; // 2 Megabytes limit

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileErr === UPLOAD_ERR_OK) {
        // 1. Validate extension
        if (!in_array($fileExt, $allowedExts)) {
            $message = "Invalid file type! Only JPG, JPEG, PNG, GIF, and WEBP formats are permitted.";
            $statusClass = "error";
        }
        // 2. Validate file size
        elseif ($fileSize > $maxSize) {
            $message = "File is too large! Maximum allowed size is 2MB (Uploaded: " . round($fileSize / 1024 / 1024, 2) . "MB).";
            $statusClass = "error";
        } else {
            // Target upload directory
            $targetDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Generate clean unique filename
            $uniqueName = uniqid("img_", true) . "." . $fileExt;
            $destination = $targetDir . $uniqueName;

            if (move_uploaded_file($fileTmp, $destination)) {
                $message = "✓ File uploaded successfully as: <strong>$uniqueName</strong>";
                $statusClass = "success";
                $uploadedFilePath = "uploads/" . $uniqueName;
            } else {
                $message = "Failed to move uploaded file to target directory.";
                $statusClass = "error";
            }
        }
    } else {
        $message = "Upload error code: $fileErr";
        $statusClass = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Question 13 - Secure File Upload Handler</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 30px; }
        .box { max-width: 550px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .msg { padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px; font-size: 14px; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        input[type="file"] { padding: 10px; border: 2px dashed #cbd5e1; border-radius: 6px; width: 100%; box-sizing: border-box; margin-bottom: 15px; }
        button { background: #2563eb; color: white; border: none; padding: 10px 22px; border-radius: 4px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Secure Image Upload Handler</h2>
        <p style="color: #64748b; font-size: 13px;">Validates file extension (JPG/PNG/WEBP) and file size (&le; 2MB) before storing.</p>

        <?php if (!empty($message)): ?>
            <div class="msg <?php echo $statusClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="user_image" style="display:block; margin-bottom: 8px; font-weight: bold;">Select Image File:</label>
            <input type="file" id="user_image" name="user_image" accept="image/*" required>
            <button type="submit">Upload Image</button>
        </form>

        <?php if (!empty($uploadedFilePath)): ?>
            <div style="margin-top: 20px; text-align: center;">
                <p>Preview:</p>
                <img src="<?php echo htmlspecialchars($uploadedFilePath); ?>" alt="Uploaded preview" style="max-width: 100%; max-height: 200px; border-radius: 6px;">
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
