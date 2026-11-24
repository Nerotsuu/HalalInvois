<?php
// c:\xampp\htdocs\halalinvois\check_payment_status.php
header('Content-Type: application/json');
require_once __DIR__ . '/config/db.php';

$order_id = trim($_GET['order_id'] ?? '');

if ($order_id === '') {
    echo json_encode(['status' => 'waiting']);
    exit();
}

$stmt = $pdo->prepare("
    SELECT p.*, t.receipt_ref, u.username AS cashier_name
    FROM pending_qr_payments p
    LEFT JOIN transactions t ON p.transaction_id = t.transaction_id
    LEFT JOIN users u ON p.user_id = u.user_id
    WHERE p.order_id = ?
");
$stmt->execute([$order_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($payment && $payment['status'] === 'paid') {
    $cart_data = json_decode($payment['cart_json'], true) ?: [];
    $receipt_items = [];
    foreach ($cart_data as $it) {
        $qty = (int)$it['quantity'];
        $price = (float)$it['price'];
        $receipt_items[] = [
            'name'     => $it['product_name'],
            'qty'      => $qty,
            'price'    => $price,
            'subtotal' => round($price * $qty, 2)
        ];
    }

    echo json_encode([
        'status'         => 'paid',
        'transaction_id' => $payment['transaction_id'],
        'receipt_ref'    => $payment['receipt_ref'] ?: ('#INV-' . (1000 + (int)$payment['transaction_id'])),
        'lhdn_uuid'      => $payment['lhdn_uuid'],
        'receipt'        => [
        'transaction_id' => $payment['transaction_id'],
        'receipt_ref'    => $payment['receipt_ref'],
        'lhdn_uuid'      => $payment['lhdn_uuid'],
        'items'          => $receipt_items,
        'subtotal'       => max(0, $payment['total_amount'] - $payment['sst_amount']),
        'sst_amount'     => (float)$payment['sst_amount'],
        'grand_total'    => (float)$payment['total_amount'],
        'payment_method' => 'duitnow_qr',
        'cashier'        => $payment['cashier_name'] ?: 'Cashier',
        'date'           => date('d/m/Y'),
        'day'            => date('l'),
        'time'           => date('h:i:s A')
        ]
    ]);
} else {
    echo json_encode(['status' => 'waiting']);
}