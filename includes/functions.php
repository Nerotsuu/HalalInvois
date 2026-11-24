<?php
// includes/functions.php

function getAvailableProducts(PDO $pdo): array {
    try {
        $stmt = $pdo->prepare("SELECT product_id, product_name, category, price, stock_quantity, status, image FROM products WHERE status = 'available'");
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

function getRecentInvoices(PDO $pdo, int $limit = 50): array {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                t.*,
                u.username AS cashier_name,
                COALESCE(t.receipt_ref, CONCAT('#INV-', (1000 + t.transaction_id))) AS ref_no,
                COALESCE(t.invoice_status, CASE WHEN t.lhdn_uuid IS NOT NULL AND t.lhdn_uuid != '' THEN 'valid' ELSE 'pending' END) AS status_clean,
                COALESCE(t.buyer_tin, 'EI00000000010') AS tin_clean,
                COALESCE(t.buyer_name, 'Walk-in Customer (General Public)') AS buyer_clean
            FROM transactions t
            LEFT JOIN users u ON t.user_id = u.user_id
            ORDER BY t.created_at DESC, t.transaction_id DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch purchased line items for each transaction
        $itemsStmt = $pdo->query("
            SELECT ti.*, p.product_name, p.price 
            FROM transaction_items ti
            LEFT JOIN products p ON ti.product_id = p.product_id
        ");
        $itemsByTxn = [];
        while ($row = $itemsStmt->fetch(PDO::FETCH_ASSOC)) {
            $itemsByTxn[$row['transaction_id']][] = $row;
        }

        foreach ($invoices as &$inv) {
            $inv['items'] = $itemsByTxn[$inv['transaction_id']] ?? [];
        }

        return $invoices;
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

// 1. Get Daily Tax & SST Breakdown Ledger
function getDailyTaxBreakdown(PDO $pdo, int $limit = 30): array {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                DATE(created_at) AS sale_date,
                COUNT(*) AS total_receipts,
                COALESCE(SUM(total_amount), 0) AS gross_sales,
                COALESCE(SUM(sst_amount), 0) AS sst_total,
                SUM(CASE WHEN invoice_status = 'valid' THEN 1 ELSE 0 END) AS valid_count,
                SUM(CASE WHEN invoice_status = 'pending' THEN 1 ELSE 0 END) AS pending_count
            FROM transactions
            GROUP BY DATE(created_at)
            ORDER BY sale_date DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// 2. Get Daily Sales vs. 6% SST Collection (Dual-Bar Chart Data)
function getDailySalesAndSstChartData(PDO $pdo): array {
    try {
        $stmt = $pdo->query("
            SELECT 
                DATE_FORMAT(created_at, '%a') AS day_short,
                DATE(created_at) AS raw_date,
                COALESCE(SUM(total_amount), 0) AS gross_sales,
                COALESCE(SUM(sst_amount), 0) AS sst_amount
            FROM transactions
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY raw_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}