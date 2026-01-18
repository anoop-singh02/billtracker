<?php
/**
 * Database (SQLite Version)
 * ------------------------------------------------------------
 * - Connects to a local SQLite file (database.sqlite)
 * - Auto-creates tables if they don't exist
 * - Inserts default admin/user accounts
 */

declare(strict_types=1);

// SQLite Database File Path
define('DB_FILE', __DIR__ . '/database.sqlite');

try {
    // ---------------------------------------------------------------------
    // Connect to SQLite
    // ---------------------------------------------------------------------
    $conn = new PDO('sqlite:' . DB_FILE);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // ---------------------------------------------------------------------
    // Create Tables (Schema Migration)
    // ---------------------------------------------------------------------

    // Create 'users' table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        role TEXT DEFAULT 'user',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create 'bills' table
    $conn->exec("CREATE TABLE IF NOT EXISTS bills (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        amount REAL NOT NULL,
        due_date TEXT NOT NULL,
        category TEXT NOT NULL,
        status TEXT DEFAULT 'unpaid',
        user_id INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // ---------------------------------------------------------------------
    // Seed Default Data (Check if empty first)
    // ---------------------------------------------------------------------
    $check = $conn->query("SELECT count(*) FROM users");
    if ($check->fetchColumn() == 0) {
        $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $user_pass = password_hash('user123', PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $admin_pass, 'admin']);
        $stmt->execute(['user', $user_pass, 'user']);
    }

} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
