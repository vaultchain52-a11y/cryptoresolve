<?php
/**
 * Ideas API
 * GET /api/ideas.php - Get all ideas
 * POST /api/ideas.php - Create idea
 * PUT /api/ideas.php - Update idea
 * DELETE /api/ideas.php - Delete idea
 */

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM ideas ORDER BY created_at DESC");
        $ideas = $stmt->fetchAll();
        jsonResponse($ideas);
        break;
        
    case 'POST':
        $data = getJsonInput();
        $stmt = $pdo->prepare("INSERT INTO ideas (title, description, status, votes) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['title'], 
            $data['description'] ?? '', 
            $data['status'] ?? 'pending', 
            $data['votes'] ?? 0
        ]);
        jsonResponse(['id' => $pdo->lastInsertId(), 'message' => 'Idea created']);
        break;
        
    case 'PUT':
        $data = getJsonInput();
        $stmt = $pdo->prepare("UPDATE ideas SET title = ?, description = ?, status = ?, votes = ? WHERE id = ?");
        $stmt->execute([
            $data['title'], 
            $data['description'] ?? '', 
            $data['status'] ?? 'pending', 
            $data['votes'] ?? 0,
            $data['id']
        ]);
        jsonResponse(['message' => 'Idea updated']);
        break;
        
    case 'DELETE':
        $data = getJsonInput();
        $stmt = $pdo->prepare("DELETE FROM ideas WHERE id = ?");
        $stmt->execute([$data['id']]);
        jsonResponse(['message' => 'Idea deleted']);
        break;
        
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}