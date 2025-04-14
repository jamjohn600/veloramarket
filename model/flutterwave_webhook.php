<?php
require_once 'db.php'; // Include DB connection
require_once 'my_fns.php'; // Include reusable functions

// Retrieve Flutterwave secret key from $_ENV
$flutterwaveSecretKey = $_ENV['FLUTTERWAVE_SECRET_HASH'] ?? null;

if (!$flutterwaveSecretKey) {
    error_log("FLUTTERWAVE_SECRET_KEY is missing.");
    http_response_code(500);
    exit();
}

// Retrieve the request signature and body
$signature = $_SERVER['HTTP_VERIF_HASH'] ?? '';
$requestBody = file_get_contents('php://input');

// Log the request body and signature for debugging purposes
logWebhookEvent("Raw body: " . $requestBody);  // Assuming you have a function to log webhooks
logWebhookEvent("Received signature: " . $signature);

// Compute the expected signature
$expectedSignature = hash('sha256', $requestBody . $flutterwaveSecretKey);

// Log the expected signature for debugging
logWebhookEvent("Expected signature: " . $expectedSignature);

// Validate the webhook signature
if (!$signature || $signature !== $expectedSignature) {
    error_log("Invalid webhook signature. Expected: $expectedSignature, Received: $signature.");
    http_response_code(403); // Forbidden
    exit();
}

// Decode the JSON request body
$data = json_decode($requestBody, true);

if (!$data) {
    error_log("Invalid JSON payload.");
    http_response_code(400); // Bad Request
    exit();
}

// Log and process the event type
$eventType = $data['event'] ?? '';
error_log("Received Flutterwave event: $eventType");

if ($eventType === 'charge.completed') {
    // Extract data
    $txRef = $data['data']['tx_ref'] ?? '';
    $flutterwaveReference = $data['data']['flw_ref'] ?? '';
    $amount = $data['data']['amount'] ?? 0;
    $currency = $data['data']['currency'] ?? '';
    $status = $data['data']['status'] ?? '';
    $method = $data['data']['payment_type'] ?? '';
    $chargedAmount = $data['data']['charged_amount'] ?? 0;
    $flutterwaveFee = $data['data']['app_fee'] ?? 0;
    $customerEmail = $data['data']['customer']['email'] ?? '';

    if ($status === 'successful') {
        // Check if the transaction reference exists
        $stmt = $pdo->prepare("SELECT * FROM deposits WHERE transaction_id = :tx_ref AND status = 'initiated'");
        $stmt->execute(['tx_ref' => $txRef]);
        $deposit = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($deposit) {
            // Update the deposit table to mark as successful
            $stmt = $pdo->prepare("
                UPDATE deposits SET
                    flutterwave_reference = :flutterwave_reference,
                    status = 'successful',
                    method = :method,
                    date_completed = NOW(),
                    flutterwave_fee = :flutterwave_fee
                WHERE transaction_id = :tx_ref
            ");
            $stmt->execute([
                'flutterwave_reference' => $flutterwaveReference,
                'method' => $method,
                'flutterwave_fee' => $flutterwaveFee,
                'tx_ref' => $txRef
            ]);

            // Update the wallet with the deposited amount
            $stmt = $pdo->prepare("
                UPDATE wallets SET
                    balance = balance + :amount,
                    total_deposited = total_deposited + :amount,
                    last_deposit = :amount,
                    last_deposit_date = NOW()
                WHERE username = (SELECT username FROM deposits WHERE transaction_id = :tx_ref)
            ");
            $stmt->execute(['amount' => $amount, 'tx_ref' => $txRef]);

            error_log("Deposit updated successfully for transaction reference: $txRef.");
        } else {
            error_log("Transaction reference not found or already processed: $txRef.");
        }
    } else {
        error_log("Payment status was not successful: $status.");
    }
} else {
    error_log("Unhandled event type: $eventType.");
}

http_response_code(200); // Acknowledge receipt of the webhook