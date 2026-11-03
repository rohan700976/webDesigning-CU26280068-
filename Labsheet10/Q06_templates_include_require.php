<?php
// Q6: Reusable header / footer / nav templates + passing data to views

/* ---------- core/View.php ---------- */
class View {
    public static function render(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);          // makes $title, $products etc. available
        $viewFile = __DIR__ . "/../views/$view.php";
        if (!file_exists($viewFile)) { http_response_code(404); echo "View not found"; return; }
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/layouts/nav.php';
        require $viewFile;
        require __DIR__ . '/../views/layouts/footer.php';
    }
}

/* ---------- views/layouts/header.php ----------
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title ?? 'MyProject') ?></title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
*/

/* ---------- views/layouts/nav.php ----------
<nav>
  <a href="/">Home</a> | <a href="/products">Products</a> | <a href="/login">Login</a>
</nav>
<main>
*/

/* ---------- views/layouts/footer.php ----------
</main>
<footer><p>&copy; <?= date('Y') ?> MyProject</p></footer>
</body></html>
*/

/* ---------- views/products.php ----------
<h1><?= htmlspecialchars($heading) ?></h1>
<ul>
<?php foreach ($products as $p): ?>
  <li><?= htmlspecialchars($p['name']) ?> - Rs. <?= number_format($p['price'], 2) ?></li>
<?php endforeach; ?>
</ul>
*/

/* ---------- controllers/ProductController.php ---------- */
class ProductController {
    public function index() {
        $products = (new Product())->all();
        View::render('products', [
            'title'    => 'Products',
            'heading'  => 'Our Products',
            'products' => $products,
        ]);
    }
}
