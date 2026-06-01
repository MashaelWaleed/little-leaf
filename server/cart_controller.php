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
            $stmt->execute([$user_id, $id]);
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

    // Fetch the new quantity for the specific item
    $stmtItem = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND plant_id = ?");
    $stmtItem->execute([$user_id, $id]);
    $itemRow = $stmtItem->fetch();
    $new_qty = $itemRow ? $itemRow['quantity'] : 0;

    // Calculate the NEW Subtotal for the entire cart from DB
    $stmtSum = $pdo->prepare("SELECT SUM(c.quantity * p.price) as subtotal 
                              FROM cart c JOIN plants p ON c.plant_id = p.id 
                              WHERE c.user_id = ?");
    $stmtSum->execute([$user_id]);
    $new_subtotal = $stmtSum->fetch()['subtotal'] ?? 0;

} else {
    // --- GUEST: SESSION LOGIC ---
    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

    switch ($action) {
        case 'add':
        case 'increase':
            $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
            break;
        case 'decrease':
            if (isset($_SESSION['cart'][$id]) && $_SESSION['cart'][$id] > 1) {
                $_SESSION['cart'][$id]--;
            }
            break;
        case 'remove':
            unset($_SESSION['cart'][$id]);
            break;
    }
    
    $new_qty = $_SESSION['cart'][$id] ?? 0;

    // Calculate the NEW Subtotal for Guest from Session
    $new_subtotal = 0;
    if (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmtPrice = $pdo->prepare("SELECT id, price FROM plants WHERE id IN ($placeholders)");
        $stmtPrice->execute($ids);
        while ($row = $stmtPrice->fetch()) {
            $new_subtotal += $row['price'] * $_SESSION['cart'][$row['id']];
        }
    }
}

// Shipping logic 
$shipping = 0; 
$new_total = $new_subtotal + $shipping;

// Return JSON for Assignment 2 Requirements
echo json_encode([
    'status' => 'success',
    'new_qty' => $new_qty,
    'new_subtotal' => number_format($new_subtotal),
    'new_total' => number_format($new_total)
]);
exit;