<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $distribution_id = $_POST['distribution_id'];
    $program         = $_POST['program'];
    $item_name       = $_POST['item_name'];
    $quantity        = (int) $_POST['quantity'];
    $beneficiaries   = (int) $_POST['beneficiaries'];
    $location        = $_POST['location'];
    $date            = $_POST['date'];

    $sql = "UPDATE distributions SET 
                program='$program',
                item_name='$item_name',
                quantity='$quantity',
                beneficiaries='$beneficiaries',
                location='$location',
                date='$date'
            WHERE distribution_id='$distribution_id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: programs.php?updated=success");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>