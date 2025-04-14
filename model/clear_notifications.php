<?php
require_once 'db.php';
require_once 'my_fns.php';

header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$username = $_SESSION['username'];

try {
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE username = :username");
    $stmt->execute(['username' => $username]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>