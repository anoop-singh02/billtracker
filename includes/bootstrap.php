<?php
declare(strict_types=1);

// Display all PHP errors and warnings on the page
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Configure MySQLi to report errors and throw exceptions for easier debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ---------------------------------------------------------------------
// Session Initialization
// ---------------------------------------------------------------------
// Start a new session or resume existing one to access session data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------
// Database Connection
// ---------------------------------------------------------------------
// Include database connection file (sets up $conn or similar)
require_once __DIR__ . '/db.php';
