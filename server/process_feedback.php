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


if (isset($_POST['feedback-submit']) && isset($_SESSION['logged_in'])) {
    // 1. Collect and sanitize data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars(trim($_POST['email']));
    $rating = $_POST['rating'] ?? null;
    $preference = $_POST['preference'];
    $comments = htmlspecialchars($_POST['comments']);

    // 2. Security Check: Does the email match their account?
    $reg_email = $_SESSION['user_email']; // The email they registered with
    if ($email !== $reg_email) {
        // Redirect with a specific error status
        header("Location: " . BASE_URL . "pages/feedback.php?error=email_mismatch");
        exit();

    }

    // 3. Handle Checkboxes (Array to String)
    // Since users can pick multiple services, we join them with a comma
    $services = isset($_POST['service']) ? implode(", ", $_POST['service']) : "None";

    try {
    // 4. The SQL Statement
    // We use VALUES(column_name) to refer to the value we tried to insert
    $sql = "INSERT INTO feedback (user_id, user_name, email, rating, services, preference, comments) 
            VALUES (:user_id, :name, :email, :rating, :services, :preference, :comments)
            ON DUPLICATE KEY UPDATE 
                rating     = VALUES(rating),
                services   = VALUES(services),
                preference = VALUES(preference),
                comments   = VALUES(comments), 
                updated_at = CURDATE()"; 

    $stmt = $pdo->prepare($sql);

    // 5. Execute with bound parameters (prevents SQL injection)
    $stmt->execute([
        ':user_id'    => $_SESSION['user_id'],
        ':name'       => $name,
        ':email'      => $email,
        ':rating'     => $rating,
        ':services'   => $services,
        ':preference' => $preference,
        ':comments'   => $comments
    ]);
     // 6. Success! Refresh to show the new comment (or use a success flag)
    header("Location: " . BASE_URL . "pages/feedback.php?status=success");
    exit();

    } catch (PDOException $e) {
        // Log the error for yourself, but show a clean message to the user
        error_log("Feedback Error: " . $e->getMessage());
        header("Location: " . BASE_URL . "pages/feedback.php?error=error");
        exit();
    }
}
?>