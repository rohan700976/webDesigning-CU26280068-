<?php
// Q7: public/api/products.php - RESTful JSON API (GET, POST, PUT, DELETE)
// URLs:  GET /api/products.php        -> list
//        GET /api/products.php?id=1   -> single
//        POST, PUT ?id=1, DELETE ?id=1
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../core/Database.php';   // Database + Model + Product (Q5)

$method  = $_SERVER['REQUEST_METHOD'];
$id      = isset($_GET['id']) ? (int)$_GET['id'] : null;
$input   = json_decode(file_get_contents('php://input'), true) ?? [];
$product = new Product();

function respond(int $code, $data): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $row = $product->find($id);
                $row ? respond(200, $row) : respond(404, ['error' => 'Product not found']);
            }
            respond(200, $product->all());

        case 'POST':
            if (empty($input['name']) || !isset($input['price']))
                respond(422, ['error' => 'name and price are required']);
            $newId = $product->create([
                'name' => $input['name'], 'price' => $input['price'],
                'description' => $input['description'] ?? null,
            ]);
            respond(201, ['message' => 'Created', 'id' => $newId]);

        case 'PUT':
            if (!$id || !$product->find($id)) respond(404, ['error' => 'Product not found']);
            $allowed = array_intersect_key($input, array_flip(['name','price','description']));
            if (!$allowed) respond(422, ['error' => 'No valid fields']);
            $product->update($id, $allowed);
            respond(200, ['message' => 'Updated']);

        case 'DELETE':
            if (!$id || !$product->find($id)) respond(404, ['error' => 'Product not found']);
            $product->delete($id);
            respond(200, ['message' => 'Deleted']);

        default:
            respond(405, ['error' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    respond(500, ['error' => 'Server error']);
}

/* Test with curl:
curl -X POST   -H "Content-Type: application/json" -d '{"name":"Pen","price":10}' http://myproject.local/api/products.php
curl -X PUT    -H "Content-Type: application/json" -d '{"price":15}' "http://myproject.local/api/products.php?id=1"
curl -X DELETE "http://myproject.local/api/products.php?id=1"
*/
