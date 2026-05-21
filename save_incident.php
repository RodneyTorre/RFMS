<?php
include 'database.php';

$code        = "INC-" . date("Y") . "-" . rand(100, 999);
$type        = $_POST['type'];
$location    = $_POST['location'];
$date        = $_POST['date_reported'];
$affected    = $_POST['affected'];
$damage      = $_POST['damage'];
$status      = $_POST['status'];
$remarks     = $_POST['remarks'] ?? '';

// Derive claim_status from status
$claim_status = (strtolower(trim($status)) === 'approved') ? 'Already Claimed' : 'Not Claimed';

$sql = "INSERT INTO incidents 
        (incident_code, type, location, date_reported, affected, damage, status, claim_status, remarks)
        VALUES
        ('$code', '$type', '$location', '$date', '$affected', '$damage', '$status', '$claim_status', '$remarks')";

mysqli_query($conn, $sql);

header("Location: incidents.php");
exit();
?>