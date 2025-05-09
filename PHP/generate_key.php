<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';


if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}


$data = json_decode(file_get_contents('php://input'), true);
$duration = isset($data['duration']) ? (int)$data['duration'] : 0;
$max_uses = isset($data['max_uses']) ? (int)$data['max_uses'] : 0;
$key_type = isset($data['key_type']) && in_array($data['key_type'], ['standard', 'premium']) ? $data['key_type'] : 'standard';
$product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;


if ($duration < 1 || $max_uses < 1 || $product_id < 1) {
    echo json_encode(['success' => false, 'message' => 'Duração, usos e produto devem ser válidos']);
    exit;
}


function generateUniqueKey($pdo) {
    $max_attempts = 10; 
    $attempt = 0;

    do {
        
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $key = '';
        for ($i = 0; $i < 4; $i++) { 
            for ($j = 0; $j < 4; $j++) {
                $key .= $chars[random_int(0, strlen($chars) - 1)];
            }
            if ($i < 3) $key .= '-';
        }

        
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM license_keys WHERE key_code = ?');
        $stmt->execute([$key]);
        $exists = $stmt->fetchColumn();

        $attempt++;
        if ($attempt >= $max_attempts) {
            throw new Exception('Não foi possível gerar uma chave única após ' . $max_attempts . ' tentativas');
        }
    } while ($exists);

    return $key;
}

try {
    
    $pdo->beginTransaction();

    $key = generateUniqueKey($pdo);
    $created_at = date('Y-m-d H:i:s');

   
    $stmt = $pdo->prepare('
        INSERT INTO license_keys (key_code, duration_days, max_uses, key_type, created_at, product_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$key, $duration, $max_uses, $key_type, $created_at, $product_id]);

    
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Chave gerada com sucesso',
        'key' => $key
    ]);
} catch (Exception $e) {
   
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
   
    error_log('Erro ao gerar chave: ' . $e->getMessage(), 3, '/var/log/php_errors.log');
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao gerar chave: ' . $e->getMessage()
    ]);
}
?>