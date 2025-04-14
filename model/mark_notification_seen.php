<?php
// mark_notification_seen.php

require_once 'db.php';
require_once 'my_fns.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $notification_id = $_GET['id'] ?? null; // Assuming notification ID is passed via GET

    if (!$notification_id) {
        echo json_encode(['error' => 'Notification ID is required']);
        exit;
    }

    try {
        $result = mark_notification_seen($notification_id);

        if ($result > 0) {
            // Fetch updated notifications count
            $username = $_SESSION['username']; // Ensure session is started and username is set
            $unseen_count = get_unseen_notifications_count($username);

            echo json_encode([
                'success' => 'Notification marked as seen',
                'unseen_count' => $unseen_count
            ]);
        } else {
            echo json_encode(['error' => 'Failed to mark notification as seen']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Function to get unseen notifications count
function get_unseen_notifications_count($username) {
    global $pdo;

    $sql = "SELECT COUNT(*) FROM notifications WHERE username = :username AND seen = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchColumn();
}
?>