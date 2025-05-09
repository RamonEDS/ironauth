<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$key_code = $data['key_code'] ?? '';
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($key_code) || empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Chave, username e senha são obrigatórios']);
    exit;
}


$stmt = $pdo->prepare('
    SELECT id, duration_days, max_uses, product_id, redeemed_by
    FROM license_keys
    WHERE key_code = ? AND redeemed_by IS NULL
');
$stmt->execute([$key_code]);
$key = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$key) {
    echo json_encode(['success' => false, 'message' => 'Chave inválida ou já resgatada']);
    exit;
}


$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'Este username já está em uso']);
    exit;
}


$expires_at = new DateTime();
$expires_at->modify("+{$key['duration_days']} days");


try {
    $stmt = $pdo->prepare('
        INSERT INTO users (username, password, expires_at, max_devices, product_id, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $username,
        $password, 
        $expires_at->format('Y-m-d H:i:s'),
        $key['max_uses'], 
        $key['product_id'],
        $_SESSION['client_id']
    ]);

    
    $stmt = $pdo->prepare('
        UPDATE license_keys
        SET redeemed_by = ?, redeemed_at = NOW()
        WHERE id = ?
    ');
    $stmt->execute([$_SESSION['client_id'], $key['id']]);

    echo json_encode(['success' => true, 'message' => 'Chave resgatada com sucesso']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao resgatar chave: ' . $e->getMessage()]);
}
?>