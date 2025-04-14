<?php
// Load the environment variables and database connection
require 'db.php';

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Initialize response array
    $response = ['success' => false, 'error' => ''];

    // Validate required fields (e.g. dob, gender, phone, emp_status)
    if (!isset($_POST['dob'], $_POST['gender'], $_POST['phone'], $_POST['emp_status'], $_POST['id'])) {
        $response['error'] = 'Required fields are missing.';
        echo json_encode($response);
        exit();
    }

    // File upload handling
    $avatar = NULL;
    if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $fileName = basename($_FILES['fileUpload']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow only specific file formats (e.g. JPG, PNG, GIF)
        $allowedTypes = ['jpg', 'png', 'gif', 'jpeg'];
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            $response['error'] = 'Only JPG, PNG, and GIF files are allowed.';
            echo json_encode($response);
            exit();
        }

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $targetFilePath)) {
            $avatar = $fileName;
        } else {
            $response['error'] = 'There was an error uploading the file.';
            echo json_encode($response);
            exit();
        }
    }

    // Prepare the data for database insertion
    $id = $_POST['id'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $emp_status = $_POST['emp_status'];
    $address = $_POST['address'] ?? NULL;

    try {
        // Update the user's profile (excluding username and email)
        $sql = "UPDATE users 
                SET dob = :dob, gender = :gender, phone = :phone, emp_status = :emp_status, address = :address, updated_at = NOW(), transaction_status = 'ready'";
        
        if ($avatar) {
            $sql .= ", avatar = :avatar";  // Include avatar if uploaded
        }
        
        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':emp_status', $emp_status);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':id', $id);
        
        if ($avatar) {
            $stmt->bindParam(':avatar', $avatar);
        }

        // Execute the query
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Failed to update profile. Please try again.';
        }
    } catch (PDOException $e) {
        error_log("Error updating profile: " . $e->getMessage());
        $response['error'] = 'Database error. Please try again later.';
    }

    // Return response
    echo json_encode($response);
}