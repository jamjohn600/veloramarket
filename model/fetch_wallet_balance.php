session_start();
require_once 'db.php'; // Loads your .env and DB connection
require_once 'my_fns.php'; // Include reusable functions

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
$userDetails = fetch_wallet_balance($username); 
var_dump($userDetails);

// Ensure email is available
if (!$email) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User email not found.'
    ]);
    exit();
}

        <!--echo json_encode([-->
        <!--    'status' => 'success',-->
        <!--    'balance' => $response['data']['authorization_url'] // Correct the field to 'link'-->
        <!--]);-->
    
}