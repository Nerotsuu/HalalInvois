<?php
// includes/functions.php

function getAvailableProducts(PDO $pdo): array {
    try {
        $stmt = $pdo->prepare("SELECT product_id, product_name, category, price, stock_quantity FROM products WHERE status = 'available'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getIngredientsList(PDO $pdo): array {
    try {
        $stmt = $pdo->query("SELECT * FROM ingredients ORDER BY expiry_date ASC, ingredient_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getSalesSummary(PDO $pdo): array {
    try {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) AS total_transactions,
                COALESCE(SUM(total_amount), 0) AS total_sales,
                COALESCE(SUM(sst_amount), 0) AS total_sst,
                COALESCE(SUM(CASE WHEN payment_method = 'duitnow_qr' THEN total_amount ELSE 0 END), 0) AS duitnow_sales,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END), 0) AS cash_sales,
                COALESCE(SUM(CASE WHEN payment_method = 'card' THEN total_amount ELSE 0 END), 0) AS card_sales
            FROM transactions
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        return [];
    }
}

function getRecentInvoices(PDO $pdo, int $limit = 15): array {
    try {
        $stmt = $pdo->prepare("
            SELECT t.transaction_id, t.total_amount, t.sst_amount, t.payment_method, t.lhdn_uuid, t.created_at, u.username
            FROM transactions t
            LEFT JOIN users u ON t.user_id = u.user_id
            ORDER BY t.created_at DESC, t.transaction_id DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getAllUsers(PDO $pdo): array {
    try {
        $stmt = $pdo->query("SELECT user_id, username, role, created_at FROM users ORDER BY user_id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}