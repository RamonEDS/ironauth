<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    session_unset(); 
    session_destroy(); 
    echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Nenhum usuário logado']);
}
?>