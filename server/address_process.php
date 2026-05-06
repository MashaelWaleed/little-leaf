<?php
session_start();
require_once('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $action  = $_POST['action'] ?? null;
    $address_id = $_POST['address_id'] ?? null;

    header('Content-Type: application/json');

    // Capture the data for addresses
    $label    = $_POST['label'] ?? null;
    $name     = $_POST['full_name'] ?? null;
    $address  = $_POST['address_line'] ?? null;
    $city     = $_POST['city'] ?? null;
    $province = $_POST['province'] ?? null;
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    switch ($action) {
        case 'add':
            // Check if this should be default
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_addresses WHERE user_id = ?");
            $countStmt->execute([$user_id]);
            if ($countStmt->fetchColumn() == 0) {
                $is_default = 1; 
            } elseif ($is_default) {
                $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?")->execute([$user_id]);
            }

            $sql = "INSERT INTO user_addresses (user_id, label, full_name, address_line, city, province, is_default) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([$user_id, $label, $name, $address, $city, $province, $is_default]);
            
            // 🆕 RETURN DATA FOR AJAX INJECTION
            echo json_encode([
                'status' => $success ? 'success' : 'notAdded',
                'data' => [
                    'id' => $pdo->lastInsertId(),
                    'label' => $label,
                    'full_name' => $name,
                    'address_line' => $address,
                    'city' => $city,
                    'province' => $province,
                    'is_default' => $is_default
                ]
            ]);
            break;

        case 'edit':
            try {
                $pdo->beginTransaction();
                if ($is_default) {
                    $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?")->execute([$user_id]);
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
            $checkStmt = $pdo->prepare("SELECT is_default FROM user_addresses WHERE id = ?");
            $checkStmt->execute([$address_id]);
            $wasDefault = $checkStmt->fetchColumn();
            
            $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
            $success = $stmt->execute([$address_id, $user_id]);

            if ($success && $wasDefault) {
                $pdo->prepare("UPDATE user_addresses SET is_default = 1 WHERE user_id = ? ORDER BY id ASC LIMIT 1")->execute([$user_id]);
            }
            echo json_encode(['status' => $success ? 'success' : 'notDeleted']);
            break;
            
        case 'add_payment':
            $card_brand = $_POST['card_brand'] ?? 'Visa';
            $last4 = $_POST['last4'] ?? '0000';
            $exp_m = $_POST['exp_month'] ?? 0;
            $exp_y = $_POST['exp_year'] ?? 0;
            $holder = $_POST['cardholder_name'] ?? '';
            $is_def_pay = isset($_POST['is_default']) ? 1 : 0;

            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_payments WHERE user_id = ?");
            $countStmt->execute([$user_id]);
            if ($countStmt->fetchColumn() == 0) {
                $is_def_pay = 1;
            } elseif ($is_def_pay) {
                $pdo->prepare("UPDATE user_payments SET is_default = 0 WHERE user_id = ?")->execute([$user_id]);
            }

            $sql = "INSERT INTO user_payments (user_id, card_brand, last4, expiry_month, expiry_year, cardholder_name, is_default) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([$user_id, $card_brand, $last4, $exp_m, $exp_y, $holder, $is_def_pay]);

            // 🆕 RETURN DATA FOR AJAX INJECTION
            echo json_encode([
                'status' => $success ? 'success' : 'notAdded',
                'data' => [
                    'id' => $pdo->lastInsertId(),
                    'card_brand' => $card_brand,
                    'last4' => $last4,
                    'expiry_month' => $exp_m,
                    'expiry_year' => $exp_y,
                    'cardholder_name' => $holder,
                    'is_default' => $is_def_pay
                ]
            ]);
            break;

        case 'deleteCard':
            $card_id = $_POST['card_id'] ?? null; 
            $checkStmt = $pdo->prepare("SELECT is_default FROM user_payments WHERE id = ?");
            $checkStmt->execute([$card_id]);
            $wasDefault = $checkStmt->fetchColumn();

            $stmt = $pdo->prepare("DELETE FROM user_payments WHERE id = ? AND user_id = ?");
            $success = $stmt->execute([$card_id, $user_id]);

            if ($success && $wasDefault) {
                $pdo->prepare("UPDATE user_payments SET is_default = 1 WHERE user_id = ? ORDER BY id ASC LIMIT 1")->execute([$user_id]);
            }
            echo json_encode(['status' => $success ? 'success' : 'notDeleted']);
            break;
    }
    exit;
}