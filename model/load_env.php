<?php
function load_env($file = '.env') {
    if (!file_exists($file)) {
        return;
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip lines starting with a comment
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Split the line by '=' to get key-value pairs
        list($key, $value) = explode('=', $line, 2);

        // Trim spaces and any surrounding quotes
        $key = trim($key);
        $value = trim($value, "\"'");

        // Only set the value if it doesn't already exist
        if (!isset($_ENV[$key])) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load the .env file in the current directory
load_env(__DIR__ . '/.env');
