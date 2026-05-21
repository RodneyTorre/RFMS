<?php
session_start();
include 'database.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

/* =========================
   UPDATE
========================= */
if ($action === 'update') {

    $id        = (int) ($_POST['id']        ?? 0);
    $name      = trim($_POST['item_name']   ?? '');
    $category  = trim($_POST['category']    ?? '');
    $warehouse = trim($_POST['warehouse']   ?? '');
    $quantity  = (int) ($_POST['quantity']  ?? 0);

    if (!$id || !$name || !$category || !$warehouse) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: inventory.php");
        exit();
    }

    $stmt = $conn->prepare("
        UPDATE inventory
        SET item_name  = ?,
            category   = ?,
            warehouse  = ?,
            quantity   = ?
        WHERE id = ?
    ");
    $stmt->bind_param('sssii', $name, $category, $warehouse, $quantity, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Item updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update item. Please try again.";
    }

    $stmt->close();
    header("Location: inventory.php");
    exit();
}

/* =========================
   DELETE
========================= */
if ($action === 'delete') {

    $id = (int) ($_GET['id'] ?? 0);

    if (!$id) {
        $_SESSION['error'] = "Invalid item.";
        header("Location: inventory.php");
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Item deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete item. Please try again.";
    }

    $stmt->close();
    header("Location: inventory.php");
    exit();
}

/* =========================
   INVALID ACTION
========================= */
$_SESSION['error'] = "Invalid action.";
header("Location: inventory.php");
exit();