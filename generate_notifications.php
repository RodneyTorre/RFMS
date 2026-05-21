<?php
include 'database.php';

$today = date('Y-m-d');
$warning_date = date('Y-m-d', strtotime('+30 days'));


// =======================
// 1. INSURANCE ALERT
// =======================
$sql = "SELECT * FROM insurance WHERE expiry_date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $today, $warning_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $msg = "Insurance for " . $row['farmer_name'] . " is expiring soon.";

    $check = $conn->prepare("SELECT id FROM notifications WHERE type='insurance' AND user_id=? AND message=?");
    $check->bind_param("ss", $row['user_id'], $msg);
    $check->execute();

    if ($check->get_result()->num_rows == 0) {

        $insert = $conn->prepare("
            INSERT INTO notifications (user_id, message, type, status)
            VALUES (?, ?, 'insurance', 'unread')
        ");

        $insert->bind_param("ss", $row['user_id'], $msg);
        $insert->execute();
    }
}


// =======================
// 2. STOCK ALERT
// =======================
$sql = "SELECT * FROM inventory WHERE quantity <= minimum_stock";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {

    $msg = $row['item_name'] . " is low in stock (" . $row['quantity'] . " left)";

    $check = $conn->prepare("SELECT id FROM notifications WHERE type='stock' AND message=?");
    $check->bind_param("s", $msg);
    $check->execute();

    if ($check->get_result()->num_rows == 0) {

        $insert = $conn->prepare("
            INSERT INTO notifications (user_id, message, type, status)
            VALUES ('SYSTEM', ?, 'stock', 'unread')
        ");

        $insert->bind_param("s", $msg);
        $insert->execute();
    }
}
?>