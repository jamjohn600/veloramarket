<?php
// Include your database configuration file
require_once 'db.php';

// Start session if not already started
session_start();

// Assuming the user is logged in, retrieve the user's username from the session
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$username = $_SESSION['username'];

try {
    // Query to fetch the email_verified and transaction_status for the logged-in user
    $query = "SELECT email_verified, transaction_status FROM users WHERE username = ?";
    
    // Prepare and execute the statement
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    
    // Fetch the user's data
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no result, return error
    if (!$result) {
        echo json_encode(['error' => 'User not found']);
        exit();
    }

    // Return the data in JSON format
    echo json_encode([
        'email_verified' => $result['email_verified'],
        'transaction_status' => $result['transaction_status']
    ]);
    
} catch (PDOException $e) {
    // Handle any potential database errors
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}