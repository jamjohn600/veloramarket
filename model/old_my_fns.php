<?php

require_once('db.php');

// Track login attempts
function trackLoginAttempts($email)
{
    global $pdo;

    $sql = "SELECT failed_attempts, last_attempt FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Check if the user is currently locked out
        if ($user['failed_attempts'] >= 5 && (time() - strtotime($user['last_attempt'])) < 900) {
            return true; // Block login
        }
    }
    return false; // Allow login
}

// Log failed login attempts
function logFailedLogin($email)
{
    global $pdo;
    $sql = "UPDATE users SET failed_attempts = failed_attempts + 1, last_attempt = NOW() WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
}

// Reset login attempts
function resetLoginAttempts($email)
{
    global $pdo;
    $sql = "UPDATE users SET failed_attempts = 0 WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
}

// Sanitize input
function sanite($data)
{
    $data = strip_tags($data);
    $data = htmlspecialchars($data);
    $data = trim($data);
    return $data;
}

// Manage session timeout
function sessionExpired()
{
    $inactiveDuration = 600; // 10 minutes
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactiveDuration) {
        session_unset();     // Unset all session variables
        session_destroy();   // Destroy the session
        return true;         // Session has expired
    }
    $_SESSION['last_activity'] = time(); // Update last activity time
    return false;
}

function fetch_user_details($username)
{
    global $pdo;

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    return $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch user details as an associative array
}

// Function to send email verification
function send_email_verification($email, $code)
{
    // Use a mailer API (e.g., SendGrid or Mailgun) to send the code
}

// Function to verify email
function verify_email($email, $input_code)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT email_verification_code FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['email_verification_code'] == $input_code) {
        // Update email verification status
        $update_stmt = $pdo->prepare("UPDATE users SET email_verified = 1 WHERE email = ?");
        $update_stmt->execute([$email]);
        return true;
    }
    return false;
}

// Function to send phone verification
function send_phone_verification($phone, $code)
{
    // Use an SMS API like Twilio to send the verification code
}

// Function to verify phone number
function verify_phone($phone, $input_code)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT phone_verification_code FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user && $user['phone_verification_code'] == $input_code) {
        // Update phone verification status
        $update_stmt = $pdo->prepare("UPDATE users SET phone_verified = 1 WHERE phone = ?");
        $update_stmt->execute([$phone]);
        return true;
    }
    return false;
}

function get_verification_status($email)
{
    global $pdo;

    // Get email and phone verification status from the database
    $stmt = $pdo->prepare("SELECT email_verified, phone_verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function is_profile_complete($user_id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT dob, address, gender, employment_status FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user['dob'] && $user['address'] && $user['gender'] && $user['employment_status']) {
        return true;
    }
    return false;
}

function initiateFlutterwavePayment($data)
{
    // Load Flutterwave credentials from the .env file
    $apiUrl = $_ENV['FLUTTERWAVE_BASE_URL'] . '/payments';
    $secretKey = $_ENV['FLUTTERWAVE_SECRET_KEY'];

    // Prepare the headers for the API request
    $headers = [
        'Authorization: Bearer ' . $secretKey,
        'Content-Type: application/json'
    ];

    // Initialize cURL session for the request
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // JSON-encode the data payload
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set the headers
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string

    // Execute the API call
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $curlError = curl_error($ch);
        error_log("cURL Error: $curlError"); // Log cURL error
        return null;
    }

    curl_close($ch); // Close the cURL session

    // Decode the response and return it
    return json_decode($response, true);
}

function generatePaystackUrl($email, $amount, $invoice)
{

    $unique = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 13)), 0, 13);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.paystack.co/transaction/initialize',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
