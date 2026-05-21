<?php
include 'database.php';

if(isset($_GET['id'])){

    $id = $_GET['id'];

    $sql = "SELECT * FROM farmers WHERE farmer_id='$id'";

    $result = mysqli_query($conn, $sql);

    $farmer = mysqli_fetch_assoc($result);

    // CHECK INSURANCE
    $insurance = mysqli_query($conn,
        "SELECT * FROM insurance_policies
         WHERE farmer_id='$id'"
    );

    $insured = mysqli_num_rows($insurance) > 0;

    echo "
        <p><strong>Full_name:</strong> ".$farmer['full_name']."</p>

        <p><strong>Address:</strong> ".$farmer['address']."</p>

        <p><strong>Contact:</strong> ".$farmer['contact_number']."</p>

        <p><strong>Status:</strong> Registered Farmer</p>
    ";

    if($insured){

        echo "
        <p style='color:green; font-weight:bold;'>
            ✔ Insured
        </p>
        ";

    } else {

        echo "
        <p style='color:red; font-weight:bold;'>
            ✘ Not Insured
        </p>
        ";
    }
}
?>