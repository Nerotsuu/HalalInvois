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
$newly_added_product = $newly_added_product ?? null;

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

                        <!-- 1B. Products Catalog & Menu Management (Main Full-Width View) -->
            <div id="sec-products" class="content-section d-none">
                
                <!-- Section Header with Add Product Button -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Products Catalog &amp; Menu Management</h4>
                        <p class="text-muted small mb-0">Browse, filter, add, edit, and manage all food and beverage menu items</p>
                    </div>
                    <button type="button" class="btn btn-success fw-semibold px-3 py-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="bi bi-plus-lg me-1"></i> Add New Product / Menu
                    </button>
                </div>

                <!-- Products Card Container -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    
                    <!-- Search Bar & Category Filter Pills Toolbar -->
                    <div class="card-header bg-white border-bottom p-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <!-- Search Input -->
                            <div class="input-group" style="max-width: 320px;">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="productSearchInput" class="form-control border-start-0 ps-0" placeholder="Search menu / category..." onkeyup="filterProductsCatalog()">
                            </div>

                            <!-- Dynamic Category Filter Pills -->
                            <div class="d-flex flex-wrap gap-2" id="productCategoryPills">
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 product-cat-pill active" onclick="filterProductByCategory('all', this)">
                                    All (<?= count($productsList ?? []); ?>)
                                </button>
                                <?php 
                                    $allCats = array_unique(array_column($productsList ?? [], 'category'));
                                    foreach ($allCats as $cat): 
                                        $catCount = count(array_filter($productsList ?? [], fn($i) => strcasecmp($i['category'], $cat) === 0));
                                ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 product-cat-pill" onclick="filterProductByCategory('<?= htmlspecialchars(strtolower($cat)); ?>', this)">
                                        <?= htmlspecialchars($cat); ?> (<?= $catCount; ?>)
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover align-middle mb-0" id="productsTable">
                            <thead class="table-light text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 py-3">Photo</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price (RM)</th>
                                    <th>Stock</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($productsList)): ?>
                                    <?php foreach ($productsList as $p): ?>
                                        <?php 
                                            $imgPath = !empty($p['image']) ? $p['image'] : 'assets/default-food.jpeg';
                                            $isAvail = ($p['status'] ?? 'available') === 'available';
                                        ?>
                                        <tr class="product-row" data-category="<?= htmlspecialchars(strtolower($p['category'])); ?>" data-search="<?= strtolower(htmlspecialchars($p['product_name'] . ' ' . $p['category'])); ?>">
                                            <td class="ps-4">
                                                <img src="<?= htmlspecialchars($imgPath); ?>" 
                                                     onerror="this.onerror=null; this.src='assets/default-food.jpeg';"
                                                     alt="<?= htmlspecialchars($p['product_name']); ?>" 
                                                     style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px;" 
                                                     class="border shadow-sm bg-light">
                                            </td>
                                            <td class="fw-bold text-dark">
                                                <?= htmlspecialchars($p['product_name']); ?>
                                                <div class="text-muted small font-monospace">#<?= (int)$p['product_id']; ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1"><?= htmlspecialchars($p['category']); ?></span>
                                            </td>
                                            <td class="text-primary fw-bold">
                                                RM <?= number_format((float)$p['price'], 2); ?>
                                            </td>
                                            <td>
                                                <span class="fw-semibold <?= (int)$p['stock_quantity'] <= 10 ? 'text-danger' : 'text-dark'; ?>">
                                                    <?= (int)$p['stock_quantity']; ?> units
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="rounded-pill px-3 py-1 fw-semibold small" 
                                                      style="<?= $isAvail ? 'background-color: #dcfce7; color: #16a34a;' : 'background-color: #fee2e2; color: #dc2626;'; ?>">
                                                    <?= $isAvail ? 'Available' : 'Unavailable'; ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-2 me-1" 
                                                        onclick="openEditProductModal(<?= (int)$p['product_id']; ?>, '<?= addslashes($p['product_name']); ?>', '<?= addslashes($p['category']); ?>', <?= (float)$p['price']; ?>, <?= (int)$p['stock_quantity']; ?>, '<?= $p['status']; ?>', '<?= addslashes($imgPath); ?>')">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>

                                                <!-- Delete Button -->
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete \'<?= addslashes($p['product_name']); ?>\' from catalog?');">
                                                    <input type="hidden" name="delete_product_id" value="<?= (int)$p['product_id']; ?>">
                                                    <button type="submit" name="delete_product" class="btn btn-sm btn-outline-danger rounded-2">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-5">No products found. Click "+ Add New Product / Menu" to add items.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

                        <!-- 2. Simulated LHDN e-Invoicing Hub (Matches Image 1) -->
            <div id="sec-lhdn" class="content-section d-none">
                
                <!-- Section Header with Generate Button -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Simulated LHDN e-Invoicing Hub</h4>
                        <p class="text-muted small mb-0">Convert POS sales transactions into simulated LHDN UBL 2.1 digital invoices with mock UUID validation tokens and QR codes.</p>
                    </div>
                    <button type="button" class="btn btn-success fw-semibold px-3 py-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#generateInvoiceModal">
                        <i class="bi bi-plus-lg me-1"></i> Generate e-Invoice from POS
                    </button>
                </div>

                <!-- 3 Top Metric KPI Cards -->
                <?php
                    $validCount   = 0;
                    $pendingCount = 0;
                    $totalTaxSst  = 0;
                    $pendingList  = [];

                    foreach ($invoices as $inv) {
                        $totalTaxSst += (float)$inv['sst_amount'];
                        if ($inv['status_clean'] === 'valid') {
                            $validCount++;
                        } else {
                            $pendingCount++;
                            $pendingList[] = $inv;
                        }
                    }
                    $latestPendingRef = !empty($pendingList) ? $pendingList[0]['ref_no'] : 'None';
                ?>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                            <div class="text-muted small">Total Simulated e-Invoices</div>
                            <div class="fs-3 fw-bold text-dark my-1"><?= $validCount; ?></div>
                            <div class="small text-success fw-semibold"><i class="bi bi-check-circle me-1"></i> All UBL 2.1 compliant</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                            <div class="text-muted small">Pending POS Conversion</div>
                            <div class="fs-3 fw-bold text-dark my-1"><?= $pendingCount; ?></div>
                            <div class="small text-warning fw-semibold"><i class="bi bi-hourglass-split me-1"></i> <?= htmlspecialchars($latestPendingRef); ?> ready</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                            <div class="text-muted small">Total Tax (SST) Reported</div>
                            <div class="fs-3 fw-bold text-dark my-1">RM <?= number_format($totalTaxSst, 2); ?></div>
                            <div class="small text-muted">6% &amp; 8% calculated</div>
                        </div>
                    </div>
                </div>

                <!-- Main e-Invoicing Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    
                    <!-- Search Bar & Filter Pills -->
                    <div class="card-header bg-white border-bottom p-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="input-group" style="max-width: 380px;">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="invoiceSearchInput" class="form-control border-start-0 ps-0" placeholder="Search by Receipt no, Mock UUID, or Buyer TIN..." onkeyup="filterInvoices()">
                            </div>

                            <div class="d-flex flex-wrap gap-2" id="invoiceFilterPills">
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 invoice-pill active" onclick="filterInvoicesByStatus('all', this)">
                                    All Records (<?= count($invoices); ?>)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 invoice-pill" onclick="filterInvoicesByStatus('valid', this)">
                                    Simulated Valid (<?= $validCount; ?>)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 invoice-pill" onclick="filterInvoicesByStatus('pending', this)">
                                    Pending Conversion (<?= $pendingCount; ?>)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Table -->
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover align-middle mb-0" id="invoiceTable">
                            <thead class="table-light text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 py-3">Receipt Ref</th>
                                    <th>LHDN Mock UUID</th>
                                    <th>Date &amp; Time</th>
                                    <th>Buyer TIN</th>
                                    <th>Total (RM)</th>
                                    <th>SST (RM)</th>
                                    <th class="text-end pe-4">Validation Status &amp; Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($invoices)): ?>
                                    <?php foreach ($invoices as $inv): ?>
                                        <?php 
                                            $isValid = ($inv['status_clean'] === 'valid');
                                            $createdFormatted = date('d M Y, h:i A', strtotime($inv['created_at']));
                                            $jsonEncodedInv = htmlspecialchars(json_encode($inv), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <tr class="invoice-row" data-status="<?= $inv['status_clean']; ?>" data-search="<?= strtolower(htmlspecialchars($inv['ref_no'] . ' ' . $inv['lhdn_uuid'] . ' ' . $inv['tin_clean'] . ' ' . $inv['buyer_clean'])); ?>">
                                            <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($inv['ref_no']); ?></td>
                                            <td>
                                                <?php if ($isValid): ?>
                                                    <code class="text-dark small font-monospace"><?= htmlspecialchars($inv['lhdn_uuid']); ?></code>
                                                <?php else: ?>
                                                    <span class="text-muted small fst-italic">Awaiting UBL conversion...</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small fw-semibold text-muted"><?= $createdFormatted; ?></td>
                                            <td>
                                                <div class="fw-semibold small text-dark"><?= htmlspecialchars($inv['tin_clean']); ?></div>
                                                <small class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($inv['buyer_clean']); ?></small>
                                            </td>
                                            <td class="fw-bold text-dark"><?= number_format((float)$inv['total_amount'], 2); ?></td>
                                            <td class="small text-muted"><?= number_format((float)$inv['sst_amount'], 2); ?></td>
                                            <td class="text-end pe-4">
                                                <div>
                                                    <?php if ($isValid): ?>
                                                        <span class="text-success fw-bold small"><i class="bi bi-dot fs-5 align-middle"></i> Valid</span>
                                                    <?php else: ?>
                                                        <span class="text-warning fw-bold small"><i class="bi bi-dot fs-5 align-middle"></i> Pending</span>
                                                    <?php endif; ?>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-0 text-secondary mt-1 shadow-sm" 
                                                        onclick='openInspectModal(<?= $jsonEncodedInv; ?>)'>
                                                    Inspect
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-5">No transactions recorded yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

                        <!-- 3A. Tax & SST Compliance Reports (Matches Image 1 & Integrates Payment Breakdown) -->
            <div id="sec-sales-overview" class="content-section d-none">
                
                <!-- Section Header with Period Selector & Export Actions -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Tax &amp; SST Compliance Reports</h4>
                        <p class="text-muted small mb-0">Monthly 6% Sales &amp; Service Tax (SST) summary and LHDN e-Invoice tax audit statement</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <select class="form-select form-select-sm border rounded-3 bg-white" style="width: auto;">
                            <option selected><?= date('F Y'); ?> (Current Cycle)</option>
                            <option><?= date('F Y', strtotime('-1 month')); ?></option>
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 px-3 shadow-sm" onclick="exportLedgerToCSV()">
                            <i class="bi bi-download me-1"></i> Export CSV
                        </button>
                        <button type="button" class="btn btn-sm btn-success rounded-3 px-3 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#sst02Modal">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Generate SST-02 PDF Report
                        </button>
                    </div>
                </div>

                <!-- 3 Top Compliance KPI Cards -->
                <?php
                    $b2cCount = 0;
                    $b2bCount = 0;
                    foreach ($invoices as $inv) {
                        if (($inv['tin_clean'] ?? '') === 'EI00000000010') {
                            $b2cCount++;
                        } else {
                            $b2bCount++;
                        }
                    }
                ?>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                            <div class="text-muted small">Total Taxable Gross Sales (<?= date('M Y'); ?>)</div>
                            <div class="fs-3 fw-bold text-dark my-1">RM <?= number_format($totalSales, 2); ?></div>
                            <div class="small text-success fw-semibold"><i class="bi bi-graph-up-arrow me-1"></i> +12.4% vs last month</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                            <div class="text-muted small">Total SST Collected (6%)</div>
                            <div class="fs-3 fw-bold text-dark my-1">RM <?= number_format($totalSst, 2); ?></div>
                            <div class="small text-muted">6% Service Tax on F&amp;B sales</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                            <div class="text-muted small">LHDN Validated e-Invoices</div>
                            <div class="fs-3 fw-bold text-dark my-1"><?= count($invoices); ?> Receipts</div>
                            <div class="small text-success fw-semibold"><i class="bi bi-check-circle me-1"></i> 100% UBL 2.1 Simulated Valid</div>
                        </div>
                    </div>
                </div>

                <!-- Middle Row: Dual-Bar Chart + SST-02 Readiness Checklist -->
                <div class="row g-4 mb-4">
                    <!-- Daily Sales vs 6% SST Collection Chart -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-dark mb-0">Daily Sales vs. 6% SST Collection</h6>
                                <div class="d-flex align-items-center gap-3 small">
                                    <span><span class="d-inline-block rounded-circle me-1" style="width: 10px; height: 10px; background-color: #059669;"></span> Gross Sales</span>
                                    <span><span class="d-inline-block rounded-circle me-1" style="width: 10px; height: 10px; background-color: #94a3b8;"></span> 6% SST</span>
                                </div>
                            </div>
                            <div style="height: 250px;">
                                <canvas id="dailySstDualChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- SST-02 Filing Readiness Checklist -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-3">SST-02 Filing Readiness Checklist</h6>
                                <ul class="list-unstyled small mb-3">
                                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span><i class="bi bi-check-circle-fill text-success me-2"></i> POS Sales Reconciliation</span>
                                        <strong class="text-success">Complete (RM <?= number_format($totalSales, 2); ?>)</strong>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span><i class="bi bi-check-circle-fill text-success me-2"></i> B2C Consolidated e-Invoices</span>
                                        <span class="fw-semibold text-muted"><?= $b2cCount; ?> Validated</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span><i class="bi bi-check-circle-fill text-success me-2"></i> B2B Corporate TIN e-Invoices</span>
                                        <span class="fw-semibold text-muted"><?= $b2bCount; ?> Validated</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Due Date Alert Box -->
                            <div class="p-3 rounded-3" style="background-color: #fffbeb; border: 1px solid #fde68a;">
                                <div class="d-flex align-items-center text-warning-emphasis small fw-semibold">
                                    <i class="bi bi-exclamation-circle-fill text-warning me-2 fs-5"></i>
                                    <span>Next Bi-Monthly SST-02 Submission Due: <strong><?= date('t M Y', strtotime('+1 month')); ?></strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Channel Breakdown (Retained from existing function) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-header bg-white border-bottom p-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-credit-card-2-back text-success me-1"></i> Payment Channel Settlement</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-7">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="p-3 border rounded-3 bg-light">
                                            <div class="text-muted small">DuitNow QR Sales</div>
                                            <div class="fs-4 fw-bold text-success">RM <?= number_format($duitnowSales, 2); ?></div>
                                            <small class="text-muted">Instant Bank &amp; e-Wallet</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 border rounded-3 bg-light">
                                            <div class="text-muted small">Cash Sales</div>
                                            <div class="fs-4 fw-bold text-dark">RM <?= number_format($cashSales, 2); ?></div>
                                            <small class="text-muted">Physical Register</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered table-sm small mb-0">
                                        <thead class="table-light">
                                            <tr><th>Metric</th><th class="text-end">Amount (RM)</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>Gross Sales Amount</td><td class="text-end fw-bold">RM <?= number_format($totalSales, 2); ?></td></tr>
                                            <tr><td>Total SST Tax Recorded</td><td class="text-end fw-bold text-primary">RM <?= number_format($totalSst, 2); ?></td></tr>
                                            <tr><td>Net Sales (Excl. SST)</td><td class="text-end fw-bold text-success">RM <?= number_format(max(0, $totalSales - $totalSst), 2); ?></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-5 text-center">
                                <div style="max-height: 180px;" class="d-flex justify-content-center">
                                    <canvas id="paymentDonutChart"></canvas>
                                </div>
                                <small class="text-muted mt-2 d-block">Channel Settlement Ratio</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section: Daily Tax & SST Breakdown Ledger -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-white border-bottom p-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h6 class="fw-bold text-dark mb-0">Daily Tax &amp; SST Breakdown Ledger</h6>
                            <div class="input-group" style="max-width: 280px;">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="ledgerSearchInput" class="form-control border-start-0 ps-0" placeholder="Search by date..." onkeyup="filterLedger()">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover align-middle mb-0" id="dailyTaxTable">
                            <thead class="table-light text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 py-3">Date</th>
                                    <th class="text-center">Total Receipts</th>
                                    <th>Gross Sales (RM)</th>
                                    <th>Taxable Amount (RM)</th>
                                    <th>SST 6% (RM)</th>
                                    <th class="text-center">E-Invoice Status</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dailyTaxLedger)): ?>
                                    <?php foreach ($dailyTaxLedger as $row): ?>
                                        <?php 
                                            $dateFormatted = date('d M Y', strtotime($row['sale_date']));
                                        ?>
                                        <tr class="ledger-row" data-search="<?= strtolower($dateFormatted . ' ' . $row['sale_date']); ?>">
                                            <td class="ps-4 fw-bold text-dark"><?= $dateFormatted; ?></td>
                                            <td class="text-center fw-semibold"><?= (int)$row['total_receipts']; ?></td>
                                            <td class="fw-semibold">RM <?= number_format((float)$row['gross_sales'], 2); ?></td>
                                            <td>RM <?= number_format((float)$row['gross_sales'], 2); ?></td>
                                            <td class="fw-bold text-primary">RM <?= number_format((float)$row['sst_total'], 2); ?></td>
                                            <td class="text-center">
                                                <span class="rounded-pill px-3 py-1 fw-semibold small" style="background-color: #dcfce7; color: #16a34a;">
                                                    Simulated Valid
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button type="button" class="btn btn-sm btn-link text-success text-decoration-none fw-semibold" 
                                                        onclick="downloadSingleDayCSV('<?= $dateFormatted; ?>', <?= (int)$row['total_receipts']; ?>, <?= (float)$row['gross_sales']; ?>, <?= (float)$row['sst_total']; ?>)">
                                                    Download Daily CSV
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-5">No tax records found for this period.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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

    <!-- MODAL 3: Add New Product / Menu (With File Upload & Live Image Preview) -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="addProductModalLabel">Add New Product / Menu</h5>
                        <p class="text-muted small mb-0">Create a new catalogue item with custom photos and pricing</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        
                        <!-- Upload Photo from PC / Folder with Live Preview -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Upload Product Photo</label>
                            <div class="row align-items-center g-3">
                                <div class="col-auto">
                                    <img id="addPhotoPreview" src="assets/default-food.jpeg" alt="Preview" 
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;" 
                                         class="border shadow-sm bg-light">
                                </div>
                                <div class="col">
                                    <input type="file" name="product_image" id="addPhotoInput" class="form-control rounded-3" 
                                           accept="image/png, image/jpeg, image/jpg, image/webp" 
                                           onchange="previewSelectedImage(this, 'addPhotoPreview')">
                                    <small class="text-muted" style="font-size: 11px;">Select JPG, PNG or WEBP from your computer (leave empty for default food image).</small>
                                </div>
                            </div>
                        </div>

                        <!-- Product Name -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Product / Menu Name *</label>
                            <input type="text" name="product_name" class="form-control form-control-lg fs-6 rounded-3" placeholder="e.g. Nasi Lemak Sambal Sotong" required>
                        </div>

                        <!-- Category & Price -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Category *</label>
                                <input type="text" name="category" class="form-control rounded-3" placeholder="e.g. Mains, Beverages, Desserts" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Price (RM) *</label>
                                <input type="number" step="0.01" name="price" class="form-control rounded-3" placeholder="0.00" required>
                            </div>
                        </div>

                        <!-- Stock & Availability -->
                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Initial Stock Quantity *</label>
                                <input type="number" name="stock_quantity" class="form-control rounded-3" value="50" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Availability Status</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="available" selected>Available</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_product" class="btn btn-success px-4 rounded-3 fw-semibold">
                            <i class="bi bi-save me-1"></i> Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL 4: Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="editProductModalLabel">Edit Product / Menu</h5>
                        <p class="text-muted small mb-0">Update pricing, photo, stock quantity or availability</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <input type="hidden" name="existing_image" id="edit_existing_image">

                    <div class="modal-body p-4">
                        <!-- Replace Photo -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Product Photo</label>
                            <div class="row align-items-center g-3">
                                <div class="col-auto">
                                    <img id="editPhotoPreview" src="assets/default-food.jpeg" alt="Preview" 
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;" 
                                         class="border shadow-sm bg-light">
                                </div>
                                <div class="col">
                                    <input type="file" name="product_image" class="form-control rounded-3" 
                                           accept="image/png, image/jpeg, image/jpg, image/webp" 
                                           onchange="previewSelectedImage(this, 'editPhotoPreview')">
                                    <small class="text-muted" style="font-size: 11px;">Select a new image from PC to replace current photo.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Product Name -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Product Name *</label>
                            <input type="text" name="product_name" id="edit_product_name" class="form-control form-control-lg fs-6 rounded-3" required>
                        </div>

                        <!-- Category & Price -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Category *</label>
                                <input type="text" name="category" id="edit_category" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Price (RM) *</label>
                                <input type="number" step="0.01" name="price" id="edit_price" class="form-control rounded-3" required>
                            </div>
                        </div>

                        <!-- Stock & Status -->
                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Stock Quantity *</label>
                                <input type="number" name="stock_quantity" id="edit_stock_quantity" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Availability Status</label>
                                <select name="status" id="edit_status" class="form-select rounded-3">
                                    <option value="available">Available</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_product" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-check2 me-1"></i> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- MODAL 5: Product Success Modal (Verified Popup With Checkmark) -->
    <div class="modal fade" id="productSuccessModal" tabindex="-1" aria-labelledby="productSuccessLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 p-4 text-center">
                
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>

                <!-- Green Checkmark Circle -->
                <div class="my-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; background-color: #10b981;">
                        <i class="bi bi-check-lg text-white fs-3"></i>
                    </div>
                </div>

                <!-- Product Name Title -->
                <h4 class="fw-bold text-dark mt-2 mb-1">
                    <?= htmlspecialchars($newly_added_product['name'] ?? 'Product'); ?>
                </h4>
                <div class="text-muted small mb-3">Product Added to Halal Catalogue</div>

                <!-- Entered Details Box -->
                <div class="border rounded-3 p-3 text-start bg-light mb-4" style="font-size: 0.88rem;">
                    <div class="d-flex align-items-center gap-3 mb-2 pb-2 border-bottom">
                        <img src="<?= htmlspecialchars($newly_added_product['image'] ?? 'assets/default-food.jpeg'); ?>" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" class="border">
                        <div>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($newly_added_product['name'] ?? ''); ?></div>
                            <span class="badge bg-success-subtle text-success"><?= htmlspecialchars($newly_added_product['category'] ?? ''); ?></span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Price:</span>
                        <strong class="text-primary">RM <?= htmlspecialchars($newly_added_product['price'] ?? '0.00'); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Initial Stock:</span>
                        <strong class="text-dark"><?= htmlspecialchars((string)($newly_added_product['stock'] ?? '0')); ?> units</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-success text-uppercase"><?= htmlspecialchars($newly_added_product['status'] ?? 'available'); ?></span>
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-lg w-100 rounded-3 fw-semibold py-2" data-bs-dismiss="modal">
                    Done
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 6: e-Invoice Inspection Modal (Matches Image 2) -->
    <div class="modal fade" id="inspectInvoiceModal" tabindex="-1" aria-labelledby="inspectInvoiceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 p-4">
                
                <!-- Modal Top Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-dot fs-4 text-success align-middle"></i> e-Invoice Inspection &bull; <span id="insp_header_ref">#INV-1043</span>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Printable Official Invoice Container -->
                <div class="border rounded-4 p-4 bg-white" id="printableInvoiceCard">
                    
                    <!-- Company & Invoice Header -->
                    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h4 class="fw-bold text-success mb-0"><i class="bi bi-shield-check"></i> HalalInvois</h4>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">E-INVOICE (01)</span>
                            </div>
                            <div class="fw-bold text-dark">D'Melaka Food &amp; Supplies Sdn Bhd</div>
                            <small class="text-muted">Lot 14, Kawasan Perindustrian Durian Tunggal, 76100 Melaka</small>
                            <div class="small text-muted mt-1">
                                <strong>TIN:</strong> C25881029010 &nbsp;|&nbsp; <strong>SST Reg:</strong> W10-2601-32000145
                            </div>
                        </div>
                        <div class="text-end">
                            <h4 class="fw-bold text-dark mb-0" id="insp_receipt_ref">#INV-1043</h4>
                            <div class="small text-muted" id="insp_datetime">13 Nov 2026, 11:09 AM</div>
                            <div class="mt-2" id="insp_status_badge">
                                <span class="badge bg-warning-subtle text-warning border px-3 py-1">Pending Conversion</span>
                            </div>
                        </div>
                    </div>

                    <!-- Two-Column Meta Box (Buyer & Validation Token) -->
                    <div class="row g-3 bg-light rounded-3 p-3 mb-3">
                        <div class="col-md-6 border-end">
                            <div class="text-uppercase text-muted fw-bold" style="font-size: 11px;">BUYER DETAILS</div>
                            <div class="fw-bold text-dark mt-1" id="insp_buyer_name">Walk-in Customer (General Public)</div>
                            <div class="small text-muted">Buyer TIN: <span class="font-monospace fw-bold" id="insp_buyer_tin">EI00000000010</span></div>
                        </div>
                        <div class="col-md-6 ps-md-4">
                            <div class="text-uppercase text-muted fw-bold" style="font-size: 11px;">LHDN VALIDATION TOKEN</div>
                            <div class="fw-bold text-dark font-monospace small mt-1 text-truncate" id="insp_lhdn_token">Pending UBL 2.1 Conversion</div>
                            <div class="small text-muted" id="insp_validated_node">Validated: Sandbox Node MY-02</div>
                        </div>
                    </div>

                    <!-- Line Items Table -->
                    <div class="mb-3">
                        <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 11px;">LINE ITEMS</div>
                        <table class="table table-sm table-borderless align-middle small mb-0">
                            <thead class="border-bottom text-muted">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Amount (RM)</th>
                                </tr>
                            </thead>
                            <tbody id="insp_items_tbody">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Bottom Summary & QR Verification -->
                    <div class="row align-items-center border-top pt-3">
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-3 p-2 border rounded-3 bg-light">
                                <img id="insp_qr_img" src="" alt="LHDN QR" style="width: 80px; height: 80px;" class="border bg-white p-1 rounded">
                                <div style="font-size: 11px;">
                                    <div class="fw-bold text-dark">LHDN Verification QR</div>
                                    <div class="text-muted">Scan to verify UBL 2.1 digital signature &amp; UUID</div>
                                    <div class="font-monospace text-success mt-1 text-truncate" style="max-width: 220px;" id="insp_qr_ref">REF: PENDING</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-end">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Subtotal:</span>
                                <span id="insp_subtotal">RM 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>SST (6%):</span>
                                <span id="insp_sst">RM 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between fs-5 fw-bold text-dark border-top pt-1">
                                <span>Grand Total:</span>
                                <span id="insp_grand_total">RM 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success px-4 rounded-3 fw-semibold" onclick="window.print()">
                        <i class="bi bi-download me-1"></i> Download Official PDF
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL 7: Generate Simulated LHDN e-Invoice Form (Matches Image 3) -->
    <div class="modal fade" id="generateInvoiceModal" tabindex="-1" aria-labelledby="generateInvoiceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3">
                    <h5 class="fw-bold text-dark mb-0">Generate Simulated LHDN e-Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST">
                    <!-- 1. Select Pending POS Receipt -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Pending POS Receipt</label>
                        <select name="transaction_id" class="form-select form-select-lg fs-6 rounded-3" required>
                            <?php if (!empty($pendingList)): ?>
                                <?php foreach ($pendingList as $p): ?>
                                    <option value="<?= (int)$p['transaction_id']; ?>">
                                        <?= htmlspecialchars($p['ref_no']); ?> (RM <?= number_format((float)$p['total_amount'], 2); ?>) &bull; <?= htmlspecialchars(date('d M, h:i A', strtotime($p['created_at']))); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No pending POS receipts available</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- 2. Invoice Type Radio Buttons -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Invoice Type</label>
                        <div class="border rounded-3 p-3 mb-2 bg-light">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="invoice_type" id="type_b2c" value="b2c" checked onclick="toggleCorporateTin(false)">
                                <label class="form-check-label fw-bold text-dark small" for="type_b2c">
                                    B2C Walk-in Customer (Auto TIN: EI00000000010)
                                </label>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 bg-light">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="invoice_type" id="type_b2b" value="b2b" onclick="toggleCorporateTin(true)">
                                <label class="form-check-label fw-bold text-dark small" for="type_b2b">
                                    B2B Corporate Buyer TIN
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Buyer Corporate Details (Toggled if B2B) -->
                    <div id="corporateTinBox" class="mb-4 d-none">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Buyer Corporate TIN</label>
                            <input type="text" name="buyer_corporate_tin" class="form-control rounded-3" placeholder="e.g. C2584563200">
                        </div>
                        <div>
                            <label class="form-label small fw-bold">Corporate Company Name</label>
                            <input type="text" name="buyer_corporate_name" class="form-control rounded-3" placeholder="e.g. Koperasi Belia Melaka Berhad">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="generate_einvoice" class="btn btn-success px-4 rounded-3 fw-semibold">
                            Validate &amp; Generate UBL 2.1
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 8: SST-02 Tax Statement Preview (Matches Image 2) -->
    <div class="modal fade" id="sst02Modal" tabindex="-1" aria-labelledby="sst02ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">SST-02 Tax Statement Preview (<?= date('M Y'); ?>)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="border rounded-3 p-3 bg-light mb-4" style="font-size: 0.92rem;">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Business</span>
                        <strong class="text-dark">HalalInvois F&amp;B Enterprise (Melaka)</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Taxable Gross Sales</span>
                        <strong class="text-dark">RM <?= number_format($totalSales, 2); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">6% Service Tax Payable</span>
                        <strong class="text-primary">RM <?= number_format($totalSst, 2); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Validated UBL e-Invoices</span>
                        <strong class="text-dark"><?= count($invoices); ?> Receipts</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted">Status</span>
                        <span class="rounded-pill px-3 py-1 fw-semibold small" style="background-color: #dcfce7; color: #16a34a;">
                            Ready for MySST Submission
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success px-4 rounded-3 fw-semibold" onclick="window.print()">
                        <i class="bi bi-download me-1"></i> Download Official PDF
                    </button>
                </div>
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

        // 1. Live Image Preview when user selects photo from PC
        function previewSelectedImage(input, previewElementId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewElementId).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // 2. Category Filter Pills for Products Catalog
        let activeProductCategory = 'all';

        function filterProductByCategory(category, btnEl) {
            activeProductCategory = category.toLowerCase();
            document.querySelectorAll('.product-cat-pill').forEach(b => {
                b.classList.remove('btn-success', 'active');
                b.classList.add('btn-outline-secondary');
            });
            btnEl.classList.remove('btn-outline-secondary');
            btnEl.classList.add('btn-success', 'active');
            applyProductFilters();
        }

        // 3. Search Bar Filter for Products Catalog
        function filterProductsCatalog() {
            applyProductFilters();
        }

        function applyProductFilters() {
            const query = (document.getElementById('productSearchInput')?.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.product-row');

            rows.forEach(row => {
                const rowCat = row.getAttribute('data-category') || '';
                const rowSearch = row.getAttribute('data-search') || '';

                const matchesCat = (activeProductCategory === 'all' || rowCat === activeProductCategory);
                const matchesQuery = query === '' || rowSearch.includes(query);

                if (matchesCat && matchesQuery) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // 4. Pre-fill Edit Product Modal
        function openEditProductModal(id, name, category, price, stock, status, image) {
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_product_name').value = name;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock_quantity').value = stock;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_existing_image').value = image;
            document.getElementById('editPhotoPreview').src = image;

            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        }

        // 5. Auto-trigger Product Success Modal upon adding a new product
        <?php if (!empty($newly_added_product)): ?>
        document.addEventListener("DOMContentLoaded", function() {
            new bootstrap.Modal(document.getElementById('productSuccessModal')).show();
        });
        <?php endif; ?>

    // 1. Toggle B2B Corporate TIN input
    function toggleCorporateTin(isB2b) {
        const box = document.getElementById('corporateTinBox');
        if (isB2b) {
            box.classList.remove('d-none');
        } else {
            box.classList.add('d-none');
        }
    }

    // 2. Filter Invoices by Status Pill (All, Valid, Pending)
    let activeInvoiceStatus = 'all';

    function filterInvoicesByStatus(status, btnEl) {
        activeInvoiceStatus = status;
        document.querySelectorAll('.invoice-pill').forEach(b => {
            b.classList.remove('btn-success', 'active');
            b.classList.add('btn-outline-secondary');
        });
        btnEl.classList.remove('btn-outline-secondary');
        btnEl.classList.add('btn-success', 'active');
        applyInvoiceFilters();
    }

    function filterInvoices() {
        applyInvoiceFilters();
    }

    function applyInvoiceFilters() {
        const query = (document.getElementById('invoiceSearchInput')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.invoice-row');

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowSearch = row.getAttribute('data-search') || '';

            const matchesStatus = (activeInvoiceStatus === 'all' || rowStatus === activeInvoiceStatus);
            const matchesQuery = query === '' || rowSearch.includes(query);

            if (matchesStatus && matchesQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // 3. Open and populate the Inspection Modal (Image 2)
    function openInspectModal(inv) {
        const isValid = (inv.status_clean === 'valid');
        const total = parseFloat(inv.total_amount) || 0;
        const sst = parseFloat(inv.sst_amount) || 0;
        const subtotal = Math.max(0, total - sst);

        document.getElementById('insp_header_ref').innerText = inv.ref_no;
        document.getElementById('insp_receipt_ref').innerText = inv.ref_no;
        document.getElementById('insp_datetime').innerText = new Date(inv.created_at).toLocaleString('en-MY', {
            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true
        });

        document.getElementById('insp_buyer_name').innerText = inv.buyer_clean;
        document.getElementById('insp_buyer_tin').innerText = inv.tin_clean;

        // Status Badge
        const badgeEl = document.getElementById('insp_status_badge');
        const tokenEl = document.getElementById('insp_lhdn_token');
        if (isValid) {
            badgeEl.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">Valid Simulated</span>';
            tokenEl.innerText = inv.lhdn_uuid;
            document.getElementById('insp_validated_node').innerText = 'Validated: ' + (inv.validated_at || inv.created_at) + ' • Sandbox Node MY-02';
            document.getElementById('insp_qr_ref').innerText = 'UUID: ' + inv.lhdn_uuid.substring(0, 18) + '...';
            document.getElementById('insp_qr_img').src = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://myinvois.hasil.gov.my/uuid/${inv.lhdn_uuid}`;
        } else {
            badgeEl.innerHTML = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1">Pending Conversion</span>';
            tokenEl.innerText = 'Pending UBL 2.1 Conversion';
            document.getElementById('insp_validated_node').innerText = 'Awaiting generation in dashboard';
            document.getElementById('insp_qr_ref').innerText = 'REF: PENDING';
            document.getElementById('insp_qr_img').src = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=PENDING-UBL-CONVERSION-${inv.ref_no}`;
        }

        // Totals
        document.getElementById('insp_subtotal').innerText = 'RM ' + subtotal.toFixed(2);
        document.getElementById('insp_sst').innerText = 'RM ' + sst.toFixed(2);
        document.getElementById('insp_grand_total').innerText = 'RM ' + total.toFixed(2);

        // Line items table
        const tbody = document.getElementById('insp_items_tbody');
        tbody.innerHTML = '';
        if (inv.items && inv.items.length > 0) {
            inv.items.forEach(it => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-semibold text-dark">${it.product_name || 'Item'}</td>
                    <td class="text-center">${it.quantity}</td>
                    <td class="text-end">RM ${parseFloat(it.subtotal).toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td class="fw-semibold text-dark">POS Order Items</td>
                    <td class="text-center">1</td>
                    <td class="text-end">RM ${subtotal.toFixed(2)}</td>
                </tr>
            `;
        }
        new bootstrap.Modal(document.getElementById('inspectInvoiceModal')).show();
    }

    // 1. Dual-Bar Chart (Daily Sales vs 6% SST Collection)
    const sstChartData = <?= json_encode($dailyTaxChartData ?? []); ?>;

    new Chart(document.getElementById('dailySstDualChart'), {
        type: 'bar',
        data: {
            labels: sstChartData.map(d => d.day_short || d.raw_date),
            datasets: [
                {
                    label: 'Gross Sales (RM)',
                    data: sstChartData.map(d => d.gross_sales),
                    backgroundColor: '#059669',
                    borderRadius: 4
                },
                {
                    label: '6% SST (RM)',
                    data: sstChartData.map(d => d.sst_amount),
                    backgroundColor: '#94a3b8',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(v) { return 'RM ' + v; }
                    }
                }
            }
        }
    });

    // 2. Filter Daily Tax & SST Ledger by Date
    function filterLedger() {
        const query = (document.getElementById('ledgerSearchInput')?.value || '').toLowerCase().trim();
        document.querySelectorAll('.ledger-row').forEach(row => {
            const searchVal = row.getAttribute('data-search') || '';
            if (query === '' || searchVal.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // 3. Export Full Ledger to CSV
    function exportLedgerToCSV() {
        let csv = "Date,Total Receipts,Gross Sales (RM),Taxable Amount (RM),SST 6% (RM),Status\n";
        document.querySelectorAll('#dailyTaxTable tbody tr.ledger-row').forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 6) {
                const date = cols[0].innerText.trim();
                const receipts = cols[1].innerText.trim();
                const gross = cols[2].innerText.replace('RM', '').trim();
                const taxable = cols[3].innerText.replace('RM', '').trim();
                const sst = cols[4].innerText.replace('RM', '').trim();
                const status = cols[5].innerText.trim();
                csv += `"${date}",${receipts},${gross},${taxable},${sst},"${status}"\n`;
            }
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `HalalInvois_SST02_Ledger_${new Date().toISOString().slice(0, 10)}.csv`;
        a.click();
    }

    // 4. Download Single Row CSV
    function downloadSingleDayCSV(date, receipts, gross, sst) {
        const csv = `Date,Total Receipts,Gross Sales (RM),Taxable Amount (RM),SST 6% (RM),Status\n"${date}",${receipts},${gross.toFixed(2)},${gross.toFixed(2)},${sst.toFixed(2)},"Simulated Valid"\n`;
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `SST_Report_${date.replace(/ /g, '_')}.csv`;
        a.click();
    }
    </script>
</body>
</html>