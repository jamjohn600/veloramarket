<?php
require_once 'my_fns.php';

function validatePassword($password) {
    if (strlen($password) < 8)
        return 'Password must be at least 8 characters long.';
    if (!preg_match('/[A-Z]/', $password))
        return 'Password must include at least one uppercase letter.';
    if (!preg_match('/[a-z]/', $password))
        return 'Password must include at least one lowercase letter.';
    if (!preg_match('/\d/', $password))
        return 'Password must include at least one number.';
    if (!preg_match('/[\W_]/', $password))
        return 'Password must include at least one special character.';
    return true;
}

function createAccount($username, $email, $password, $confirm_password) {
    global $pdo;
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['message' => 'Please fill in all fields.', 'success' => false]);
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['message' => 'Invalid email format.', 'success' => false]);
        return;
    }
    if ($password !== $confirm_password) {
        echo json_encode(['message' => 'Passwords do not match.', 'success' => false]);
        return;
    }
    $passwordValidation = validatePassword($password);
    if ($passwordValidation !== true) {
        echo json_encode(['message' => $passwordValidation, 'success' => false]);
        return;
    }
    try {
        $sql = "SELECT id FROM users WHERE email = ? OR username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $username]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Email or username already exists.', 'success' => false]);
            return;
        }
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, email, password, country) VALUES (?, ?, ?, 'Unknown')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $email, $hashedPassword]);
        $user_id = $pdo->lastInsertId();
        // Create only NGN wallet
        $currency = 'NGN';
        $wallet_id = generateWalletID($pdo, $currency);
        $sql = "INSERT INTO wallets (user_id, currency, wallet_id, balance) VALUES (?, ?, ?, 0.00)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $currency, $wallet_id]);
        echo json_encode(['message' => 'Account created successfully! Please log in.', 'success' => true, 'redirect' => 'signin.php']);
    } catch (PDOException $e) {
        error_log("Signup query failed: " . $e->getMessage());
        echo json_encode(['message' => 'Database error. Please try again.', 'success' => false]);
    }
}

$username = sanite($_POST['username'] ?? '');
$email = sanite($_POST['email'] ?? '');
$password = sanite($_POST['password'] ?? '');
$confirm_password = sanite($_POST['confirm_password'] ?? '');
createAccount($username, $email, $password, $confirm_password);