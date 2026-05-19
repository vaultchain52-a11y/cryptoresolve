<?php
/**
 * Inquiries API
 * GET /api/inquiries.php - Get all inquiries
 * POST /api/inquiries.php - Create inquiry
 * PUT /api/inquiries.php - Update inquiry status
 * DELETE /api/inquiries.php - Delete inquiry
 */

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC");
        $inquiries = $stmt->fetchAll();
        jsonResponse($inquiries);
        break;
        
    case 'POST':
        $data = getJsonInput();
        $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, phone, service, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'], 
            $data['email'], 
            $data['phone'] ?? '', 
            $data['service'] ?? '', 
            $data['message'] ?? ''
        ]);
        jsonResponse(['id' => $pdo->lastInsertId(), 'message' => 'Inquiry submitted']);
        break;
        
    case 'PUT':
        $data = getJsonInput();
        $stmt = $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['id']]);
        jsonResponse(['message' => 'Inquiry updated']);
        break;
        
    case 'DELETE':
        $data = getJsonInput();
        $stmt = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
        $stmt->execute([$data['id']]);
        jsonResponse(['message' => 'Inquiry deleted']);
        break;
        
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}