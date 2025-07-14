<?php

declare(strict_types=1);

// Bootstrap the application (autoload, config, database connections, etc.)
require_once __DIR__ . '/includes/bootstrap.php';

// Include authentication helper functions (isLoggedIn, isAdmin)
require_once __DIR__ . '/includes/auth.php';

// ---------------------------------------------------------------------
// Session – start it once, gracefully
// ---------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------
// Routing – map ?page=xyz to an actual file
// ---------------------------------------------------------------------
// Default to 'home' if no page parameter is provided
$page = $_GET['page'] ?? 'home';

// Define all valid routes and their corresponding file paths
$routes = [
    'home'      => __DIR__ . '/pages/home.php',
    'login'     => __DIR__ . '/pages/login.php',
    'register'  => __DIR__ . '/pages/register.php',
    'dashboard' => __DIR__ . '/pages/dashboard.php',
    'bills'     => __DIR__ . '/pages/bills.php',
    'logout'    => __DIR__ . '/pages/logout.php',
    'admin'     => __DIR__ . '/pages/admin.php',
];

// Determine which content file to include, fall back to 'home' route
$content = $routes[$page] ?? $routes['home'];

// ---------------------------------------------------------------------
// Access control – protect certain pages behind login and role checks
// ---------------------------------------------------------------------
$protected = ['dashboard', 'bills', 'logout', 'admin'];

// If the requested page is protected and the user is not logged in, redirect to login
if (in_array($page, $protected, true) && !isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

// Additional check: only allow admins on the 'admin' page
if ($page === 'admin' && !isAdmin()) {
    // Non-admin users go back to dashboard
    header('Location: index.php?page=dashboard');
    exit;
}

// ---------------------------------------------------------------------
// Render the selected page content
// ---------------------------------------------------------------------
include $content;
