<?php
include('../config.php'); 
include('db_connect.php'); 

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 5;

// We select both date columns
$stmt = $pdo->prepare("SELECT * FROM feedback ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$feedback = $stmt->fetchAll();

if ($feedback) {
    foreach ($feedback as $row) {
        ?>
        <div class="comment-card">
            <h1 class="comment-sender"><span> From </span><?php echo htmlspecialchars($row['user_name']); ?></h1>
            <p class="comment-content"><?php echo htmlspecialchars($row['comments']); ?></p>
            
            <span>
                <?php 
                // LOGIC: Check if updated_at is set and valid
                if (!empty($row['updated_at']) && $row['updated_at'] !== '0000-00-00') {
                    echo 'Updated: ' . date('d/m/Y', strtotime($row['updated_at']));
                } else {
                    echo date('d/m/Y', strtotime($row['created_at']));
                }
                ?>
            </span>
        </div>
        <?php
    }
} else {
    // This only triggers if the WHOLE query returns 0 rows (end of list)
    echo "done";
}
?>