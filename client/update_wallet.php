<?php
header('Content-Type: application/json');
session_start();

//require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$profit = floatval($data['profit']);
$userId = $_SESSION['user_id'];
echo $profit;

// $sql = "UPDATE wallets SET balance = balance + ? WHERE user_id = ?";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param('di', $profit, $userId);

// if ($stmt->execute()) {
//     $newBalance = $conn->query("SELECT balance FROM wallets WHERE user_id = $userId")->fetch_assoc()['balance'];
//     echo json_encode(["status" => "success", "message" => "Wallet updated", "new_balance" => $newBalance]);
// } else {
//     echo json_encode(["status" => "error", "message" => "Failed to update wallet"]);
// }

// $stmt->close();
// $conn->close();
?>
