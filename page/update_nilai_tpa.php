<?php
// update_nilai_tpa.php

// Assuming you have already connected to the database in db_connect.php
require_once "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the ID and status sent via AJAX
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Validate the status (ensure it is either "Lolos" or "Tidak Lolos")
    if ($status !== "Lolos" && $status !== "Tidak Lolos") {
        echo "Invalid status!";
        exit();
    }

    // Perform the update query
    $query = "UPDATE tab_pacab SET nilai_tpa = '$status' WHERE id_calabar = $id";

    if (mysqli_query($conn, $query)) {
        echo "Update success!";
    } else {
        echo "Update failed: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
