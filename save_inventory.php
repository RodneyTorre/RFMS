<?php

session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* =========================
       GET FORM DATA
    ========================= */

    $item_name = trim($_POST['item_name']);
    $category  = trim($_POST['category']);
    $warehouse = trim($_POST['warehouse']);
    $quantity  = (int) $_POST['quantity'];

    /* =========================
       AUTO ITEM CODE
    ========================= */

    $item_code = 'INV-' . strtoupper(substr(md5(uniqid()), 0, 6));

    /* =========================
       STOCK STATUS
    ========================= */

    $status = ($quantity <= 20) ? 'Low Stock' : 'In Stock';

    /* =========================
       INSERT INVENTORY
    ========================= */

    $stmt = $conn->prepare("
        INSERT INTO inventory
            (item_code, item_name, category, warehouse, quantity, status, created_at)
        VALUES
            (?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param("ssssis", $item_code, $item_name, $category, $warehouse, $quantity, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Stock added successfully.";
    } else {
        $_SESSION['error'] = "Failed to save inventory: " . $stmt->error;
    }

    $stmt->close();

}

header("Location: inventory.php");
exit();