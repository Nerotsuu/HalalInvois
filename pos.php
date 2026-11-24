<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get logged-in user information
$username = $_SESSION['username'] ?? 'Cashier';
$user_role = $_SESSION['role'] ?? 'cashier';
$login_time = $_SESSION['login_time'] ?? time(); // Fallback to now if not set

// Retrieve flash receipt and messages from session (if redirected after checkout)
$receipt_data    = $_SESSION['receipt_data'] ?? null;
unset($_SESSION['receipt_data']);
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
$error_message   = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);

// Correct path to db.php inside your config folder
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$success_message = "";
$error_message = "";

// Handle form submission when checkout is triggered
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $sst_rate = 0.06; // Change to 0.08 or 0.00 as needed
    $cart_data = json_decode($_POST['cart_json'], true);
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $user_id = $_SESSION['user_id']; // Default to cashier_1 if session isn't set yet
    
    if (!empty($cart_data)) {
        try {
            $pdo->beginTransaction();

            $subtotal_amount = 0.00;
            foreach ($cart_data as $item) {
                $subtotal_amount += ((float)$item['price']) * ((int)$item['quantity']);
            }

            $sst_amount  = round($subtotal_amount * $sst_rate, 2);
            $grand_total = round($subtotal_amount + $sst_amount, 2);

            // Generate simulated LHDN UUID for e-invoicing compliance
            $lhdn_uuid = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            // 1. Insert into transactions table as 'pending' e-invoice
            $stmt = $pdo->prepare("
                INSERT INTO transactions (user_id, total_amount, sst_amount, payment_method, lhdn_uuid, buyer_tin, buyer_name, invoice_status) 
                VALUES (?, ?, ?, ?, NULL, 'EI00000000010', 'Walk-in Customer (General Public)', 'pending')
            ");
            $stmt->execute([$user_id, $grand_total, $sst_amount, $payment_method]);
            $transaction_id = $pdo->lastInsertId();
            // Set receipt reference
            $receipt_ref = '#INV-' . (1000 + $transaction_id);
            $pdo->prepare("UPDATE transactions SET receipt_ref = ? WHERE transaction_id = ?")->execute([$receipt_ref, $transaction_id]);

            // 2. Insert items into transaction_items table & reduce product stock
            $item_stmt = $pdo->prepare("INSERT INTO transaction_items (transaction_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $stock_stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");

            $receipt_items = [];
            foreach ($cart_data as $item) {
                $qty = (int)$item['quantity'];
                $price = (float)$item['price'];
                $prod_id = (int)$item['product_id'];
                $line_subtotal = round($price * $qty, 2);
                $item_stmt->execute([$transaction_id, $prod_id, $qty, $line_subtotal]);
                $stock_stmt->execute([$qty, $prod_id]);
                $receipt_items[] = [
                    'name'     => $item['product_name'],
                    'qty'      => $qty,
                    'price'    => $price,
                    'subtotal' => $line_subtotal
                ];
            }
            $pdo->commit();
            // Prepare digital receipt data for session
            $receipt_data = [
                'transaction_id' => $transaction_id,
                'lhdn_uuid'      => 'Pending e-Invoice Validation',
                'receipt_ref'    => $receipt_ref,
                'items'          => $receipt_items,
                'subtotal'       => $subtotal_amount,
                'sst_amount'     => $sst_amount,
                'grand_total'    => $grand_total,
                'payment_method' => $payment_method,
                'cashier'        => $username,
                'date'           => date('d/m/Y'),
                'day'            => date('l'),
                'time'           => date('h:i:s A')
            ];
            // PRG (Post-Redirect-Get) Pattern: Redirect so F5 / reload NEVER repeats the order
            $_SESSION['receipt_data'] = $receipt_data;
            $_SESSION['success_message'] = "Transaction #{$transaction_id} successful! LHDN UUID: " . $lhdn_uuid;
            header("Location: pos.php");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Checkout failed: " . $e->getMessage();
            header("Location: pos.php");
            exit();
        }
    } else {
        $error_message = "Your cart is empty.";
    }
}

// Fetch products using our reusable function
$products = getAvailableProducts($pdo);

// Load the HTML view template
require_once __DIR__ . '/views/pos_views.php';