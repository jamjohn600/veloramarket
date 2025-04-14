<?php
require_once '../../model/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['symbol'], $data['positionId'], $data['orderType'], $data['duration'], $data['result'], $data['profitOrLossAmount'], $data['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data received']);
    exit;
}

$symbol = htmlspecialchars($data['symbol']);
$positionId = htmlspecialchars($data['positionId']);
$orderType = htmlspecialchars($data['orderType']);
$duration = htmlspecialchars($data['duration']);
$result = htmlspecialchars($data['result']);
$profitOrLossAmount = abs((float)$data['profitOrLossAmount']); // Ensure it's positive
$username = htmlspecialchars($data['username']);

try {
    $pdo->beginTransaction();

    // Log the trade
    $logTradeSql = "INSERT INTO trades (symbol, position_id, order_type, duration, result, profit_or_loss_amount, username)
                    VALUES (:symbol, :positionId, :orderType, :duration, :result, :profitOrLossAmount, :username)";
    $stmt = $pdo->prepare($logTradeSql);
    $stmt->execute([
        ':symbol' => $symbol,
        ':positionId' => $positionId,
        ':orderType' => $orderType,
        ':duration' => $duration,
        ':result' => $result,
        ':profitOrLossAmount' => $profitOrLossAmount,
        ':username' => $username,
    ]);

    // Update the wallet balance
    $updateWalletSql = "
        UPDATE wallets
        SET 
            balance = balance + CASE 
                WHEN :result = 'Profit' THEN :profitOrLossAmount
                WHEN :result = 'Loss' THEN -:profitOrLossAmount
                ELSE 0
            END
        WHERE username = :username";
    $stmt = $pdo->prepare($updateWalletSql);
    $stmt->execute([
        ':result' => $result,
        ':profitOrLossAmount' => $profitOrLossAmount,
        ':username' => $username,
    ]);

    // Check affected rows for the wallet update
    if ($stmt->rowCount() === 0) {
        throw new Exception('No wallet update action taken.');
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Trade closed and wallet updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Transaction error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}