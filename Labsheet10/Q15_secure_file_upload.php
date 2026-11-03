<?php
// Q15: Secure file upload - checks MIME + size, renames file, stores OUTSIDE web root
// Folder layout:  myproject/public/ (web root)   myproject/storage/uploads/ (private)

const UPLOAD_DIR = __DIR__ . '/../storage/uploads/';
const MAX_SIZE   = 2 * 1024 * 1024; // 2 MB
const ALLOWED    = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];

function handle_upload(array $file): array {
    if ($file['error'] !== UPLOAD_ERR_OK)       return ['ok' => false, 'msg' => 'Upload error code ' . $file['error']];
    if (!is_uploaded_file($file['tmp_name']))   return ['ok' => false, 'msg' => 'Invalid upload'];
    if ($file['size'] > MAX_SIZE)               return ['ok' => false, 'msg' => 'File exceeds 2 MB'];

    // Check REAL MIME type from file contents (not the client-supplied one)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!isset(ALLOWED[$mime]))                 return ['ok' => false, 'msg' => "Type $mime not allowed"];

    if (str_starts_with($mime, 'image/') && !getimagesize($file['tmp_name']))
        return ['ok' => false, 'msg' => 'Corrupt image'];

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0750, true);

    // Rename: random name + extension derived from MIME (ignore user's extension)
    $newName = bin2hex(random_bytes(16)) . '.' . ALLOWED[$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newName))
        return ['ok' => false, 'msg' => 'Could not save file'];
    chmod(UPLOAD_DIR . $newName, 0640);

    return ['ok' => true, 'name' => $newName, 'original' => basename($file['name'])];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $r = handle_upload($_FILES['upload']);
    echo $r['ok'] ? 'Saved as ' . htmlspecialchars($r['name']) : 'Error: ' . htmlspecialchars($r['msg']);
}

// Serving files back (since they are not public): download.php?f=<name>
// $f = basename($_GET['f']); $path = UPLOAD_DIR.$f;
// if (is_file($path)) { header('Content-Type: '.(new finfo(FILEINFO_MIME_TYPE))->file($path)); readfile($path); }
?>
<form method="POST" enctype="multipart/form-data">
  <input type="file" name="upload" required>
  <button>Upload</button>
</form>
