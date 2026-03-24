<?php
// 1. connect to db
 include('db_connect.php'); 
// 2. Prepare the query to get orders and a summary of items
// GROUP_CONCAT joins all item names into one string separated by a comma
$sql = "SELECT 
            o.id, 
            o.status, 
            o.created_at, 
            o.total_price,
            GROUP_CONCAT(CONCAT(oi.quantity, 'x ', oi.product_name) SEPARATOR ', ') AS items_summary
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = :user_id
        GROUP BY o.id
        ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);




$addresses = []; // Initialize as empty array

 // 3. Query the database for address
 // We order by is_default DESC so the "Default" one shows up first in the list
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);   
 
    
$payments=[]; // Initialize as empty array
// 4. Query the database for cards
 // We order by is_default DESC so the "Default" one shows up first in the list
    $stmt = $pdo->prepare("SELECT * FROM user_payments WHERE user_id = ? ORDER BY is_default DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);   
?>