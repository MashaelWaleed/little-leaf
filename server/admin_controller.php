<?php
session_start();
require_once('db_connect.php');

// 1. Authentication & Role-Based Access Control (7 Marks)
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access blocked.']);
    exit;
}

header('Content-Type: application/json');
$action = $_POST['action'] ?? null;

try {
    switch ($action) {
        case 'add_plant':
        case 'edit_plant':
            $name = $_POST['name'];
            $category = $_POST['category'];
            $price = $_POST['price'];
            $plant_id = $_POST['plant_id'] ?? null;
            $image_name = null;
            $stock = $_POST['stock_quantity'] ?? 0; // Capture the new field

            // --- 2. File Upload System (6 Marks) ---
            if (isset($_FILES['plant_image']) && $_FILES['plant_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['plant_image'];
                $file_size = $file['size'];
                $file_tmp = $file['tmp_name'];
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                // Validate File Type [cite: 36]
                $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
                if (!in_array($file_ext, $allowed)) {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, and PDF allowed.']);
                    exit;
                }

                // Validate File Size (Limit: 2MB) [cite: 36]
                if ($file_size > 2 * 1024 * 1024) {
                    echo json_encode(['status' => 'error', 'message' => 'File too large. Maximum size is 2MB.']);
                    exit;
                }

                // Security: Unique naming [cite: 35, 36]
                $image_name = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = '../images/products/' . $image_name;

                if (!move_uploaded_file($file_tmp, $upload_path)) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
                    exit;
                }
            }

            // --- 3. Database Operations (4 Marks) --- 
            if ($action === 'add_plant') {
               $sql = "INSERT INTO plants (name, category, price, image, stock_quantity) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $category, $price, $image_name ?? 'default.png', $stock]);
                
                $final_id = $pdo->lastInsertId();
                $final_image = $image_name ?? 'default.png';
            } else {
                // EDIT LOGIC
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

            // Return full JSON response for AJAX UI update [cite: 28, 32]
            echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => $final_id,
                'name' => $name,
                'category' => $category,
                'price' => $price,
                'image' => $final_image,
                'stock_quantity' => $stock // Send it back to JS
            ]
        ]);
            break;

        case 'delete_plant':
            $plant_id = $_POST['plant_id'];
            $stmt = $pdo->prepare("DELETE FROM plants WHERE id = ?");
            $success = $stmt->execute([$plant_id]);
            echo json_encode(['status' => $success ? 'success' : 'error']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid Action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}