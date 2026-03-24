<?php
session_start();
include_once("../config.php");
if (!isset($_SESSION['logged_in'])) { header("Location: ".BASE_URL."pages/login.php"); exit(); }

include_once('db_connect.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    try {
        $pdo->beginTransaction();
        // 1.Fetch item from card db to ensure single source of truth
        $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.price ,c.quantity 
        FROM plants p 
        JOIN cart c ON p.id = c.plant_id 
        WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_products = $stmt->fetchAll();

        // Calculate total manually for security
        $real_total = 0;
        foreach ($cart_products as $item) {
            $real_total += $item['price'] * $item['quantity'];
        }

         // 2. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'Processing')");
        $stmt->execute([$_SESSION['user_id'],$real_total]); //  do not take total from session
        $orderId = $pdo->lastInsertId();

        // 3. Insert Order Items
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, plant_id, product_name, quantity, price_at_purchase) VALUES (?,?, ?, ?, ?)");
        foreach ($cart_products as $product) {
            $itemStmt->execute([$orderId, $product['id'] ,$product['name'], $product['quantity'], $product['price']]);
        }

        // 4. CLEAR THE DATABASE CART (Crucial!)
        $clearStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearStmt->execute([$_SESSION['user_id']]);

        $pdo->commit();
        // 5.redirect
        header("Location: " . BASE_URL  . "pages/profile.php?status=order");
        exit; // Always exit after a header redirect

    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: " . BASE_URL . "pages/cart.php?error=fail_order");
    }
}