<?php
// Include the database connection file
require_once '../../model/db.php';

// Retrieve and validate POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Username is required']);
    exit;
}

$username = htmlspecialchars($data['username']);

try {
    // Query trades for the specific user
    $sql = "SELECT symbol, position_id, order_type, duration, result, profit_or_loss_amount AS amount
            FROM trades
            WHERE username = :username
            ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);

    $trades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($trades) {
        echo json_encode(['status' => 'success', 'data' => $trades]);
    } else {
        echo json_encode(['status' => 'success', 'data' => []]); // No trades found
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch trade history.']);
}
?>
