<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

header('Content-Type: application/json');
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$id = $input['id'] ?? '';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($action === 'pause') {
        $stmt = $pdo->prepare('UPDATE dlls SET status = "paused" WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'DLL pausada']);
    } elseif ($action === 'activate') {
        $stmt = $pdo->prepare('UPDATE dlls SET status = "active" WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'DLL ativada']);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('SELECT filename FROM dlls WHERE id = ?');
        $stmt->execute([$id]);
        $dll = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dll) {
            unlink('Uploads/dlls/' . $dll['filename']);
            $stmt = $pdo->prepare('DELETE FROM dlls WHERE id = ?');
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'DLL deletada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'DLL não encontrada']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>