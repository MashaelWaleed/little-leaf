<?php 
// Force PHP to show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../config.php'); 
include('db_connect.php'); 

if (!isset($pdo)) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}

if (isset($_POST['feedback-submit']) && isset($_SESSION['logged_in'])) {
    header('Content-Type: application/json');

    // 1. Collect and sanitize data
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $rating = $_POST['rating'] ?? null;
    $preference = $_POST['preference'] ?? '';
    $comments = htmlspecialchars(trim($_POST['comments'] ?? ''));

    // 2. Security Check
    $reg_email = $_SESSION['user_email']; 
    if ($email !== $reg_email) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Security mismatch: Email field must match your registered account email.'
        ]);
        exit();
    }

    if (empty($rating)) {
        echo json_encode(['status' => 'error', 'message' => 'Please select an experience rating.']);
        exit();
    }

    // 3. Handle Checkboxes
    $services = isset($_POST['service']) ? implode(", ", $_POST['service']) : "None";

    try {
        // 4. The SQL Statement
        $sql = "INSERT INTO feedback (user_id, user_name, email, rating, services, preference, comments) 
                VALUES (:user_id, :name, :email, :rating, :services, :preference, :comments)
                ON DUPLICATE KEY UPDATE 
                    rating     = VALUES(rating),
                    services   = VALUES(services),
                    preference = VALUES(preference),
                    comments   = VALUES(comments), 
                    updated_at = CURDATE()"; 

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id'    => $_SESSION['user_id'],
            ':name'       => $name,
            ':email'      => $email,
            ':rating'     => $rating,
            ':services'   => $services,
            ':preference' => $preference,
            ':comments'   => $comments
        ]);

        // Determine output timestamp flag format match
        // If updating an existing user record entry, badge it with an updated prefix flag
        $isUpdate = $stmt->rowCount() == 2; // PDO returns 2 if an existing row gets updated via ON DUPLICATE KEY
        $formattedDate = ($isUpdate ? 'Updated: ' : '') . date('d/m/Y');

        // 5. Return success data string map to update the list instantly
        echo json_encode([
            'status' => 'success',
            'message' => 'Thank you for your valuable feedback!',
            'user_id' => $_SESSION['user_id'],
            'user_name' => $name,
            'comments' => $comments,
            'date' => $formattedDate
        ]);
        exit();

    } catch (PDOException $e) {
        error_log("Feedback Error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error', 
            'message' => 'An error occurred while saving your feedback to the database.'
        ]);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized submission access or invalid operation request.']);
    exit();
}
?>