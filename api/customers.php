<?php
/**
 * Customers API
 * GET /api/customers.php - Get all customers
 * POST /api/customers.php - Create customer
 * PUT /api/customers.php - Update customer
 * DELETE /api/customers.php - Delete customer
 */

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
        $customers = $stmt->fetchAll();
        jsonResponse($customers);
        break;
        
    case 'POST':
        $data = getJsonInput();
        $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, company) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['name'], 
            $data['email'] ?? '', 
            $data['phone'] ?? '', 
            $data['company'] ?? ''
        ]);
        jsonResponse(['id' => $pdo->lastInsertId(), 'message' => 'Customer created']);
        break;
        
    case 'PUT':
        $data = getJsonInput();
        $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, company = ? WHERE id = ?");
        $stmt->execute([
            $data['name'], 
            $data['email'] ?? '', 
            $data['phone'] ?? '', 
            $data['company'] ?? '',
            $data['id']
        ]);
        jsonResponse(['message' => 'Customer updated']);
        break;
        
    case 'DELETE':
        $data = getJsonInput();
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$data['id']]);
        jsonResponse(['message' => 'Customer deleted']);
        break;
        
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}