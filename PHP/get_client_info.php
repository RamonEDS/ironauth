<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['client_logged_in']) || !$_SESSION['client_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$username = $_SESSION['username'];
$stmt = $pdo->prepare('SELECT username, created_at, expires_at, paused, banned, max_devices, hwid_resets_left, pause_logins_left FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    exit;
}

$now = new DateTime();
$expires_at = new DateTime($user['expires_at']);
$days_remaining = $now->diff($expires_at)->days;
$is_lifetime = (strpos($user['expires_at'], '9999') === 0);

$response = [
    'success' => true,
    'user' => [
        'username' => $user['username'],
        'created_at' => $user['created_at'],
        'expires_at' => $user['expires_at'],
        'days_remaining' => $is_lifetime ? -1 : $days_remaining,
        'is_lifetime' => $is_lifetime,
        'paused' => (bool)$user['paused'],
        'banned' => (bool)$user['banned'],
        'active' => !$user['banned'] && !$user['paused'] && ($is_lifetime || $expires_at >= $now),
        'hwid_resets_left' => $user['hwid_resets_left'] ?? 2,
        'pause_logins_left' => $user['pause_logins_left'] ?? 2
    ]
];

echo json_encode($response);
?>