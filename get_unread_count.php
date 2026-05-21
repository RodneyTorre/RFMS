<?php
include 'database.php';
session_start();

$email = $_SESSION['email'] ?? 'SYSTEM';

$sql = "SELECT COUNT(*) as count FROM notifications 
        WHERE user_id=? AND status='unread'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

echo json_encode($result);
?>