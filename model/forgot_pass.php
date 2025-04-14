<?php

require 'db.php'; // include your db connection (with PDO)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    // Fetch the user's email from the database using PDO
    try {
        $query = $pdo->prepare("SELECT id, email FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'No account found with this email.']);
            exit;
        }
    } catch (PDOException $e) {
        error_log("Database query error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database query error: ' . $e->getMessage()]);
        exit;
    }

    // Generate a secure new password
    $newPassword = bin2hex(random_bytes(6)); // Generates a random 12-character password

    // Hash the new password before storing it in the database
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the user's password in the database using PDO
    try {
        $update_query = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $update_query->execute([
            'password' => $hashedPassword,
            'id' => $user['id']
        ]);
    } catch (PDOException $e) {
        error_log("Database update error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Failed to reset password: ' . $e->getMessage()]);
        exit;
    }

    // Send the new password email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST']; // Set the SMTP server to send through
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER']; // SMTP username
        $mail->Password   = $_ENV['SMTP_PASS']; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['SMTP_PORT'];

        // Recipients
        $mail->setFrom($_ENV['SENDER_EMAIL'], $_ENV['SENDER_NAME']);
        $mail->addAddress($user['email']); // Send the email to the user

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Password Reset';
        $mail->Body    = 'Your new password is: <strong>' . $newPassword . '</strong><br>Please log in and change it immediately for security.';

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Password reset successfully. Check your email for the new password.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Mailer Error: ' . $mail->ErrorInfo]);
    }
}