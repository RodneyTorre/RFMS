<?php
include 'database.php';

$program = $_POST['program'];
$item = $_POST['item'];
$quantity = $_POST['quantity'];
$beneficiaries = $_POST['beneficiaries'];
$date = $_POST['date'];

$distribution_id = "DIST-" . date("Y") . "-" . rand(100,999);

$sql = "INSERT INTO distributions 
(distribution_id, program, item, quantity, beneficiaries, date, status)
VALUES 
('$distribution_id', '$program', '$item', '$quantity', '$beneficiaries', '$date', 'Ongoing')";

mysqli_query($conn, $sql);

header("Location: programs.php");
exit();
?>