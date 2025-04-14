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

// Validate the deposit amount from the AJAX request
$depositAmount = $data['amount'];

$intendedAddress = 'bc1qynjvjxzxvazkwmu4g40r67sz689ntkv6nzzqqf';
//$intendedAmount = 0; //
echo validateRecipient($depositAmount, $intendedAddress,$username);


// if (!$depositAmount || $depositAmount <= 0) {
//     echo json_encode([
//         'status' => 'error',
//         'message' => 'Invalid deposit amount.'
//     ]);
//     exit();
// }

// Generate a unique transaction reference
$tx_ref = "TX-" . uniqid() . "-" . $username;

function validateRecipient($txid, $intendedAddress,$username) {
    $api_url = "https://api.blockchair.com/bitcoin/dashboards/transaction/$txid";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $transactionData = json_decode($response, true);

    if (!isset($transactionData['data'][$txid]['transaction']['block_id']) || $transactionData['data'][$txid]['transaction']['block_id'] == null) {
        
        return json_encode([
            "status" => "error",
            "message" => "Transaction is unconfirmed or invalid."
        ]);
    }
    $outputs = $transactionData['data'][$txid]['outputs'];

    foreach ($outputs as $output) {
        if ($output['recipient'] === $intendedAddress) {
            $main_amount = $output['value_usd'] * exchange();
            updatePaymentStatus($main_amount,$username,$main_amount,$main_amount);
            return json_encode([
                "status" => "success",
                "message" => "Wallet Funded Successfully"
            ]);
        }
    }

    return json_encode([
        "status" => "error",
        "message" => "Wallet funding failed"
    ]);
}


function exchange(){
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.currencyfreaks.com/v2.0/rates/latest?apikey=089c9b14ab80444baeb9a2acc8e0eef6',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
$data = json_decode($response,true);
return $data['rates']['NGN'];
}

