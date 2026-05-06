<!-- 
 Mshael Waleed Alshwaf   2212857
 Shahad Mohammed Ebrahim 2212855
 Jumanah Jamal Banabilah 2206898
 BAR/403
 3/10/2026
-->
<?php
session_start();
// Since this is in the /pages/ folder, we go up one level
require_once('../config.php');
require_once('../server/db_connect.php');

// Fetch all 12 plants from database
$stmt = $pdo->query("SELECT * FROM plants");
$plants = $stmt->fetchAll();

// Helper function to assign a day/task based on category or ID
function getSchedule($category, $id) {
    if ($category == 'Succulent') return ['Day' => 'Sunday', 'Task' => 'Light Mist'];
    if ($category == 'Tropical') return ['Day' => 'Tuesday', 'Task' => 'Deep Watering'];
    if ($category == 'Indoor') return ['Day' => 'Wednesday', 'Task' => 'Leaf Cleaning'];
    if ($category == 'Hanging') return ['Day' => 'Friday', 'Task' => 'Pruning'];
    return ['Day' => 'Thursday', 'Task' => 'General Checkup'];
}

// Group plants by care day
$groupedPlants = [];

foreach ($plants as $row) {
    $care = getSchedule($row['category'], $row['id']);
    $day = $care['Day'];

    $row['care'] = $care;
    $groupedPlants[$day][] = $row;
}
?>

<!doctype html>
    <html lang="en">

        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Care Schedule | Little Leaf</title>

        <link rel="stylesheet" href="<?= BASE_URL ?>global/main.css">
        <link rel="stylesheet" href="<?= BASE_URL ?>css/schedule.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
        </head>

    <body>

        <div class="container fill-container scroll-page">
              <!-- HEADER -->
            <?php require_once(BASE_PATH . 'parts/nav.php'); ?>
            <main>
            <div class="page-header">
                <h2>Nurturing Rhythms</h2>
                <p>
                    Your botanical companions flourish with consistent, mindful care.
                    Below is a personalized care guide for our current collection of
                    <?= count($plants) ?> plants.
                </p>           
            </div>

            <table class="schedule-table">
                    <thead>
                        <tr class="table-title">
                            <th colspan="5">Plant Care Details</th>
                        </tr>
                        <tr>
                            <th>Plant Image</th>
                            <th>Plant Name</th>
                            <th>Category</th>
                            <th>Care Day</th>
                            <th>Primary Task</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($groupedPlants as $day => $plantsInDay): ?>
                        <tr class="day-section">
                            <td colspan="5"><strong><?= $day ?> Care Routine</strong></td>
                        </tr>
                            <?php foreach ($plantsInDay as $index => $row): ?>
                            <tr>
                                <td>
                                    <img src="<?= BASE_URL ?>images/products/<?= $row['image'] ?>"
                                    alt="<?= $row['name'] ?>"
                                    style="width:50px;height:50px;object-fit:cover;border-radius:50%;">
                                </td>
                                <td><strong><?= $row['name'] ?></strong></td>
                                <td><?= $row['category'] ?></td>
                                <?php if ($index === 0): ?>
                                    <td rowspan="<?= count($plantsInDay) ?>">
                                    <?= $row['care']['Day'] ?>
                                    </td>
                                    <td rowspan="<?= count($plantsInDay) ?>">
                                    <?= $row['care']['Task'] ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>    
                </main>
                <!-- SIMPLE FOOTER -->
                <?php include("../parts/footer.php")?>    
                                </div>

        <!-- 🔍 Floating Search Overlay -->
        <?php include("../parts/floatingSearch.php")?>

        <script src="<?= BASE_URL ?>scripts/main.js"></script>

    </body>
</html>