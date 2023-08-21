<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memeriksa apakah ada permintaan POST dari AJAX
if (isset($_POST['lpjId'])) {
    $lpjId = mysqli_real_escape_string($conn, $_POST['lpjId']);

    // Query to delete LPJ data
    $deleteQuery = "DELETE FROM tab_lpj WHERE id_laporan = '$lpjId'";
    
    if (mysqli_query($conn, $deleteQuery)) {
        echo "LPJ deleted successfully";
    } else {
        echo "Error deleting LPJ: " . mysqli_error($conn);
    }
}
?>
