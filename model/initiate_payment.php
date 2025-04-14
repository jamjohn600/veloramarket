<?php
header('Content-Type: application/json'); // Add this at the top
session_start();
require_once 'db.php'; 
require_once 'my_fns.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
    exit();
}

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Session expired. Please log in again.'
    ]);
    exit();
}

// Retrieve user information from session
$username = $_SESSION['username'];
$userDetails = fetch_user_details($username); // Assuming fetch_user_details returns user data
$email = $userDetails['email'] ?? null;
$user_id = $userDetails['user_id'] ?? null;

// Ensure email is available
if (!$email) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User email not found.'
    ]);
    exit();
}

// Validate the deposit amount from the AJAX request
$depositAmount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

if (!$depositAmount || $depositAmount <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid deposit amount.'
    ]);
    exit();
}

// Generate a unique transaction reference
$tx_ref = "TX-" . uniqid() . "-" . $username;

// Call the Flutterwave API to initiate the payment
$response = generatePaystackUrl($email,$depositAmount,$tx_ref);

// Handle the API response
if ($response['status'] == 1) {
    try {

        // Send the payment link back to the frontend
        echo json_encode([
            'status' => 'success',
            'payment_url' => $response['data']['authorization_url'] // Correct the field to 'link'
        ]);
    } catch (Exception $e) {
        // Handle database errors and log
      //  error_log("Database Error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to log the transaction. Please try again.'
        ]);
    }
} else {
    // Handle Flutterwave API errors and log
    error_log("Flutterwave API Error: " . json_encode($response));

    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to initiate payment. Please try again later.'
    ]);
}