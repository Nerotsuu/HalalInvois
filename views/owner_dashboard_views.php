<?php
$username        = $username        ?? 'Owner';
$user_role       = $user_role       ?? 'owner';
$login_time      = $login_time      ?? time();
$success_message = $success_message ?? '';
$error_message   = $error_message   ?? '';
$active_tab      = $active_tab      ?? 'halal';
$ingredients     = $ingredients     ?? [];
$salesSummary    = $salesSummary    ?? [];
$invoices        = $invoices        ?? [];
$usersList       = $usersList       ?? [];

$newly_added_ingredient = $newly_added_ingredient ?? null;
$totalSales        = (float)($salesSummary['total_sales'] ?? 0);
$totalSst          = (float)($salesSummary['total_sst'] ?? 0);
$totalTransactions = (int)($salesSummary['total_transactions'] ?? 0);
$duitnowSales      = (float)($salesSummary['duitnow_sales'] ?? 0);
$cashSales         = (float)($salesSummary['cash_sales'] ?? 0);
$cardSales         = (float)($salesSummary['card_sales'] ?? 0);

$todayStr = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HalalInvois - Owner Dashboard</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; }
        .dashboard-container { display: flex; flex: 1; }
        
        /* Left Collapsible Sidebar */
        #sidebar {
            width: 260px;
            min-height: calc(100vh - 56px);
            background-color: #1e293b;
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }
        #sidebar.collapsed {
            width: 75px;
        }
        #sidebar .nav-link {
            color: #94a3b8;
            font-size: 0.9rem;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            border-radius: 6px;
            margin: 2px 8px;
            white-space: nowrap;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #fff;
            background-color: #059669;
        }
        #sidebar .nav-link i {
            font-size: 1.2rem;
            min-width: 32px;
        }
        #sidebar.collapsed .link-text,
        #sidebar.collapsed .dropdown-toggle::after,
        #sidebar.collapsed .sub-menu {
            display: none !important;
        }
        .sub-menu {
            list-style: none;
            padding-left: 36px;
            margin: 4px 0;
        }
        .sub-menu .nav-link {
            font-size: 0.82rem;
            padding: 6px 12px;
            color: #cbd5e1;
        }
        .main-content {
            flex: 1;
            padding: 24px;
            background-color: #f8fafc;
            transition: margin-left 0.3s;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm sticky-top">
        <div class="container-fluid">
            <!-- Sidebar Toggle Button -->
            <button class="btn btn-sm btn-outline-light me-2" id="sidebarToggle" title="Toggle Sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <a class="navbar-brand mb-0 h1" href="owner_dashboard.php">
                <i class="bi bi-speedometer2"></i> HalalInvois Owner Dashboard
            </a>

            <div class="d-flex align-items-center gap-3 text-white small ms-auto flex-wrap">
                <div><i class="bi bi-calendar3"></i> <span id="live-day-date">--</span></div>
                <div><i class="bi bi-clock"></i> <span id="live-clock" class="fw-bold">--:--:--</span></div>
                <div class="badge bg-white text-success px-2 py-1 shadow-sm">
                    <i class="bi bi-hourglass-split"></i> Active: <span id="live-session-duration">00h 00m 00s</span>
                </div>
                <div class="border-start ps-3">
                    <i class="bi bi-person-circle"></i> <strong><?= htmlspecialchars($username); ?></strong>
                    <span class="badge bg-light text-success ms-1 text-uppercase"><?= htmlspecialchars($user_role); ?></span>
                </div>
                <a href="pos.php" class="btn btn-sm btn-light text-success fw-semibold"><i class="bi bi-shop"></i> POS Terminal</a>
                <a href="login.php?logout=1" class="btn btn-sm btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <!-- App Body: Left Sidebar + Main Content -->
    <div class="dashboard-container">
        
        <!-- Left Collapsible Sidebar Pane -->
        <aside id="sidebar">
            <div class="py-3">
                <ul class="nav flex-column" id="dashboardNav">
                    
                    <!-- 1. Halal & Inventory Tracking with Sub-list -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#sub-halal" role="button">
                            <i class="bi bi-patch-check"></i> <span class="link-text">Halal &amp; Inventory</span>
                        </a>
                        <ul class="collapse show sub-menu" id="sub-halal">
                            <li><a href="#" class="nav-link sub-link active" onclick="switchSection('sec-ingredients', this)"><i class="bi bi-egg-fried"></i> <span class="link-text">Ingredients Ledger</span></a></li>
                            <li><a href="#" class="nav-link sub-link" onclick="switchSection('sec-products', this)"><i class="bi bi-box-seam"></i> <span class="link-text">Products Catalog</span></a></li>
                        </ul>
                    </li>

                    <!-- 2. LHDN e-Invoicing Simulation -->
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="switchSection('sec-lhdn', this)">
                            <i class="bi bi-upc-scan"></i> <span class="link-text">LHDN e-Invoicing</span>
                        </a>
                    </li>

                    <!-- 3. Tax & Sales Reporting with Sub-list -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#sub-tax" role="button">
                            <i class="bi bi-bar-chart-line"></i> <span class="link-text">Tax &amp; Sales</span>
                        </a>
                        <ul class="collapse show sub-menu" id="sub-tax">
                            <li><a href="#" class="nav-link sub-link" onclick="switchSection('sec-sales-overview', this)"><i class="bi bi-cash-stack"></i> <span class="link-text">Overview &amp; Tax</span></a></li>
                            <li><a href="#" class="nav-link sub-link" onclick="switchSection('sec-sales-comparison', this)"><i class="bi bi-graph-up-arrow"></i> <span class="link-text">Period Comparison</span></a></li>
                        </ul>
                    </li>

                    <!-- 4. RBAC Module with Sub-list -->
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="collapse" href="#sub-rbac" role="button">
                            <i class="bi bi-shield-lock"></i> <span class="link-text">RBAC Access</span>
                        </a>
                        <ul class="collapse show sub-menu" id="sub-rbac">
                            <li><a href="#" class="nav-link sub-link" onclick="switchSection('sec-rbac-users', this)"><i class="bi bi-people"></i> <span class="link-text">User Accounts</span></a></li>
                            <li><a href="#" class="nav-link sub-link" onclick="switchSection('sec-rbac-stats', this)"><i class="bi bi-person-badge"></i> <span class="link-text">Staff Performance</span></a></li>
                        </ul>
                    </li>

                </ul>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">

            <!-- Top KPI Overview Row (Updated: Today's Sales & SST) -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                        <div class="card-body">
                            <div class="text-muted small fw-bold text-uppercase">Today's Sales (<?= date('d M'); ?>)</div>
                            <div class="fs-4 fw-bold text-success">RM <?= number_format((float)($todaySales['today_sales'] ?? 0), 2); ?></div>
                            <div class="small text-muted"><?= (int)($todaySales['today_transactions'] ?? 0); ?> orders processed today</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                        <div class="card-body">
                            <div class="text-muted small fw-bold text-uppercase">Today's SST Collected</div>
                            <div class="fs-4 fw-bold text-primary">RM <?= number_format((float)($todaySales['today_sst'] ?? 0), 2); ?></div>
                            <div class="small text-muted">SST (6%) for current day</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                        <div class="card-body">
                            <div class="text-muted small fw-bold text-uppercase">Tracked Ingredients</div>
                            <div class="fs-4 fw-bold text-dark"><?= count($ingredients); ?></div>
                            <div class="small text-muted">Halal &amp; inventory items</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                        <div class="card-body">
                            <div class="text-muted small fw-bold text-uppercase">Active RBAC Accounts</div>
                            <div class="fs-4 fw-bold text-dark"><?= count($usersList); ?></div>
                            <div class="small text-muted">Owner / Cashier / Auditor</div>
                        </div>
                    </div>
                </div>
            </div>

                        <!-- 1A. Halal & Ingredients Ledger (Main Full-Width View) -->
            <div id="sec-ingredients" class="content-section">
                
                <!-- Section Header with Register Button -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Halal Ingredient &amp; Batch Compliance</h4>
                        <p class="text-muted small mb-0">Monitor raw material batches, supplier records, and JAKIM certificate validity</p>
                    </div>
                    <button type="button" class="btn btn-success fw-semibold px-3 py-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addIngredientModal">
                        <i class="bi bi-plus-lg me-1"></i> Register New Batch
                    </button>
                </div>

                <!-- Card Container -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    
                    <!-- Search Bar & Status Filter Pills Toolbar -->
                    <div class="card-header bg-white border-bottom p-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <!-- Search Input -->
                            <div class="input-group" style="max-width: 320px;">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="ingredientSearchInput" class="form-control border-start-0 ps-0" placeholder="Search..." onkeyup="filterIngredients()">
                            </div>

                            <!-- Filter Pills (All, Valid, Expiring Soon, Expired) -->
                            <div class="d-flex flex-wrap gap-2" id="statusFilterPills">
                                <?php
                                    $cntAll = count($ingredients);
                                    $cntValid = 0;
                                    $cntExpiring = 0;
                                    $cntExpired = 0;
                                    $todayTs = strtotime($todayStr);

                                    foreach ($ingredients as $ing) {
                                        $exp = $ing['cert_expiry_date'] ?: $ing['expiry_date'];
                                        $expTs = strtotime($exp);
                                        $daysLeft = ($expTs - $todayTs) / 86400;

                                        if ($daysLeft < 0) {
                                            $cntExpired++;
                                        } elseif ($daysLeft <= 30) {
                                            $cntExpiring++;
                                        } else {
                                            $cntValid++;
                                        }
                                    }
                                ?>
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 filter-pill active" onclick="filterByStatus('all', this)">
                                    All Batches (<?= $cntAll; ?>)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-pill" onclick="filterByStatus('valid', this)">
                                    Valid (<?= $cntValid; ?>)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-pill" onclick="filterByStatus('expiring', this)">
                                    Expiring Soon (<?= $cntExpiring; ?>)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-pill" onclick="filterByStatus('expired', this)">
                                    Expired (<?= $cntExpired; ?>)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Main Table -->
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover align-middle mb-0" id="ingredientTable">
                            <thead class="table-light text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 py-3">Ingredient Name</th>
                                    <th>Batch Number</th>
                                    <th>Supplier</th>
                                    <th>JAKIM Cert No.</th>
                                    <th>Expiry Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ingredients)): ?>
                                    <?php foreach ($ingredients as $idx => $ing): ?>
                                        <?php
                                            $certExp = $ing['cert_expiry_date'] ?: $ing['expiry_date'];
                                            $expTs = strtotime($certExp);
                                            $daysLeft = floor(($expTs - $todayTs) / 86400);

                                            if ($daysLeft < 0) {
                                                $statusKey = 'expired';
                                                $statusLabel = 'Expired';
                                                $badgeStyle = 'background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca;';
                                                $actionText = '<a href="#" class="text-danger fw-semibold text-decoration-none small" data-bs-toggle="modal" data-bs-target="#addIngredientModal">Renew</a>';
                                                $timeNotice = abs($daysLeft) . ' days ago';
                                            } elseif ($daysLeft <= 30) {
                                                $statusKey = 'expiring';
                                                $statusLabel = 'Expiring Soon';
                                                $badgeStyle = 'background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;';
                                                $actionText = '<a href="#" class="text-secondary fw-semibold text-decoration-none small" onclick="alert(\'Viewing Cert for: ' . addslashes($ing['ingredient_name']) . '\')">View Cert</a>';
                                                $timeNotice = 'Expires in ' . $daysLeft . ' days';
                                            } else {
                                                $statusKey = 'valid';
                                                $statusLabel = 'Valid';
                                                $badgeStyle = 'background-color: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0;';
                                                $actionText = '<a href="#" class="text-secondary fw-semibold text-decoration-none small" onclick="alert(\'Viewing Cert for: ' . addslashes($ing['ingredient_name']) . '\')">View Cert</a>';
                                                $timeNotice = date('Y', $expTs);
                                            }

                                            $batchDisplay = !empty($ing['batch_number']) ? $ing['batch_number'] : ('BTH-' . date('Y') . '-B' . ($idx + 1));
                                        ?>
                                        <tr class="ingredient-row" data-status="<?= $statusKey; ?>" data-search="<?= strtolower(htmlspecialchars($ing['ingredient_name'] . ' ' . $ing['supplier_name'] . ' ' . $batchDisplay . ' ' . $ing['halal_cert_no'])); ?>">
                                            <td class="ps-4 fw-bold text-dark">
                                                <?= htmlspecialchars($ing['ingredient_name']); ?>
                                                <div class="text-muted fw-normal small"><?= number_format((float)$ing['current_stock'], 2); ?> <?= htmlspecialchars($ing['unit']); ?></div>
                                            </td>
                                            <td class="text-muted font-monospace small"><?= htmlspecialchars($batchDisplay); ?></td>
                                            <td class="small fw-semibold"><?= htmlspecialchars($ing['supplier_name']); ?></td>
                                            <td class="font-monospace small text-muted"><?= htmlspecialchars($ing['halal_cert_no'] ?: 'JAKIM.700-2/3/1'); ?></td>
                                            <td>
                                                <div class="fw-semibold small"><?= date('d M Y', $expTs); ?></div>
                                                <small class="text-muted" style="font-size: 11px;"><?= $timeNotice; ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="rounded-pill px-3 py-1 fw-semibold small" style="<?= $badgeStyle; ?>">
                                                    <?= $statusLabel; ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <?= $actionText; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-5">No ingredient batches recorded yet. Click "+ Register New Batch" to add one.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sub-section: Products (New Form Left + Existing Products Table Right) -->
            <div id="sec-products" class="content-section d-none">
                <div class="row g-4">
                    <!-- Left Panel: Form to Add New Product -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success">
                                <i class="bi bi-plus-square"></i> Add New Product / Menu
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Image URL / Path</label>
                                        <input type="text" name="image" class="form-control form-control-sm" placeholder="e.g. assets/yihii.jpeg or online URL">
                                        <small class="text-muted" style="font-size: 11px;">Leave blank for default food placeholder.</small>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Product Name *</label>
                                        <input type="text" name="product_name" class="form-control form-control-sm" placeholder="e.g. Nasi Lemak Sambal Sotong" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Category *</label>
                                        <input type="text" name="category" class="form-control form-control-sm" placeholder="e.g. Mains, Beverages, Desserts" required>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Price (RM) *</label>
                                            <input type="number" step="0.01" name="price" class="form-control form-control-sm" placeholder="0.00" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Initial Stock *</label>
                                            <input type="number" name="stock_quantity" class="form-control form-control-sm" value="50" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Availability Status</label>
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="available" selected>Available</option>
                                            <option value="unavailable">Unavailable</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="add_product" class="btn btn-success btn-sm w-100">
                                        <i class="bi bi-save"></i> Save Product
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Right Panel: Existing Products Table -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-card-checklist"></i> Current Products Catalog</span>
                                <span class="badge bg-success"><?= count($productsList ?? []); ?> Items</span>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover align-middle small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Image</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($productsList)): ?>
                                            <?php foreach ($productsList as $p): ?>
                                            <tr>
                                                <td>
                                                    <?php 
                                                        $imgPath = !empty($p['image']) ? $p['image'] : 'assets/default-food.jpeg';
                                                    ?>
                                                    <img src="<?= htmlspecialchars($imgPath); ?>" 
                                                        onerror="this.onerror=null; this.src='assets/default-food.jpeg';"
                                                        alt="<?= htmlspecialchars($p['product_name']); ?>" 
                                                        style="width: 42px; height: 42px; object-fit: cover; border-radius: 6px;" 
                                                        class="border bg-light">
                                                </td>
                                                <td class="fw-bold"><?= htmlspecialchars($p['product_name']); ?></td>
                                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category']); ?></span></td>
                                                <td class="text-primary fw-bold">RM <?= number_format((float)$p['price'], 2); ?></td>
                                                <td><?= (int)$p['stock_quantity']; ?></td>
                                                <td>
                                                    <span class="badge bg-<?= ($p['status'] ?? 'available') === 'available' ? 'success' : 'danger'; ?>">
                                                        <?= htmlspecialchars($p['status'] ?? 'available'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center text-muted py-4">No products recorded yet.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. LHDN e-Invoicing Simulation Records -->
            <div id="sec-lhdn" class="content-section d-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold text-success d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-upc-scan"></i> Simulated LHDN e-Invoice Records (UBL 2.1 Standard)</span>
                        <span class="badge bg-success"><?= count($invoices); ?> Invoices</span>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Txn ID</th>
                                    <th>Simulated LHDN UUID</th>
                                    <th>Cashier</th>
                                    <th>Payment</th>
                                    <th>SST (RM)</th>
                                    <th>Total (RM)</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($invoices)): ?>
                                    <?php foreach ($invoices as $inv): ?>
                                        <tr>
                                            <td>#<?= (int)$inv['transaction_id']; ?></td>
                                            <td><code><?= htmlspecialchars($inv['lhdn_uuid'] ?? 'PENDING'); ?></code></td>
                                            <td><?= htmlspecialchars($inv['username'] ?? 'N/A'); ?></td>
                                            <td><span class="badge bg-info text-dark text-uppercase"><?= htmlspecialchars($inv['payment_method']); ?></span></td>
                                            <td><?= number_format((float)$inv['sst_amount'], 2); ?></td>
                                            <td class="fw-bold">RM <?= number_format((float)$inv['total_amount'], 2); ?></td>
                                            <td><?= htmlspecialchars($inv['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">No transactions found yet. Complete a checkout in POS terminal to generate an invoice.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sub-section: Tax & Sales Overview (Card Payment Removed) -->
            <div id="sec-sales-overview" class="content-section d-none">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold text-success">
                        <i class="bi bi-cash-coin"></i> Breakdown by Payment Method
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-white shadow-sm">
                                    <div class="text-muted small">DuitNow QR Sales</div>
                                    <div class="fs-4 fw-bold text-success">RM <?= number_format($duitnowSales, 2); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-white shadow-sm">
                                    <div class="text-muted small">Cash Sales</div>
                                    <div class="fs-4 fw-bold text-dark">RM <?= number_format($cashSales, 2); ?></div>
                                </div>
                            </div>
                            <!-- Card sales removed as requested -->
                        </div>

                        <!-- Metrics Table & Payment Donut Chart -->
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <table class="table table-bordered small">
                                    <thead class="table-light">
                                        <tr><th>Metric</th><th>Value (RM)</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>Gross Sales Amount</td><td class="fw-bold">RM <?= number_format($totalSales, 2); ?></td></tr>
                                        <tr><td>Total SST Tax Recorded</td><td class="fw-bold text-primary">RM <?= number_format($totalSst, 2); ?></td></tr>
                                        <tr><td>Net Sales (Excl. SST)</td><td class="fw-bold text-success">RM <?= number_format(max(0, $totalSales - $totalSst), 2); ?></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-6">
                                <div class="card p-3 border-0 bg-light text-center">
                                    <h6 class="fw-bold text-muted small">Payment Channel Ratio (Interactive)</h6>
                                    <div style="max-height: 200px;" class="d-flex justify-content-center">
                                        <canvas id="paymentDonutChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub-section: Periodic Sales Comparison (Daily, Weekly, Monthly, Yearly) -->
            <div id="sec-sales-comparison" class="content-section d-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-success"><i class="bi bi-graph-up"></i> Sales Comparison Strategy Hub</span>
                        <!-- Filter buttons -->
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-success active" onclick="updateChartPeriod('daily', this)">Daily (Last 7 Days)</button>
                            <button type="button" class="btn btn-outline-success" onclick="updateChartPeriod('weekly', this)">Weekly (Last 4 Weeks)</button>
                            <button type="button" class="btn btn-outline-success" onclick="updateChartPeriod('monthly', this)">Monthly</button>
                            <button type="button" class="btn btn-outline-success" onclick="updateChartPeriod('yearly', this)">Yearly</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 320px;">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4A. RBAC User Accounts Management -->
            <div id="sec-rbac-users" class="content-section d-none">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success">
                                <i class="bi bi-person-plus"></i> Create Staff / Role Account
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Username *</label>
                                        <input type="text" name="new_username" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Password (min 6 chars) *</label>
                                        <input type="password" name="new_password" class="form-control form-control-sm" minlength="6" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Assigned Role *</label>
                                        <select name="new_role" class="form-select form-select-sm" required>
                                            <option value="cashier">cashier (POS Terminal)</option>
                                            <option value="auditor">auditor (Compliance &amp; Reports)</option>
                                            <option value="owner">owner (Full Dashboard)</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="create_user" class="btn btn-success btn-sm w-100">
                                        <i class="bi bi-check2-circle"></i> Create User Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-people"></i> Registered System Users &amp; Permissions</span>
                                <span class="badge bg-success"><?= count($usersList); ?> Accounts</span>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover align-middle small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usersList as $usr): ?>
                                            <tr>
                                                <td>#<?= (int)$usr['user_id']; ?></td>
                                                <td class="fw-semibold"><?= htmlspecialchars($usr['username']); ?></td>
                                                <td><span class="badge bg-success text-uppercase"><?= htmlspecialchars($usr['role']); ?></span></td>
                                                <td><?= htmlspecialchars($usr['created_at']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub-section: Staff Performance Review -->
            <div id="sec-rbac-stats" class="content-section d-none">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success">
                                <i class="bi bi-trophy"></i> Cashier &amp; Staff Sales Performance Review
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover align-middle small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Staff Member</th>
                                            <th>Role</th>
                                            <th class="text-center">Orders Handled</th>
                                            <th>Total Revenue Generated</th>
                                            <th>SST Processed</th>
                                            <th>Last Active</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($staffStats)): ?>
                                            <?php foreach ($staffStats as $st): ?>
                                            <tr>
                                                <td class="fw-bold"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($st['username']); ?></td>
                                                <td><span class="badge bg-secondary"><?= htmlspecialchars($st['role']); ?></span></td>
                                                <td class="text-center fw-bold"><?= (int)$st['total_orders']; ?></td>
                                                <td class="text-success fw-bold">RM <?= number_format((float)$st['total_sales'], 2); ?></td>
                                                <td>RM <?= number_format((float)$st['total_sst'], 2); ?></td>
                                                <td class="text-muted"><?= htmlspecialchars($st['last_active'] ?? 'No orders yet'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Staff Performance Bar Chart -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm p-3">
                            <h6 class="fw-bold text-muted small mb-3">Revenue by Cashier</h6>
                            <canvas id="staffChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL 1: Register Raw Ingredient Batch (Matches Image 2) -->
    <div class="modal fade" id="addIngredientModal" tabindex="-1" aria-labelledby="addIngredientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="addIngredientModalLabel">Register Raw Ingredient Batch</h5>
                        <p class="text-muted small mb-0">Manual entry for JAKIM certification tracking &amp; SME compliance</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form method="POST">
                    <div class="modal-body p-4">
                        <!-- Ingredient Name -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Ingredient / Raw Material Name *</label>
                            <input type="text" name="ingredient_name" class="form-control form-control-lg fs-6 rounded-3" placeholder="e.g. Frozen Boneless Chicken 10kg" required>
                        </div>

                        <!-- Batch Number & Supplier Name -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Batch Number *</label>
                                <input type="text" name="batch_number" class="form-control rounded-3" placeholder="e.g. BTH-2026-X09" value="BTH-<?= date('Y'); ?>-B<?= mt_rand(10, 99); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Supplier Name *</label>
                                <input type="text" name="supplier_name" class="form-control rounded-3" placeholder="e.g. Ayam Segar Halal Ent" required>
                            </div>
                        </div>

                        <!-- Stock & Unit -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Current Stock Quantity *</label>
                                <input type="number" step="0.01" name="current_stock" class="form-control rounded-3" value="10.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Measurement Unit *</label>
                                <input type="text" name="unit" class="form-control rounded-3" placeholder="kg, L, pack" value="kg" required>
                            </div>
                        </div>

                        <!-- JAKIM Cert No & Expiry Dates -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">JAKIM Halal Cert Number *</label>
                                <input type="text" name="halal_cert_no" class="form-control rounded-3" placeholder="JAKIM.700-2/3/1 ..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Cert Expiry Date *</label>
                                <input type="date" name="cert_expiry_date" class="form-control rounded-3" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Item Material Expiry Date *</label>
                            <input type="date" name="expiry_date" class="form-control rounded-3" required>
                        </div>

                        <!-- Upload Box UI -->
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Upload Digital Certificate (Optional PDF / Scan)</label>
                            <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light" style="cursor: pointer;">
                                <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                                <div class="small mt-1 text-muted">Drag &amp; drop JAKIM certificate PDF or <span class="text-success fw-bold">browse</span></div>
                                <div style="font-size: 11px;" class="text-muted mt-1">PDF, JPG, PNG up to 5MB</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_ingredient" class="btn btn-success px-4 rounded-3 fw-semibold">
                            Save Batch &amp; Verify
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL 2: Verified Success Modal (Matches Image 3) -->
    <div class="modal fade" id="ingredientSuccessModal" tabindex="-1" aria-labelledby="ingredientSuccessLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 p-4 text-center">
                
                <!-- Close Button -->
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>

                <!-- Big Green Check Circle Icon -->
                <div class="my-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; background-color: #10b981;">
                        <i class="bi bi-check-lg text-white fs-3"></i>
                    </div>
                </div>

                <!-- Title & Verified Tagline -->
                <h4 class="fw-bold text-dark mt-2 mb-1">
                    <?= htmlspecialchars($newly_added_ingredient['name'] ?? 'Ingredient'); ?>
                </h4>
                <div class="text-muted small mb-3">Verified Halal Raw Material Record</div>

                <!-- Details Two-Column Box -->
                <div class="border rounded-3 p-3 text-start bg-light mb-4" style="font-size: 0.88rem;">
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Supplier:</span>
                        <strong class="text-dark"><?= htmlspecialchars($newly_added_ingredient['supplier'] ?? ''); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Batch No:</span>
                        <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($newly_added_ingredient['batch_no'] ?? ''); ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">JAKIM Ref:</span>
                        <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($newly_added_ingredient['jakim_cert_no'] ?? ''); ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Valid Until:</span>
                        <strong class="text-success"><?= htmlspecialchars($newly_added_ingredient['valid_until'] ?? ''); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-top mt-1 pt-1">
                        <span class="text-muted">Authority:</span>
                        <span class="text-muted"><?= htmlspecialchars($newly_added_ingredient['authority'] ?? 'JAKIM'); ?></span>
                    </div>
                </div>

                <!-- Big Green Done Button -->
                <button type="button" class="btn btn-success btn-lg w-100 rounded-3 fw-semibold py-2" data-bs-dismiss="modal">
                    Done
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const loginTimestamp = <?= (int)$login_time ?> * 1000;

        // 1. Sidebar Toggle (Expand / Collapse)
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });

        // 2. Switching Content Sections
        function switchSection(sectionId, linkEl) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(sec => sec.classList.add('d-none'));
            // Show selected section
            const target = document.getElementById(sectionId);
            if (target) target.classList.remove('d-none');
            // Update active nav link
            document.querySelectorAll('#sidebar .nav-link').forEach(link => link.classList.remove('active'));
            if (linkEl) linkEl.classList.add('active');
        }
        // 3. Initialize Interactive Charts (Chart.js)
        const periodData = <?= json_encode($periodSales ?? []); ?>;
        // Payment Method Doughnut Chart
        new Chart(document.getElementById('paymentDonutChart'), {
            type: 'doughnut',
            data: {
                labels: ['DuitNow QR', 'Cash'],
                datasets: [{
                    data: [<?= (float)$duitnowSales; ?>, <?= (float)$cashSales; ?>],
                    backgroundColor: ['#10b981', '#3b82f6']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
        // Periodic Sales Trend Chart (Changed from 'bar' to 'line')
        let salesChart = new Chart(document.getElementById('salesTrendChart'), {
            type: 'line',
            data: {
                labels: (periodData.daily || []).map(d => d.label),
                datasets: [{
                    label: 'Sales Revenue (RM)',
                    data: (periodData.daily || []).map(d => d.sales),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.15)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#059669',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'RM ' + value; }
                        }
                    }
                }
            }
        });
        function updateChartPeriod(period, btnEl) {
            document.querySelectorAll('#sec-sales-comparison .btn').forEach(b => b.classList.remove('active'));
            btnEl.classList.add('active');
            const data = periodData[period] || [];
            salesChart.data.labels = data.map(d => d.label);
            salesChart.data.datasets[0].data = data.map(d => d.sales);
            salesChart.update();
        }
        // Staff Performance Chart
        const staffData = <?= json_encode($staffStats ?? []); ?>;
        new Chart(document.getElementById('staffChart'), {
            type: 'bar',
            data: {
                labels: staffData.map(s => s.username),
                datasets: [{
                    label: 'Revenue (RM)',
                    data: staffData.map(s => s.total_sales),
                    backgroundColor: '#6366f1',
                    borderRadius: 4
                }]
            },
            options: { indexAxis: 'y', responsive: true }
        });
        function updateDateTimeAndDuration() {
            const now = new Date();
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayName = days[now.getDay()];
            const dateFormatted = now.toLocaleDateString('en-MY', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            document.getElementById('live-day-date').innerText = `${dayName}, ${dateFormatted}`;

            const timeFormatted = now.toLocaleTimeString('en-MY', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            document.getElementById('live-clock').innerText = timeFormatted;

            const elapsedSeconds = Math.max(0, Math.floor((now.getTime() - loginTimestamp) / 1000));
            const hours = Math.floor(elapsedSeconds / 3600);
            const minutes = Math.floor((elapsedSeconds % 3600) / 60);
            const seconds = elapsedSeconds % 60;
            const pad = (n) => String(n).padStart(2, '0');
            document.getElementById('live-session-duration').innerText =
                `${pad(hours)}h ${pad(minutes)}m ${pad(seconds)}s`;
        }

        updateDateTimeAndDuration();
        setInterval(updateDateTimeAndDuration, 1000);

        // Status Filter (All, Valid, Expiring Soon, Expired)
        let activeStatusFilter = 'all';
        function filterByStatus(status, btnEl) {
            activeStatusFilter = status;
            document.querySelectorAll('.filter-pill').forEach(b => {
                b.classList.remove('btn-success', 'active');
                b.classList.add('btn-outline-secondary');
            });
            btnEl.classList.remove('btn-outline-secondary');
            btnEl.classList.add('btn-success', 'active');
            applyIngredientFilters();
        }
        // Search Filter
        function filterIngredients() {
            applyIngredientFilters();
        }
        function applyIngredientFilters() {
            const query = (document.getElementById('ingredientSearchInput')?.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.ingredient-row');
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const rowSearch = row.getAttribute('data-search') || '';
                const matchesStatus = (activeStatusFilter === 'all' || rowStatus === activeStatusFilter);
                const matchesQuery = query === '' || rowSearch.includes(query);
                if (matchesStatus && matchesQuery) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        // Auto-trigger Success Modal when new ingredient is recorded
        <?php if (!empty($newly_added_ingredient)): ?>
        document.addEventListener("DOMContentLoaded", function() {
            const successModal = new bootstrap.Modal(document.getElementById('ingredientSuccessModal'));
            successModal.show();
        });
        <?php endif; ?>
    </script>
</body>
</html>