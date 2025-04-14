<?php
// Set session cookie parameters for security
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true, // Only if using HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// // Regenerate session ID to prevent session fixation
// session_regenerate_id();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../../signin.php');
    exit(); // Stop further execution if the user is not logged in
}

require_once('../../model/my_fns.php');

// Fetch user details using the session username
$username = $_SESSION['username'];
$userDetails = fetch_user_details($username);

if (!$userDetails) {
    die("User details not found.");
}

// Fetch required user data
$userEmail = $userDetails['email'];
$avatar = $userDetails['avatar'] ?? null; // Assuming $userDetails['avatar'] may be null

// Check if the user has uploaded an avatar or use a default image
if (!empty($avatar)) {
    $avatarPath = "../../uploads/" . $avatar;
} else {
    $avatarPath = "../assets/img/avatar.svg";
}

// Fetch wallet details
$walletDetails = fetch_wallet_details($username);
$walletBalance = fetch_wallet_balance($username);

// Fetch transactions and trade deposits
$transaction = fetch_transactions($username);
$trading = fetch_tradeDeposits($username);

// Fetch verification status using the get_verification_status function
$verificationStatus = get_verification_status($userEmail);
$emailVerified = $verificationStatus['email_verified'] ?? 0; // Default to 0 if null
$phoneVerified = $verificationStatus['phone_verified'] ?? 0; // Default to 0 if null

// Check for the required parameter
if (!isset($_GET['pair'])) {
    // Redirect to client page if the 'pair' parameter is missing
    header('Location: ../../client');
    exit();
}

