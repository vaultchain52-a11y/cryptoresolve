<?php
require __DIR__ . '/db.php';
// session_start();

// if (!isset($_SESSION['admin_id'])) {
//     jsonResponse(['error' => 'Unauthorized access.'], 403);
// }

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$dataFile = __DIR__ . '/recovery-data.json';
if (file_exists($dataFile)) {
    $requests = json_decode(file_get_contents($dataFile), true) ?: [];
} else {
    $requests = [];
}

// Filter out expired
$currentTime = date('Y-m-d H:i:s');
$validRequests = array_filter($requests, function($r) use ($currentTime) {
    return $r['expires_at'] > $currentTime;
});

// Update the file with only valid
file_put_contents($dataFile, json_encode(array_values($validRequests), JSON_PRETTY_PRINT));

jsonResponse(['success' => true, 'data' => $validRequests]);
