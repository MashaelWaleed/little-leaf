<?php
// 1. MUST BE FIRST
session_start();

// 2. Turn on error reporting (Temporary for debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 3. 
require_once('../config.php');
require_once('db_connect.php');

// 4. Match the button name or just check the request method
if (isset($_POST['login_submit'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Find the user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    

    if ($user && password_verify($password, $user['password'])) {
        // SUCCESS
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_fname'] = $user['first_name'];
        $_SESSION['user_lname'] = $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['logged_in'] = true;
        $_SESSION['role']=$user['role'];

        //if a user adds plants as a guest and then logs in
        //we should "sync" those items to the database immediately
        // after they successfully log in.
        // After password is verified and $user_id is set:
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $p_id => $qty) {
                // This query adds the guest quantity to the existing database quantity
                $syncStmt = $pdo->prepare("
                    INSERT INTO cart (user_id, plant_id, quantity) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE quantity = quantity + ?
                ");
                $syncStmt->execute([$user['id'], $p_id, $qty, $qty]);
            }
            // Clear the session cart now that it's safe in the DB
            unset($_SESSION['cart']); 
        }

        header("Location: " . BASE_URL . "index.php");
        exit();
    } else {
        // FAIL: Redirect back to login with error
        header("Location: ../pages/login.php?error=invalid");
        exit();
    }
}
?>