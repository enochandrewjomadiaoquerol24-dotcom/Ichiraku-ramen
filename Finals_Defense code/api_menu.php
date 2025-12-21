<?php
require_once 'config.php';
header('Content-Type: application/json');

$rows = [];
$res = $conn->query("
    SELECT 
        product_id AS id,
        product_name AS title,
        price,
        IFNULL(image_url, '') AS image,
        category_id AS category
    FROM products
");

while ($r = $res->fetch_assoc()) {
    $r['description'] = ""; // FE expects description field
    $rows[] = $r;
}

echo json_encode($rows);
?>
