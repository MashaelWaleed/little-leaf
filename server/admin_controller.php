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
            $name = $_POST['name'] ?? null;
            $category = $_POST['category'] ?? null;
            $price = $_POST['price'] ?? null;
            $plant_id = $_POST['plant_id'] ?? null;
            $image_name = null;
            $stock = $_POST['stock_quantity'] ?? 0;

            // Simple validation to ensure data exists
            if (!$name || !$category || !$price) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
                exit;
            }

            // --- File Upload System ---
            if (isset($_FILES['plant_image']) && $_FILES['plant_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['plant_image'];
                $file_size = $file['size'];
                $file_tmp = $file['tmp_name'];
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                // Validate File Type
                $allowed = ['jpg', 'jpeg', 'png'];
                if (!in_array($file_ext, $allowed)) {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG and PNG allowed.']);
                    exit;
                }

                // Validate File Size (Limit: 2MB) 
                if ($file_size > 2 * 1024 * 1024) {
                    echo json_encode(['status' => 'error', 'message' => 'File too large. Maximum size is 2MB.']);
                    exit;
                }

                // Security: Unique naming 
                $image_name = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = '../images/products/' . $image_name;

                if (!move_uploaded_file($file_tmp, $upload_path)) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
                    exit;
                }
            }

            // --- Database Operations --- 
            if ($action === 'add_plant') {
                $sql = "INSERT INTO plants (name, category, price, image, stock_quantity) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $category, $price, $image_name ?? 'default.png', $stock]);
                
                $final_id = $pdo->lastInsertId();
                $final_image = $image_name ?? 'default.png';
            } else {
                // EDIT LOGIC
                if (!$plant_id) {
                    echo json_encode(['status' => 'error', 'message' => 'Missing plant ID for update.']);
                    exit;
                }

                if ($image_name) {
                    $sql = "UPDATE plants SET name=?, category=?, price=?, stock_quantity=?, image=? WHERE id=?";
                    $params = [$name, $category, $price, $stock, $image_name, $plant_id];
                    $final_image = $image_name;
                } else {
                    // Fetch existing image if no new one is uploaded
                    $stmtImg = $pdo->prepare("SELECT image FROM plants WHERE id = ?");
                    $stmtImg->execute([$plant_id]);
                    $final_image = $stmtImg->fetchColumn();

                    $sql = "UPDATE plants SET name=?, category=?, price=?, stock_quantity=? WHERE id=?";
                    $params = [$name, $category, $price, $stock, $plant_id];
                }
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $final_id = $plant_id;
            }

            // Return full JSON response for AJAX UI update 
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'id' => $final_id,
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                    'image' => $final_image,
                    'stock_quantity' => $stock
                ]
            ]);
            exit; // Use exit instead of break to stop further execution cleanly

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
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}