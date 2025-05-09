<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';

if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Username é obrigatório']);
    exit;
}


$stmt = $pdo->prepare('SELECT hwid_reset_count FROM users WHERE username = ? AND created_by = ?');
$stmt->execute([$username, $_SESSION['client_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário inválido ou não pertence à sua conta']);
    exit;
}

if ($user['hwid_reset_count'] >= 2) {
    echo json_encode(['success' => false, 'message' => 'Limite de redefinições de HWID atingido (2)']);
    exit;
}

try {
    
    $stmt = $pdo->prepare('DELETE FROM user_devices WHERE username = ?');
    $stmt->execute([$username]);

    
    $stmt = $pdo->prepare('UPDATE users SET hwid_reset_count = hwid_reset_count + 1 WHERE username = ?');
    $stmt->execute([$username]);

    echo json_encode(['success' => true, 'message' => 'HWID redefinido com sucesso']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao redefinir HWID: ' . $e->getMessage()]);
}
?>