<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Character encoding and responsive viewport settings -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Page title displayed in browser tab -->
    <title>BillTracker - Manage Your Bills Efficiently</title>
    <!-- Bootstrap CSS framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom stylesheet for additional styling -->
    <link href="css/styles.css" rel="stylesheet">
    <!-- Lucide icon library -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <!-- Primary navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <!-- Brand/logo linking to home page -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i data-lucide="file-text" class="me-2"></i>
                BillTracker
            </a>
            <!-- Mobile menu toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Nav links container -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Always show Home link -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <!-- Links for authenticated users -->
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=bills">Bills</a>
                        </li>
                        <!-- Admin-specific link -->
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=admin">Admin</a>
                            </li>
                        <?php endif; ?>
                        <!-- Logout link to end session -->
                        <li class="nav-item">
                            <a class="nav-link" href="pages/logout.php">Logout</a>
                        </li>
                    <!-- Links for guests -->
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>