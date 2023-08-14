<?php
require_once "db_connect.php";

// Check if the 'id_ukm' parameter is present in the URL
if (isset($_GET['id_ukm'])) {
    // Get the 'id_ukm' parameter from the URL
    $id_ukm = $_GET['id_ukm'];
    
    // Construct the URL to the get_data_ukm.php script
    $get_data_url = "get_data_ukm.php?id_ukm=" . urlencode($id_ukm);
    
    // Fetch the data from the get_data_ukm.php script using cURL or file_get_contents
    // For example, using cURL:
    $ch = curl_init($get_data_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Parse the JSON response
    $ukmData = json_decode($response, true);
    
    // Check if the data was successfully retrieved
    if (isset($ukmData['error'])) {
        echo "Data not found";
    } else {
        // Display the UKM information
        echo "ID UKM: " . $ukmData['id_ukm'] . "<br>";
        echo "Nama UKM: " . $ukmData['nama_ukm'] . "<br>";
        // Display other information as needed
    }
} else {
    echo "No UKM ID provided.";
}
?>
