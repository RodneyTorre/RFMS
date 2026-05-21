<?php
session_start();
if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

include 'database.php';

header('Content-Type: application/json');

$items = [];
$result = mysqli_query($conn, "SELECT item_name, category, warehouse, quantity, status FROM inventory ORDER BY quantity ASC");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
}

echo json_encode(['items' => $items, 'timestamp' => date('h:i:s A')]);