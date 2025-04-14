<?php
session_start();
require 'db.php'; // Load the database connection

$userId = $_SESSION['user_id']; // Assume user is logged in and user ID is stored in session

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$currentPassword = $_POST['currentPass'];
$newPassword = $_POST['newPass'];
$securityQuestion = $_POST['securityOne'];
$securityAnswer = $_POST['securityAnswer'];

// Check if current password is provided
if (empty($currentPassword)) {
    echo json_encode(['success' => false, 'error' => 'Current password is required.']);
    exit;
}

// Verify current password
if (!password_verify($currentPassword, $user['password'])) {
    echo json_encode(['success' => false, 'error' => 'Incorrect current password.']);
    exit;
}

// Check if new password is provided
if (empty($newPassword)) {
    echo json_encode(['success' => false, 'error' => 'New password is required.']);
    exit;
}

// Check if a security question and answer are provided if updating them
if (empty($securityQuestion) || empty($securityAnswer)) {
    echo json_encode(['success' => false, 'error' => 'Security question and answer are required.']);
    exit;
}

// If a security question already exists, validate the answer
if (!empty($user['security_question']) && !empty($user['security_answer'])) {
    if (strtolower($securityAnswer) !== strtolower($user['security_answer'])) {
        echo json_encode(['success' => false, 'error' => 'Incorrect security answer.']);
        exit;
    }
}

// Update new password and security question if provided
$hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
$updateQuery = "UPDATE users SET password = ?, updated_at = NOW()";

// If security question and answer are provided, include them in the update
if (!empty($securityQuestion) && !empty($securityAnswer)) {
    $updateQuery .= ", security_question = ?, security_answer = ?";
    $stmt = $pdo->prepare($updateQuery . " WHERE id = ?");
    $stmt->execute([$hashedNewPassword, $securityQuestion, strtolower($securityAnswer), $userId]);
} else {
    // Only update the password
    $stmt = $pdo->prepare($updateQuery . " WHERE id = ?");
    $stmt->execute([$hashedNewPassword, $userId]);
}

echo json_encode(['success' => true]);