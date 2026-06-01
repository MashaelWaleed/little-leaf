<?php 
// Force PHP to show errors during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../config.php'); 
include('db_connect.php'); 

// Check if PDO is actually connected
if (!isset($pdo)) {
    echo json_encode(['status' => 'error', 'message' => 'Database configuration issue.']);
    exit();
}

if (isset($_POST['contact-submit'])) {
    // Standardize content output headers as clean JSON format data
    header('Content-Type: application/json');
    $errors = [];

    $fname = trim($_POST['fName'] ?? '');
    $lname = trim($_POST['lName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg   = trim($_POST['msg'] ?? '');

    if (!preg_match("/^[A-Za-z\s]{2,50}$/", $fname)) {
        $errors[] = "Invalid first name layout.";
    }

    if (!preg_match("/^[A-Za-z\s]{2,50}$/", $lname)) {
        $errors[] = "Invalid last name layout.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (strlen($msg) < 10 || strlen($msg) > 1000) {
        $errors[] = "Message must be between 10 and 1000 characters.";
    }

    // Return list of errors natively via AJAX instead of redirection URLs
    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error', 
            'message' => implode(" ", $errors)
        ]);
        exit();
    }

    $fname = htmlspecialchars($fname);
    $lname = htmlspecialchars($lname);
    $email = htmlspecialchars($email);
    $msg   = htmlspecialchars($msg);
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        // Store inside Database
        $sql = "INSERT INTO contact_messages (user_id, first_name, last_name, email, message) 
                VALUES (:user_id, :fname, :lname, :email, :msg)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':fname'   => $fname,
            ':lname'   => $lname,
            ':email'   => $email,
            ':msg'     => $msg
        ]);

        // Send to Email
        $to = "littleleafstore.1@gmail.com"; 
        $subject = "New Contact Message from $fname $lname";
        $body = "You received a new message:\n\n" .
                "From: $fname $lname ($email)\n" .
                "Message:\n$msg";
        $headers = "From: webmaster@littleleaf.com";

        @mail($to, $subject, $body, $headers);

        // Success JSON Payload
        echo json_encode([
            'status' => 'success', 
            'message' => 'Thank you! Your green query message has been submitted successfully.'
        ]);
        exit();

    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Database process error. Please try again later.'
        ]);
        exit();
    }
}
?>