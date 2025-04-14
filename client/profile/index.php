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

// // Check if the user is logged in
// if (!isset($_SESSION['username'])) {
//     header('Location: ../../signin.php');
//     exit(); // Stop further execution if the user is not logged in
// }

require_once('../../model/my_fns.php');

// Fetch user details using the session username
$username = $_SESSION['username'];
$userDetails = fetch_user_details($username);

$transaction = fetch_transactions($username);

$tranding = fetch_tradeDeposits($username);


if (!$userDetails) {
    die("User details not found.");
}

$userEmail = $userDetails['email'];

// $userId = $userDetails['id']; // Assume user is logged in and user ID is stored in session

// Fetch verification status using the get_verification_status function
$verificationStatus = get_verification_status($userEmail);

// Ensure the values are properly set (in case they are null)
$emailVerified = $verificationStatus['email_verified'] ?? 0; // Default to 0 if null
$phoneVerified = $verificationStatus['phone_verified'] ?? 0; // Default to 0 if null

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
            <a class="nav-link active" href="../profile" role="tab" aria-selected="true"><i class="icon ion-md-person"></i> Profile</a>
            <a class="nav-link" href="../wallet" role="tab" aria-selected="false"><i class="icon ion-md-settings"></i> Wallet</a>
            <a class="nav-link" href="../settings" role="tab" aria-selected="false"><i class="icon ion-md-settings"></i> Settings</a>
          </div>
        </div>
        <div class="col-md-12 col-lg-9">
          <div class="tab-content" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="settings-profile" role="tabpanel"
              aria-labelledby="settings-profile-tab">
              <!-- Alert component -->
              <div class="alert alert-warning alert-dismissible fade show <?php echo ($emailVerified == 1) ? 'd-none' : ''; ?>" role="alert">
                  <strong>Verify Account!</strong> Your Email Address has not been verified, please verify now to be able to deposit into your account.
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <!-- Alert component -->
              <div class="alert alert-warning alert-dismissible fade show <?php echo ($userDetails['transaction_status'] === 'ready') ? 'd-none' : ''; ?>" role="alert">
                <strong>Update!</strong> Please provide more details about you - Let us get to know you more.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <!-- Alert component -->
              <div class="alert alert-warning alert-dismissible fade show <?php echo ($emailVerified === 1) ? 'd-none' : ''; ?>" role="alert">
                <strong>KYC Verification!</strong> Verify your identity and information and unlock withdrawal of funds.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Verifications</h5>
                  <div class="d-flex justify-content-between align-items-center">
                      <span class="text-white lead">Email Verification</span>
                      <button id="emailVerificationButton" class="btn btn-primary" data-toggle="modal" data-target="#emailModal">Verify</button>
                  </div>
                  <!-- <div class="d-flex justify-content-between align-items-center mt-3">
                      <span class="text-white lead">Phone Verification</span>
                      <button id="phoneVerificationButton" class="btn btn-primary" data-toggle="modal" data-target="#phoneModal">Verify</button>
                  </div> -->
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Profile Information</h5>
                  <div class="settings-profile">
                  <?php if ($userDetails['transaction_status'] == 'ready'): ?>
                      <div class="text-center">
                          <h5 class="display-5 fw-bold text-white">Profile Completely Up to Date</h5>
                          <a href="#">Would you like to change some details?</a>
                      </div>
                  <?php else: ?>
                      <!-- Display the form here -->
                      <form id="updateProfileForm" enctype="multipart/form-data">
                        <img src="../assets/img/avatar.svg" alt="avatar">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="fileUpload" name="fileUpload" required /> 
                            <label class="custom-file-label" for="fileUpload">Choose avatar</label>
                        </div>
                        <div class="form-row mt-4">
                          <div class="col-md-6">
                            <label for="username">Last name</label>
                            <input id="username" name="username" type="text" class="form-control" value=<?php echo $userDetails['username']; ?> readonly>
                          </div>
                          <div class="col-md-6">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="text" class="form-control" value=<?php echo $userDetails['email']; ?> readonly>
                          </div>
                          <div class="col-md-6">
                            <label for="dob">Date of Birth</label>
                            <input id="dob" name="dob" type="date" class="form-control" required>
                          </div>
                          <div class="col-md-6">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-control">
                              <option value="" disabled selected>Select your gender</option>
                              <option value="male">Male</option>
                              <option value="female">Female</option>
                              <option value="prefer-not-to-say">Prefer not to say</option>
                            </select>
                          </div>
                          <div class="col-md-6">
                            <label for="phone">Phone Number</label>
                            <input id="phone" name="phone" type="tel" class="form-control" placeholder="Enter your phone number">
                          </div>
                          <div class="col-md-6">
                            <label for="emp_status">Employment Status</label>
                            <select id="emp_status" name="emp_status" class="form-control">
                              <option value="" disabled selected>Select your employment status</option>
                              <option value="employed-full-time">Employed Full-Time</option>
                              <option value="employed-part-time">Employed Part-Time</option>
                              <option value="self-employed">Self-Employed</option>
                              <option value="unemployed">Unemployed</option>
                              <option value="student">Student</option>
                              <option value="retired">Retired</option>
                              <option value="homemaker">Homemaker</option>
                              <option value="prefer-not-to-say">Prefer not to say</option>
                            </select>
                          </div>
                          <!-- <div class="col-md-6">
                            <label for="selectLanguage">Language</label>
                            <select id="selectLanguage" class="custom-select">
                              <option selected>English</option>
                              <option>Mandarin Chinese</option>
                              <option>Spanish</option>
                              <option>Arabic</option>
                              <option>Russian</option>
                            </select>
                          </div>
                          <div class="col-md-6">
                            <label for="selectCurrency">Currency</label>
                            <select id="selectCurrency" class="custom-select">
                              <option selected>USD</option>
                              <option>EUR</option>
                              <option>GBP</option>
                              <option>CHF</option>
                            </select>
                          </div> -->
                          <div class="col-md-12">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="4" placeholder="Enter your address"></textarea>
                          </div>
                          <input type="hidden" id="id" name="id" value="<?php echo $userDetails['id']; ?>">
                          <div class="col-md-12">
                            <input type="submit" value="Update">
                          </div>
                        </div>
                      </form>
                  <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Security Information</h5>
                  <div class="settings-profile">
                    <form id="securityUpdateForm">
                      <div class="form-row">
                        <div class="col-md-6">
                          <label for="currentPass">Current password</label>
                          <input id="currentPass" name="currentPass" type="password" class="form-control" placeholder="Enter your password" required />
                        </div>
                        <div class="col-md-6">
                          <label for="newPass">New password</label>
                          <input id="newPass" name="newPass" type="password" class="form-control" placeholder="Enter new password">
                        </div>
                        <div class="col-md-6">
                          <label for="securityOne">Security question</label>
                          <select id="securityOne" name="securityOne" class="custom-select">
                            <option selected disabled>Select a question</option>
                            <option value="first_pet">What was the name of your first pet?</option>
                            <option value="mother_middle_name">What's your Mother's middle name?</option>
                            <option value="first_school">What was the name of your first school?</option>
                            <option value="first_travel">Where did you travel for the first time?</option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label for="securityAnswer">Answer</label>
                          <input id="securityAnswer" name="securityAnswer" type="text" class="form-control" placeholder="Enter your answer">
                        </div>
                        <div class="col-md-12">
                          <input type="submit" value="Update">
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="settings-wallet" role="tabpanel" aria-labelledby="settings-wallet-tab">
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
                  <div class="alert alert-warning d-flex justify-content-between align-items-center">
                    <span>Your Live Accounts</span>
                    <button type="button" class="btn text-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <i class="fa-solid fa-plus"></i>
                    </button>
                  </div>
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

                    <div class="alert alert-warning d-flex justify-content-between align-items-center mt-3">
                    <span>Your Demo Accounts</span>
                    <button type="button" class="btn text-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <i class="fa-solid fa-plus"></i>
                    </button>
                  </div>
                    <!--  
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
                            <button class="btn red">Withdraw</button>
                            <button id="transferBtn" class="btn btn-primary">Transfer</button>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Wallet Deposit Address</h5>
                            <div class="row wallet-address">
                              <div class="col-md-8">
                                <p>Deposits to this address are unlimited. Note that you may not be able to withdraw all
                                  of your
                                  funds at once if you deposit more than your daily withdrawal limit.</p>
                                <div class="input-group">
                                  <input type="text" class="form-control" value="bc1qynjvjxzxvazkwmu4g40r67sz689ntkv6nzzqqf" readonly>
                                  <div class="input-group-prepend">
                                    <button class="btn btn-primary" id="copies">COPY</button>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <img src="../assets/img/qr-code-light.svg" alt="qr-code">
                              </div>
                            </div>
                          </div>
                        </div>
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
        $transaction = fetch_transactions($username); // Fetch the transactions
        if (!empty($transaction)) {
            foreach ($transaction as $index => $txn) {
                $statusIcon = ($txn['transaction_type'] == 'DEBIT') ? 'icon ion-md-close-circle-outline red' : 'icon ion-md-checkmark-circle-outline green';
                $amount = $txn['balance'];
                $date = $txn['created_at'];
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
                      <div class="tab-pane fade" id="coinETH" role="tabpanel">
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Balances</h5>
                            <ul>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-cash"></i>
                                  <h2>Total Equity</h2>
                                </div>
                                <div>
                                  <h3>4.1542 ETH</h3>
                                </div>
                              </li>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-checkmark"></i>
                                  <h2>Available Margin</h2>
                                </div>
                                <div>
                                  <h3>1.334 ETH</h3>
                                </div>
                              </li>
                            </ul>
                            <button class="btn green">Deposit</button>
                            <button class="btn red">Withdraw</button>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Wallet Deposit Address</h5>
                            <div class="row wallet-address">
                              <div class="col-md-8">
                                <p>Deposits to this address are unlimited. Note that you may not be able to withdraw all
                                  of your
                                  funds at once if you deposit more than your daily withdrawal limit.</p>
                                <div class="input-group">
                                  <input type="text" class="form-control" value="Ad87deD4gEe8dG57Ede4eEg5dREs4d5e8f4e">
                                  <div class="input-group-prepend">
                                    <button class="btn btn-primary">COPY</button>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <img src="../assets/img/qr-code-light.svg" alt="qr-code">
                              </div>
                            </div>
                          </div>
                        </div>
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
                                  <tr>
                                    <td>1</td>
                                    <td>25-04-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>4.5454334</td>
                                  </tr>
                                  <tr>
                                    <td>2</td>
                                    <td>25-05-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>0.5484468</td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>25-06-2019</td>
                                    <td><i class="icon ion-md-close-circle-outline red"></i></td>
                                    <td>2.5454545</td>
                                  </tr>
                                  <tr>
                                    <td>4</td>
                                    <td>25-07-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>1.45894147</td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>25-08-2019</td>
                                    <td><i class="icon ion-md-close-circle-outline red"></i></td>
                                    <td>2.5454545</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="coinBNB" role="tabpanel">
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Balances</h5>
                            <ul>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-cash"></i>
                                  <h2>Total Equity</h2>
                                </div>
                                <div>
                                  <h3>7.342 BNB</h3>
                                </div>
                              </li>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-checkmark"></i>
                                  <h2>Available Margin</h2>
                                </div>
                                <div>
                                  <h3>0.332 BNB</h3>
                                </div>
                              </li>
                            </ul>
                            <button class="btn green">Deposit</button>
                            <button class="btn red">Withdraw</button>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Wallet Deposit Address</h5>
                            <div class="row wallet-address">
                              <div class="col-md-8">
                                <p>Deposits to this address are unlimited. Note that you may not be able to withdraw all
                                  of your
                                  funds at once if you deposit more than your daily withdrawal limit.</p>
                                <div class="input-group">
                                  <input type="text" class="form-control" value="Ad87deD4gEe8dG57Ede4eEg5dREs4d5e8f4e">
                                  <div class="input-group-prepend">
                                    <button class="btn btn-primary">COPY</button>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <img src="../assets/img/qr-code-light.svg" alt="qr-code">
                              </div>
                            </div>
                          </div>
                        </div>
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
                                  <tr>
                                    <td>1</td>
                                    <td>25-04-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>4.5454334</td>
                                  </tr>
                                  <tr>
                                    <td>2</td>
                                    <td>25-05-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>0.5484468</td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>25-06-2019</td>
                                    <td><i class="icon ion-md-close-circle-outline red"></i></td>
                                    <td>2.5454545</td>
                                  </tr>
                                  <tr>
                                    <td>4</td>
                                    <td>25-07-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>1.45894147</td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>25-08-2019</td>
                                    <td><i class="icon ion-md-close-circle-outline red"></i></td>
                                    <td>2.5454545</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="coinTRX" role="tabpanel">
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Balances</h5>
                            <ul>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-cash"></i>
                                  <h2>Total Equity</h2>
                                </div>
                                <div>
                                  <h3>4.3344 TRX</h3>
                                </div>
                              </li>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-checkmark"></i>
                                  <h2>Available Margin</h2>
                                </div>
                                <div>
                                  <h3>1.453 TRX</h3>
                                </div>
                              </li>
                            </ul>
                            <button class="btn green">Deposit</button>
                            <button class="btn red">Withdraw</button>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Wallet Deposit Address</h5>
                            <div class="row wallet-address">
                              <div class="col-md-8">
                                <p>Deposits to this address are unlimited. Note that you may not be able to withdraw all
                                  of your
                                  funds at once if you deposit more than your daily withdrawal limit.</p>
                                <div class="input-group">
                                  <input type="text" class="form-control" value="Ad87deD4gEe8dG57Ede4eEg5dREs4d5e8f4e">
                                  <div class="input-group-prepend">
                                    <button class="btn btn-primary">COPY</button>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <img src="../assets/img/qr-code-light.svg" alt="qr-code">
                              </div>
                            </div>
                          </div>
                        </div>
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
                                  <tr>
                                    <td>1</td>
                                    <td>25-04-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>4.5454334</td>
                                  </tr>
                                  <tr>
                                    <td>2</td>
                                    <td>25-05-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>0.5484468</td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>25-06-2019</td>
                                    <td><i class="icon ion-md-close-circle-outline red"></i></td>
                                    <td>2.5454545</td>
                                  </tr>
                                  <tr>
                                    <td>4</td>
                                    <td>25-07-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>1.45894147</td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>25-08-2019</td>
                                    <td><i class="icon ion-md-close-circle-outline red"></i></td>
                                    <td>2.5454545</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="coinEOS" role="tabpanel">
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Balances</h5>
                            <ul>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-cash"></i>
                                  <h2>Total Equity</h2>
                                </div>
                                <div>
                                  <h3>33.35 EOS</h3>
                                </div>
                              </li>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-checkmark"></i>
                                  <h2>Available Margin</h2>
                                </div>
                                <div>
                                  <h3>4.445 EOS</h3>
                                </div>
                              </li>
                            </ul>
                            <button class="btn green">Deposit</button>
                            <button class="btn red">Withdraw</button>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Wallet Deposit Address</h5>
                            <div class="row wallet-address">
                              <div class="col-md-8">
                                <p>Deposits to this address are unlimited. Note that you may not be able to withdraw all
                                  of your
                                  funds at once if you deposit more than your daily withdrawal limit.</p>
                                <div class="input-group">
                                  <input type="text" class="form-control" value="bc1qynjvjxzxvazkwmu4g40r67sz689ntkv6nzzqqf">
                                  <div class="input-group-prepend">
                                    <button class="btn btn-primary" id="copy">COPY</button>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <img src="../assets/img/qr-code-light.svg" alt="qr-code">
                              </div>
                            </div>
                          </div>
                        </div>
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
                                  <tr>
                                    <td>1</td>
                                    <td>25-04-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>4.5454334</td>
                                  </tr>
                                  <tr>
                                    <td>2</td>
                                    <td>25-05-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>0.5484468</td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>25-06-2019</td>
                                    <td><i class="icon ion-md-close-circle-outline red"></i></td>
                                    <td>2.5454545</td>
                                  </tr>
                                  <tr>
                                    <td>4</td>
                                    <td>25-07-2019</td>
                                    <td><i class="icon ion-md-checkmark-circle-outline green"></i></td>
                                    <td>1.45894147</td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>25-08-2019</td>
                                    <td><i class="icon ion-md-close-circle-outline red"></i></td>
                                    <td>2.5454545</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="coinXMR" role="tabpanel">
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Balances</h5>
                            <ul>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-cash"></i>
                                  <h2>Total Equity</h2>
                                </div>
                                <div>
                                  <h3>34.333 XMR</h3>
                                </div>
                              </li>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-checkmark"></i>
                                  <h2>Available Margin</h2>
                                </div>
                                <div>
                                  <h3>2.354 XMR</h3>
                                </div>
                              </li>
                            </ul>
                            <button class="btn green">Deposit</button>
                            <button class="btn red">Withdraw</button>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Wallet Deposit Address</h5>
                            <div class="row wallet-address">
                              <div class="col-md-8">
                                <p>Deposits to this address are unlimited. Note that you may not be able to withdraw all
                                  of your
                                  funds at once if you deposit more than your daily withdrawal limit.</p>
                                <div class="input-group">
                                  <input type="text" class="form-control" id="address" value="bc1qynjvjxzxvazkwmu4g40r67sz689ntkv6nzzqqf">
                                  <div class="input-group-prepend">
                                    <button class="btn btn-primary" id="copied">COPY</button>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <img src="../assets/img/qr-code-light.svg" alt="qr-code">
                              </div>
                            </div>
                          </div>
                        </div>
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
        $transaction = fetch_transactions($username); // Fetch the transactions
        if (!empty($transaction)) {
            foreach ($transaction as $index => $txn) {
                $statusIcon = ($txn['transaction_type'] == 'DEBIT') ? 'icon ion-md-close-circle-outline red' : 'icon ion-md-checkmark-circle-outline green';
                $amount = $txn['balance'];
                $date = $txn['created_at'];
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
                      <div class="tab-pane fade" id="coinKCS" role="tabpanel">
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Balances</h5>
                            <ul>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-cash"></i>
                                  <h2>Total Equity</h2>
                                </div>
                                <div>
                                  <h3>86.577 KCS</h3>
                                </div>
                              </li>
                              <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                  <i class="icon ion-md-checkmark"></i>
                                  <h2>Available Margin</h2>
                                </div>
                                <div>
                                  <h3>5.78 KCS</h3>
                                </div>
                              </li>
                            </ul>
                            <button class="btn green">Deposit</button>
                            <button class="btn red">Withdraw</button>
                          </div>
                        </div>
                        <div class="card">
                          <div class="card-body">
                            <h5 class="card-title">Wallet Deposit Address</h5>
                            <div class="row wallet-address">
                              <div class="col-md-8">
                                <p>Deposits to this address are unlimited. Note that you may not be able to withdraw all
                                  of your
                                  funds at once if you deposit more than your daily withdrawal limit.</p>
                                <div class="input-group">
                                  <input type="text" class="form-control" id="addresses" value="Ad87deD4gEe8dG57Ede4eEg5dREs4d5e8f4e">
                                  <div class="input-group-prepend">
                                    <button class="btn btn-primary">COPY</button>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <img src="../assets/img/qr-code-light.svg" alt="qr-code">
                              </div>
                            </div>
                          </div>
                        </div>
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
        $transaction = fetch_transactions($username); // Fetch the transactions
        if (!empty($transaction)) {
            foreach ($transaction as $index => $txn) {
                $statusIcon = ($txn['transaction_type'] == 'DEBIT') ? 'icon ion-md-close-circle-outline red' : 'icon ion-md-checkmark-circle-outline green';
                $amount = $txn['balance'];
                $date = $txn['created_at'];
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
            <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Notifications</h5>
                  <div class="settings-notification">
                    <ul>
                      <li>
                        <div class="notification-info">
                          <p>Update price</p>
                          <span>Get the update price in your dashboard</span>
                        </div>
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="notification1">
                          <label class="custom-control-label" for="notification1"></label>
                        </div>
                      </li>
                      <li>
                        <div class="notification-info">
                          <p>2FA</p>
                          <span>Unable two factor authentication service</span>
                        </div>
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="notification2" checked>
                          <label class="custom-control-label" for="notification2"></label>
                        </div>
                      </li>
                      <li>
                        <div class="notification-info">
                          <p>Latest news</p>
                          <span>Get the latest news in your mail</span>
                        </div>
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="notification3">
                          <label class="custom-control-label" for="notification3"></label>
                        </div>
                      </li>
                      <li>
                        <div class="notification-info">
                          <p>Email Service</p>
                          <span>Get security code in your mail</span>
                        </div>
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="notification4" checked>
                          <label class="custom-control-label" for="notification4"></label>
                        </div>
                      </li>
                      <li>
                        <div class="notification-info">
                          <p>Phone Notify</p>
                          <span>Get transition notification in your phone </span>
                        </div>
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="notification5" checked>
                          <label class="custom-control-label" for="notification5"></label>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="card settings-profile">
                <div class="card-body">
                  <h5 class="card-title">Create API Key</h5>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label for="generateKey">Generate key name</label>
                      <input id="generateKey" type="text" class="form-control" placeholder="Enter your key name">
                    </div>
                    <div class="col-md-6">
                      <label for="rewritePassword">Confirm password</label>
                      <input id="rewritePassword" type="password" class="form-control"
                        placeholder="Confirm your password">
                    </div>
                    <div class="col-md-12">
                      <input type="submit" value="Create API key">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Your API Keys</h5>
                  <div class="wallet-history">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Key</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>zRmWVcrAZ1C0RZkFMu7K5v0KWC9jUJLt</td>
                          <td>
                            <div class="custom-control custom-switch">
                              <input type="checkbox" class="custom-control-input" id="apiStatus1" checked>
                              <label class="custom-control-label" for="apiStatus1"></label>
                            </div>
                          </td>
                          <td><i class="icon ion-md-trash"></i></td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td>Rv5dgnKdmVPyHwxeExBYz8uFwYQz3Jvg</td>
                          <td>
                            <div class="custom-control custom-switch">
                              <input type="checkbox" class="custom-control-input" id="apiStatus2">
                              <label class="custom-control-label" for="apiStatus2"></label>
                            </div>
                          </td>
                          <td><i class="icon ion-md-trash"></i></td>
                        </tr>
                        <tr>
                          <td>3</td>
                          <td>VxEYIs1HwgmtKTUMA4aknjSEjjePZIWu</td>
                          <td>
                            <div class="custom-control custom-switch">
                              <input type="checkbox" class="custom-control-input" id="apiStatus3">
                              <label class="custom-control-label" for="apiStatus3"></label>
                            </div>
                          </td>
                          <td><i class="icon ion-md-trash"></i></td>
                        </tr>
                        <tr>
                          <td>4</td>
                          <td>M01DueJ4x3awI1SSLGT3CP1EeLSnqt8o</td>
                          <td>
                            <div class="custom-control custom-switch">
                              <input type="checkbox" class="custom-control-input" id="apiStatus4">
                              <label class="custom-control-label" for="apiStatus4"></label>
                            </div>
                          </td>
                          <td><i class="icon ion-md-trash"></i></td>
                        </tr>
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

  <!-- Email Verification Modal -->
  <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="emailModalLabel">Verify Your Email</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="emailVerificationForm">
            <div class="form-group">
              <label for="emailVerificationCode">Enter the 6-digit code</label>
              <input type="text" class="form-control" id="emailVerificationCode" placeholder="Enter code">
              <p class="text-center mt-2">Didn't recieve a code? <button id="emailVerificationButton" style="background: none; border: none; color: #007bff; text-decoration: underline; cursor: pointer; padding: 0; font: inherit;">Resend</button></p>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Verify</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Phone Verification Modal -->
  <div class="modal fade" id="phoneModal" tabindex="-1" role="dialog" aria-labelledby="phoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="phoneModalLabel">Verify Your Phone</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="phoneVerificationForm">
            <div class="form-group">
              <label for="phoneVerificationCode">Enter the 6-digit code</label>
              <input type="text" class="form-control" id="phoneVerificationCode" placeholder="Enter code">
            </div>
            <button type="submit" class="btn btn-success btn-block">Verify</button>
          </form>
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
                <button type="button" id="btc" class="btn btn-info w-100 mb-2">
                        <i class="fab fa-bitcoin"></i> Deposit with Bitcoin
                    </button>
                    
                    <!-- Ethereum Button with Ethereum Icon -->
                    <button type="submit" class="btn btn-info w-100">
                        <i class="fab fa-ethereum"></i> Deposit with Ethereum
                    </button>
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

<!--     <div class="modal fade" id="transfer_funds" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">-->
<!--    <div class="modal-dialog modal-dialog-centered">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <h5 class="modal-title" id="depositModalLabel">Transfer funds</h5>-->
<!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
<!--            </div>-->
<!--            <div class="modal-body">-->
<!--                <div id="btc_form">-->
                    <!-- Amount Input -->
<!--                    <div class="mb-3">-->
<!--                        <label for="transaction" class="form-label">Enter Recipient Username</label>-->
<!--                        <input type="text" class="form-control" id="toUser" name="toUser" placeholder="Gian012394......." required>-->
<!--                    </div>-->
                    
<!--                     <div class="mb-3">-->
<!--                        <label for="transaction" class="form-label">Enter Transaction Amount</label>-->
<!--                        <input type="number" class="form-control" id="transfer_amount" name="transfer_amount" placeholder="1000" required>-->
<!--                    </div>-->

                    <!-- Hidden Fields -->
<!--                    <input type="hidden" id="username" name="username" value="<?php echo $userDetails['username']; ?>">-->
<!--                    <input type="hidden" id="email" name="email" value="<?php echo $userDetails['email']; ?>">-->

                    <!-- Terms of Service -->
<!--                    <div class="mb-3">-->
<!--                        <p>By proceeding, you agree to our <a href="/terms" target="_blank">Terms of Service</a>.</p>-->
<!--                    </div>-->

                    <!-- Proceed Button with NGN Icon -->
                    
                    <!-- Bitcoin Button with Bitcoin Icon -->
<!--                    <button type="button" id="check_transfer" class="btn btn-info w-100 mb-2">-->
<!--                        <i class="fab fa-bitcoin"></i> Submit-->
<!--                    </button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<style>
        /* Toast container styles */
        #toastContainer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* Individual toast style */
        .toast {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            opacity: 0;
            transition: opacity 0.5s ease, transform 0.5s ease;
            transform: translateY(20px);
        }

        /* Show toast animation */
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

<!-- <script>
    $(document).ready(function(){
        $('#depositForm').on('submit', function(event){
            event.preventDefault();

            let amount = $('#depositAmount').val();
            if(amount >= 1000){
                // Proceed with Flutterwave integration or AJAX for payment processing
                alert('Processing deposit of ' + amount + ' NGN');
                // You can initiate the Flutterwave payment process here
            } else {
                alert('Please enter an amount greater than or equal to 1,000 NGN.');
            }
        });
    });
</script> -->



  <script src="../assets/js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap JS and Icons -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/js/amcharts-core.min.js"></script>
  <script src="../assets/js/amcharts.min.js"></script>
  <script src="../assets/js/custom.js"></script>

  <script src="../../model/ajax.js"></script>

  <script>
      $(document).ready(function() {
        // Check if email is verified
        if (emailVerified == 1) {
            console.log("Email is verified: Updating button to 'Done'");
            $('#emailVerificationButton')
                .removeClass('btn-primary')
                .addClass('btn-success')
                .prop('disabled', true)
                .text('Done');
        } else {
            console.log("Email is not verified: Keeping button as 'Verify'");
            $('#emailVerificationButton')
                .removeClass('btn-success')
                .addClass('btn-primary')
                .prop('disabled', false)
                .text('Verify');
        }

        // Check if phone is verified
        if (phoneVerified == 1) {
            console.log("Phone is verified: Updating button to 'Done'");
            $('#phoneVerificationButton')
                .removeClass('btn-primary')
                .addClass('btn-success')
                .prop('disabled', true)
                .text('Done');
        } else {
            console.log("Phone is not verified: Keeping button as 'Verify'");
            $('#phoneVerificationButton')
                .removeClass('btn-success')
                .addClass('btn-primary')
                .prop('disabled', false)
                .text('Verify');
        }

        // Output values for debugging
        console.log("Email Verified: " + emailVerified);
        console.log("Phone Verified: " + phoneVerified);
    });
  </script>

<script>
    // Pass the verification status to JavaScript
    var emailVerified = <?php echo json_encode($emailVerified); ?>;
    var phoneVerified = <?php echo json_encode($phoneVerified); ?>;
    
     function copyToClipboard() {
            console.log('how far')
            const text = document.getElementById("addresses").value;
            
            navigator.clipboard.writeText(text).then(() => {
                showToast("Text copied to clipboard!",3000);
            }).catch((error) => {
                showToast("Could not copy text: ",3000);
            });
        }

        // Attach the event listener to the button
         document.getElementById("copies").addEventListener("click", copyToClipboard);
         
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

    
    </script>
  </script>
</body>

</html>