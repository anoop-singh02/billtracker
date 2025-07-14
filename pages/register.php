<?php
declare(strict_types=1);

// If user is already logged in, send them straight to dashboard
if (isLoggedIn()) {
    header('Location: index.php?page=dashboard');
    exit();
}

// Handle form submission on POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve submitted form fields, defaulting to empty strings
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate that passwords match
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    }
    // Validate minimum password length
    elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    }
    // Attempt to register the new user
    elseif (register($username, $password)) {
        // On successful registration, log them in and redirect
        login($username, $password);
        header('Location: index.php?page=dashboard');
        exit();
    }
    // Registration failed (e.g., username taken)
    else {
        $error = 'Username already exists';
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Registration form layout -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <!-- Page title and icon -->
                    <div class="text-center mb-4">
                        <i data-lucide="user-plus" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
                        <h2 class="h3">Create an Account</h2>
                    </div>

                    <!-- Display validation errors if set -->
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <!-- Registration form -->
                    <form method="POST" action="?page=register">
                        <!-- Username input -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <!-- Password input -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6" placeholder="Minimum 6 characters">
                        </div>
                        <!-- Confirm password input -->
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                        <!-- Submit button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </div>
                    </form>

                    <!-- Link to login for existing users -->
                    <div class="text-center mt-4">
                        <p class="mb-0">Already have an account? <a href="?page=login">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
