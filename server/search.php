<?php
require_once('../server/db_connect.php');

$query = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$maxPrice = $_GET['price'] ?? 500;

$allowedCategories = [
    '', 'Indoor', 'Outdoor', 'Trees', 'Tropical',
    'Succulent', 'Hanging', 'Flowering', 'Low Light'
];

if (strlen($query) > 50) {
    $query = substr($query, 0, 50);
}

if (!in_array($category, $allowedCategories)) {
    $category = '';
}

if (!is_numeric($maxPrice)) {
    $maxPrice = 500;
}

$maxPrice = (float)$maxPrice;

if ($maxPrice < 0) {
    $maxPrice = 0;
}

if ($maxPrice > 500) {
    $maxPrice = 500;
}

// Build dynamic SQL
$sql = "SELECT * FROM plants WHERE name LIKE ? AND price <= ?";
$params = ["%$query%", $maxPrice];

if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results);