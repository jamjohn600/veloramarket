<?php
ini_set('display_errors', '0');
ini_set('log_errors', '1');

function load_env($file) {
    $env_path = realpath($file);
    if (!$env_path || !file_exists($env_path)) {
        error_log("Error: .env file not found at $file (resolved to: " . ($env_path ?: 'null') . "). Expected in model/ folder.");
        return false;
    }
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!str_contains($line, '=')) {
            error_log("Error: Invalid .env line: $line. Expected format: KEY=VALUE");
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
    error_log("Successfully loaded .env from $env_path");
    return true;
}

try {
    $env_file = __DIR__ . '/.env';
    error_log("Attempting to load .env from: " . realpath($env_file));
    if (!load_env($env_file)) {
        throw new Exception('Failed to load .env file at ' . $env_file);
    }
    if (!isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'])) {
        error_log('Error: Missing required .env variables. Found: ' . print_r(array_keys($_ENV), true));
        throw new Exception('Missing required environment variables (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD).');
    }
    $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
    error_log("Attempting DB connection with DSN: $dsn, User: {$_ENV['DB_USER']}");
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    error_log("Database connection successful.");
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    http_response_code(500);
    echo json_encode(['message' => 'Server configuration error. Please contact support.', 'success' => false]);
    exit;
}