<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $training_id  = "TRN-" . date("Y") . "-" . rand(100,999);
    $title        = mysqli_real_escape_string($conn, $_POST['title']);
    $location     = mysqli_real_escape_string($conn, $_POST['location']);
    $trainer      = mysqli_real_escape_string($conn, $_POST['trainer']);
    $participants = (int) $_POST['participants'];
    $date         = $_POST['date'];

    // Auto status based on date
    $today = date("Y-m-d");

    if ($date < $today) {
        $status = "Completed";
    } elseif ($date == $today) {
        $status = "Ongoing";
    } else {
        $status = "Scheduled";
    }

    $sql = "INSERT INTO trainings
            (training_id, title, location, trainer, participants, date, status)
            VALUES
            ('$training_id', '$title', '$location', '$trainer', '$participants', '$date', '$status')";

    if (mysqli_query($conn, $sql)) {
        header("Location: programs.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>