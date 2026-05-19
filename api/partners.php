<?php
/**
 * Partners API
 * GET /api/partners.php - Get all partners
 * POST /api/partners.php - Create partner
 * PUT /api/partners.php - Update partner
 * DELETE /api/partners.php - Delete partner
 */

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM partners ORDER BY created_at DESC");
        $partners = $stmt->fetchAll();
        jsonResponse($partners);
        break;
        
    case 'POST':
        $data = getJsonInput();
        $stmt = $pdo->prepare("INSERT INTO partners (name, logo, website) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['logo'] ?? '', $data['website'] ?? '']);
        jsonResponse(['id' => $pdo->lastInsertId(), 'message' => 'Partner created']);
        break;
        
    case 'PUT':
        $data = getJsonInput();
        $stmt = $pdo->prepare("UPDATE partners SET name = ?, logo = ?, website = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['logo'] ?? '', $data['website'] ?? '', $data['id']]);
        jsonResponse(['message' => 'Partner updated']);
        break;
        
    case 'DELETE':
        $data = getJsonInput();
        $stmt = $pdo->prepare("DELETE FROM partners WHERE id = ?");
        $stmt->execute([$data['id']]);
        jsonResponse(['message' => 'Partner deleted']);
        break;
        
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}