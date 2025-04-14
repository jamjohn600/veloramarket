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
            <a class="nav-link" href="../wallet" role="tab" aria-selected="true"><i class="icon ion-md-wallet"></i> Wallet</a>
            <a class="nav-link active" href="../settings" role="tab" aria-selected="false"><i class="icon ion-md-settings"></i> Settings</a>
          </div>
        </div>
        <div class="col-md-12 col-lg-9">
          <div class="tab-content" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="settings-tab">
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
              <!--<div class="card settings-profile">-->
              <!--  <div class="card-body">-->
              <!--    <h5 class="card-title">Create API Key</h5>-->
              <!--    <div class="form-row">-->
              <!--      <div class="col-md-6">-->
              <!--        <label for="generateKey">Generate key name</label>-->
              <!--        <input id="generateKey" type="text" class="form-control" placeholder="Enter your key name">-->
              <!--      </div>-->
              <!--      <div class="col-md-6">-->
              <!--        <label for="rewritePassword">Confirm password</label>-->
              <!--        <input id="rewritePassword" type="password" class="form-control"-->
              <!--          placeholder="Confirm your password">-->
              <!--      </div>-->
              <!--      <div class="col-md-12">-->
              <!--        <input type="submit" value="Create API key">-->
              <!--      </div>-->
              <!--    </div>-->
              <!--  </div>-->
              <!--</div>-->
              <!--<div class="card">-->
              <!--  <div class="card-body">-->
              <!--    <h5 class="card-title">Your API Keys</h5>-->
              <!--    <div class="wallet-history">-->
              <!--      <table class="table">-->
              <!--        <thead>-->
              <!--          <tr>-->
              <!--            <th>No.</th>-->
              <!--            <th>Key</th>-->
              <!--            <th>Status</th>-->
              <!--            <th>Action</th>-->
              <!--          </tr>-->
              <!--        </thead>-->
              <!--        <tbody>-->
              <!--          <tr>-->
              <!--            <td>1</td>-->
              <!--            <td>zRmWVcrAZ1C0RZkFMu7K5v0KWC9jUJLt</td>-->
              <!--            <td>-->
              <!--              <div class="custom-control custom-switch">-->
              <!--                <input type="checkbox" class="custom-control-input" id="apiStatus1" checked>-->
              <!--                <label class="custom-control-label" for="apiStatus1"></label>-->
              <!--              </div>-->
              <!--            </td>-->
              <!--            <td><i class="icon ion-md-trash"></i></td>-->
              <!--          </tr>-->
              <!--          <tr>-->
              <!--            <td>2</td>-->
              <!--            <td>Rv5dgnKdmVPyHwxeExBYz8uFwYQz3Jvg</td>-->
              <!--            <td>-->
              <!--              <div class="custom-control custom-switch">-->
              <!--                <input type="checkbox" class="custom-control-input" id="apiStatus2">-->
              <!--                <label class="custom-control-label" for="apiStatus2"></label>-->
              <!--              </div>-->
              <!--            </td>-->
              <!--            <td><i class="icon ion-md-trash"></i></td>-->
              <!--          </tr>-->
              <!--          <tr>-->
              <!--            <td>3</td>-->
              <!--            <td>VxEYIs1HwgmtKTUMA4aknjSEjjePZIWu</td>-->
              <!--            <td>-->
              <!--              <div class="custom-control custom-switch">-->
              <!--                <input type="checkbox" class="custom-control-input" id="apiStatus3">-->
              <!--                <label class="custom-control-label" for="apiStatus3"></label>-->
              <!--              </div>-->
              <!--            </td>-->
              <!--            <td><i class="icon ion-md-trash"></i></td>-->
              <!--          </tr>-->
              <!--          <tr>-->
              <!--            <td>4</td>-->
              <!--            <td>M01DueJ4x3awI1SSLGT3CP1EeLSnqt8o</td>-->
              <!--            <td>-->
              <!--              <div class="custom-control custom-switch">-->
              <!--                <input type="checkbox" class="custom-control-input" id="apiStatus4">-->
              <!--                <label class="custom-control-label" for="apiStatus4"></label>-->
              <!--              </div>-->
              <!--            </td>-->
              <!--            <td><i class="icon ion-md-trash"></i></td>-->
              <!--          </tr>-->
              <!--        </tbody>-->
              <!--      </table>-->
              <!--    </div>-->
              <!--  </div>-->
              <!--</div>-->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/jquery-3.4.1.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/js/amcharts-core.min.js"></script>
  <script src="../assets/js/amcharts.min.js"></script>
  <script src="../assets/js/custom.js"></script>
</body>


<!-- Mirrored from crypo-laravel-live.netlify.app/settings/ by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 16 Sep 2024 05:20:00 GMT -->
</html>