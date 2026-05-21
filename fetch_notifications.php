<?php
include 'database.php';
session_start();

$email = $_SESSION['email'] ?? 'SYSTEM';

$sql = "SELECT * FROM notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div style='padding:12px;font-size:12px;color:gray'>No notifications</div>";
    exit;
}

while ($row = $result->fetch_assoc()) {

    echo "<div class='notif-item {$row['status']}' onclick='markAsRead({$row['id']})'>
            <div class='notif-dot-small'></div>
            <div>
                <div class='notif-text'>{$row['message']}</div>
                <div class='notif-time'>{$row['created_at']}</div>
            </div>
          </div>";
}
?>