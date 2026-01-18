<?php

declare(strict_types=1);

// Start or resume session to access user data
session_start();

// Include database connection and authentication helpers
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Set response content type to JSON
header('Content-Type: application/json');

// Ensure the user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Decode JSON payload from request body
$data = json_decode(file_get_contents('php://input'), true);
$billId = $data['billId'] ?? null;

// Validate that a bill ID was provided
if (!$billId) {
    echo json_encode(['success' => false, 'message' => 'Bill ID is required']);
    exit();
}

// ---------------------------------------------------------------------
// Fetch current bill status and ownership
// ---------------------------------------------------------------------
// ---------------------------------------------------------------------
// Fetch current bill status and ownership
// ---------------------------------------------------------------------
$stmt = $conn->prepare(
    "SELECT status, user_id FROM bills WHERE id = ?"
);
$stmt->execute([$billId]);
$bill = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if bill exists and user is allowed to modify it
if (!$bill || (!isAdmin() && $bill['user_id'] !== $_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// ---------------------------------------------------------------------
// Toggle status and update database
// ---------------------------------------------------------------------
$newStatus = $bill['status'] === 'paid' ? 'unpaid' : 'paid';
$stmt = $conn->prepare(
    "UPDATE bills SET status = ? WHERE id = ?"
);

if ($stmt->execute([$newStatus, $billId])) {
    // Return updated status on success
    echo json_encode(['success' => true, 'status' => $newStatus]);
} else {
    // Handle possible database errors
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
