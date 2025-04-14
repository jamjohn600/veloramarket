<?php
session_start();
require_once 'db.php'; // Loads your .env and DB connection
require_once 'my_fns.php'; // Include reusable functions

// $txid = 'f854aebae95150b379cc1187d848d58225f3c4157fe992bcd166f58bd5063449';
// $intendedAddress = '1N2f642sbgCMbNtXFajz9XDACDFnFzdXzV';
// $intendedAmount = 70320221545; // Amount in satoshis
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

$data = json_decode(@file_get_contents("php://input"), true);

$depositAmount = $data['amount'];
//$to_user = $data['to_user'];


// Generate a unique transaction reference
$tx_ref = "TX-" . uniqid() . "-" . $username;

   
echo transferFunds($tx_ref,$username, $depositAmount);
            





