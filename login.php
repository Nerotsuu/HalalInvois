<?php
session_start();
require_once __DIR__ . '/config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        // Query user from database
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password hash
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on RBAC role
            if ($user['role'] == 'owner') {
                header("Location: owner_dashboard.php");
                exit();
            } elseif ($user['role'] == 'cashier') {
                header("Location: pos.php");
                exit();
            } elseif ($user['role'] == 'auditor') {
                header("Location: auditor_reports.php");
                exit();
            }
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HalalInvois - Login</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center py-4" style="height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-success">HalalInvois</h3>
                        <p class="text-muted">Sign in to your account</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2" role="alert">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2">Login</button>
                    </form>

                    <div class="mt-3 text-center">
                        <small class="text-muted">Default Test Credentials:<br>
                        Owner: <code>owner_store1</code> / <code>password123</code><br>
                        Cashier: <code>cashier_1</code> / <code>password123</code></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>