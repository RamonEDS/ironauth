<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$duration = (int)($data['duration'] ?? 0);
$durationType = $data['durationType'] ?? '';
$max_devices = (int)($data['max_devices'] ?? 0);
$product_id = (int)($data['product_id'] ?? 0);


if (empty($username) || empty($password) || empty($durationType) || ($duration <= 0 && $durationType !== 'lifetime') || $max_devices <= 0 || $product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios e devem ser válidos']);
    exit;
}


$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'Este username já está em uso']);
    exit;
}


$stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE id = ?');
$stmt->execute([$product_id]);
if ($stmt->fetchColumn() == 0) {
    echo json_encode(['success' => false, 'message' => 'Produto inválido']);
    exit;
}


$expires_at = new DateTime();
if ($durationType === 'lifetime') {
    $expires_at->modify('+999 days');
} else {
    switch ($durationType) {
        case 'seconds':
            $expires_at->modify("+$duration seconds");
            break;
        case 'hours':
            $expires_at->modify("+$duration hours");
            break;
        case 'days':
            $expires_at->modify("+$duration days");
            break;
        case 'weeks':
            $expires_at->modify("+$duration weeks");
            break;
        case 'months':
            $expires_at->modify("+$duration months");
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Tipo de duração inválido']);
            exit;
    }
}


$created_by = isset($_SESSION['admin_discord_id']) ? $_SESSION['admin_discord_id'] : 'admin_default';


try {
    $stmt = $pdo->prepare('INSERT INTO users (username, password, expires_at, max_devices, banned, created_by, product_id) VALUES (?, ?, ?, ?, 0, ?, ?)');
    $stmt->execute([
        $username,
        $password,
        $expires_at->format('Y-m-d H:i:s'),
        $max_devices,
        $created_by,
        $product_id
    ]);
    echo json_encode(['success' => true, 'message' => 'Usuário gerado com sucesso']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao gerar usuário: ' . $e->getMessage()]);
}
?>