<?php
session_start();
include 'database.php';

$distribution_id = uniqid("DIST-");
$program = $_POST['program'];
$location     = mysqli_real_escape_string($conn, $_POST['location']);
$item = $_POST['item'];
$quantity = (int) $_POST['quantity'];
$beneficiaries = (int) $_POST['beneficiaries'];
$date = $_POST['date'];
$status = "Scheduled";

/* =========================
   1. CHECK INVENTORY
========================= */

$stmt = $conn->prepare("SELECT quantity FROM inventory WHERE item_name = ?");
$stmt->bind_param("s", $item);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();

if (!$row) {
    $_SESSION['error'] = "Item not found in inventory!";
    header("Location: programs.php");
    exit();
}

if ($row['quantity'] < $quantity) {
    $_SESSION['error'] = "Not enough stock available!";
    header("Location: programs.php");
    exit();
}

/* =========================
   2. INSERT DISTRIBUTION
========================= */

$stmt = $conn->prepare("
    INSERT INTO distributions 
    (distribution_id, location, program, item_name, quantity, beneficiaries, date, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssissss",
    $distribution_id,
    $location,
    $program,
    $item,
    $quantity,
    $beneficiaries,
    $date,
    $status
);

$stmt->execute();

/* =========================
   3. UPDATE INVENTORY
========================= */

$stmt = $conn->prepare("
    UPDATE inventory
    SET quantity = quantity - ?
    WHERE item_name = ?
");

$stmt->bind_param("is", $quantity, $item);
$stmt->execute();

/* =========================
   4. UPDATE STATUS AUTOMATICALLY
========================= */

$stmt = $conn->prepare("
    UPDATE inventory
    SET status = CASE
        WHEN quantity <= minimum_stock THEN 'Low Stock'
        ELSE 'In Stock'
    END
    WHERE item_name = ?
");

$stmt->bind_param("s", $item);
$stmt->execute();

/* =========================
   DONE
========================= */

$_SESSION['success'] = "Distribution saved and inventory updated!";
header("Location: programs.php");
exit();
?>