<?php

require 'db.php'; // include your db connection (with PDO)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
$userId = $_SESSION['user_id']; // Assuming user is logged in

// Fetch the user's email and verification data from the database using PDO
try {
    $query = $pdo->prepare("SELECT email, verification_code, code_expires_at FROM users WHERE id = :id");
    $query->execute(['id' => $userId]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }
} catch (PDOException $e) {
    error_log("Database query error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database query error: ' . $e->getMessage()]);
    exit;
}

// Debug: Print SMTP information
// echo json_encode(['debug' => "User found, email is {$user['email']}"]);

// // Check SMTP environment variables
// $smtp_host = $_ENV['SMTP_HOST'] ?? 'Not set';
// $smtp_user = $_ENV['SMTP_USER'] ?? 'Not set';
// $smtp_pass = $_ENV['SMTP_PASS'] ?? 'Not set';
// $smtp_port = $_ENV['SMTP_PORT'] ?? 'Not set';
// echo json_encode(['SMTP_HOST' => $smtp_host, 'SMTP_USER' => $smtp_user, 'SMTP_PASS' => $smtp_pass, 'SMTP_PORT' => $smtp_port]);

$current_time = new DateTime();
$expires_at = new DateTime($user['code_expires_at']);
$interval = $current_time->diff($expires_at);

// Check if verification code is expired or does not exist
if (empty($user['verification_code']) || $interval->i >= 15) {
    // Generate a random 6-digit code
    $verification_code = mt_rand(100000, 999999);

    // Set expiration time to 15 minutes from now
    $code_expires_at = (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');

    // Update the database with the new code and expiration using PDO
    try {
        $update_query = $pdo->prepare("UPDATE users SET verification_code = :code, code_expires_at = :expires WHERE id = :id");
        $update_query->execute([
            'code' => $verification_code,
            'expires' => $code_expires_at,
            'id' => $userId
        ]);
        echo json_encode(['debug' => 'Verification code updated successfully']);
    } catch (PDOException $e) {
        error_log("Database update error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Failed to update verification code: ' . $e->getMessage()]);
        exit;
    }

    // Send the verification email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'mail.nexustrader.com.ng'; // Set the SMTP server to send through
        $mail->SMTPAuth   = true;
        $mail->Username   = 'no-reply@nexustrader.com.ng'; // SMTP username
        $mail->Password   = ']Gz,QQZa(~b]';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // SSL options for debugging (remove in production)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        // Recipients
        $mail->setFrom('no-reply@nexustrader.com.ng', 'Nexus Activation Token');
        $mail->addAddress($user['email']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Email Verification Code';
        $mail->Body    = 'Your verification code is: <strong>' . $verification_code . '</strong>';

        $mail->send();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Mailer Error: ' . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['status' => 'exists', 'message' => 'Code is still valid']);
}
