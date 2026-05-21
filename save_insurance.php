<?php
include 'database.php';

$policy_no = "POL-" . date("Y") . "-" . rand(1000,9999);

$farmer_name = $_POST['farmer_name'];
$asset_type = $_POST['asset_type'];
$coverage = $_POST['coverage'];
$payment = $_POST['payment'];
$valid_until = $_POST['valid_until'];

$sql = "INSERT INTO insurance_policies 
(policy_no, farmer_name, asset_type, coverage, payment, valid_until, status)
VALUES
('$policy_no', '$farmer_name', '$asset_type', '$coverage', '$payment', '$valid_until', 'Active')";

mysqli_query($conn, $sql);

header("Location: insurance.php");
exit();
?>