// Fetch the pair parameter and store it in a variable
$tradingPair = htmlspecialchars($_GET['pair'], ENT_QUOTES, 'UTF-8'); // Sanitize the input
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Nexus Dashboard</title>
  <link rel="shortcut icon" href="../../images/logo.jfif" type="image/x-icon">
  <link rel="stylesheet" href="../assets/css/style.css">
  <!-- Bootstrap CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<style>
    .alert-box {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1050;
        min-width: 300px;
    }
    .modal-content {
        background: linear-gradient(135deg, #1f4037, #99f2c8);
        color: white;
        border-radius: 10px;
    }
    .profit-display {
        font-size: 1.5rem;
        font-weight: bold;
        color: #ffcc00;
        animation: glow 1s infinite alternate;
    }
    @keyframes glow {
        from { text-shadow: 0 0 10px #ffcc00; }
        to { text-shadow: 0 0 20px #ffcc00; }
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .custom-lots-input::-webkit-inner-spin-button,
    .custom-lots-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .custom-lots-input {
        -moz-appearance: textfield; /* Firefox */
    }
</style>
</head>

<body id="dark">
    <div id="loader-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; justify-content: center; align-items: center;">
        <div id="loader" style="width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
    </div>
    
  <header class="dark-bb">
    <?php include '../navbar.php'; ?>
  </header>
  
  <div class="container-fluid mtb15 no-fluid">
        <div class="row sm-gutters">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- Back to Home Button -->
                    <a href="../../client" class="btn btn-primary rounded-pill shadow-sm px-4 py-2">
                        <i class="fas fa-home mr-2"></i>Back to Home
                    </a>
                    
                    <!-- Refactored Balance Area -->
                    <a href="../wallet" class="btn btn-light rounded-pill shadow-sm px-4 py-2">
                        <i class="fas fa-wallet mr-2"></i>
                        ₦<?php echo number_format($walletBalance['balance'],2); ?>
                    </a>
                </div>
            </div>
            
            <div class="container-fluid py-3">
                <h1 class="lead fw-bold text-white">Place a Trade on <?php echo $tradingPair ?></h1>
            </div>
            
            <!-- Chart Container -->
            <div class="col-md-9">
                <!-- Light Theme Chart -->
                <div class="main-chart mb15 light-variant">
                    <div class="tradingview-widget-container">
                        <div id="tradingview_chart_light"></div>
                        <script src="https://s3.tradingview.com/tv.js"></script>
                        <script>
                            const pair = "<?php echo $tradingPair ?>";
                            new TradingView.widget({
                                "width": "100%",
                                "height": 400,
                                "symbol": "OANDA:" + pair,
                                "interval": "5",
                                "timezone": "Etc/UTC",
                                "theme": "Light",
                                "style": "1",
                                "locale": "en",
                                "toolbar_bg": "#f1f3f6",
                                "container_id": "tradingview_chart_light",
                                "studies": [] // Disables all default studies, including volume
                            });
                        </script>
                    </div>
                </div>
            
                <!-- Dark Theme Chart -->
                <div class="main-chart mb15 dark-variant">
                    <div class="tradingview-widget-container">
                        <div id="tradingview_chart_dark"></div>
                        <script>
                            new TradingView.widget({
                                "width": "100%",
                                "height": 400,
                                "symbol": "OANDA:" + pair,
                                "interval": "5",
                                "timezone": "Etc/UTC",
                                "theme": "dark",
                                "style": "1",
                                "locale": "en",
                                "toolbar_bg": "#f1f3f6",
                                "container_id": "tradingview_chart_dark",
                                "studies": [] // Disables all default studies, including volume
                            });
                        </script>
                    </div>
                </div>
            </div>
    
            <!-- Market-Trade Section -->
            <div class="col-md-3">
                <div class="market-trade">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#pills-trade-limit" role="tab">Place Trade</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="pills-trade-limit" role="tabpanel">
                            <div class="d-flex justify-content-between">
                                <div class="market-trade-buy">
                                    <form id="place-trade" class="needs-validation" novalidate>
                                        <input type="hidden" id="username" name="username" value="<?php echo $username; ?>">
                                        <input type="hidden" id="symbol" name="symbol" value="<?php echo $tradingPair; ?>">
                                        <input type="hidden" id="balance" name="balance" value="<?php echo number_format($walletBalance['balance'], 2); ?>">
                                    
                                        <!-- Max Stake Input -->
                                        <div class="form-group mb-3">
                                            <label for="max-stake" class="form-label text-white d-flex align-items-center">
                                                Max Stake
                                                <i class="fas fa-info-circle ml-2 info-icon" data-toggle="tooltip" title="Set the maximum amount you want to risk on the trade. Minimum is 5000 and maximum is 70% of your balance."></i>
                                            </label>
                                            <input type="number" id="max-stake" name="max-stake" class="form-control" value="5000" min="5000" placeholder="Enter max stake" required>
                                        </div>
                                    
                                        <!-- Lots Input -->
                                        <div class="form-group mb-3">
                                            <label for="lots" class="form-label text-white d-flex align-items-center">
                                                Lots
                                                <i class="fas fa-info-circle ml-2 info-icon" data-toggle="tooltip" title="Lots must be between 0.1 and 20.0, in increments of 0.1."></i>
                                            </label>
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary" id="decrement-lots">-</button>
                                                <input type="number" id="lots" name="lots" class="form-control custom-lots-input" value="0.1" step="0.1" min="0.1" max="20.0" placeholder="0.1" required>
                                                <button type="button" class="btn btn-outline-secondary" id="increment-lots">+</button>
                                            </div>
                                        </div>
                                    
                                        <!-- Duration Input -->
                                        <div class="form-group mb-3">
                                            <label for="duration" class="form-label text-white d-flex align-items-center">
                                                Duration
                                                <i class="fas fa-info-circle ml-2 info-icon" data-toggle="tooltip" title="Duration can range from 5 seconds to 24 hours."></i>
                                            </label>
                                            <div class="d-flex align-items-center">
                                                <input type="number" id="duration-value" name="duration-value" class="form-control" value="1" min="1" placeholder="Enter duration" required>
                                                <select id="duration-unit" name="duration-unit" class="form-control w-auto ml-2">
                                                    <!--<option value="seconds">Seconds</option>-->
                                                    <option value="minutes">Minutes</option>
                                                    <option value="hours">Hours</option>
                                                </select>
                                            </div>
                                        </div>
                                    
                                        <!-- Order Type Input -->
                                        <div class="form-group mb-3">
                                            <label for="order-type" class="form-label text-white d-flex align-items-center">
                                                Order Type
                                                <i class="fas fa-info-circle ml-2 info-icon" data-toggle="tooltip" title="Choose Up or Down as the order type."></i>
                                            </label>
                                            <select id="order-type" name="order-type" class="form-control" required>
                                                <option value="" disabled selected>Select Order Type</option>
                                                <option value="buy">Up</option>
                                                <option value="sell">Down</option>
                                            </select>
                                        </div>
                                    
                                        <!-- Confirm Button -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success w-100" id="confirm-trade" disabled>Confirm</button>
                                            <small id="message" class="form-text text-center text-success mt-2 d-none">Looks good, let's go...</small>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alert Box -->
    <div class="alert-box"></div>

    <!-- Modal -->
    <div class="modal fade" id="tradeModal" tabindex="-1" aria-labelledby="tradeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tradeModalLabel">Trade Details</h5>
                </div>
                <div class="modal-body">
                    <p><strong>Position ID:</strong> <span id="positionId"></span></p>
                    <p><strong>Duration:</strong> <span id="remainingTime"></span></p>
                    <p class="profit-display">Profit: $<span id="currentProfit">0.00</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="closeTrade" class="btn btn-secondary">Close Trade</button>
                </div>
            </div>
        </div>
    </div>
        
    <script src="../assets/js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap JS and Icons -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/js/amcharts-core.min.js"></script>
  <script src="../assets/js/amcharts.min.js"></script>
  <script src="../assets/js/custom.js"></script>
  <script src="submitTrade.js?v=<?php echo time(); ?>"></script>
  <script src="https://unpkg.com/metaapi.cloud-sdk"></script>
  
  <script>
      document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('place-trade');
        const confirmButton = document.getElementById('confirm-trade');
        const messageElement = document.getElementById('message');
    
        const maxStakeInput = document.getElementById('max-stake');
        const lotsInput = document.getElementById('lots');
        const durationValueInput = document.getElementById('duration-value');
        const durationUnitInput = document.getElementById('duration-unit');
        const orderTypeInput = document.getElementById('order-type');
        const balanceInput = document.getElementById('balance');
    
        const formattedBalance = balanceInput.value.replace(/,/g, ''); // Remove commas
        const balance = parseFloat(formattedBalance);
    
        // Set the message and display it
        function setMessage(msg, isSuccess = false) {
            messageElement.classList.remove('text-danger', 'text-success', 'd-none');
            messageElement.classList.add(isSuccess ? 'text-success' : 'text-danger');
            messageElement.textContent = msg;
        }
    
        // Enable or disable the confirm button
        function toggleConfirmButton(enable = false) {
            confirmButton.disabled = !enable;
        }
    
        // Validate the inputs
        function validateInputs() {
            const maxStake = parseFloat(maxStakeInput.value);
            const lots = parseFloat(lotsInput.value);
            const durationValue = parseInt(durationValueInput.value, 10);
            const durationUnit = durationUnitInput.value;
            const orderType = orderTypeInput.value;
    
            // Validation: Max Stake and Lots Proportion
            const requiredStake = lots * 50000; // 5000 per 0.1 lots
            if (isNaN(maxStake) || maxStake < 5000 || maxStake > balance * 0.7) {
                setMessage(`Max Stake must be between 5000 and ${Math.floor(balance * 0.7)}.`);
                toggleConfirmButton(false);
                return;
            }
    
            if (maxStake < requiredStake) {
                setMessage(`For ${lots.toFixed(1)} lots, the minimum stake should be ${requiredStake}.`);
                toggleConfirmButton(false);
                return;
            }
    
            // Validation: Lots
            if (!lots || lots < 0.1 || lots > 20.0 || Math.round(lots * 10) / 10 !== lots) {
                setMessage('Lots must be a valid number between 0.1 and 20.0, in increments of 0.1.');
                toggleConfirmButton(false);
                return;
            }
    
            // Validation: Duration
            if (
                !durationValue ||
                // (durationUnit === 'seconds' && (durationValue < 5 || durationValue > 59)) ||
                (durationUnit === 'minutes' && (durationValue < 1 || durationValue > 59)) ||
                (durationUnit === 'hours' && (durationValue < 1 || durationValue > 24))
            ) {
                setMessage('Duration must be within valid ranges: minutes (1-59), or hours (1-24).');
                toggleConfirmButton(false);
                return;
            }
    
            // Validation: Order Type
            if (!orderType) {
                setMessage('Please select an order type.');
                toggleConfirmButton(false);
                return;
            }
    
            // Success: All validations passed
            setMessage(`Looks good! Ready to place a ${lots.toFixed(1)} lots trade with a stake of ${maxStake}.`, true);
            confirmButton.textContent = `Confirm ${orderType} ${lots.toFixed(1)} lots`;
            toggleConfirmButton(true);
        }
    
        // Increment and decrement buttons for lots
        document.getElementById('increment-lots').addEventListener('click', function () {
            let value = parseFloat(lotsInput.value) || 0;
            value = Math.min(20.0, value + 0.1);
            lotsInput.value = value.toFixed(1);
            validateInputs();
        });
    
        document.getElementById('decrement-lots').addEventListener('click', function () {
            let value = parseFloat(lotsInput.value) || 0;
            value = Math.max(0.1, value - 0.1);
            lotsInput.value = value.toFixed(1);
            validateInputs();
        });
    
        // Input change listeners for validation
        [maxStakeInput, lotsInput, durationValueInput, durationUnitInput, orderTypeInput].forEach((input) => {
            input.addEventListener('input', validateInputs);
        });
    
        // Tooltip Initialization
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl, { html: true, placement: 'top' });
        });
    });
    </script>

</body>

</html>