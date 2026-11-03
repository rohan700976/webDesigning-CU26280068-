<?php
// Q11: Validation & sanitization with filter_var()

function clean_string(?string $s): string {
    return htmlspecialchars(trim((string)$s), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$errors = [];
$data   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Email
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address';
    else $data['email'] = $email;

    // URL
    $url = filter_var(trim($_POST['website'] ?? ''), FILTER_SANITIZE_URL);
    if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url))
        $errors['website'] = 'Invalid URL (must start with http/https)';
    else $data['website'] = $url;

    // Integer with range
    $age = filter_var($_POST['age'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 120]]);
    if ($age === false) $errors['age'] = 'Age must be an integer between 1 and 120';
    else $data['age'] = $age;

    // Float
    $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
    if ($price === false || $price < 0) $errors['price'] = 'Invalid price';
    else $data['price'] = $price;

    // String
    $name = clean_string($_POST['name'] ?? '');
    if ($name === '' || mb_strlen($name) > 50 || !preg_match('/^[\p{L} .\'-]+$/u', trim($_POST['name'] ?? '')))
        $errors['name'] = 'Name must be 1-50 letters';
    else $data['name'] = $name;

    if (!$errors) echo "<pre>Valid data:\n" . print_r($data, true) . "</pre>";
}
?>
<form method="POST">
  Name: <input name="name" value="<?= clean_string($_POST['name'] ?? '') ?>"><?= $errors['name'] ?? '' ?><br>
  Email: <input name="email" value="<?= clean_string($_POST['email'] ?? '') ?>"><?= $errors['email'] ?? '' ?><br>
  Website: <input name="website" value="<?= clean_string($_POST['website'] ?? '') ?>"><?= $errors['website'] ?? '' ?><br>
  Age: <input name="age" value="<?= clean_string($_POST['age'] ?? '') ?>"><?= $errors['age'] ?? '' ?><br>
  Price: <input name="price" value="<?= clean_string($_POST['price'] ?? '') ?>"><?= $errors['price'] ?? '' ?><br>
  <button>Submit</button>
</form>
