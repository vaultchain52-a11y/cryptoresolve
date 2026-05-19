<?php
/**
 * Tickets API
 * GET /api/tickets.php - Get all tickets
 * POST /api/tickets.php - Create ticket
 * PUT /api/tickets.php - Update ticket
 * DELETE /api/tickets.php - Delete ticket
 */

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM tickets ORDER BY created_at DESC");
        $tickets = $stmt->fetchAll();
        jsonResponse($tickets);
        break;
        
    case 'POST':
        $data = getJsonInput();
        $stmt = $pdo->prepare("INSERT INTO tickets (customer_id, subject, description, status, priority) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['customer_id'] ?? null, 
            $data['subject'], 
            $data['description'] ?? '', 
            $data['status'] ?? 'open', 
            $data['priority'] ?? 'medium'
        ]);
        jsonResponse(['id' => $pdo->lastInsertId(), 'message' => 'Ticket created']);
        break;
        
    case 'PUT':
        $data = getJsonInput();
        $stmt = $pdo->prepare("UPDATE tickets SET subject = ?, description = ?, status = ?, priority = ? WHERE id = ?");
        $stmt->execute([
            $data['subject'], 
            $data['description'] ?? '', 
            $data['status'] ?? 'open', 
            $data['priority'] ?? 'medium',
            $data['id']
        ]);
        jsonResponse(['message' => 'Ticket updated']);
        break;
        
    case 'DELETE':
        $data = getJsonInput();
        $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
        $stmt->execute([$data['id']]);
        jsonResponse(['message' => 'Ticket deleted']);
        break;
        
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}