<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if ($username === ADMIN_USER && password_verify($password, ADMIN_PASS)) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = '768913772843761704';
    echo json_encode(['success' => true, 'message' => 'Login bem-sucedido']);
} else {
    echo json_encode(['success' => false, 'message' => 'Credenciais inválidas']);
}
?> 