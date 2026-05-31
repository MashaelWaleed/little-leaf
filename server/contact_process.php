<?php 
// Force PHP to show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../config.php'); 
include('db_connect.php'); 
// Check if PDO is actually connected
if (!isset($pdo)) {
    die("Database connection variable \$pdo is not defined. Check your config.php.");
}

if (isset($_POST['contact-submit'])) {
    $errors = [];

    $fname = trim($_POST['fName'] ?? '');
    $lname = trim($_POST['lName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg   = trim($_POST['msg'] ?? '');

    if (!preg_match("/^[A-Za-z\s]{2,50}$/", $fname)) {
        $errors[] = "Invalid first name";
    }

    if (!preg_match("/^[A-Za-z\s]{2,50}$/", $lname)) {
        $errors[] = "Invalid last name";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email";
    }

    if (strlen($msg) < 10 || strlen($msg) > 1000) {
        $errors[] = "Message must be between 10 and 1000 characters";
    }

    if (!empty($errors)) {
        header("Location: ../pages/contact.php?error=validation");
        exit();
    }

    $fname = htmlspecialchars($fname);
    $lname = htmlspecialchars($lname);
    $email = htmlspecialchars($email);
    $msg   = htmlspecialchars($msg);
    
    // Get user_id if logged in, otherwise set to NULL
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        // 2. Store in Database
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

        // 3. Send to Email (The simple PHP way)
        $to = "littleleafstore.1@gmail.com"; // store email
        $subject = "New Contact Message from $fname $lname";
        $body = "You received a new message:\n\n" .
                "From: $fname $lname ($email)\n" .
                "Message:\n$msg";
        $headers = "From: webmaster@littleleaf.com";

        //  mail() often requires a live server to work (not local XAMPP)
        mail($to, $subject, $body, $headers);

        // 4. Redirect with success
        header("Location: ../pages/contact.php?status=sent");
        exit();

    } catch (PDOException $e) {
        header("Location: ../pages/contact.php?error=error");
        exit();
    }
}
?>