"email": "' . $email . '",
"amount": "' . ceil($amount * 100) . '",
"reference": "' . $unique . '",
"channel": [
    "card",
    "bank",
    "ussd",
    "qr",
    "mobile_money",
    "bank_transfer",
    "eft"
],
"metadata":{
    "invoice_id": "' . $invoice . '",
     "value":"' . ceil($amount * 100) . '"
}
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer sk_live_bbe6ee0e98df8ef88660732ccedc9ad4eca91e4a',
            'Cookie: __cf_bm=BSkBMSjLQbf9enL2E8lMGDt0OWzfxvAjzKWjkBEDSgA-1699968935-0-Ae5PY+7AXO639oenVzzBDM7pwHoEAaqrRUU5azzQZ+5uZE0q0NHyKhi9GbHXlBllCzZJLNRdP2SiuLkRdjpE2jE=; sails.sid=s%3AQhlYMRx9mkFGqfgawIT5rjcAo9hMWlFc.N8lGhtGWxaDrbLeuIpX2Q7vKeO0HgSaa%2Fjyd0tqEFyE'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response, true);
}

function initiatePaystackTransfer($data) {
    $paystack_secret = getenv('PAYSTACK_SECRET'); // Load Paystack secret from environment variables

    $ch = curl_init('https://api.paystack.co/transfer');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer sk_test_904c67c53f8574831e2e694c4831a897966ea8a4',
        'Cookie: __cf_bm=BSkBMSjLQbf9enL2E8lMGDt0OWzfxvAjzKWjkBEDSgA-1699968935-0-Ae5PY+7AXO639oenVzzBDM7pwHoEAaqrRUU5azzQZ+5uZE0q0NHyKhi9GbHXlBllCzZJLNRdP2SiuLkRdjpE2jE=; sails.sid=s%3AQhlYMRx9mkFGqfgawIT5rjcAo9hMWlFc.N8lGhtGWxaDrbLeuIpX2Q7vKeO0HgSaa%2Fjyd0tqEFyE'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function get_user_recipient_code($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT recipient_code FROM user_bank_accounts WHERE user_id = :user_id LIMIT 1");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchColumn();
}



function validateSignature($payload, $signature, $secretKey)
{
    // Generate the expected signature using HMAC-SHA256
    $expectedSignature = hash_hmac('sha256', json_encode($payload), $secretKey);

    // Compare the expected signature with the one provided in the headers
    return hash_equals($expectedSignature, $signature);
}

function logWebhookEvent($payload)
{
    // Define the log file path
    $logFile = __DIR__ . '/webhook.log';

    // Prepare the log message
    $logMessage = "[" . date('Y-m-d H:i:s') . "] Webhook received: " . json_encode($payload, JSON_PRETTY_PRINT) . PHP_EOL;

    // Write to the log file
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

function fetch_wallet_details($username)
{
    global $pdo;

    $sql = "SELECT * FROM wallets WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    return $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch user details as an associative array
}

function fetch_wallet_balance($username){
    global $pdo;

    $sql = "SELECT SUM(balance) as balance FROM wallets WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}



function fetch_transactions($email)
{
    global $pdo;

    $sql = "SELECT * FROM transactions WHERE email = ? ORDER BY transaction_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function fetch_tradeDeposits($username)
{
    global $pdo;

    $sql = "SELECT  SUM(amount) as balance  FROM `trading_deposits` WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function transferFunds($transaction,$from, $amount){
    $balance = fetch_wallet_balance($from);
    $detailsFrom = fetch_wallet_details($from);
    //$detailsTo = fetch_user_details($to);
    // print_r($detailsTo);
    $status = 'N';
    if($balance['balance'] < $amount){
            return json_encode([
                "status" => "error",
                "message" => "Insufficient Funds"
            ]);
    }else{
        global $pdo;
      $sql = "INSERT INTO `trading_deposits` (`transaction`, `username`, `amount`, `profit`, `loss`, `cumulative`, `status`) 
        VALUES (:transaction, :username, :amount, '0', '0', '0', :status)";

$stmt = $pdo->prepare($sql);

// Bind parameters and execute the statement
$stmt->bindParam(':transaction', $transaction);
$stmt->bindParam(':username', $from);
$stmt->bindParam(':amount', $amount);
$stmt->bindParam(':status', $status);

$stmt->execute();
         // updatePaymentStatusA($transaction,$to,$amount,$amount,'CREDIT');
    updatePaymentStatusB($transaction,$from,'-'.$amount,'-'.$amount,'DEBIT');
    return json_encode([
                "status" => "success",
                "message" => "Transfer successful"
     ]);
     
    }
    
   
}



function updatePaymentStatusA($transaction,$username,$amount,$total_deposited,$transaction_type){
    
    global $pdo;
        $sql = "INSERT INTO `wallets` (`wallet_id`, `username`, `balance`, `total_deposited`,transaction_type) 
            VALUES (:transaction, :username, :amount, :total_deposited,:transaction_type)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters and execute the statement
    $stmt->bindParam(':transaction', $transaction);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':total_deposited', $total_deposited);
    $stmt->bindParam(':transaction_type', $transaction_type);

    // Execute the query and check for success
  $stmt->execute();
    
    
}



function updatePaymentStatusB($transaction,$username,$amount,$total_deposited,$transaction_type){
    
    global $pdo;
        $sql = "INSERT INTO `wallets` (`wallet_id`, `username`, `balance`, `total_deposited`,transaction_type) 
            VALUES (:transaction, :username, :amount, :total_deposited,:transaction_type)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters and execute the statement
    $stmt->bindParam(':transaction', $transaction);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':total_deposited', $total_deposited);
    $stmt->bindParam(':transaction_type', $transaction_type);

    // Execute the query and check for success
  $stmt->execute();
    
    
}



function updatePaymentStatus($transaction,$username,$amount,$total_deposited){
    
    global $pdo;
        $sql = "INSERT INTO `wallets` (`wallet_id`, `username`, `balance`, `total_deposited`) 
            VALUES (:transaction, :username, :amount, :total_deposited)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters and execute the statement
    $stmt->bindParam(':transaction', $transaction);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':total_deposited', $total_deposited);

    // Execute the query and check for success
  $stmt->execute();
    
    
}

// Fetch notifications for a specific user
function fetch_notifications($username) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE username = :username ORDER BY created_at DESC");
    $stmt->execute(['username' => $username]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function mark_notification_seen($notification_id) {
    global $pdo;

    $sql = "UPDATE notifications SET seen = 1 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $notification_id, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->rowCount(); // Returns the number of rows affected
}

// Fetch unseen notifications count for a specific user
function fetch_unseen_notifications_count($username) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT COUNT(*) AS unseen_count FROM notifications WHERE username = :username AND seen = 0");
    $stmt->execute(['username' => $username]);

    return $stmt->fetch(PDO::FETCH_ASSOC)['unseen_count'];
}

// Insert a new notification
function add_notification($username, $message) {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO notifications (username, message, seen, created_at) VALUES (:username, :message, 0, NOW())");
    $stmt->execute([
        'username' => $username,
        'message' => $message
    ]);

    return $pdo->lastInsertId();
}

// function validateRecipient($txid, $intendedAddress, $intendedAmount) {
//     $api_url = "https://api.blockcypher.com/v1/btc/test3/txs/$txid";
    
//     // Initialize CURL to fetch transaction data
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $api_url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     $response = curl_exec($ch);
//     curl_close($ch);

//     $transaction = json_decode($response, true);

//     // Check if the transaction is valid and confirmed
//     if (!isset($transaction['confirmations']) || $transaction['confirmations'] <= 0) {
//         return "Transaction is unconfirmed or invalid.";
//     }

//     // Iterate through the outputs to check if intended address and amount match
//     foreach ($transaction['outputs'] as $output) {
//         if (in_array($intendedAddress, $output['addresses']) && $output['value'] == $intendedAmount) {
//             return "Transaction $txid matches the intended recipient and amount.";
//         }
//     }

//     return "Transaction $txid does not match the intended recipient or amount.";
// }

// $txid = 'f854aebae95150b379cc1187d848d58225f3c4157fe992bcd166f58bd5063449';
// $intendedAddress = '1N2f642sbgCMbNtXFajz9XDACDFnFzdXzV';
// $intendedAmount = 70320221545; 
