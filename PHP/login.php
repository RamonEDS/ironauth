<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';


$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$hwid = $data['hwid'] ?? '';
$product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;


if (empty($username) || empty($password) || empty($hwid) || $product_id < 1) {
    echo json_encode(['success' => false, 'message' => 'Usuário, senha, HWID e product_id são obrigatórios']);
    exit;
}


$stmt = $pdo->prepare('
    SELECT username, password, expires_at, paused, banned, max_devices, product_id
    FROM users
    WHERE username = ? AND password = ? AND banned = 0
');
$stmt->execute([$username, $password]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário ou senha inválidos']);
    exit;
}


if ($user['product_id'] != $product_id) {
    $expected_product = $user['product_id'] == 1 ? 'Void_trick' : 'Void_bypass';
    echo json_encode(['success' => false, 'message' => "Produto inválido: este usuário é para $expected_product"]);
    exit;
}


if ($user['paused'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Login pausado']);
    exit;
}


$now = new DateTime();
$expires_at = new DateTime($user['expires_at']);
if ($expires_at < $now) {
    echo json_encode(['success' => false, 'message' => 'Login expirado']);
    exit;
}
$days_remaining = $now->diff($expires_at)->days;
$is_lifetime = (strpos($user['expires_at'], '9999') === 0);


$stmt = $pdo->prepare('SELECT COUNT(*) FROM user_devices WHERE username = ?');
$stmt->execute([$username]);
$current_devices = $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT hwid FROM user_devices WHERE username = ? AND hwid = ?');
$stmt->execute([$username, $hwid]);
$device_exists = $stmt->fetchColumn();

if (!$device_exists) {
    if ($current_devices >= $user['max_devices']) {
        echo json_encode(['success' => false, 'message' => 'Limite de dispositivos atingido']);
        exit;
    }
    
    $stmt = $pdo->prepare('INSERT INTO user_devices (username, hwid) VALUES (?, ?)');
    $stmt->execute([$username, $hwid]);
}


if ($device_exists) {
    $stmt = $pdo->prepare('UPDATE user_devices SET last_login = NOW() WHERE username = ? AND hwid = ?');
    $stmt->execute([$username, $hwid]);
}


$response = [
    'success' => true,
    'message' => $device_exists ? 'Login OK' : 'Login OK, novo dispositivo registrado',
    'username' => $user['username'],
    'expires_at' => $user['expires_at'],
    'days_remaining' => $is_lifetime ? -1 : $days_remaining,
    'is_lifetime' => $is_lifetime,
    'product_id' => $user['product_id'] 
];


echo json_encode($response);
?>