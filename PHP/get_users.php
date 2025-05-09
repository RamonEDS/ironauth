<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT 
            u.username, 
            u.created_at, 
            u.expires_at, 
            u.max_devices, 
            u.banned, 
            GROUP_CONCAT(d.hwid) as hwid
        FROM users u
        LEFT JOIN user_devices d ON u.username = d.username
        GROUP BY u.username, u.created_at, u.expires_at, u.max_devices, u.banned
    ');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar usuários: ' . $e->getMessage()]);
}
?>