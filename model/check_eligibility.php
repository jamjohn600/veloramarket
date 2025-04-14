<?php
session_start(); // Ensure the session is started
require_once 'db.php'; // Database connection

if (!isset($_SESSION['username'])) {
    echo json_encode(['eligible' => false, 'message' => 'Session expired. Please log in again.']);
    exit();
}

$username = $_SESSION['username'];

try {
    $stmt = $pdo->prepare("SELECT email_verified, transaction_status FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['email_verified'] == 1 && $user['transaction_status'] == 'ready') {
            echo json_encode(['eligible' => true]);
        } else {
            $message = $user['email_verified'] == 0 ? 'Your email is not verified.' : 'Your transaction status is not ready.';
            echo json_encode(['eligible' => false, 'message' => $message]);
        }
    } else {
        echo json_encode(['eligible' => false, 'message' => 'User not found.']);
    }
} catch (PDOException $e) {
    error_log("Database error in check_eligibility.php: " . $e->getMessage());
    echo json_encode(['eligible' => false, 'message' => 'An error occurred. Please try again later.']);
}