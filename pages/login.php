<?php
require_once __DIR__ . '/../includes/bootstrap.php';   // error reporting + DB
require_once __DIR__ . '/../includes/auth.php';        // login(), isLoggedIn(), register()

// Already authenticated?  Ship them straight to the dashboard
if (isLoggedIn()) {
    header('Location: index.php?page=dashboard');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '' && login($username, $password)) {
        header('Location: index.php?page=dashboard');
        exit();
    }
    $error = 'Invalid username or password';
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i data-lucide="key" class="text-primary mb-3" style="width:48px;height:48px;"></i>
                        <h2 class="h3">Login to Your Account</h2>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="?page=login">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">Don't have an account? <a href="?page=register">Register here</a></p>
                    </div>

                    <!-- Demo Account Info -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h5 class="h6 mb-2">Demo Accounts:</h5>
                        <div class="small">
                            <p class="mb-1"><strong>Admin:</strong> tempadmin / Test123!</p>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
