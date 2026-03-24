<?php 
// 1. MUST BE FIRST
session_start();

// 2. Turn on error reporting (Temporary for debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('../config.php');
require_once('db_connect.php');

if (isset($_POST['update_profile'])) {
    // 1. Get the phone number from the form
    $phone = $_POST['phone'];
    // 2. Define the Saudi Mobile pattern
    // Allows: 05, 5, +9665 followed by 8 digits
    $phone_regex = "/^(05|5|\+9665|009665)[0-9]{8}$/";

    if (!preg_match($phone_regex, $phone)) {
        // If it doesn't match, send back an error to trigger Global Toast
        header("Location: ../pages/profile.php?error=invalid_phone");
        exit();
    }

    // 3. If it passes, proceed SQL UPDATE
        $user_id = $_SESSION['user_id'];
        $fname   = $_POST['fName'];
        $lname   = $_POST['lName'];
        $email   = $_POST['email']; //uniquness will be checked by db
   

    try {
        // 3. Update the Database with the phone column
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fname, $lname, $email, $phone, $user_id]);

        // 4. Refresh the Session memory
        $_SESSION['user_fname'] = $fname;
        $_SESSION['user_lname'] = $lname;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_phone'] = $phone; // Sync the session

        header("Location: ../pages/profile.php?status=updated");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            header("Location: ../pages/profile.php?error=exists");
        } else {
            // This will tell exactly what went wrong if it fails again
            header("Location: ../pages/profile.php?error=" . urlencode($e->getMessage()));
        }
        exit();
    }
}
?>
