<?php
require 'db.php'; // Ensure this file sets up $pdo

session_start();

$userId = $_SESSION['user_id'];
$entered_code = $_POST['code'];

// Fetch verification code and expiration time
try {
    $query = $pdo->prepare("SELECT verification_code, code_expires_at FROM users WHERE id = :id");
    $query->execute(['id' => $userId]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }

    $current_time = new DateTime();
    $expires_at = new DateTime($user['code_expires_at']);

    if ($entered_code == $user['verification_code'] && $current_time < $expires_at) {
        // Mark email as verified
        $update_query = $pdo->prepare("UPDATE users SET email_verified = 1 WHERE id = :id");
        $update_query->execute(['id' => $userId]);
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired code']);
    }
} catch (PDOException $e) {
    error_log("Database query error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database query error: ' . $e->getMessage()]);
}