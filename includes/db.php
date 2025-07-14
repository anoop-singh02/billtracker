<?php
/**
 * Database
 * ------------------------------------------------------------
 * - Defines database connection constants
 * - Connects to MySQL server
 * - Creates the application database and required tables
 * - Inserts default admin and user accounts
 */

declare(strict_types=1);

// ---------------------------------------------------------------------
// Database Connection Constants
// ---------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'billtracker');

// ---------------------------------------------------------------------
// Connect to MySQL Server
// ---------------------------------------------------------------------
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection and halt on error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ---------------------------------------------------------------------
// Create Database if it doesn't exist
// ---------------------------------------------------------------------
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    // Select the newly created (or existing) database
    $conn->select_db(DB_NAME);
    
    // -----------------------------------------------------------------
    // Create 'users' table
    // -----------------------------------------------------------------
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    
    // -----------------------------------------------------------------
    // Create 'bills' table
    // -----------------------------------------------------------------
    $sql = "CREATE TABLE IF NOT EXISTS bills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        due_date DATE NOT NULL,
        category VARCHAR(50) NOT NULL,
        status ENUM('paid', 'unpaid') DEFAULT 'unpaid',
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->query($sql);

    // -----------------------------------------------------------------
    // Insert Default Admin Account
    // -----------------------------------------------------------------
    $admin_username = 'admin';
    $admin_password = 'admin123';
    $admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO users (username, password_hash, role) VALUES (?, ?, 'admin')"
    );
    $stmt->bind_param("ss", $admin_username, $admin_hash);
    $stmt->execute();

    // -----------------------------------------------------------------
    // Insert Default User Account
    // -----------------------------------------------------------------
    $user_username = 'user';
    $user_password = 'user123';
    $user_hash = password_hash($user_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO users (username, password_hash, role) VALUES (?, ?, 'user')"
    );
    $stmt->bind_param("ss", $user_username, $user_hash);
    $stmt->execute();

} else {
    // Halt on database creation error
    die("Error creating database: " . $conn->error);
}
