<?php
/**
 * Products API
 * GET /api/products.php - Get all products
 * POST /api/products.php - Create product
 * PUT /api/products.php - Update product
 * DELETE /api/products.php - Delete product
 */

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        $products = $stmt->fetchAll();
        jsonResponse($products);
        break;
        
    case 'POST':
        $data = getJsonInput();
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'], 
            $data['description'] ?? '', 
            $data['price'] ?? 0, 
            $data['image'] ?? '', 
            $data['category'] ?? ''
        ]);
        jsonResponse(['id' => $pdo->lastInsertId(), 'message' => 'Product created']);
        break;
        
    case 'PUT':
        $data = getJsonInput();
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, category = ? WHERE id = ?");
        $stmt->execute([
            $data['name'], 
            $data['description'] ?? '', 
            $data['price'] ?? 0, 
            $data['image'] ?? '', 
            $data['category'] ?? '',
            $data['id']
        ]);
        jsonResponse(['message' => 'Product updated']);
        break;
        
    case 'DELETE':
        $data = getJsonInput();
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$data['id']]);
        jsonResponse(['message' => 'Product deleted']);
        break;
        
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}