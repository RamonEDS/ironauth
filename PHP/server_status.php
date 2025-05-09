<?php
session_start();
header('Content-Type: application/json');

// Verifica se é admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado!']);
    exit;
}

// Inclui o arquivo de conexão com o banco de dados
require_once 'db.php';

try {
    // Obter status do cheat
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'cheat_api_status'");
    $stmt->execute();
    $cheatStatus = $stmt->fetchColumn() ?: 'on'; // Default 'on' se não existir

    // Obter status dos logins
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE paused = 1 AND banned = 0");
    $stmt->execute();
    $pausedCount = $stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE banned = 0");
    $stmt->execute();
    $totalActive = $stmt->fetchColumn();
    $loginStatus = ($pausedCount > 0 && $pausedCount == $totalActive) ? 'off' : 'active';

    // Preparar resposta no formato esperado pela dashboard
    $response = [
        'success' => true,
        'server_status' => $cheatStatus === 'on',
        'login_status' => $loginStatus === 'active'
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar status: ' . $e->getMessage()
    ]);
}
?>