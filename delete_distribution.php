<?php
include 'database.php';

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $sql = "DELETE FROM distributions WHERE distribution_id = $id";

        echo $id;
        echo $sql;
    // if (mysqli_query($conn, $sql)) {
    //     // header("Location: programs.php?deleted=success");
    //     exit();
    // } else {
    //     echo "Error deleting record: " . mysqli_error($conn);
    // }
}
?>