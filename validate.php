<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cardNumber = $input['cardNumber'] ?? '';

if (empty($cardNumber)) {
    echo json_encode(['valid' => false, 'message' => 'Card number required']);
    exit;
}

try {
    $pdo = getDatabase();
    
    // Check if card exists and is valid
    $stmt = $pdo->prepare("
        SELECT id, expiry_date, usage_count, max_usage, is_active 
        FROM cards 
        WHERE card_number = ? AND is_active = 1
    ");
    $stmt->execute([$cardNumber]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$card) {
        echo json_encode(['valid' => false, 'message' => 'Invalid card']);
        exit;
    }
    
    // Check expiry
    if ($card['expiry_date'] && strtotime($card['expiry_date']) < time()) {
        echo json_encode(['valid' => false, 'message' => 'Card expired']);
        exit;
    }
    
    // Check usage limit
    if ($card['max_usage'] > 0 && $card['usage_count'] >= $card['max_usage']) {
        echo json_encode(['valid' => false, 'message' => 'Usage limit exceeded']);
        exit;
    }
    
    // Update usage count
    $stmt = $pdo->prepare("UPDATE cards SET usage_count = usage_count + 1, last_used = NOW() WHERE id = ?");
    $stmt->execute([$card['id']]);
    
    // Log the usage
    $stmt = $pdo->prepare("INSERT INTO usage_logs (card_id, ip_address, user_agent, login_time) VALUES (?, ?, ?, NOW())");
    $stmt->execute([
        $card['id'],
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    echo json_encode(['valid' => true, 'message' => 'Card validated']);
    
} catch (Exception $e) {
    error_log("Card validation error: " . $e->getMessage());
    echo json_encode(['valid' => true, 'message' => 'Validation unavailable']);
}
?>