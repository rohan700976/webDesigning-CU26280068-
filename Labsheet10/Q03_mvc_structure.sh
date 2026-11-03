#!/bin/bash
# Q3: Restructure project into MVC layout
mkdir -p myproject/{models,views/layouts,controllers,public,config}
cd myproject

cat > config/config.php <<'PHP'
<?php
return [
    'db' => ['host'=>'localhost','name'=>'myproject','user'=>'app_user','pass'=>'StrongPass!23','charset'=>'utf8mb4'],
    'base_url' => 'https://myproject.local',
];
PHP

cat > controllers/HomeController.php <<'PHP'
<?php
class HomeController {
    public function index() { require __DIR__ . '/../views/home.php'; }
}
PHP

cat > models/Product.php <<'PHP'
<?php
class Product { /* see Q5 */ }
PHP

cat > views/home.php <<'PHP'
<h1>Welcome to MyProject (MVC)</h1>
PHP

cat > public/index.php <<'PHP'
<?php
require __DIR__ . '/../controllers/HomeController.php';
(new HomeController)->index();
PHP

# Resulting tree:
# myproject/
# ├── config/      (settings)
# ├── controllers/ (request handling)
# ├── models/      (data + DB logic)
# ├── views/       (HTML templates)
# └── public/      (web root: index.php, css, js)
find . -type f | sort
git add . && git commit -m "refactor: restructure into MVC layout"
