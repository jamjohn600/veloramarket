<?php
require_once 'db.php'; // Include DB connection
require_once 'my_fns.php'; // Include reusable functions

// Retrieve the transaction reference from the URL or session
$txRef = $_GET['tx_ref'] ?? null;

if (!$txRef) {
    echo json_encode(['status' => 'error', 'message' => 'Transaction reference missing.']);
    exit();
}

// Call Flutterwave to get transaction details (API call)
$flutterwaveSecretKey = getenv('FLUTTERWAVE_SECRET_KEY');
$verificationUrl = getenv('FLUTTERWAVE_BASE_URL') . "/v3/transactions/{$txRef}/verify";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $verificationUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Authorization: Bearer $flutterwaveSecretKey"],
]);

$response = curl_exec($curl);
curl_close($curl);

if (!$response) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to verify payment.']);
    exit();
}

// Decode the API response
$responseData = json_decode($response, true);

if ($responseData['status'] === 'success' && $responseData['data']['status'] === 'successful') {
    $amount = $responseData['data']['amount'];
    $txRef = $responseData['data']['tx_ref'];
    $flutterwaveReference = $responseData['data']['flw_ref'];
    $flutterwaveFee = $responseData['data']['app_fee'];
    
    // Check if the deposit is already marked as successful
    $stmt = $pdo->prepare("SELECT status FROM deposits WHERE transaction_id = :tx_ref");
    $stmt->execute(['tx_ref' => $txRef]);
    $depositStatus = $stmt->fetchColumn();

    if ($depositStatus !== 'successful') {
        // Update the deposit status to 'successful'
        $updateStmt = $pdo->prepare("
            UPDATE deposits SET
                flutterwave_reference = :flutterwave_reference,
                status = 'successful',
                flutterwave_fee = :flutterwave_fee,
                date_completed = NOW(),
                updated_at = NOW()
            WHERE transaction_id = :tx_ref
        ");
        $updateStmt->execute([
            'flutterwave_reference' => $flutterwaveReference,
            'flutterwave_fee' => $flutterwaveFee,
            'tx_ref' => $txRef
        ]);

        // Update the wallet balance
        $walletStmt = $pdo->prepare("
            UPDATE wallets SET
                balance = balance + :amount,
                total_deposited = total_deposited + :amount,
                last_deposit = :amount,
                last_deposit_date = NOW()
            WHERE username = (SELECT username FROM deposits WHERE transaction_id = :tx_ref)
        ");
        $walletStmt->execute([
            'amount' => $amount,
            'tx_ref' => $txRef
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Payment verified and wallet updated.']);
    } else {
        echo json_encode(['status' => 'info', 'message' => 'Payment already processed.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to verify payment.']);
}