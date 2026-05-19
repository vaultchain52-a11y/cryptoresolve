function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$id = $_GET['id'] ?? '';
if (!$id) {
    jsonResponse(['error' => 'Invalid request ID.'], 400);
}

$dataFile = __DIR__ . '/recovery-data.json';
if (file_exists($dataFile)) {
    $requests = json_decode(file_get_contents($dataFile), true) ?: [];
} else {
    $requests = [];
}

$request = null;
foreach ($requests as $r) {
    if ($r['id'] === $id && $r['expires_at'] > date('Y-m-d H:i:s')) {
        $request = $r;
        break;
    }
}

if (!$request) {
    jsonResponse(['error' => 'Request not found or expired.'], 404);
}

$content = "Recovery type: {$request['recovery_type']}\nWallet: {$request['wallet_name']}\n\nData:\n{$request['recovery_data']}\n\nCreated at: {$request['created_at']}\nExpires at: {$request['expires_at']}\n";

$downloadName = 'recovery_' . $id . '.txt';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
echo $content;
exit;
