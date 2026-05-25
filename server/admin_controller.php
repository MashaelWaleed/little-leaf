<?php
session_start();

// 1. Set Header immediately before ANY output
header('Content-Type: application/json');

require_once('db_connect.php');

// 2. Authentication & Role-Based Access Control
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access blocked.']);
    exit;
}

$action = $_POST['action'] ?? null;

try {
    switch ($action) {
        case 'add_plant':
        case 'edit_plant':
            // 1. Collect and Trim data inputs
            $name     = trim($_POST['name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $price    = $_POST['price'] ?? null;
            $plant_id = $_POST['plant_id'] ?? null;
            $stock    = $_POST['stock_quantity'] ?? 0;
            $image_name = null;

            $errors = [];

            // --- 2. ROBUST TEXT & NUMERIC VALIDATIONS ---
            
            if (empty($name)) {
                $errors[] = "Plant name is required.";
            } elseif (strlen($name) < 2 || strlen($name) > 100) {
                $errors[] = "Plant name must be between 2 and 100 characters.";
            }

            if (empty($category)) {
                $errors[] = "Category selection is required.";
            }

            if ($price === null || $price === '') {
                $errors[] = "Price is required.";
            } elseif (!is_numeric($price) || floatval($price) < 0) {
                $errors[] = "Price must be a valid positive number.";
            }

            if (!is_numeric($stock) || intval($stock) < 0) {
                $errors[] = "Stock quantity must be a non-negative whole number.";
            }

            if ($action === 'edit_plant' && empty($plant_id)) {
                $errors[] = "Missing plant ID for update target.";
            }

            // --- 3. SECURED FILE UPLOAD SYSTEM ---
            if (isset($_FILES['plant_image']) && $_FILES['plant_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['plant_image'];
                $file_size = $file['size'];
                $file_tmp = $file['tmp_name'];
                
                // Secure validation: Check actual contents, not just the string extension
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file_tmp);
                finfo_close($finfo);

                $allowed_mimes = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png'
                ];

                if (!array_key_exists($mime_type, $allowed_mimes)) {
                    $errors[] = "Invalid image file type. Only real JPG and PNG images allowed.";
                } else {
                    $file_ext = $allowed_mimes[$mime_type];
                }

                // Validate File Size (Limit: 2MB) 
                if ($file_size > 2 * 1024 * 1024) {
                    $errors[] = "File size is too large. Maximum size allowed is 2MB.";
                }

                // Stop file processing early if text validations or image check failed
                if (empty($errors)) {
                    // Security: Unique name obfuscation
                    $image_name = time() . '_' . uniqid() . '.' . $file_ext;
                    $upload_path = '../images/products/' . $image_name;

                    if (!move_uploaded_file($file_tmp, $upload_path)) {
                        $errors[] = "Failed to save uploaded file to disk.";
                    }
                }
            }

            // If any batch validations failed, bail out and output them to Toasts
            if (!empty($errors)) {
                echo json_encode([
                    'status' => 'validation_failed',
                    'errors' => $errors
                ]);
                exit;
            }

            // --- 4. SECURED DATABASE OPERATIONS --- 
            if ($action === 'add_plant') {
                $sql = "INSERT INTO plants (name, category, price, image, stock_quantity) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $category, floatval($price), $image_name ?? 'default.png', intval($stock)]);
                
                $final_id = $pdo->lastInsertId();
                $final_image = $image_name ?? 'default.png';
            } else {
                // EDIT LOGIC
                if ($image_name) {
                    $sql = "UPDATE plants SET name=?, category=?, price=?, stock_quantity=?, image=? WHERE id=?";
                    $params = [$name, $category, floatval($price), intval($stock), $image_name, $plant_id];
                    $final_image = $image_name;
                } else {
                    // Fetch existing image metadata safely
                    $stmtImg = $pdo->prepare("SELECT image FROM plants WHERE id = ?");
                    $stmtImg->execute([$plant_id]);
                    $final_image = $stmtImg->fetchColumn() ?: 'default.png';

                    $sql = "UPDATE plants SET name=?, category=?, price=?, stock_quantity=? WHERE id=?";
                    $params = [$name, $category, floatval($price), intval($stock), $plant_id];
                }
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $final_id = $plant_id;
            }

            // Return clean JSON response context
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'id' => $final_id,
                    'name' => $name,
                    'category' => $category,
                    'price' => floatval($price),
                    'image' => $final_image,
                    'stock_quantity' => intval($stock)
                ]
            ]);
            exit;

        case 'delete_plant':
            $plant_id = $_POST['plant_id'] ?? null;
            if (!$plant_id) {
                echo json_encode(['status' => 'error', 'message' => 'Missing plant ID.']);
                exit;
            }
            $stmt = $pdo->prepare("DELETE FROM plants WHERE id = ?");
            $success = $stmt->execute([$plant_id]);
            echo json_encode(['status' => $success ? 'success' : 'error']);
            exit;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid Action']);
            exit;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'A system processing error occurred.']);
    exit;
}