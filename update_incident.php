<?php
include 'database.php';

$id      = (int) $_POST['id'];
$status  = mysqli_real_escape_string($conn, trim($_POST['status']));
$remarks = mysqli_real_escape_string($conn, trim($_POST['remarks'] ?? ''));

// Auto-set claim_status based on status
$claim_status = (strtolower($status) === 'approved') ? 'Already Claimed' : 'Not Claimed';

$sql = "UPDATE incidents 
        SET status='$status', claim_status='$claim_status', remarks='$remarks' 
        WHERE id=$id";

mysqli_query($conn, $sql);

header("Location: incidents.php");
exit();
?>