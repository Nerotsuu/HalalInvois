<?php
// c:\xampp\htdocs\halalinvois\create_qr_order.php
header('Content-Type: application/json');
require_once __DIR__ . '/config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Empty cart']);
    exit();
}

$order_id     = 'ORD-' . date('YmdHis') . '-' . mt_rand(100, 999);
$user_id      = (int)($data['user_id'] ?? 2);
$total_amount = (float)($data['total_amount'] ?? 0);
$sst_amount   = (float)($data['sst_amount'] ?? 0);
$cart_json    = json_encode($data['cart']);

try {
    $stmt = $pdo->prepare("
        INSERT INTO pending_qr_payments (order_id, user_id, total_amount, sst_amount, cart_json, status)
        VALUES (?, ?, ?, ?, ?, 'waiting')
    ");
    $stmt->execute([$order_id, $user_id, $total_amount, $sst_amount, $cart_json]);

    echo json_encode([
        'status'   => 'success',
        'order_id' => $order_id
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}