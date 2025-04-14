<?php
require_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 1800);
    ini_set('session.cookie_lifetime', 1800);
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => false,
        'cookie_samesite' => 'Strict'
    ]);
}

function sanite($data)
{
    return htmlspecialchars(trim(strip_tags($data)), ENT_QUOTES, 'UTF-8');
}

function sessionExpired()
{
    $timeout = 30 * 60;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        return true;
    }
    $_SESSION['last_activity'] = time();
    return false;
}

function trackLoginAttempts($email)
{
    global $pdo;
    $max_attempts = 5;
    $lockout_time = 15 * 60;
    $sql = "SELECT * FROM login_attempts WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $attempt = $stmt->fetch();
    if ($attempt && $attempt['locked_until'] && strtotime($attempt['locked_until']) > time()) {
        return true;
    }
    if ($attempt && $attempt['attempt_count'] >= $max_attempts) {
        $sql = "UPDATE login_attempts SET locked_until = DATE_ADD(NOW(), INTERVAL ? SECOND) WHERE email = ?";
        $pdo->prepare($sql)->execute([$lockout_time, $email]);
        return true;
    }
    return false;
}

function logFailedLogin($email)
{
    global $pdo;
    $sql = "INSERT INTO login_attempts (email, attempt_count, last_attempt) VALUES (?, 1, NOW()) 
            ON DUPLICATE KEY UPDATE attempt_count = attempt_count + 1, last_attempt = NOW()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
}

function resetLoginAttempts($email)
{
    global $pdo;
    $sql = "DELETE FROM login_attempts WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
}

function generateWalletID($pdo, $currency)
{
    do {
        $wallet_id = strtoupper($currency . bin2hex(random_bytes(8)));
        $sql = "SELECT id FROM wallets WHERE wallet_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$wallet_id]);
    } while ($stmt->rowCount() > 0);
    return $wallet_id;
}

function fetch_user_details($username)
{
    global $pdo;
    try {
        $sql = "SELECT * FROM users 
                WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: false;
    } catch (PDOException $e) {
        error_log("fetch_user_details error: " . $e->getMessage());
        return false;
    }
}

function fetch_wallet_details($username)
{
    global $pdo;
    try {
        $sql = "SELECT wallet_id, currency, balance 
                FROM wallets w 
                JOIN users u ON w.user_id = u.id 
                WHERE u.username = ? AND w.currency = 'NGN'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['wallet_id' => '', 'currency' => 'NGN', 'balance' => 0];
    } catch (PDOException $e) {
        error_log("fetch_wallet_details error: " . $e->getMessage());
        return ['wallet_id' => '', 'currency' => 'NGN', 'balance' => 0];
    }
}

function fetch_wallet_balance($username)
{
    global $pdo;
    try {
        $sql = "SELECT balance 
                FROM wallets w 
                JOIN users u ON w.user_id = u.id 
                WHERE u.username = ? AND w.currency = 'NGN'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? number_format($result['balance'], 2) : '0.00';
    } catch (PDOException $e) {
        error_log("fetch_wallet_balance error: " . $e->getMessage());
        return '0.00';
    }
}