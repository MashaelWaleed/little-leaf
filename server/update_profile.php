<?php 
session_start();
require_once('../config.php');
require_once('db_connect.php');

// Set header for JSON response
header('Content-Type: application/json');

// Ensure user is logged in before even looking at the request
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    
    // 1. Collect & Trim input data
    $fname = trim($_POST['fName'] ?? '');
    $lname = trim($_POST['lName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Array to collect validation errors
    $errors = [];

    // 2. Validation Checks

    // First Name Validation (Required, Min 2 chars, letters only)
    if (empty($fname)) {
        $errors[] = 'First name is required.';
    } elseif (strlen($fname) < 2) {
        $errors[] = 'First name must be at least 2 characters.';
    } elseif (!preg_match("/^[a-zA-Z\s\x{0600}-\x{06FF}]+$/u", $fname)) { // Supports English and Arabic characters
        $errors[] = 'First name can only contain letters.';
    }

    // Last Name Validation (Required, Min 2 chars, letters only)
    if (empty($lname)) {
        $errors[] = 'Last name is required.';
    } elseif (strlen($lname) < 2) {
        $errors[] = 'Last name must be at least 2 characters.';
    } elseif (!preg_match("/^[a-zA-Z\s\x{0600}-\x{06FF}]+$/u", $lname)) {
        $errors[] = 'Last name can only contain letters.';
    }

    // Email Validation (Required, proper format)
    if (empty($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    // Saudi Mobile pattern validation
    $phone_regex = "/^(05|5|\+9665|009665)[0-9]{8}$/";
    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    } elseif (!preg_match($phone_regex, $phone)) {
        $errors[] = 'Invalid Saudi mobile number format.';
    }

    // 3. Return errors if validation fails
    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'validation_failed',
            'errors' => $errors // Passing the full list back to JavaScript
        ]);
        exit();
    }

    try {
        // 4. Update Database
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fname, $lname, $email, $phone, $user_id]);

        // Refresh Session memory
        $_SESSION['user_fname'] = $fname;
        $_SESSION['user_lname'] = $lname;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_phone'] = $phone;

        // Return success and the updated data
        echo json_encode([
            'status' => 'success',
            'message' => 'updated',
            'data' => [
                'fName' => $fname,
                'lName' => $lname,
                'email' => $email,
                'phone' => $phone
            ]
        ]);
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['status' => 'error', 'message' => 'exists']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'server_error']);
        }
        exit();
    }
}
?>