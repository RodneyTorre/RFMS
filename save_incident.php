<?php
include 'database.php';

$code = "INC-" . date("Y") . "-" . rand(100,999);

$type = $_POST['type'];
$location = $_POST['location'];
$date = $_POST['date_reported'];
$affected = $_POST['affected'];
$damage = $_POST['damage'];

$sql = "INSERT INTO incidents 
(incident_code, type, location, date_reported, affected, damage, status)
VALUES
('$code', '$type', '$location', '$date', '$affected', '$damage', 'Assessment')";

mysqli_query($conn, $sql);

header("Location: incidents.php");
exit();
?>