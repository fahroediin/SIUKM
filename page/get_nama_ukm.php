<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Check if the id_ukm parameter is set in the request
if (isset($_GET['id_ukm'])) {
    // Sanitize the input
    $id_ukm = mysqli_real_escape_string($conn, $_GET['id_ukm']);

    // Prepare the SQL query to fetch the nama_ukm based on the given id_ukm
    $query = "SELECT nama_ukm FROM tab_ukm WHERE id_ukm = '$id_ukm'";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if ($result) {
        // Fetch the row containing the nama_ukm
        $row = mysqli_fetch_assoc($result);

        // Check if the row was found
        if ($row) {
            // Return the nama_ukm value as a response
            echo $row['nama_ukm'];
        } else {
            // If no matching id_ukm was found, return an empty string as the response
            echo "";
        }
    } else {
        // If there was an error executing the query, return an empty string as the response
        echo "";
    }
} else {
    // If the id_ukm parameter is not set, return an empty string as the response
    echo "";
}
