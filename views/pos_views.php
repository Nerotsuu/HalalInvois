<?php
    // Define fallbacks to prevent IDE undefined variable warnings
    $username        = $username        ?? 'Cashier';
    $user_role       = $user_role       ?? 'cashier';
    $login_time      = $login_time      ?? time();
    $success_message = $success_message ?? '';
    $error_message   = $error_message   ?? '';
    $products        = $products        ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HalalInvois - POS Terminal</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Top Navbar with Live Status Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-shop"></i> HalalInvois Cashier POS
            </span>
            
            <div class="d-flex align-items-center gap-3 text-white small flex-wrap">
                <!-- Current Day & Date -->
                <div>
                    <i class="bi bi-calendar3"></i> <span id="live-day-date">--</span>
                </div>

                <!-- Live Clock (Time) -->
                <div>
                    <i class="bi bi-clock"></i> <span id="live-clock" class="fw-bold">--:--:--</span>
                </div>

                <!-- Time Taken Since Login -->
                <div class="badge bg-white text-success px-2 py-1 shadow-sm">
                    <i class="bi bi-hourglass-split"></i> Active: <span id="live-session-duration">00h 00m 00s</span>
                </div>

                <!-- Username & Role -->
                <div class="border-start ps-3">
                    <i class="bi bi-person-circle"></i> <strong><?= htmlspecialchars($username); ?></strong>
                    <span class="badge bg-light text-success ms-1 text-uppercase"><?= htmlspecialchars($user_role); ?></span>
                </div>

                <!-- Logout Link -->
                <a href="login.php?logout=1" class="btn btn-sm btn-outline-light ms-2">Logout</a>
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

        <div class="row g-4">
            
            <!-- Left Side: Dynamic Product Catalog -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-success fw-bold"><i class="bi bi-grid-fill"></i> Menu / Product Catalog</h5>
                    </div>
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-md-3 g-3">
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $row): ?>
                                <div class="col">
                                    <div class="card h-100 border shadow-sm product-card" 
                                         style="cursor: pointer;"
                                         onclick="addToCart(<?= $row['product_id']; ?>, '<?= addslashes($row['product_name']); ?>', <?= $row['price']; ?>, <?= $row['stock_quantity']; ?>)">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div>
                                                <h6 class="card-title fw-bold text-dark"><?= htmlspecialchars($row['product_name']); ?></h6>
                                                <span class="badge bg-success mb-1"><?= htmlspecialchars($row['category']); ?></span>
                                                <div class="text-muted small">Stock: <?= $row['stock_quantity']; ?></div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="text-primary fw-bold">RM <?= number_format($row['price'], 2); ?></span>
                                                <button class="btn btn-sm btn-outline-success"><i class="bi bi-plus-lg"></i> Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-5">
                                    <p class="text-muted">No products found in database or table is empty. Please add items via phpMyAdmin.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Interactive Cart & Checkout Panel -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Active Order Cart</h5>
                    </div>
                    <div class="card-body">
                        
                        <form id="checkout-form" method="POST">
                            <input type="hidden" name="cart_json" id="cart_json">
                            
                            <!-- Cart Items List Container -->
                            <ul class="list-group mb-3" id="cart-list" style="max-height: 250px; overflow-y: auto;">
                                <li class="list-group-item text-center text-muted py-3">Cart is empty</li>
                            </ul>

                            <!-- Payment Method Selector -->
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Payment Method</label>
                                <select class="form-select form-select-sm" name="payment_method" id="payment_method">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="duitnow_qr" selected>DuitNow QR (Auto-calculated)</option>
                                </select>
                            </div>

                            <!-- Calculation Summary -->
                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong id="summary-subtotal">RM 0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 text-muted small">
                                    <span>Simulated LHDN e-Invoice Tax:</span>
                                    <span>RM 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 fs-5 text-success fw-bold">
                                    <span>Total Amount:</span>
                                    <span id="summary-total">RM 0.00</span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <button type="submit" name="checkout" class="btn btn-success btn-lg" id="checkout-btn" disabled>
                                        <i class="bi bi-qr-code-scan"></i> Complete & Simulate e-Invoice
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearCart()">
                                        <i class="bi bi-trash"></i> Clear Cart
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Cart Management JavaScript -->
    <script>
        let cart = [];
        const loginTimestamp = <?= (int)$login_time ?> * 1000;

        function updateDateTimeAndDuration() {
            const now = new Date();
            // 1. Format Day & Date (e.g. Saturday, 21 Nov 2026)
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayName = days[now.getDay()];
            const dateFormatted = now.toLocaleDateString('en-MY', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            document.getElementById('live-day-date').innerText = `${dayName}, ${dateFormatted}`;
            // 2. Format Live Clock (e.g. 03:45:12 AM)
            const timeFormatted = now.toLocaleTimeString('en-MY', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            document.getElementById('live-clock').innerText = timeFormatted;
            // 3. Time Taken / Session Duration Counter (HH:MM:SS)
            const elapsedSeconds = Math.max(0, Math.floor((now.getTime() - loginTimestamp) / 1000));
            const hours = Math.floor(elapsedSeconds / 3600);
            const minutes = Math.floor((elapsedSeconds % 3600) / 60);
            const seconds = elapsedSeconds % 60;
            const pad = (n) => String(n).padStart(2, '0');
            document.getElementById('live-session-duration').innerText = 
                `${pad(hours)}h ${pad(minutes)}m ${pad(seconds)}s`;
        }
        // Run immediately and update every second
        updateDateTimeAndDuration();
        setInterval(updateDateTimeAndDuration, 1000);

        function addToCart(productId, productName, price, stock) {
            let existingItem = cart.find(item => item.product_id === productId);
            
            if (existingItem) {
                if (existingItem.quantity < stock) {
                    existingItem.quantity++;
                } else {
                    alert("Not enough stock available!");
                    return;
                }
            } else {
                if (stock > 0) {
                    cart.push({
                        product_id: productId,
                        product_name: productName,
                        price: parseFloat(price),
                        quantity: 1,
                        stock: stock
                    });
                } else {
                    alert("Product is out of stock!");
                    return;
                }
            }
            renderCart();
        }

        function updateQuantity(productId, change) {
            let item = cart.find(item => item.product_id === productId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => i.product_id !== productId);
                } else if (item.quantity > item.stock) {
                    item.quantity = item.stock;
                    alert("Reached maximum available stock.");
                }
            }
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
        }

        function renderCart() {
            let cartList = document.getElementById('cart-list');
            let subtotalEl = document.getElementById('summary-subtotal');
            let totalEl = document.getElementById('summary-total');
            let checkoutBtn = document.getElementById('checkout-btn');
            let cartJsonInput = document.getElementById('cart_json');

            cartList.innerHTML = '';

            if (cart.length === 0) {
                cartList.innerHTML = '<li class="list-group-item text-center text-muted py-3">Cart is empty</li>';
                subtotalEl.innerText = 'RM 0.00';
                totalEl.innerText = 'RM 0.00';
                checkoutBtn.disabled = true;
                cartJsonInput.value = '';
                return;
            }

            let subtotal = 0;
            cart.forEach(item => {
                let itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                let li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <div>
                        <h6 class="mb-0 text-dark">${item.product_name}</h6>
                        <small class="text-muted">RM ${item.price.toFixed(2)} x ${item.quantity}</small>
                    </div>
                    <div class="btn-group btn-group-sm align-items-center">
                        <span class="fw-bold me-2">RM ${itemTotal.toFixed(2)}</span>
                        <button type="button" class="btn btn-outline-secondary px-2" onclick="updateQuantity(${item.product_id}, -1)">-</button>
                        <button type="button" class="btn btn-outline-secondary px-2" onclick="updateQuantity(${item.product_id}, 1)">+</button>
                    </div>
                `;
                cartList.appendChild(li);
            });

            subtotalEl.innerText = 'RM ' + subtotal.toFixed(2);
            totalEl.innerText = 'RM ' + subtotal.toFixed(2);
            checkoutBtn.disabled = false;
            cartJsonInput.value = JSON.stringify(cart);
        }


    </script>
</body>
</html>