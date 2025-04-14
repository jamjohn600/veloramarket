<?php
require_once 'my_fns.php';

if (!isset($pdo)) {
    http_response_code(500);
    echo json_encode(['message' => 'Database connection error. Please contact support.', 'success' => false]);
    exit;
}

function login($email, $password) {
    global $pdo;
    if (empty($email) || empty($password)) {
        echo json_encode(['message' => 'Please fill in all fields.', 'success' => false]);
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['message' => 'Invalid email format.', 'success' => false]);
        return;
    }
    if (trackLoginAttempts($email)) {
        echo json_encode(['message' => 'Too many failed attempts. Try again in 15 minutes.', 'success' => false]);
        return;
    }
    try {
        $sql = "SELECT id, username, email, password FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) {
            logFailedLogin($email);
            echo json_encode(['message' => 'Email not found.', 'success' => false]);
            return;
        }
        if (!password_verify($password, $user['password'])) {
            logFailedLogin($email);
            echo json_encode(['message' => 'Incorrect password.', 'success' => false]);
            return;
        }
        resetLoginAttempts($email);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        echo json_encode(['message' => 'Login successful!', 'success' => true, 'redirect' => 'dashboard/index.html']);
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode(['message' => 'Database error. Please try again.', 'success' => false]);
    }
}

$email = sanite($_POST['email'] ?? '');
$password = sanite($_POST['password'] ?? '');
login($email, $password);