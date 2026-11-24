<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// RBAC check: Only 'owner' can access Owner Dashboard
if (($_SESSION['role'] ?? '') !== 'owner') {
    if (($_SESSION['role'] ?? '') === 'cashier') {
        header("Location: pos.php");
        exit();
    } elseif (($_SESSION['role'] ?? '') === 'auditor') {
        header("Location: auditor_reports.php");
        exit();
    }
    header("Location: login.php?logout=1");
    exit();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$username               = $_SESSION['username'] ?? 'Owner';
$user_role              = $_SESSION['role'] ?? 'owner';
$login_time             = $_SESSION['login_time'] ?? time();
$success_message        = $_SESSION['flash_success'] ?? '';
$error_message          = $_SESSION['flash_error'] ?? '';
$newly_added_ingredient = $_SESSION['newly_added_ingredient'] ?? null;
$newly_added_product    = $_SESSION['newly_added_product'] ?? null;
$active_tab             = $_SESSION['active_tab'] ?? ($_GET['tab'] ?? 'halal');
unset(
    $_SESSION['flash_success'], 
    $_SESSION['flash_error'], 
    $_SESSION['newly_added_ingredient'], 
    $_SESSION['newly_added_product'],
    $_SESSION['active_tab']
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Module 1 action: Add Halal Ingredient
    if (isset($_POST['add_ingredient'])) {
        $active_tab       = 'halal';
        $ingredient_name  = trim($_POST['ingredient_name'] ?? '');
        $supplier_name    = trim($_POST['supplier_name'] ?? '');
        $batch_number     = trim($_POST['batch_number'] ?? '');
        $expiry_date      = trim($_POST['expiry_date'] ?? '');
        $current_stock    = (float)($_POST['current_stock'] ?? 10.00);
        $unit             = trim($_POST['unit'] ?? 'kg');
        $halal_cert_no    = trim($_POST['halal_cert_no'] ?? '');
        $cert_expiry_date = trim($_POST['cert_expiry_date'] ?? '');

        if ($ingredient_name === '' || $supplier_name === '' || $expiry_date === '') {
            $_SESSION['flash_error'] = "Please complete all required ingredient fields.";
            header("Location: owner_dashboard.php");
            exit();
        }
        // PREVENT DUPLICATE: Check if ingredient name from the same supplier already exists
        $dupStmt = $pdo->prepare("SELECT ingredient_id FROM ingredients WHERE LOWER(TRIM(ingredient_name)) = LOWER(?) AND LOWER(TRIM(supplier_name)) = LOWER(?)");
        $dupStmt->execute([$ingredient_name, $supplier_name]);
        if ($dupStmt->fetch()) {
            $_SESSION['flash_error'] = "Duplicate Entry: '{$ingredient_name}' from supplier '{$supplier_name}' is already recorded in the ledger.";
            header("Location: owner_dashboard.php");
            exit();
        }
        try {
            $stmt = $pdo->prepare("
                INSERT INTO ingredients 
                    (ingredient_name, supplier_name, expiry_date, current_stock, unit, halal_cert_no, cert_expiry_date)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $ingredient_name,
                $supplier_name,
                $expiry_date,
                $current_stock,
                $unit,
                $halal_cert_no !== '' ? $halal_cert_no : null,
                $cert_expiry_date !== '' ? $cert_expiry_date : null
            ]);
            // Save flash data for success popup
            $_SESSION['newly_added_ingredient'] = [
                'name'          => $ingredient_name,
                'supplier'      => $supplier_name,
                'batch_no'      => $batch_number !== '' ? $batch_number : ('BTH-' . date('Y') . '-X' . mt_rand(10, 99)),
                'jakim_cert_no' => $halal_cert_no !== '' ? $halal_cert_no : 'JAKIM.700-2/3/1 ' . mt_rand(100, 999),
                'valid_until'   => $cert_expiry_date !== '' ? date('d M Y', strtotime($cert_expiry_date)) : date('d M Y', strtotime($expiry_date)),
                'authority'     => 'Jabatan Kemajuan Islam Malaysia (JAKIM)'
            ];
            $_SESSION['flash_success'] = "Ingredient '{$ingredient_name}' verified and recorded.";
            // Post/Redirect/Get: Prevents duplicate on reload
            header("Location: owner_dashboard.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Failed to add ingredient: " . $e->getMessage();
            header("Location: owner_dashboard.php");
            exit();
        }
    }

    // Module 2 action: Generate Simulated LHDN e-Invoice (UBL 2.1)
    if (isset($_POST['generate_einvoice'])) {
        $txn_id       = (int)($_POST['transaction_id'] ?? 0);
        $invoice_type = $_POST['invoice_type'] ?? 'b2c';
        $buyer_tin    = 'EI00000000010';
        $buyer_name   = 'Walk-in Customer (General Public)';
        if ($invoice_type === 'b2b') {
            $corp_tin = trim($_POST['buyer_corporate_tin'] ?? '');
            if ($corp_tin !== '') {
                $buyer_tin = $corp_tin;
                $buyer_name = trim($_POST['buyer_corporate_name'] ?? 'Corporate Buyer');
            }
        }
        // Generate simulated LHDN UUID conforming to UBL 2.1 standard
        $lhdn_uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        if ($txn_id > 0) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE transactions 
                    SET lhdn_uuid = ?, invoice_status = 'valid', buyer_tin = ?, buyer_name = ?, validated_at = NOW() 
                    WHERE transaction_id = ?
                ");
                $stmt->execute([$lhdn_uuid, $buyer_tin, $buyer_name, $txn_id]);
                $success_message = "Simulated LHDN e-Invoice generated successfully with UUID: {$lhdn_uuid}";
            } catch (PDOException $e) {
                $error_message = "Failed to generate e-invoice: " . $e->getMessage();
            }
        }
    }

        // Module 1 (Sub-item 2A): Add New Product with Image File Upload
    if (isset($_POST['add_product'])) {
        $product_name   = trim($_POST['product_name'] ?? '');
        $category       = trim($_POST['category'] ?? '');
        $price          = (float)($_POST['price'] ?? 0);
        $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
        $status         = $_POST['status'] ?? 'available';
        $image          = 'assets/default-food.jpeg';

        if ($product_name === '' || $category === '' || $price <= 0) {
            $_SESSION['flash_error'] = "Please provide product name, category, and a valid price.";
            header("Location: owner_dashboard.php");
            exit();
        }

        // PREVENT DUPLICATE: Check if product with this exact name already exists
        $dupProd = $pdo->prepare("SELECT product_id FROM products WHERE LOWER(TRIM(product_name)) = LOWER(?)");
        $dupProd->execute([$product_name]);
        if ($dupProd->fetch()) {
            $_SESSION['flash_error'] = "Duplicate Product: A product named '{$product_name}' already exists in your catalogue.";
            header("Location: owner_dashboard.php");
            exit();
        }

        // Handle uploaded image from PC / folder
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['product_image']['tmp_name'];
            $fileExt = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp'])) {
                $newFileName = 'prod_' . time() . '_' . mt_rand(100, 999) . '.' . $fileExt;
                $destPath = __DIR__ . '/assets/' . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $image = 'assets/' . $newFileName;
                }
            }
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO products (product_name, category, price, stock_quantity, status, image)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$product_name, $category, $price, $stock_quantity, $status, $image]);

            // Save flash data for product success popup
            $_SESSION['newly_added_product'] = [
                'name'     => $product_name,
                'category' => $category,
                'price'    => number_format($price, 2),
                'stock'    => $stock_quantity,
                'status'   => $status,
                'image'    => $image
            ];
            $_SESSION['flash_success'] = "Product '{$product_name}' successfully added to catalog.";
            
            // Post/Redirect/Get: Prevents duplicate on reload
            header("Location: owner_dashboard.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Failed to add product: " . $e->getMessage();
            header("Location: owner_dashboard.php");
            exit();
        }
    }

    // Module 1 (Sub-item 2B): Edit Existing Product
    if (isset($_POST['edit_product'])) {
        $product_id     = (int)($_POST['product_id'] ?? 0);
        $product_name   = trim($_POST['product_name'] ?? '');
        $category       = trim($_POST['category'] ?? '');
        $price          = (float)($_POST['price'] ?? 0);
        $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
        $status         = $_POST['status'] ?? 'available';
        $image          = trim($_POST['existing_image'] ?? 'assets/default-food.jpeg');
        // Check if user uploaded a replacement photo
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['product_image']['tmp_name'];
            $fileExt = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp'])) {
                $newFileName = 'prod_' . time() . '_' . mt_rand(100, 999) . '.' . $fileExt;
                $destPath = __DIR__ . '/assets/' . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $image = 'assets/' . $newFileName;
                }
            }
        }
        if ($product_id > 0 && $product_name !== '' && $category !== '' && $price > 0) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET product_name = ?, category = ?, price = ?, stock_quantity = ?, status = ?, image = ?
                    WHERE product_id = ?
                ");
                $stmt->execute([$product_name, $category, $price, $stock_quantity, $status, $image, $product_id]);
                $success_message = "Product '{$product_name}' updated successfully.";
            } catch (PDOException $e) {
                $error_message = "Failed to update product: " . $e->getMessage();
                header("Location: owner_dashboard.php");
                exit();
            }
        }
    }
    
    // Module 1 (Sub-item 2C): Delete Product
    if (isset($_POST['delete_product'])) {
        $product_id = (int)($_POST['delete_product_id'] ?? 0);
        if ($product_id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
                $stmt->execute([$product_id]);
                $success_message = "Product #{$product_id} deleted successfully.";
            } catch (PDOException $e) {
                $error_message = "Failed to delete product: " . $e->getMessage();
            }
        }
    }

    // Module 4 action: Create RBAC User Account
    if (isset($_POST['create_user'])) {
        $active_tab   = 'rbac';
        $new_username = trim($_POST['new_username'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        $new_role     = $_POST['new_role'] ?? 'cashier';
        $allowed_roles = ['owner', 'cashier', 'auditor'];

        if ($new_username === '' || strlen($new_password) < 6 || !in_array($new_role, $allowed_roles, true)) {
            $error_message = "Provide a valid username, a password of at least 6 characters, and a valid role.";
        } else {
            try {
                $check = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
                $check->execute([$new_username]);
                if ($check->fetch()) {
                    $error_message = "Username '{$new_username}' already exists.";
                } else {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $ins = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                    $ins->execute([$new_username, $hashed, $new_role]);
                    $success_message = "Account '{$new_username}' created with role '{$new_role}'.";
                }
            } catch (PDOException $e) {
                $error_message = "Failed to create user: " . $e->getMessage();
            }
        }
    }
}

$ingredients    = getIngredientsList($pdo);
$productsList   = getAvailableProducts($pdo);
$todaySales     = getTodaySalesSummary($pdo);
$salesSummary   = getSalesSummary($pdo);
$invoices       = getRecentInvoices($pdo, 20);
$usersList      = getAllUsers($pdo);
$staffStats     = getStaffPerformanceStats($pdo);
$periodSales    = getPeriodicSalesComparison($pdo);
$dailyTaxLedger    = getDailyTaxBreakdown($pdo);
$dailyTaxChartData = getDailySalesAndSstChartData($pdo);

require_once __DIR__ . '/views/owner_dashboard_views.php';