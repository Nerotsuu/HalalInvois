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

            // 1. Insert into transactions table
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, total_amount, sst_amount, payment_method, lhdn_uuid) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $grand_total, $sst_amount, $payment_method, $lhdn_uuid]);
            $transaction_id = $pdo->lastInsertId();

            // 2. Insert items into transaction_items table & reduce product stock
            $item_stmt = $pdo->prepare("INSERT INTO transaction_items (transaction_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $stock_stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");

            foreach ($cart_data as $item) {
                $qty = (int)$item['quantity'];
                $price = (float)$item['price'];
                $prod_id = (int)$item['product_id'];
                $line_subtotal = round($price * $qty, 2);

                $item_stmt->execute([$transaction_id, $prod_id, $qty, $line_subtotal]);
                $stock_stmt->execute([$qty, $prod_id]);
            }

            $pdo->commit();
            $success_message = "Transaction successful! Total: RM " . number_format($grand_total, 2) . " (SST: RM " . number_format($sst_amount, 2) . ") | Simulated LHDN UUID: " . $lhdn_uuid;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Checkout failed: " . $e->getMessage();
        }
    } else {
        $error_message = "Your cart is empty.";
    }
}

// Fetch products using our reusable function
$products = getAvailableProducts($pdo);

// Load the HTML view template
require_once __DIR__ . '/views/pos_views.php';