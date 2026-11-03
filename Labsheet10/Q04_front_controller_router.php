<?php
// Q4: public/index.php  - Front controller with simple router
declare(strict_types=1);
spl_autoload_register(function ($class) {
    foreach (['controllers', 'models', 'core'] as $dir) {
        $file = __DIR__ . "/../$dir/$class.php";
        if (file_exists($file)) { require $file; return; }
    }
});

class Router {
    private array $routes = [];

    public function add(string $method, string $path, string $handler): void {
        $this->routes[] = [$method, $path, $handler];
    }

    public function dispatch(string $method, string $uri): void {
        $path = rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';
        foreach ($this->routes as [$m, $route, $handler]) {
            // convert /product/{id} to regex
            $pattern = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $route) . '$#';
            if ($m === $method && preg_match($pattern, $path, $matches)) {
                [$controller, $action] = explode('@', $handler);
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                (new $controller)->$action(...array_values($params));
                return;
            }
        }
        http_response_code(404);
        echo '404 - Page Not Found';
    }
}

$router = new Router();
$router->add('GET',  '/',              'HomeController@index');
$router->add('GET',  '/products',      'ProductController@index');
$router->add('GET',  '/product/{id}',  'ProductController@show');
$router->add('POST', '/product/store', 'ProductController@store');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

/* ---- controllers/ProductController.php (example) ----
class ProductController {
    public function index()      { echo "All products"; }
    public function show($id)    { echo "Product #" . (int)$id; }
    public function store()      { echo "Store product"; }
}
*/
