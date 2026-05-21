<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id           = $_POST['id'];
    $title        = $_POST['title'];
    $location     = $_POST['location'];
    $trainer      = $_POST['trainer'];
    $participants = $_POST['participants'];
    $date         = $_POST['date'];

    $sql = "
        UPDATE trainings SET
        title='$title',
        location='$location',
        trainer='$trainer',
        participants='$participants',
        date='$date'
        WHERE training_id='$id'
    ";

    if (mysqli_query($conn, $sql)) {
        header("Location: programs.php?updated=success");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>