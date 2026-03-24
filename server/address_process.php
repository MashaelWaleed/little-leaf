<?php
session_start();
require_once('db_connect.php');
//this file process address and payment 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $action  = $_POST['action'] ?? null;
    $address_id = $_POST['address_id'] ?? null;

    header('Content-Type: application/json');

    // 1. Capture the data ONCE here (used by 'add' and 'edit')
    // We use ?? null so the 'delete' action doesn't crash 
    $label    = $_POST['label'] ?? null;
    $name     = $_POST['full_name'] ?? null;
    $address  = $_POST['address_line'] ?? null;
    $city     = $_POST['city'] ?? null;
    $province = $_POST['province'] ?? null;
    $is_default=$_POST['is_default'] ?? 0;

    switch ($action) {
        case 'add':
            // --- START OF NEW LOGIC ---
            // 1. Check if the user already has any addresses
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_addresses WHERE user_id = ?");
            $countStmt->execute([$user_id]);
            $hasAddresses = $countStmt->fetchColumn() > 0;

            // 2. If they have none, this one MUST be default
            if (!$hasAddresses) {
                $is_default = 1; 
            } else {
                // If they already have addresses, respect the checkbox from the form
                $is_default = isset($_POST['is_default']) ? 1 : 0;
    }

            $sql = "INSERT INTO user_addresses (user_id, label, full_name, address_line, city, province, is_default) 
                    VALUES (?, ?, ?, ?, ?, ?,?)";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([$user_id, $label, $name, $address, $city, $province, $is_default]);
            echo json_encode(['status' => $success ? 'success' : 'notAdded']);//sendback to js
            break;


        case 'edit':
            try {
            $pdo->beginTransaction();

            // If the user wants THIS address to be default, unset the old one first
            if ($is_default) {
                $updateDefaults = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
                $updateDefaults->execute([$user_id]);
            }

            $sql = "UPDATE user_addresses SET label=?, full_name=?, address_line=?, city=?, province=?, is_default=? 
                    WHERE id=? AND user_id=?";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([$label, $name, $address, $city, $province, $is_default, $address_id, $user_id]);

            $pdo->commit();
            echo json_encode(['status' => 'success']);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['status' => 'notEdited']);
            }
            break;


        case 'delete':
        // 1. Check if the address being deleted IS the default one
        $checkStmt = $pdo->prepare("SELECT is_default FROM user_addresses WHERE id = ?");
        $checkStmt->execute([$address_id]);
        $wasDefault = $checkStmt->fetchColumn();

        // 2. Perform the deletion and capture the result in $success
        $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
        $success = $stmt->execute([$address_id, $user_id]);

        // 3. If we successfully deleted the default, pick a new one
        if ($success && $wasDefault) {
            $pickNew = $pdo->prepare("
                UPDATE user_addresses 
                SET is_default = 1 
                WHERE user_id = ? 
                ORDER BY created_at ASC 
                LIMIT 1
            ");
            $pickNew->execute([$user_id]);
        }

        // 4. Always use $success to report the status back to JavaScript
        echo json_encode(['status' => $success ? 'success' : 'notDeleted']);
        break;
        
        case 'add_payment':
            // 0. Capture variables from POST first!
            $card_brand = $_POST['card_brand'] ?? 'Visa';
            $last4 = $_POST['last4'] ?? '0000';
            $expiry_month = $_POST['exp_month'] ?? 0;
            $expiry_year = $_POST['exp_year'] ?? 0;
            $cardholder_name = $_POST['cardholder_name'] ?? '';

            // 1. Check if the user already has any cards
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_payments WHERE user_id = ?");
            $countStmt->execute([$user_id]);
            $hascards = $countStmt->fetchColumn() > 0;

            // 2. Default logic
            if (!$hascards) {
                $is_default = 1; 
            } else {
                $is_default = isset($_POST['is_default']) ? 1 : 0;
                
                // IF this new card is set as default, we must unset the old default
                if ($is_default == 1) {
                    $update = $pdo->prepare("UPDATE user_payments SET is_default = 0 WHERE user_id = ?");
                    $update->execute([$user_id]);
                }
            }

            // 3. CORRECTED TABLE NAME: user_payments
            $sql = "INSERT INTO user_payments (user_id, card_brand, last4, expiry_month, expiry_year, cardholder_name, is_default) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                $user_id, 
                $card_brand, 
                $last4, 
                $expiry_month, 
                $expiry_year, 
                $cardholder_name, 
                $is_default
            ]);

            echo json_encode(['status' => $success ? 'success' : 'notAdded']);
            break;
            
            case'deleteCard':
             $card_id= $_POST['card_id'] ?? null; 
            // 1. Check if the card being deleted IS the default one
            $checkStmt = $pdo->prepare("SELECT is_default FROM user_payments WHERE id = ?");
            $checkStmt->execute([$card_id]);
            $wasDefault = $checkStmt->fetchColumn();

            // 2. Perform the deletion and capture the result in $success
            $stmt = $pdo->prepare("DELETE FROM user_payments WHERE id = ? AND user_id = ?");
            $success = $stmt->execute([$card_id, $user_id]);

            // 3. If we successfully deleted the default, pick a new one
            if ($success && $wasDefault) {
                $pickNew = $pdo->prepare("
                    UPDATE user_payments 
                    SET is_default = 1 
                    WHERE user_id = ? 
                    ORDER BY created_at ASC 
                    LIMIT 1
                ");
                $pickNew->execute([$user_id]);
            }

            // 4. Always use $success to report the status back to JavaScript
            echo json_encode(['status' => $success ? 'success' : 'notDeleted']);
            break;

    }
    exit;
}