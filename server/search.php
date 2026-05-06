<?php
require_once('../server/db_connect.php');

$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';
$maxPrice = $_GET['price'] ?? 1000;

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