<?php

// verification_status.php
require_once('db.php');
require_once('my_fns.php');

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Fetch verification status from the database
    $status = get_verification_status($email);
    
    if ($status) {
        // Return JSON with status
        echo json_encode([
            'email_verified' => $status['email_verified'],
            'phone_verified' => $status['phone_verified']
        ]);
    } else {
        // User not found
        echo json_encode(['error' => 'User not found']);
    }
}
