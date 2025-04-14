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
    <title>Deposit Failed</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Deposit Failed!</h4>
            <p>Unfortunately, your deposit could not be processed. Transaction reference: <strong><?php echo htmlspecialchars($tx_ref); ?></strong>.</p>
            <hr>
            <p class="mb-0">Please try again later or contact support if the issue persists.</p>
        </div>
        <a href="../../client/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
        <a href="../../client/deposit.php" class="btn btn-warning">Try Again</a>
    </div>
</body>
</html>