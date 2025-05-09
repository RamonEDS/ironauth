<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT lk.key_code, lk.created_at, lk.is_redeemed, lk.redeemed_by_username
        FROM license_keys lk
        LEFT JOIN users u ON lk.redeemed_by_username = u.username
        ORDER BY lk.created_at DESC
        LIMIT 10
    ');
    $stmt->execute();
    $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($keys as &$key) {
        $key['created_at'] = (new DateTime($key['created_at'], new DateTimeZone('America/Sao_Paulo')))
            ->format('d/m/Y H:i:s');
        $key['is_redeemed'] = $key['is_redeemed'] ? 'Sim' : 'Não';
        $key['redeemed_by_username'] = !empty($key['redeemed_by_username']) ? $key['redeemed_by_username'] : 'N/A';
    }

    echo json_encode(['success' => true, 'keys' => $keys]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar chaves: ' . $e->getMessage()]);
}
?>