<?php
// 1. MUST BE FIRST
session_start();

// 2. Turn on error reporting (Temporary for debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('../config.php');
require_once('db_connect.php');


if (isset($_POST['register_submit'])) {
    $errors = [];

    $fname = trim($_POST['fName'] ?? '');
    $lname = trim($_POST['lName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $raw_password = $_POST['password'] ?? '';

    if (!preg_match("/^[A-Za-z]{2,30}$/", $fname)) {
        $errors[] = "Invalid first name";
    }

    if (!preg_match("/^[A-Za-z]{2,30}$/", $lname)) {
        $errors[] = "Invalid last name";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email";
    }

    if (strlen($raw_password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }

    if (!empty($errors)) {
        header("Location: ../pages/login.php?error=validation");
        exit();
    }

    // Create the secure hash
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

      // 🎯 Check unique email here:
    try {
        $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fname, $lname, $email, $hashed_password]);

        // Success: Redirect to login
        header("Location: ../pages/login.php?success=registered");
        exit();

    } catch (PDOException $e) {
    // 1. Determine the specific message to send
    if ($e->getCode() == 23000) {
        $msg = "exists"; // Keep it simple for duplicate emails
    } else {
        // Send the real error message for other issues (like a missing column)
        $msg = $e->getMessage();
    }

    // 2. Redirect with the message safely encoded in the URL
    header("Location: ../pages/login.php?error=" . urlencode($msg));
    exit();
}
}
?>  
