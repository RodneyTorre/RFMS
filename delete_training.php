<?php
include 'database.php';

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $sql = "DELETE FROM trainings WHERE training_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: programs.php?deleted=success");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>