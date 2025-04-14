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

// Regenerate session ID to prevent session fixation
session_regenerate_id();

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

$userEmail = $userDetails['email'];

// Assuming $userDetails['avatar'] contains the filename of the user's avatar from the database
$avatar = $userDetails['avatar'];

// Check if the user has uploaded an avatar or not
if (!empty($avatar)) {
    // If avatar exists, show the uploaded image
    $avatarPath = "../../uploads/" . $avatar;
} else {
    // If no avatar, show the default image
    $avatarPath = "../assets/img/avatar.svg";
}

$walletDetails = fetch_wallet_details($username);

$walletBalance = fetch_wallet_balance($username);

//var_dump($walletBalance);

?>


<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
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
  <script src="../assets/js/jquery-3.4.1.min.js"></script>
  <script src="../../model/ajax.js?v=<?php echo time(); ?>"></script>
</head>

<body id="dark">
    
  <header class="dark-bb">
    <?php include '../navbar.php'; ?>
  </header>
  
  <div class="settings mtb15">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 col-lg-3">
          <div class="nav flex-column nav-pills settings-nav" id="v-pills-tab" role="tablist"
            aria-orientation="vertical">
            <a class="nav-link" href="../profile" role="tab" aria-selected="false"><i class="icon ion-md-person"></i> Profile</a>
            <a class="nav-link active" href="../wallet" role="tab" aria-selected="true"><i class="icon ion-md-wallet"></i> Wallet</a>
            <a class="nav-link" href="../settings" role="tab" aria-selected="false"><i class="icon ion-md-settings"></i> Settings</a>
          </div>
        </div>
        <div class="col-md-12 col-lg-9">
          <div class="tab-content" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="settings-wallet" role="tabpanel" aria-labelledby="settings-wallet-tab">
              <div class="wallet">
                <div class="row">
                  <div class="col-md-12 col-lg-4">
                  <div class="nav flex-column nav-pills mb-3" role="tablist" aria-orientation="vertical">
                      <a class="nav-link d-flex justify-content-between align-items-center active" data-toggle="pill"
                        href="#mainWal" role="tab" aria-selected="true">
                        <div class="d-flex">
                          <img src="../assets/img/icon/18.png" alt="btc">
                          <div>
                            <h2>NGN</h2>
                            <p>Wallet</p>
                          </div>
                        </div>
                        <div>
                          <h3><?php echo number_format($walletBalance['balance'],2); ?></h3>
                          <p class="text-right"><?php echo $walletDetails['wallet_id']?></p>
                        </div>
                      </a>
                    </div>
                  <!--<div class="alert alert-warning d-flex justify-content-between align-items-center">-->
                  <!--  <span>Your Live Accounts</span>-->
                  <!--  <button type="button" class="btn text-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">-->
                  <!--  <i class="fa-solid fa-plus"></i>-->
                  <!--  </button>-->
                  <!--</div>-->
                  <!-- <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="pill"
                      href="#coinBTC" role="tab" aria-selected="true">
                      <div class="d-flex">
                        <img src="../assets/img/icon/18.png" alt="btc">
                        <div>
                          <h2>BTC</h2>
                          <p>Bitcoin</p>
                        </div>
                      </div>
                      <div>
                        <h3>4.5484254</h3>
                        <p class="text-right"><i class="icon ion-md-lock"></i> 0.0000000</p>
                      </div>
                    </a>
                    <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="pill"
                      href="#coinETH" role="tab" aria-selected="true">
                      <div class="d-flex">
                        <img src="../assets/img/icon/1.png" alt="btc">
                        <div>
                          <h2>ETH</h2>
                          <p>Ethereum</p>
                        </div>
                      </div>
                      <div>
                        <h3>13.454845</h3>
                        <p class="text-right"><i class="icon ion-md-lock"></i> 0.0000000</p>
                      </div>
                    </a>
                  </div> -->

                  <!--  <div class="alert alert-warning d-flex justify-content-between align-items-center mt-3">-->
                  <!--  <span>Your Demo Accounts</span>-->
                  <!--  <button type="button" class="btn text-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">-->
                  <!--  <i class="fa-solid fa-plus"></i>-->
                  <!--  </button>-->
                  <!--</div>-->
                  </div>
                  <div class="col-md-12 col-lg-8">
                    <div class="tab-content">
                      <div class="tab-pane fade show active" id="mainWal" role="tabpanel">
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">My Wallet - <?php echo $walletDetails['wallet_id']?></h5>
                            <ul>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-cash"></i>
                                  <h2>Balance</h2>
                                </div>
                                <div>
                                  <h3><?php echo number_format($walletBalance['balance'],2); ?></h3>
                                </div>
                              </li>
                            </ul>
                            <button class="btn green" id="depositBtn">Deposit</button>
                            <button class="btn btn-danger" id="withdrawBtn" data-bs-toggle="modal" data-bs-target="#withdrawModal">Withdraw</button>
                            <!--<button id="transferBtn" class="btn btn-primary">Transfer</button>-->
                          </div>
                        </div>
                        <!--<div class="card">-->
                        <!--  <div class="card-body">-->
                        <!--    <h5 class="card-title">Wallet Deposit Address</h5>-->
                        <!--    <div class="row wallet-address">-->
                        <!--      <div class="col-md-8">-->
                        <!--        <p>Deposits to this address are unlimited. Note that you may not be able to withdraw all-->
                        <!--          of your-->
                        <!--          funds at once if you deposit more than your daily withdrawal limit.</p>-->
                        <!--        <div class="input-group">-->
                        <!--          <input type="text" class="form-control" value="bc1qynjvjxzxvazkwmu4g40r67sz689ntkv6nzzqqf" readonly>-->
                        <!--          <div class="input-group-prepend">-->
                        <!--            <button class="btn btn-primary" id="copies">COPY</button>-->
                        <!--          </div>-->
                        <!--        </div>-->
                        <!--      </div>-->
                        <!--      <div class="col-md-4">-->
                        <!--        <img src="../assets/img/qr-code-light.svg" alt="qr-code">-->
                        <!--      </div>-->
                        <!--    </div>-->
                        <!--  </div>-->
                        <!--</div>-->
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Latest Transactions</h5>
                            <div class="wallet-history">
                             <table class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $transaction = fetch_transactions($userEmail); // Fetch the transactions using email
                                        if (!empty($transaction)) {
                                            foreach ($transaction as $index => $txn) {
                                                $statusIcon = ($txn['type'] == 'debit') 
                                                    ? 'icon ion-md-close-circle-outline red' 
                                                    : 'icon ion-md-checkmark-circle-outline green';
                                                $amount = $txn['amount'];
                                                $date = $txn['transaction_date'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($date)); ?></td>
                                                    <td><i class="<?php echo $statusIcon; ?>"></i></td>
                                                    <td><?php echo $amount; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="4">No transactions found</td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  
  <!-- Updated Deposit Modal -->
  <div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="depositModalLabel">Make a Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="depositForm">
                    <!-- Amount Input -->
                    <div class="mb-3">
                        <label for="depositAmount" class="form-label">Enter Deposit Amount (NGN)</label>
                        <input type="number" class="form-control" id="depositAmount" name="deposit_amount" placeholder="Min. 1,000 NGN" min="1000" required>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" id="username" name="username" value="<?php echo $userDetails['username']; ?>">
                    <input type="hidden" id="email" name="email" value="<?php echo $userDetails['email']; ?>">

                    <!-- Terms of Service -->
                    <div class="mb-3">
                        <p>By proceeding, you agree to our <a href="/terms" target="_blank">Terms of Service</a>.</p>
                    </div>

                    <!-- Proceed Button with NGN Icon -->
                    <button type="submit" class="btn btn-success w-100 mb-2">
                        <i class="fas fa-money-bill-wave"></i> Proceed to Deposit NGN
                    </button>
                    
                    <!-- Bitcoin Button with Bitcoin Icon -->
                    
                </form>
                <!--<button type="button" id="btc" class="btn btn-info w-100 mb-2">-->
                <!--        <i class="fab fa-bitcoin"></i> Deposit with Bitcoin-->
                <!--    </button>-->
                    
                    <!-- Ethereum Button with Ethereum Icon -->
                <!--    <button type="submit" class="btn btn-info w-100">-->
                <!--        <i class="fab fa-ethereum"></i> Deposit with Ethereum-->
                <!--    </button>-->
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="deposit_bitcoin" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="depositModalLabel">Deposit Using Bitcoin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="btc_form">
                    <!-- Amount Input -->
                    <div class="mb-3">
                        <label for="transaction" class="form-label">Enter Transaction HASH</label>
                        <input type="text" class="form-control" id="transaction" name="transaction" placeholder="0x300......." required>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" id="username" name="username" value="<?php echo $userDetails['username']; ?>">
                    <input type="hidden" id="email" name="email" value="<?php echo $userDetails['email']; ?>">

                    <!-- Terms of Service -->
                    <div class="mb-3">
                        <p>By proceeding, you agree to our <a href="/terms" target="_blank">Terms of Service</a>.</p>
                    </div>

                    <!-- Proceed Button with NGN Icon -->
                    
                    <!-- Bitcoin Button with Bitcoin Icon -->
                    <button type="button" id="check_btc" class="btn btn-info w-100 mb-2">
                        <i class="fab fa-bitcoin"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="deposit_trans" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="depositModalLabel"> Transfer to trading account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="btc_form">
                    <!-- Amount Input -->
                     <div class="mb-3">
                        <label for="transaction" class="form-label">Enter Transaction Amount</label>
                        <input type="number" class="form-control" id="transfer_amount" name="transfer_amount" placeholder="1000" required>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" id="username" name="username" value="<?php echo $userDetails['username']; ?>">
                    <input type="hidden" id="email" name="email" value="<?php echo $userDetails['email']; ?>">

                    <!-- Terms of Service -->
                    <div class="mb-3">
                        <p>By proceeding, you agree to our <a href="/terms" target="_blank">Terms of Service</a>.</p>
                    </div>

                    <!-- Proceed Button with NGN Icon -->
                    
                    <!-- Bitcoin Button with Bitcoin Icon -->
                    <button type="button" id="check_transfer" class="btn btn-info w-100 mb-2">
                        <i class="fab fa-bitcoin"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div id="withdrawModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="withdrawModalLabel">Withdraw Funds</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="withdrawForm">
          <!-- Amount Input -->
          <div class="mb-3">
            <label for="withdrawAmount" class="form-label">Enter Amount to Withdraw</label>
            <input type="number" class="form-control" id="withdrawAmount" name="withdrawAmount" min="1" step="0.01" required>
            <div class="text-danger mt-2" id="amountError" style="display: none;">Insufficient balance.</div>
          </div>

          <!-- Bank Name Dropdown -->
          <div class="mb-3">
            <label for="bankName" class="form-label">Select Bank</label>
            <select class="form-control" id="bankName" name="bankName" required>
              <option value="" selected disabled>Loading banks...</option>
            </select>
          </div>

          <!-- Account Number Input -->
          <div class="mb-3">
            <label for="accountNumber" class="form-label">Enter Account Number</label>
            <input type="text" class="form-control" id="accountNumber" name="accountNumber" maxlength="10" required>
            <div class="text-danger mt-2" id="accountError" style="display: none;">Invalid account number or bank.</div>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-primary w-100" id="submitWithdraw" disabled>Submit Withdrawal</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirm Withdrawal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Account Name:</strong> <span id="confirmAccountName"></span></p>
        <p><strong>Amount:</strong> ₦<span id="confirmAmount"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="backButton">Back to Withdrawal</button>
        <button type="button" class="btn btn-primary" id="proceedButton">Proceed</button>
      </div>
    </div>
  </div>
</div>

  <!-- Bootstrap JS and Icons -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/js/amcharts-core.min.js"></script>
  <script src="../assets/js/amcharts.min.js"></script>
  <script src="../assets/js/custom.js"></script>
  
  <script>
      $(document).ready(function() {
            $('#depositBtn').on('click', function() {
                // AJAX call to check eligibility
                $.ajax({
                    url: '../../model/check_eligibility.php', // Backend script
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.eligible) {
                            // Show deposit modal
                            $('#depositModal').modal('show');
                            alert('You are eligible to make a deposit.');
                        } else {
                            // Show error alert
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                        alert('An error occurred while verifying eligibility. Please try again.');
                    }
                });
            });
        });
  </script>
  
  <script>
      $(document).ready(function() {
        $('#depositForm').on('submit', function(event) {
            event.preventDefault();
            
            const depositAmount = $('#depositAmount').val();
    
            // AJAX call to initiate deposit
            $.ajax({
                url: '../../model/initiate_payment.php', // Backend script
                type: 'POST',
                data: { amount: depositAmount },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Deposit initiated. Redirecting to payment page...');
                        window.location.href = response.payment_url; // Redirect to Flutterwave
                    } else {
                        alert('Error: ' + response.message); // Show error if initiation failed
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                    alert('An error occurred while initiating the deposit. Please try again.');
                }
            });
        });
        
        
        
        document.getElementById('check_btc').addEventListener('click',function(){
            const depositAmount = $('#transaction').val();
    
            // AJAX call to initiate deposit
            $.ajax({
                url: '../../model/initiate_bitcoin.php', // Backend script
                type: 'POST',
                contentType: 'application/json', 
                data: JSON.stringify({ amount: depositAmount }),
                dataType: 'json',
                success: function(response) {
                    console.log(response)
                     if (response.status === 'success') {
                         showToast('Wallet funding successful...');
                         window.location.href = 'index.php'
                     }else{
                          showToast('Wallet funding failed...');
                     }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                    //alert('An error occurred while initiating the deposit. Please try again.');
                }
            });
            
            
            function showToast(message) {
                  document.getElementById("copies").textContent = "Copied!"
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top", // Positioning
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                    stopOnFocus: true, // Prevents dismissing on hover
                }).showToast();
    
            }
        });
        
        
        
        
         document.getElementById('check_transfer').addEventListener('click',function(){
            const depositAmount = $('#transfer_amount').val();
            // const to_user = $('#toUser').val();
    
            // AJAX call to initiate deposit
            $.ajax({
                url: '../../model/initiate_transfers.php', // Backend script
                type: 'POST',
                contentType: 'application/json', 
                data: JSON.stringify({ amount: depositAmount}),
                dataType: 'json',
                success: function(response) {
                    console.log(response)
                     if (response.status === 'success') {
                         showToast('Transfer Successful : '+response.message);
                        // window.location.href = 'index.php'
                        $('#transfer_amount').val('');
                         $('#deposit_trans').modal('hide')
                     }else{
                          showToast('Transfer failed : '+response.message);
                     }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                    showToast('Transfer failed : '+error);
                    //alert('An error occurred while initiating the deposit. Please try again.');
                }
            });
            
            
            function showToast(message) {
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top", // Positioning
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                    stopOnFocus: true, // Prevents dismissing on hover
                }).showToast();
    
            }
        });
        
    });
  </script>
  
  <script>
      document.addEventListener("DOMContentLoaded", function () {
        const PAYSTACK_SECRET_KEY = "Bearer sk_live_bbe6ee0e98df8ef88660732ccedc9ad4eca91e4a"; // Replace with your Paystack secret key
        const mockBalance = <?php echo $walletBalance['balance'] ?>; // Replace with the actual balance from your backend
        const withdrawForm = document.getElementById("withdrawForm");
        const withdrawAmount = document.getElementById("withdrawAmount");
        const bankName = document.getElementById("bankName");
        const accountNumber = document.getElementById("accountNumber");
        const submitWithdraw = document.getElementById("submitWithdraw");
        const amountError = document.getElementById("amountError");
        const accountError = document.getElementById("accountError");
    
        const withdrawModal = new bootstrap.Modal(document.getElementById("withdrawModal"));
        const confirmModal = new bootstrap.Modal(document.getElementById("confirmModal"));
        const confirmAccountName = document.getElementById("confirmAccountName");
        const confirmAmount = document.getElementById("confirmAmount");
        const proceedButton = document.getElementById("proceedButton");
        const backButton = document.getElementById("backButton");
    
        let bankCodes = {};
        let recipientCode = null;
    
        // Fetch bank list from Paystack
        function fetchBankList() {
          fetch("https://api.paystack.co/bank", {
            method: "GET",
            headers: {
              Authorization: PAYSTACK_SECRET_KEY,
              "Content-Type": "application/json",
            },
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.status) {
                data.data.forEach((bank) => {
                  bankCodes[bank.name] = bank.code;
                  const option = document.createElement("option");
                  option.value = bank.name;
                  option.textContent = bank.name;
                  bankName.appendChild(option);
                });
              } else {
                alert("Failed to load banks: " + data.message);
              }
            })
            .catch((error) => {
              console.error("Error fetching banks:", error);
            });
        }
    
        // Validate form fields
        function validateForm() {
          const amount = parseFloat(withdrawAmount.value);
          const selectedBank = bankName.value;
          const accountNum = accountNumber.value;
    
          let isValid = true;
    
          if (isNaN(amount) || amount <= 0 || amount > mockBalance) {
            amountError.style.display = "block";
            isValid = false;
          } else {
            amountError.style.display = "none";
          }
    
          if (!selectedBank || accountNum.length !== 10) {
            accountError.style.display = "block";
            isValid = false;
          } else {
            accountError.style.display = "none";
          }
    
          submitWithdraw.disabled = !isValid;
        }
    
        // Handle form submission
        withdrawForm.addEventListener("submit", function (event) {
          event.preventDefault();
        
          const selectedBank = bankName.value;
          const accountNum = accountNumber.value;
        
          if (!bankCodes[selectedBank]) {
            alert("Bank code not found for the selected bank.");
            return;
          }
        
          fetch(`https://api.paystack.co/bank/resolve?account_number=${accountNum}&bank_code=${bankCodes[selectedBank]}`, {
            method: "GET",
            headers: {
              Authorization: PAYSTACK_SECRET_KEY,
              "Content-Type": "application/json",
            },
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.status) {
                // Hide withdrawal modal and show confirmation modal
                withdrawModal.hide();
                setTimeout(() => {
                  confirmAccountName.textContent = data.data.account_name;
                  confirmAmount.textContent = withdrawAmount.value;
                  confirmModal.show();
                }, 300);
              } else {
                alert(data.message || "Failed to resolve account.");
              }
            })
            .catch((error) => {
              console.error("Error resolving account:", error);
              alert("An error occurred while resolving account.");
            });
        });
    
        // Handle proceed button
        proceedButton.addEventListener("click", function () {
          const accountNum = accountNumber.value;
          const selectedBank = bankName.value;
          const amount = parseFloat(withdrawAmount.value);
    
          fetch("https://api.paystack.co/transferrecipient", {
            method: "POST",
            headers: {
              Authorization: PAYSTACK_SECRET_KEY,
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              type: "nuban",
              name: confirmAccountName.textContent,
              account_number: accountNum,
              bank_code: bankCodes[selectedBank],
              currency: "NGN",
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.status) {
                recipientCode = data.data.recipient_code;
    
                // Proceed to transfer
                alert("Recipient created successfully! Proceeding with transfer...");
                initiateTransfer(amount, recipientCode);
              } else {
                alert(data.message || "Failed to create transfer recipient.");
              }
            })
            .catch((error) => {
              console.error("Error creating transfer recipient:", error);
              alert("An error occurred while creating transfer recipient.");
            });
        });
    
        // Initiate transfer
        function initiateTransfer(amount, recipientCode) {
          fetch("https://api.paystack.co/transfer", {
            method: "POST",
            headers: {
              Authorization: PAYSTACK_SECRET_KEY,
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              source: "balance",
              amount: amount * 100, // Convert to kobo
              recipient: recipientCode,
              reason: "Withdrawal request",
              type: "nuban",
                name: confirmAccountName.textContent,
                account_number: accountNum,
                bank_code: bankCodes[selectedBank],
                currency: "NGN",
                metadata: {
                  email: "<?php echo $userEmail; ?>" // Add user's email
                }
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.status) {
                alert("Transfer initiated successfully!");
                confirmModal.hide();
              } else {
                alert(data.message || "Failed to initiate transfer.");
              }
            })
            .catch((error) => {
              console.error("Error initiating transfer:", error);
              alert("An error occurred while initiating transfer.");
            });
        }
    
        // Handle back button
        backButton.addEventListener("click", function () {
          confirmModal.hide();
          setTimeout(() => withdrawModal.show(), 300);
        });
    
        // Event listeners for validation
        withdrawAmount.addEventListener("input", validateForm);
        accountNumber.addEventListener("input", validateForm);
        bankName.addEventListener("change", validateForm);
    
        // Fetch banks on page load
        fetchBankList();
      });
    </script>
  
</body>

</html>