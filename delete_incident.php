<?php
include 'database.php';

$id = (int) $_POST['id'];

$sql = "DELETE FROM incidents WHERE id=$id";
mysqli_query($conn, $sql);

header("Location: incidents.php");
exit();
?>