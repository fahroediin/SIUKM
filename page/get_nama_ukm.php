<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Check if the ID UKM is set in the POST data
if (isset($_POST['id_ukm'])) {
    // Get the ID UKM from the POST data
    $id_ukm = $_POST['id_ukm'];

    // Query to fetch the nama_ukm based on the selected id_ukm
    $query = "SELECT nama_ukm FROM tab_ukm WHERE id_ukm = '$id_ukm'";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful and fetch the result
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $nama_ukm = $row['nama_ukm'];

        // Return the nama_ukm as a response to the AJAX request
        echo $nama_ukm;
    } else {
        // If no matching data found, return an empty response or an error message
        echo "Data not found";
    }
} else {
    // If the id_ukm is not set in the POST data, return an empty response or an error message
    echo "ID UKM not provided";
}
?>
