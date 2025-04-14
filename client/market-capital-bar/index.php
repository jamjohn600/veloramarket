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
  
  <div class="markets-capital pt70 pb40">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/1.png" alt="ETH">
              <span>ETH</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,687,258.77</h4>
              <h3 class="red">-5.47% <i class="icon ion-md-arrow-down"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,59,81,81,56,55,40,80,90,82]"
              data-bg="rgba(255,35,31,.7)" data-border="ff231f">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/2.png" alt="EOS">
              <span>EOS</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="green">+4.99% <i class="icon ion-md-arrow-up"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,42,82,45,40,55,72,80,60,82]"
              data-bg="rgba(38,222,129,.7)" data-border="26de81">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/3.png" alt="LTC">
              <span>LTC</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="red">-5.47% <i class="icon ion-md-arrow-down"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,48,72,80,60,82,50,48,72,33,44]"
              data-bg="rgba(255,35,31,.7)" data-border="ff231f">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/4.png" alt="KCS">
              <span>KCS</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="green">+4.99% <i class="icon ion-md-arrow-up"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,42,72,80,60,82,82,45,40,55]"
              data-bg="rgba(38,222,129,.7)" data-border="26de81">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/5.png" alt="COTI">
              <span>COTI</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,687,258.77</h4>
              <h3 class="red">-5.47% <i class="icon ion-md-arrow-down"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,59,81,81,56,55,40,80,90,82]"
              data-bg="rgba(255,35,31,.7)" data-border="ff231f">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/6.png" alt="TRX">
              <span>TRX</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="green">+4.99% <i class="icon ion-md-arrow-up"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,42,82,45,40,55,72,80,60,82]"
              data-bg="rgba(38,222,129,.7)" data-border="26de81">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/7.png" alt="XMR">
              <span>XMR</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="red">-5.47% <i class="icon ion-md-arrow-down"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,48,72,80,60,82,50,48,72,33,44]"
              data-bg="rgba(255,35,31,.7)" data-border="ff231f">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/8.png" alt="ADA">
              <span>ADA</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="green">+4.99% <i class="icon ion-md-arrow-up"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,42,72,80,60,82,82,45,40,55]"
              data-bg="rgba(38,222,129,.7)" data-border="26de81">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/9.png" alt="BNB">
              <span>BNB</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,687,258.77</h4>
              <h3 class="red">-5.47% <i class="icon ion-md-arrow-down"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,59,81,81,56,55,40,80,90,82]"
              data-bg="rgba(255,35,31,.7)" data-border="ff231f">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/10.png" alt="NEO">
              <span>NEO</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="green">+4.99% <i class="icon ion-md-arrow-up"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,42,82,45,40,55,72,80,60,82]"
              data-bg="rgba(38,222,129,.7)" data-border="26de81">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/11.png" alt="TOMO">
              <span>TOMO</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="red">-5.47% <i class="icon ion-md-arrow-down"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,48,72,80,60,82,50,48,72,33,44]"
              data-bg="rgba(255,35,31,.7)" data-border="ff231f">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/12.png" alt="MKR">
              <span>MKR</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="green">+4.99% <i class="icon ion-md-arrow-up"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,42,72,80,60,82,82,45,40,55]"
              data-bg="rgba(38,222,129,.7)" data-border="26de81">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/13.png" alt="ZEC">
              <span>ZEC</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,687,258.77</h4>
              <h3 class="red">-5.47% <i class="icon ion-md-arrow-down"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,59,81,81,56,55,40,80,90,82]"
              data-bg="rgba(255,35,31,.7)" data-border="ff231f">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/14.png" alt="VSYS">
              <span>VSYS</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="green">+4.99% <i class="icon ion-md-arrow-up"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,42,82,45,40,55,72,80,60,82]"
              data-bg="rgba(38,222,129,.7)" data-border="26de81">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/15.png" alt="ATOM">
              <span>ATOM</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="red">-5.47% <i class="icon ion-md-arrow-down"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,48,72,80,60,82,50,48,72,33,44]"
              data-bg="rgba(255,35,31,.7)" data-border="ff231f">
              <canvas></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="markets-capital-item">
            <h2>
              <img src="../assets/img/icon/16.png" alt="MTV">
              <span>MTV</span>
            </h2>
            <div class="markets-capital-details">
              <h4>$431,684,298.45</h4>
              <h3 class="green">+4.99% <i class="icon ion-md-arrow-up"></i></h3>
            </div>
            <div class="markets-capital-chart-bar" data-charts="[50,42,72,80,60,82,82,45,40,55]"
              data-bg="rgba(38,222,129,.7)" data-border="26de81">
              <canvas></canvas>
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
  <script src="../assets/js/Chart.bundle.min.js"></script>
  <script src="../assets/js/custom.js"></script>
</body>


<!-- Mirrored from crypo-laravel-live.netlify.app/market-capital-bar/ by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 16 Sep 2024 05:19:58 GMT -->
</html>