<?php
// c:\xampp\htdocs\halalinvois\customer_pay.php
require_once __DIR__ . '/config/db.php';

$order_id = trim($_GET['order_id'] ?? ($_POST['order_id'] ?? ''));
$stmt = $pdo->prepare("SELECT * FROM pending_qr_payments WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h3>Order Expired or Not Found</h3><p>Please ask the cashier to regenerate the DuitNow QR.</p></div>");
}

$is_paid = ($order['status'] === 'paid');

// Handle customer tapping "Pay Now" on their phone
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment']) && !$is_paid) {
    $cart_data = json_decode($order['cart_json'], true);
    $user_id   = (int)$order['user_id'];
    $total     = (float)$order['total_amount'];
    $sst       = (float)$order['sst_amount'];

    // Generate simulated LHDN UUID
    $lhdn_uuid = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );

    try {
        $pdo->beginTransaction();

        // 1. Insert into transactions table
        $ins = $pdo->prepare("
            INSERT INTO transactions (user_id, total_amount, sst_amount, payment_method, lhdn_uuid, buyer_tin, buyer_name, invoice_status) 
            VALUES (?, ?, ?, 'duitnow_qr', ?, 'EI00000000010', 'Walk-in Customer (General Public)', 'pending')
        ");
        $ins->execute([$user_id, $total, $sst, $lhdn_uuid]);
        $transaction_id = $pdo->lastInsertId();

        $receipt_ref = '#INV-' . (1000 + $transaction_id);
        $pdo->prepare("UPDATE transactions SET receipt_ref = ? WHERE transaction_id = ?")->execute([$receipt_ref, $transaction_id]);

        // 2. Insert items & reduce product stock
        $item_stmt  = $pdo->prepare("INSERT INTO transaction_items (transaction_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
        $stock_stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");

        foreach ($cart_data as $item) {
            $qty = (int)$item['quantity'];
            $price = (float)$item['price'];
            $sub = round($price * $qty, 2);
            $item_stmt->execute([$transaction_id, (int)$item['product_id'], $qty, $sub]);
            $stock_stmt->execute([$qty, (int)$item['product_id']]);
        }

        // 3. Mark pending payment as paid
        $upd = $pdo->prepare("UPDATE pending_qr_payments SET status = 'paid', transaction_id = ?, lhdn_uuid = ? WHERE order_id = ?");
        $upd->execute([$transaction_id, $lhdn_uuid, $order_id]);

        $pdo->commit();
        $is_paid = true;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

$cart_items = json_decode($order['cart_json'], true) ?: [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DuitNow QR Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .payment-card { max-width: 440px; margin: 20px auto; border-radius: 20px; overflow: hidden; }
        .duitnow-header { background: linear-gradient(135deg, #ed1c24 0%, #b91c1c 100%); color: #fff; padding: 24px; text-align: center; }
        .btn-pay { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; padding: 14px; font-size: 1.1rem; border-radius: 12px; }
        .btn-pay:hover { background: #047857; }
    </style>
</head>
<body>

<div class="container py-3">
    <div class="card payment-card border-0 shadow-lg bg-white">
        
        <!-- DuitNow Header -->
        <div class="duitnow-header">
            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle p-2 mb-2 shadow-sm" style="width: 48px; height: 48px;">
                <i class="bi bi-qr-code text-danger fs-4"></i>
            </div>
            <h5 class="fw-bold mb-0">DuitNow National QR</h5>
            <small class="opacity-75">Instant Interbank &amp; e-Wallet Settlement</small>
        </div>

        <div class="card-body p-4">
            <?php if ($is_paid): ?>
                <!-- Screen 2: Payment Successful Screen on Phone -->
                <div class="text-center py-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow" style="width: 72px; height: 72px; background-color: #10b981;">
                        <i class="bi bi-check-lg text-white fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-success mb-1">Payment Successful!</h4>
                    <p class="text-muted small">Your payment has been received by HalalInvois POS.</p>

                    <div class="border rounded-3 p-3 bg-light text-start my-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Merchant:</span>
                            <strong class="text-dark">HalalInvois - D'Melaka F&amp;B</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Amount Paid:</span>
                            <strong class="text-success fs-6">RM <?= number_format((float)$order['total_amount'], 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Order Ref:</span>
                            <span class="font-monospace text-dark"><?= htmlspecialchars($order['order_id']); ?></span>
                        </div>
                    </div>

                    <div class="alert alert-success border-0 small py-2">
                        <i class="bi bi-printer me-1"></i> Receipt is being issued on POS screen. You may close this window.
                    </div>
                </div>

            <?php else: ?>
                <!-- Screen 1: Mobile Payment Confirmation -->
                <div class="text-center pb-3 border-bottom">
                    <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill mb-2">Payment Request</span>
                    <h6 class="text-muted small mb-1">Total Amount Due</h6>
                    <h1 class="display-5 fw-bold text-dark mb-0">RM <?= number_format((float)$order['total_amount'], 2); ?></h1>
                    <small class="text-muted">Includes 6% SST (RM <?= number_format((float)$order['sst_amount'], 2); ?>)</small>
                </div>

                <!-- Merchant Details -->
                <div class="py-3 border-bottom small">
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Merchant:</span>
                        <strong class="text-dark">HalalInvois - D'Melaka Food</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Compliance:</span>
                        <span class="text-success fw-bold"><i class="bi bi-patch-check-fill"></i> 100% Halal Verified</span>
                    </div>
                </div>

                <!-- Order Item Summary -->
                <div class="py-3 border-bottom small">
                    <div class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 11px;">Order Summary</div>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($cart_items as $ci): ?>
                            <li class="d-flex justify-content-between py-1">
                                <span><?= htmlspecialchars($ci['product_name']); ?> &times; <?= (int)$ci['quantity']; ?></span>
                                <span class="fw-semibold">RM <?= number_format($ci['price'] * $ci['quantity'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Pay Button Form -->
                <form method="POST" class="mt-4">
                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id); ?>">
                    <button type="submit" name="confirm_payment" class="btn btn-success btn-pay w-100 text-white fw-bold shadow">
                        <i class="bi bi-shield-lock-fill me-1"></i> Pay RM <?= number_format((float)$order['total_amount'], 2); ?> with DuitNow
                    </button>
                </form>
                <div class="text-center mt-3">
                    <small class="text-muted" style="font-size: 11px;"><i class="bi bi-lock-fill"></i> 256-bit Simulated Payment Gateway</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>