<?php
session_start();
header('Content-Type: application/json');

// Verifica se é admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado!']);
    exit;
}

// Inclui o arquivo de conexão com o banco de dados
require_once 'db.php'; // Já está no mesmo diretório ../api/, então o caminho é relativo

// Recebe a requisição
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    switch ($action) {
        case 'get_cheat_status':
            $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'cheat_api_status'");
            $stmt->execute();
            $status = $stmt->fetchColumn() ?: 'on'; // Default 'on' se não existir
            echo json_encode(['status' => $status]);
            break;
        
        case 'get_login_status':
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE paused = 1 AND banned = 0");
            $stmt->execute();
            $pausedCount = $stmt->fetchColumn();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE banned = 0");
            $stmt->execute();
            $totalActive = $stmt->fetchColumn();
            $status = ($pausedCount > 0 && $pausedCount == $totalActive) ? 'off' : 'active';
            echo json_encode(['status' => $status]);
            break;

        case 'pause_all':
            $stmt = $pdo->prepare("UPDATE users SET paused = 1 WHERE banned = 0");
            $stmt->execute();
            $affected = $stmt->rowCount();
            echo json_encode([
                'success' => true,
                'message' => "Pausados $affected logins com sucesso!"
            ]);
            break;

        case 'unpause_all':
            $stmt = $pdo->prepare("UPDATE users SET paused = 0 WHERE banned = 0");
            $stmt->execute();
            $affected = $stmt->rowCount();
            echo json_encode([
                'success' => true,
                'message' => "Despausados $affected logins com sucesso!"
            ]);
            break;

        case 'pause_cheat':
            $stmt = $pdo->prepare("UPDATE settings SET value = 'off' WHERE name = 'cheat_api_status'");
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Cheat pausado e API desligada com sucesso!']);
            break;

        case 'unpause_cheat':
            $stmt = $pdo->prepare("UPDATE settings SET value = 'on' WHERE name = 'cheat_api_status'");
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Cheat despausado e API ligada com sucesso!']);
            break;

        case 'add_days_all':
            $days = intval($input['days'] ?? 0);
            if ($days <= 0) {
                echo json_encode(['success' => false, 'message' => 'Quantidade de dias inválida!']);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE users SET expires_at = DATE_ADD(expires_at, INTERVAL :days DAY) WHERE banned = 0 AND expires_at > NOW()");
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            $affected = $stmt->rowCount();
            echo json_encode([
                'success' => true,
                'message' => "Adicionados $days dias a $affected usuários ativos!"
            ]);
            break;

        case 'delete_expired_users':
            $stmt = $pdo->prepare("DELETE FROM users WHERE expires_at < NOW()");
            $stmt->execute();
            $affected = $stmt->rowCount();
            if ($affected > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => "Deletados $affected usuários expirados com sucesso!"
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => "Nenhum usuário expirado encontrado para deletar."
                ]);
            }
            break;

        case 'reset_all_hwids':
            $stmt = $pdo->prepare("DELETE FROM user_devices");
            $stmt->execute();
            $affected = $stmt->rowCount();
            if ($affected > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => "Resetados $affected HWIDs com sucesso!"
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => "Nenhum HWID encontrado para resetar."
                ]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida!']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao processar ação: ' . $e->getMessage()]);
}
?>