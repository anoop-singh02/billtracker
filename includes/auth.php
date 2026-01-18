<?php
/**
 * Authentication 
 * ------------------------------------------------------------
 * - Manages user session checks, login, registration, and logout
 */

declare(strict_types=1);

// ---------------------------------------------------------------------
// Session Initialization
// ---------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    // Start a session if none exists
    session_start();
}

// Include database connection
require_once __DIR__ . '/db.php';

// ---------------------------------------------------------------------
// isLoggedIn
// ---------------------------------------------------------------------
/**
 * Checks if the user is currently logged in by verifying session data.
 *
 * @return bool True if user_id exists in session, false otherwise.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

// ---------------------------------------------------------------------
// isAdmin
// ---------------------------------------------------------------------
/**
 * Checks if the logged-in user has admin privileges.
 *
 * @return bool True if user role is 'admin', false otherwise.
 */
function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// ---------------------------------------------------------------------
// login
// ---------------------------------------------------------------------
/**
 * Attempts to authenticate a user with provided credentials.
 * On success, saves user data to session.
 *
 * @param string $username The username to authenticate.
 * @param string $password The plaintext password to verify.
 * @return bool True on successful login, false on failure.
 */
function login(string $username, string $password): bool
{
    global $conn;

    // Prepare and execute query to fetch user record
    $stmt = $conn->prepare("SELECT id, password_hash, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify exactly one matching user
    if ($user) {
        // Verify password hash
        if (password_verify($password, $user['password_hash'])) {
            // Store user info in session for later checks
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}

// ---------------------------------------------------------------------
// register
// ---------------------------------------------------------------------
/**
 * Registers a new user if the username is not already taken.
 * Stores a hashed password in the database.
 *
 * @param string $username Desired username.
 * @param string $password Plaintext password to hash.
 * @return bool True on successful registration, false if username exists or error.
 */
function register(string $username, string $password): bool
{
    global $conn;

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        return false;
    }

    // Hash the password for secure storage
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    // Insert new user record
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");

    return $stmt->execute([$username, $password_hash]);
}

// ---------------------------------------------------------------------
// logout
// ---------------------------------------------------------------------
/**
 * Logs the user out by clearing session data and redirecting to login page.
 */
function logout(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Remove all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header('Location: index.php?page=login');
    exit();
}