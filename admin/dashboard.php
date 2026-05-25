<?php 
require_once('../parts/auth_admin.php'); 
require_once('../config.php');
require_once('../server/db_connect.php');

// Fetch all plants for the management table
$stmt = $pdo->query("SELECT * FROM plants ORDER BY id DESC");
$plants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard | Little Leaf</title>
    <link rel="stylesheet" href="../global/main.css" />
    <link rel="stylesheet" href="../css/admin.css" />
</head>
<body>
    <div class="container fill-container scroll-page">
        <?php include('../parts/nav.php'); ?>
        <main>
        <div class="page-header">
            <h2>Sanctuary Management</h2>
            <p>Add, edit, or remove botanical companions from the collection.</p>
        </div>

        <section class="admin-section">
            <div class="admin-actions">
                <button class="main-btn" onclick="openAddPlantModal()">+ Add New Plant</button>
            </div>

            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
               <tbody id="admin-plant-list">
                    <?php foreach ($plants as $plant): ?>
                    <tr data-id="<?= $plant['id'] ?>">
                        <td data-label="Image"><img src="../images/products/<?= $plant['image'] ?>" width="50"></td>
                        <td data-label="Name"><?= htmlspecialchars($plant['name']) ?></td>
                        <td data-label="Category"><?= htmlspecialchars($plant['category']) ?></td>
                        <td data-label="Price"><?= $plant['price'] ?> SAR</td>
                        <td data-label="Quantity"><?= $plant['stock_quantity'] ?></td>
                        <td data-label="Actions">
                            <button class="text-btn" onclick='editPlant(<?= json_encode($plant) ?>)'>Edit</button>
                            <button class="text-btn danger" onclick="deletePlant(<?= $plant['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
                    </div>
</main>
    <!-- Modal for adding/editing plants (Required for File Upload later) -->
    <?php include('../parts/admin_plant_modal.php'); ?>
     <!-- toast -->
     <?php require_once(BASE_PATH . 'parts/toast.php'); ?>
    
    <script src="../scripts/admin_manage.js"></script>
</body>
</html>