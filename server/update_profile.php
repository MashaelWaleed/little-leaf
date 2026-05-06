<?php 
session_start();
require_once('../config.php');
require_once('db_connect.php');

// Set header for JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $fname   = $_POST['fName'];
    $lname   = $_POST['lName'];
    $email   = $_POST['email'];
    $phone   = $_POST['phone'];

    // Saudi Mobile pattern validation
    $phone_regex = "/^(05|5|\+9665|009665)[0-9]{8}$/";

    if (!preg_match($phone_regex, $phone)) {
        echo json_encode(['status' => 'error', 'message' => 'invalid_phone']);
        exit();
    }

    try {
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
