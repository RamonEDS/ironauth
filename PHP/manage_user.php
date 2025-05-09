<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';
$username = $data['username'] ?? '';
$value = $data['value'] ?? '';
$durationType = $data['durationType'] ?? '';

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    exit;
}

try {
    switch ($action) {
        case 'delete':
            $stmt = $pdo->prepare('DELETE FROM users WHERE username = ?');
            $stmt->execute([$username]);
            echo json_encode(['success' => true, 'message' => 'Usuário deletado com sucesso']);
            break;
        case 'ban':
            if ($user['banned']) throw new Exception('Usuário já está banido');
            $stmt = $pdo->prepare('UPDATE users SET banned = 1 WHERE username = ?');
            $stmt->execute([$username]);
            echo json_encode(['success' => true, 'message' => 'Usuário banido']);
            break;
        case 'unban':
            if (!$user['banned']) throw new Exception('Usuário não está banido');
            $stmt = $pdo->prepare('UPDATE users SET banned = 0 WHERE username = ?');
            $stmt->execute([$username]);
            echo json_encode(['success' => true, 'message' => 'Usuário desbanido']);
            break;
        case 'change_username':
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
            $stmt->execute([$value]);
            if ($stmt->fetchColumn() > 0) throw new Exception('Novo username já está em uso');
            $stmt = $pdo->prepare('UPDATE users SET username = ? WHERE username = ?');
            $stmt->execute([$value, $username]);
            echo json_encode(['success' => true, 'message' => 'Username alterado']);
            break;
        case 'change_password':
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE username = ?');
            $stmt->execute([$value, $username]);
            echo json_encode(['success' => true, 'message' => 'Senha alterada']);
            break;
        case 'add_duration':
        case 'remove_duration':
            $expiresAt = new DateTime($user['expires_at'] ?? 'now');
            $amount = (int)$value;
            if ($action === 'remove_duration') $amount = -$amount;
            if ($durationType === 'lifetime') $amount = 999;
            switch ($durationType) {
                case 'seconds':
                    $expiresAt->modify("$amount seconds");
                    break;
                case 'hours':
                    $expiresAt->modify("$amount hours");
                    break;
                case 'days':
                    $expiresAt->modify("$amount days");
                    break;
                case 'weeks':
                    $expiresAt->modify("$amount weeks");
                    break;
                case 'months':
                    $expiresAt->modify("$amount months");
                    break;
                case 'lifetime':
                    $expiresAt->modify("+999 days");
                    break;
                default:
                    throw new Exception('Tipo de duração inválido');
            }
            $stmt = $pdo->prepare('UPDATE users SET expires_at = ? WHERE username = ?');
            $stmt->execute([$expiresAt->format('Y-m-d H:i:s'), $username]);
            echo json_encode(['success' => true, 'message' => $amount > 0 ? 'Duração adicionada' : 'Duração removida']);
            break;
        case 'add_devices':
        case 'remove_devices':
            $newMaxDevices = ($user['max_devices'] ?? 0) + (int)$value;
            if ($newMaxDevices < 0) $newMaxDevices = 0;
            $stmt = $pdo->prepare('UPDATE users SET max_devices = ? WHERE username = ?');
            $stmt->execute([$newMaxDevices, $username]);
            echo json_encode(['success' => true, 'message' => $value > 0 ? 'Devices adicionados' : 'Devices removidos']);
            break;
        case 'reset_hwid':
            $stmt = $pdo->prepare('UPDATE user_devices SET hwid = NULL WHERE username = ?');
            $stmt->execute([$username]);
            echo json_encode(['success' => true, 'message' => 'HWID resetado']);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>