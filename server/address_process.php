<?php 
session_start();
require_once('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $action  = $_POST['action'] ?? null;
    $address_id = $_POST['address_id'] ?? null;

    header('Content-Type: application/json');

    // Capture and Clean the data for addresses
    $label    = trim($_POST['label'] ?? '');
    $name     = trim($_POST['full_name'] ?? '');
    $address  = trim($_POST['address_line'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    switch ($action) {
        case 'add':
        case 'edit':
            // 1. Array to hold validation error messages
            $errors = [];

            // Label validations (Min 2, Max 20)
            if (empty($label)) {
                $errors[] = "Label field is required.";
            } elseif (strlen($label) < 2 || strlen($label) > 20) {
                $errors[] = "Label must be between 2 and 20 characters.";
            }

            // Full Name validations (Min 6, Max 50, Alphabetic regex match)
            if (empty($name)) {
                $errors[] = "Full name is required.";
            } elseif (strlen($name) < 6 || strlen($name) > 50) {
                $errors[] = "Full name must be between 6 and 50 characters.";
            } elseif (!preg_match("/^[A-Za-z\s'-]+$/", $name)) {
                $errors[] = "Please enter a valid name (letters only).";
            }

            // Address Line validations (Min 5, Max 100)
            if (empty($address)) {
                $errors[] = "Address line is required.";
            } elseif (strlen($address) < 5 || strlen($address) > 100) {
                $errors[] = "Address line must be between 5 and 100 characters.";
            }

            // City validations (Letters and spaces only)
            if (empty($city)) {
                $errors[] = "City is required.";
            } elseif (!preg_match("/^[A-Za-z\s]+$/", $city)) {
                $errors[] = "City can only contain letters.";
            }

            // Province validations (Letters and spaces only)
            if (empty($province)) {
                $errors[] = "Province is required.";
            } elseif (!preg_match("/^[A-Za-z\s]+$/", $province)) {
                $errors[] = "Province can only contain letters.";
            }

            // If action is edit, make sure we have a valid address ID
            if ($action === 'edit' && empty($address_id)) {
                $errors[] = "Missing address identifier record.";
            }

            // 2. Return Validation Errors to Toast early if found
            if (!empty($errors)) {
                echo json_encode([
                    'status' => 'validation_failed',
                    'errors' => $errors
                ]);
                exit;
            }

            // 3. Proceed with Database Operations if Validation Passes
            if ($action === 'add') {
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
            } else {
                // EDIT ACTION RUNS HERE
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
                    
                    echo json_encode([
                        'status' => $success ? 'success' : 'notEdited',
                        'data' => [
                            'id' => $address_id,
                            'label' => $label,
                            'full_name' => $name,
                            'address_line' => $address,
                            'city' => $city,
                            'province' => $province,
                            'is_default' => $is_default
                        ]
                    ]);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    echo json_encode(['status' => 'notEdited']);
                }
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
            $errors = [];

            // 1. Capture parameters sent by JavaScript and HTML Form
            $holder     = trim($_POST['cardholder_name'] ?? '');
            $raw_card   = trim($_POST['card_number'] ?? ''); // Captures raw card string
            $exp_m      = intval($_POST['exp_month'] ?? 0);
            $exp_y      = intval($_POST['exp_year'] ?? 0);
            $cvv        = trim($_POST['cvv'] ?? '');
            $is_def_pay = isset($_POST['is_default']) ? 1 : 0;

            // Strip spaces out to validate digits correctly
            $clean_card = str_replace(' ', '', $raw_card);

            // 2. SERVER VALIDATIONS (Matches HTML attributes)
            if (strlen($holder) < 3 || !preg_match("/^[A-Za-z\s'-]+$/", $holder)) {
                $errors[] = "Please enter a valid cardholder name.";
            }

            if (!preg_match("/^[0-9]{16}$/", $clean_card)) {
                $errors[] = "Card number must be exactly 16 digits.";
            }

            if ($exp_m < 1 || $exp_m > 12) {
                $errors[] = "Expiry month must be between 01 and 12.";
            }

            // Expiry Year Check (Enforces current year context bounds)
            if ($exp_y < 2026) { 
                $errors[] = "Expiry year cannot be in the past.";
            } elseif ($exp_y == 2026 && $exp_m < intval(date('n'))) {
                $errors[] = "This card has already expired.";
            }

            if (!preg_match("/^[0-9]{3}$/", $cvv)) {
                $errors[] = "CVV code must be exactly 3 digits.";
            }

            // If validation fails, return errors to Toast immediately
            if (!empty($errors)) {
                echo json_encode([
                    'status' => 'validation_failed',
                    'errors' => $errors
                ]);
                break;
            }

            // 3. GENERATE RELIABLE METADATA FOR DATABASE
            $last4 = substr($clean_card, -4); // Always extracts the exact final 4 digits cleanly
            
            // Determine Brand reliably
            $card_brand = 'Visa';
            if (str_starts_with($clean_card, '5')) {
                $card_brand = 'Mastercard';
            }

            // 4. Default Payment Strategy Manager
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_payments WHERE user_id = ?");
            $countStmt->execute([$user_id]);
            if ($countStmt->fetchColumn() == 0) {
                $is_def_pay = 1;
            } elseif ($is_def_pay) {
                $pdo->prepare("UPDATE user_payments SET is_default = 0 WHERE user_id = ?")->execute([$user_id]);
            }

            // 5. Store data safely (Do NOT store full card or CVV!)
            $sql = "INSERT INTO user_payments (user_id, card_brand, last4, expiry_month, expiry_year, cardholder_name, is_default) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([$user_id, $card_brand, $last4, $exp_m, $exp_y, $holder, $is_def_pay]);

            // 6. Return response back to your JS createPaymentCardHTML function
            echo json_encode([
                'status' => $success ? 'success' : 'notAdded',
                'data' => [
                    'id' => $pdo->lastInsertId(),
                    'card_brand' => $card_brand,
                    'last4' => $last4,
                    'expiry_month' => sprintf("%02d", $exp_m), // Formats month safely to 2 digits (e.g. 5 -> 05)
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