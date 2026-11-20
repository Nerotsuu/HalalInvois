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
    $cart_data = json_decode($_POST['cart_json'], true);
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $user_id = $_SESSION['user_id']; // Default to cashier_1 if session isn't set yet

    if (!empty($cart_data)) {
        try {
            $pdo->beginTransaction();

            $total_amount = 0;
            foreach ($cart_data as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }
            $sst_amount = 0.00;
            $grand_total = $total_amount + $sst_amount;
            
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

            // 2. Insert items into transaction_items table & reduce stock
            $item_stmt = $pdo->prepare("INSERT INTO transaction_items (transaction_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $stock_stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");

            foreach ($cart_data as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $item_stmt->execute([$transaction_id, $item['product_id'], $item['quantity'], $subtotal]);
                $stock_stmt->execute([$item['quantity'], $item['product_id']]);
            }

            $pdo->commit();
            $success_message = "Transaction successful! Simulated LHDN UUID: " . $lhdn_uuid;
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