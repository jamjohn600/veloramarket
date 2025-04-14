<?php
session_start();
require_once 'db.php'; // Database connection
require_once 'my_fns.php'; // Utility functions

header('Content-Type: application/json');

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please log in again.']);
    exit();
}

$username = $_SESSION['username'];
$userDetails = fetch_user_details($username);

if (!$userDetails) {
    echo json_encode(['status' => 'error', 'message' => 'User details not found.']);
    exit();
}

$user_id = $userDetails['user_id'];
$wallet_balance = $userDetails['wallet_balance'] ?? 0;

// Validate withdrawal amount
$withdrawAmount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
if (!$withdrawAmount || $withdrawAmount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid withdrawal amount.']);
    exit();
}

if ($withdrawAmount > $wallet_balance) {
    echo json_encode(['status' => 'error', 'message' => 'Insufficient wallet balance.']);
    exit();
}

// Get recipient code
$recipient_code = get_user_recipient_code($user_id);
if (!$recipient_code) {
    echo json_encode(['status' => 'error', 'message' => 'Recipient code not found. Please set up your bank account.']);
    exit();
}

// Prepare transfer data
$amount = $withdrawAmount * 100; // Convert to kobo
$reason = "Withdrawal for $username";
$url = "https://api.paystack.co/transfer";

$fields = [
    "source" => "balance",
    "reason" => $reason,
    "amount" => $amount,
    "recipient" => $recipient_code
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer SECRET_KEY", // Replace with your Paystack secret key
    "Cache-Control: no-cache"
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$response_data = json_decode($response, true);
curl_close($ch);

// Handle Paystack response
if ($response_data['status'] === true) {
    echo json_encode(['status' => 'success', 'message' => 'Withdrawal initiated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => $response_data['message'] ?? 'Failed to process withdrawal.']);
}
?>