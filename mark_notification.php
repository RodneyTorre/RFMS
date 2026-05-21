<?php
include 'database.php';

$id = $_GET['id'] ?? 0;

$sql = "UPDATE notifications SET status='read' WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
?>