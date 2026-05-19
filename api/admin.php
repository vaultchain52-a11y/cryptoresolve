<?php
/**
 * Admin API
 * POST /api/admin.php?action=login - Admin login
 * GET /api/admin.php?action=check - Check if logged in
 */

require_once 'db.php';
session_start();

$pdo->exec("CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$adminCount = $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
if ($adminCount === 0) {
    $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO admins (username, password, email) VALUES (?, ?, ?)');
    $stmt->execute(['admin', $defaultPassword, 'admin@codecraft.com']);
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'POST' && $action === 'login') {
    $data = getJsonInput();
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        // Create session
        session_start();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        
        jsonResponse([
            'success' => true, 
            'admin' => ['id' => $admin['id'], 'username' => $admin['username'], 'email' => $admin['email']]
        ]);
    } else {
        jsonResponse(['success' => false, 'error' => 'Invalid credentials'], 401);
    }
} elseif ($method === 'GET' && $action === 'check') {
    session_start();
    if (isset($_SESSION['admin_id'])) {
        jsonResponse(['loggedIn' => true, 'admin' => ['username' => $_SESSION['admin_username']]]);
    } else {
        jsonResponse(['loggedIn' => false]);
    }
} elseif ($method === 'POST' && $action === 'logout') {
    session_start();
    session_destroy();
    jsonResponse(['success' => true]);
} else {
    jsonResponse(['error' => 'Invalid request'], 400);
}