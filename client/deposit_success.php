<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../../signin.php');
    exit();
}

// Check if the transaction reference is available
if (isset($_GET['tx_ref'])) {
    $tx_ref = $_GET['tx_ref'];
} else {
    $tx_ref = "Unknown"; // Fallback in case no tx_ref is passed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Success</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Deposit Successful!</h4>
            <p>Your deposit has been successfully processed. Transaction reference: <strong><?php echo htmlspecialchars($tx_ref); ?></strong>.</p>
            <hr>
            <p class="mb-0">Thank you for your deposit. You can now view your updated balance in your dashboard.</p>
        </div>
        <a href="../../client/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
    </div>
</body>
</html>
