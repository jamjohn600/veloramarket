<?php
// fetch_notifications.php

require_once 'db.php';
require_once 'my_fns.php';

// Set session cookie parameters for security
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true, // Only if using HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// Regenerate session ID to prevent session fixation
session_regenerate_id();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../../signin.php');
    exit(); // Stop further execution if the user is not logged in
}

// Fetch user details using the session username
$username = $_SESSION['username'];

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $notifications = fetch_notifications($username);
        echo json_encode(['notifications' => $notifications]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>