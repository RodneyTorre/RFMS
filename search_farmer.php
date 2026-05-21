<?php
include 'database.php';

if(isset($_GET['query'])){

    $search = $_GET['query'];

    $sql = "SELECT * FROM farmers
            WHERE full_name LIKE '%$search%'
            OR address LIKE '%$search%'
            LIMIT 10";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){

        while($row = mysqli_fetch_assoc($result)){

            echo '
            <div class="search-item"
                 onclick="showFarmerDetails('.$row['farmer_id'].')">

                <strong>'.$row['full_name'].'</strong><br>

                <small>'.$row['address'].'</small>

            </div>
            ';
        }

    } else {

        echo "<div class='search-item'>No farmer found</div>";

    }
}
?>