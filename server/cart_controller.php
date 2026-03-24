<?php
session_start();
require_once('db_connect.php'); 

$user_id = $_SESSION['user_id'] ?? null; 
header('Content-Type: application/json');

$id = $_POST['plant_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$id || !$action) {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    exit;
}

if ($user_id) {
    // --- LOGGED IN: DATABASE LOGIC ---
    switch ($action) {
        case 'add':
        case 'increase':
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, plant_id, quantity) 
                                   VALUES (?, ?, 1) 
                                   ON DUPLICATE KEY UPDATE quantity = quantity + 1");
            $stmt->execute([$user_id, $id]); // Fixed variable name
            break;

        case 'decrease':
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity - 1 
                                   WHERE user_id = ? AND plant_id = ? AND quantity > 1");
            $stmt->execute([$user_id, $id]);
            break;

        case 'remove':
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND plant_id = ?");
            $stmt->execute([$user_id, $id]);
            break;
    }

    // IMPORTANT: Fetch the new quantity and total from DB to send back to JS
    $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND plant_id = ?");
    $stmt->execute([$user_id, $id]);
    $row = $stmt->fetch();
    $new_qty = $row ? $row['quantity'] : 0;
    $stmtTotal = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmtTotal->execute([$user_id]);
    $total_items = $stmtTotal->fetch()['total'] ?? 0;

} else {
    // --- GUEST: SESSION LOGIC ---
    //If the user is new and doesn't have a basket yet, we give them an empty one ([])
    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

    switch ($action) {
        case 'add':
        case 'increase':
            //This line is a shortcut. It says: "Look at the current quantity for this plant. If it’s not there, assume it's 0. Now add 1 to it."
            $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
            break;
        case 'decrease':
            if (isset($_SESSION['cart'][$id]) && $_SESSION['cart'][$id] > 1) {
                $_SESSION['cart'][$id]--;
            }
            break;
        case 'remove':
            //It deletes that specific ID from the list entirely.
            unset($_SESSION['cart'][$id]);
            break;
    }
    $new_qty = $_SESSION['cart'][$id] ?? 0;
    $total_items = array_sum($_SESSION['cart']);
}

// NOW THIS ECHO USES THE CORRECT VARIABLES for each item
echo json_encode([
    'status' => 'success',
    'new_qty' => $new_qty,
    'total_items' => $total_items
]);
exit;