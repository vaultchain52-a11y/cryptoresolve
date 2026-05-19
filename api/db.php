<?php
/**
 * Database Connection Configuration
 * Update these values with your MySQL credentials
 */

$db_host = 'localhost';
$db_name = 'codecraft';
$db_user = 'root';        // Change to your MySQL username
$db_pass = '';            // Change to your MySQL password

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

// Helper function to send JSON response
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Helper function to get JSON input
function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}