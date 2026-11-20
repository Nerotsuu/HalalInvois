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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand mb-0 h1" href="owner_dashboard.php">
                <i class="bi bi-speedometer2"></i> HalalInvois Owner Dashboard
            </a>

            <div class="d-flex align-items-center gap-3 text-white small flex-wrap">
                <div><i class="bi bi-calendar3"></i> <span id="live-day-date">--</span></div>
                <div><i class="bi bi-clock"></i> <span id="live-clock" class="fw-bold">--:--:--</span></div>
                <div class="badge bg-white text-success px-2 py-1 shadow-sm">
                    <i class="bi bi-hourglass-split"></i> Active: <span id="live-session-duration">00h 00m 00s</span>
                </div>
                <div class="border-start ps-3">
                    <i class="bi bi-person-circle"></i> <strong><?= htmlspecialchars($username); ?></strong>
                    <span class="badge bg-light text-success ms-1 text-uppercase"><?= htmlspecialchars($user_role); ?></span>
                </div>
                <a href="pos.php" class="btn btn-sm btn-light text-success fw-semibold">
                    <i class="bi bi-shop"></i> POS Terminal
                </a>
                <a href="login.php?logout=1" class="btn btn-sm btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Top KPI Overview Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total Sales</div>
                        <div class="fs-4 fw-bold text-success">RM <?= number_format($totalSales, 2); ?></div>
                        <div class="small text-muted"><?= $totalTransactions; ?> recorded invoices</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">SST Collected</div>
                        <div class="fs-4 fw-bold text-primary">RM <?= number_format($totalSst, 2); ?></div>
                        <div class="small text-muted">Tax &amp; Sales Reporting</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Tracked Ingredients</div>
                        <div class="fs-4 fw-bold text-dark"><?= count($ingredients); ?></div>
                        <div class="small text-muted">Halal &amp; Inventory items</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Active RBAC Accounts</div>
                        <div class="fs-4 fw-bold text-dark"><?= count($usersList); ?></div>
                        <div class="small text-muted">Owner / Cashier / Auditor</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4-Module Navigation Tabs -->
        <ul class="nav nav-pills mb-3 gap-2" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $active_tab === 'halal' ? 'active bg-success' : ''; ?>"
                        data-bs-toggle="pill" data-bs-target="#mod-halal" type="button" role="tab">
                    <i class="bi bi-patch-check"></i> 1. Halal &amp; Inventory Tracking
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $active_tab === 'lhdn' ? 'active bg-success' : ''; ?>"
                        data-bs-toggle="pill" data-bs-target="#mod-lhdn" type="button" role="tab">
                    <i class="bi bi-upc-scan"></i> 2. LHDN e-Invoicing Simulation
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $active_tab === 'sales' ? 'active bg-success' : ''; ?>"
                        data-bs-toggle="pill" data-bs-target="#mod-sales" type="button" role="tab">
                    <i class="bi bi-bar-chart-line"></i> 3. Tax &amp; Sales Reporting
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $active_tab === 'rbac' ? 'active bg-success' : ''; ?>"
                        data-bs-toggle="pill" data-bs-target="#mod-rbac" type="button" role="tab">
                    <i class="bi bi-shield-lock"></i> 4. Role-Based Access Control (RBAC)
                </button>
            </li>
        </ul>

        <div class="tab-content">

            <!-- MODULE 1: Halal & Inventory Tracking -->
            <div class="tab-pane fade <?= $active_tab === 'halal' ? 'show active' : ''; ?>" id="mod-halal" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success">
                                <i class="bi bi-plus-circle"></i> Add Halal Ingredient
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="active_tab" value="halal">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Ingredient Name *</label>
                                        <input type="text" name="ingredient_name" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Supplier Name *</label>
                                        <input type="text" name="supplier_name" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Stock Qty *</label>
                                            <input type="number" step="0.01" name="current_stock" class="form-control form-control-sm" value="0.00" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Unit *</label>
                                            <input type="text" name="unit" class="form-control form-control-sm" placeholder="kg, L, pack" required>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Item Expiry Date *</label>
                                        <input type="date" name="expiry_date" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Halal Cert No.</label>
                                        <input type="text" name="halal_cert_no" class="form-control form-control-sm" placeholder="e.g. JAKIM-MS1500-...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Halal Cert Expiry</label>
                                        <input type="date" name="cert_expiry_date" class="form-control form-control-sm">
                                    </div>
                                    <button type="submit" name="add_ingredient" class="btn btn-success btn-sm w-100">
                                        Save Ingredient
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success">
                                <i class="bi bi-list-check"></i> Ingredient Halal &amp; Stock Ledger
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover align-middle small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ingredient</th>
                                            <th>Supplier</th>
                                            <th>Stock</th>
                                            <th>Item Expiry</th>
                                            <th>Halal Cert</th>
                                            <th>Cert Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($ingredients)): ?>
                                            <?php foreach ($ingredients as $ing): ?>
                                                <?php
                                                $certExp = $ing['cert_expiry_date'] ?? null;
                                                $isExpiredCert = $certExp && ($certExp < $todayStr);
                                                $isExpiredItem = ($ing['expiry_date'] ?? '') < $todayStr;
                                                ?>
                                                <tr>
                                                    <td class="fw-semibold"><?= htmlspecialchars($ing['ingredient_name']); ?></td>
                                                    <td><?= htmlspecialchars($ing['supplier_name']); ?></td>
                                                    <td><?= number_format((float)$ing['current_stock'], 2); ?> <?= htmlspecialchars($ing['unit']); ?></td>
                                                    <td>
                                                        <span class="<?= $isExpiredItem ? 'text-danger fw-bold' : ''; ?>">
                                                            <?= htmlspecialchars($ing['expiry_date']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($ing['halal_cert_no'] ?: 'Unset'); ?></td>
                                                    <td>
                                                        <?php if (!$certExp): ?>
                                                            <span class="badge bg-secondary">No Cert Date</span>
                                                        <?php elseif ($isExpiredCert): ?>
                                                            <span class="badge bg-danger">Expired (<?= htmlspecialchars($certExp); ?>)</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">Valid (<?= htmlspecialchars($certExp); ?>)</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center text-muted py-4">No ingredients recorded yet.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODULE 2: LHDN e-Invoicing Simulation -->
            <div class="tab-pane fade <?= $active_tab === 'lhdn' ? 'show active' : ''; ?>" id="mod-lhdn" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold text-success">
                        <i class="bi bi-file-earmark-check"></i> Simulated LHDN e-Invoice Records
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
                                            <td class="fw-bold"><?= number_format((float)$inv['total_amount'], 2); ?></td>
                                            <td><?= htmlspecialchars($inv['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">No transactions found yet. Complete a sale in POS to generate a simulated LHDN UUID.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MODULE 3: Tax & Sales Reporting -->
            <div class="tab-pane fade <?= $active_tab === 'sales' ? 'show active' : ''; ?>" id="mod-sales" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold text-success">
                        <i class="bi bi-cash-stack"></i> Tax &amp; Sales Breakdown by Payment Method
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-white">
                                    <div class="text-muted small">DuitNow QR Sales</div>
                                    <div class="fs-5 fw-bold text-success">RM <?= number_format($duitnowSales, 2); ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-white">
                                    <div class="text-muted small">Cash Sales</div>
                                    <div class="fs-5 fw-bold text-dark">RM <?= number_format($cashSales, 2); ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-white">
                                    <div class="text-muted small">Card Sales</div>
                                    <div class="fs-5 fw-bold text-dark">RM <?= number_format($cardSales, 2); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered small mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Metric</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Gross Sales Amount</td>
                                        <td class="fw-bold">RM <?= number_format($totalSales, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Total SST Tax Recorded</td>
                                        <td class="fw-bold">RM <?= number_format($totalSst, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Net Sales (Excl. SST)</td>
                                        <td class="fw-bold">RM <?= number_format(max(0, $totalSales - $totalSst), 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODULE 4: Role-Based Access Control (RBAC) -->
            <div class="tab-pane fade <?= $active_tab === 'rbac' ? 'show active' : ''; ?>" id="mod-rbac" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success">
                                <i class="bi bi-person-plus"></i> Create Staff / Role Account
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="active_tab" value="rbac">
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
                                        Create User Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold text-success">
                                <i class="bi bi-people"></i> Registered System Users &amp; Permissions
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
                                                <td><?= (int)$usr['user_id']; ?></td>
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

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const loginTimestamp = <?= (int)$login_time ?> * 1000;

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
    </script>
</body>
</html>