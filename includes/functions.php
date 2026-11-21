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

// 1. Get Sales & SST specifically for TODAY (Current Day KPI)
function getTodaySalesSummary(PDO $pdo): array {
    try {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) AS today_transactions,
                COALESCE(SUM(total_amount), 0) AS today_sales,
                COALESCE(SUM(sst_amount), 0) AS today_sst
            FROM transactions 
            WHERE DATE(created_at) = CURDATE()
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['today_transactions' => 0, 'today_sales' => 0, 'today_sst' => 0];
    } catch (PDOException $e) {
        return ['today_transactions' => 0, 'today_sales' => 0, 'today_sst' => 0];
    }
}

// 2. Get Staff Performance Stats for RBAC Module
function getStaffPerformanceStats(PDO $pdo): array {
    try {
        $stmt = $pdo->query("
            SELECT 
                u.user_id,
                u.username,
                u.role,
                COUNT(t.transaction_id) AS total_orders,
                COALESCE(SUM(t.total_amount), 0) AS total_sales,
                COALESCE(SUM(t.sst_amount), 0) AS total_sst,
                MAX(t.created_at) AS last_active
            FROM users u
            LEFT JOIN transactions t ON u.user_id = t.user_id
            WHERE u.role = 'cashier' OR u.role = 'owner'
            GROUP BY u.user_id, u.username, u.role
            ORDER BY total_sales DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// 3. Get Periodic Comparison Data (Daily, Weekly, Monthly, Yearly)
function getPeriodicSalesComparison(PDO $pdo): array {
    try {
        // Last 7 days
        $daily = $pdo->query("
            SELECT DATE(created_at) as label, COALESCE(SUM(total_amount), 0) as sales
            FROM transactions
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at) ORDER BY label ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        // Last 4 weeks
        $weekly = $pdo->query("
            SELECT CONCAT('Week ', WEEK(created_at, 1)) as label, COALESCE(SUM(total_amount), 0) as sales
            FROM transactions
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
            GROUP BY YEARWEEK(created_at, 1) ORDER BY created_at ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        // Last 6 months
        $monthly = $pdo->query("
            SELECT DATE_FORMAT(created_at, '%b %Y') as label, COALESCE(SUM(total_amount), 0) as sales
            FROM transactions
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY created_at ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        // Yearly
        $yearly = $pdo->query("
            SELECT YEAR(created_at) as label, COALESCE(SUM(total_amount), 0) as sales
            FROM transactions
            GROUP BY YEAR(created_at) ORDER BY label ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        return ['daily' => $daily, 'weekly' => $weekly, 'monthly' => $monthly, 'yearly' => $yearly];
    } catch (PDOException $e) {
        return ['daily' => [], 'weekly' => [], 'monthly' => [], 'yearly' => []];
    }
}