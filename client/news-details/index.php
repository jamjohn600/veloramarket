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

  <div class="news-details">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h2>KCS Pay Fees Feature is Coming Soon</h2>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam nemo illo natus iure earum recusandae autem
            quibusdam iste excepturi aut, provident eum maiores ad assumenda doloremque sint explicabo itaque adipisci!
          </p>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Asperiores eveniet dicta perferendis corporis
            ullam autem cum unde iste. Minus corporis, eaque accusamus commodi et molestiae illo laudantium odit
            asperiores numquam eos harum, quia quibusdam obcaecati dolorem sapiente voluptates aut dolores cumque modi
            ullam at repellendus. At, pariatur provident voluptates labore quia nulla qui illo! Veritatis sapiente
            perferendis nemo deleniti numquam maxime suscipit quas iusto? Distinctio est obcaecati reiciendis
            consequuntur accusantium nostrum officiis eveniet perferendis quisquam ratione quis, repellat quia numquam.
            Dolor ea quam veniam facere. Unde explicabo libero, doloremque quisquam illo, iusto ut voluptate cupiditate
            ipsum dignissimos reiciendis eligendi magnam!</p>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Atque reprehenderit dolorum inventore vero ratione
            eum, molestiae doloremque et magnam rem perferendis aperiam! Neque tempora ipsum culpa quidem aliquam
            pariatur incidunt illo rem eius inventore asperiores, reprehenderit libero possimus nihil laborum reiciendis
            adipisci hic perferendis officia? Adipisci molestiae accusantium est sequi fugit numquam! Modi ipsum sed
            laboriosam quo rem cupiditate soluta facere! Quod minus voluptatum sint? Eum temporibus asperiores vel
            perspiciatis deleniti perferendis possimus enim. Numquam eius alias voluptatum fugit nesciunt doloremque
            accusantium similique obcaecati, error explicabo accusamus ducimus nam nobis. Enim voluptate illum rem qui
            exercitationem quo veritatis! Veritatis tempora quaerat aperiam, provident temporibus sunt fugit! Officiis
            tenetur soluta ad totam, aspernatur nostrum et expedita rerum consequuntur. Doloremque accusamus ex, beatae,
            in totam cupiditate inventore dicta qui soluta consectetur, enim repellat velit. Distinctio modi totam
            repellat laudantium tenetur impedit, explicabo suscipit rerum eaque tempore nobis fuga numquam at
            exercitationem praesentium quasi aperiam pariatur, molestiae ipsa voluptatum? Esse veniam aliquam unde
            quibusdam nulla obcaecati eius eos, illum incidunt eligendi dolores pariatur odit, repudiandae et hic sint!
            Impedit ullam soluta nobis veritatis quibusdam, quisquam minima repellat suscipit. Beatae, dolores esse
            ducimus, id officia reprehenderit unde incidunt ex quaerat laudantium sint nam debitis?</p>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Atque reprehenderit dolorum inventore vero ratione
            eum, molestiae doloremque et magnam rem perferendis aperiam! Neque tempora ipsum culpa quidem aliquam
            pariatur incidunt illo rem eius inventore asperiores, reprehenderit libero possimus nihil laborum reiciendis
            adipisci hic perferendis officia? Adipisci molestiae accusantium est sequi fugit numquam! Modi ipsum sed
            laboriosam quo rem cupiditate soluta facere! Quod minus voluptatum sint? Eum temporibus asperiores vel
            perspiciatis deleniti perferendis possimus enim. Numquam eius alias voluptatum fugit nesciunt doloremque
            accusantium similique obcaecati, error explicabo accusamus ducimus nam nobis. Enim voluptate illum rem qui
            exercitationem quo veritatis! Veritatis tempora quaerat aperiam, provident temporibus sunt fugit! Officiis
            tenetur soluta ad totam, aspernatur nostrum et expedita rerum consequuntur. Doloremque accusamus ex, beatae,
            in totam cupiditate inventore dicta qui soluta consectetur, enim repellat velit. Distinctio modi totam
            repellat laudantium tenetur impedit, explicabo suscipit rerum eaque tempore nobis fuga numquam at
            exercitationem praesentium quasi aperiam pariatur, molestiae ipsa voluptatum? Esse veniam aliquam unde
            quibusdam nulla obcaecati eius eos, illum incidunt eligendi dolores pariatur odit, repudiandae et hic sint!
            Impedit ullam soluta nobis veritatis quibusdam, quisquam minima repellat suscipit. Beatae, dolores esse
            ducimus, id officia reprehenderit unde incidunt ex quaerat laudantium sint nam debitis?</p>
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


<!-- Mirrored from crypo-laravel-live.netlify.app/news-details/ by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 16 Sep 2024 05:20:00 GMT -->
</html>