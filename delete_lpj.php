<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lpjId'])) {
    // Include your database connection
    require_once "db_connect.php";

    $lpjId = $_POST['lpjId'];

    // Delete the LPJ record from the database using the lpjId
    $deleteQuery = "DELETE FROM tab_lpj WHERE id_laporan = '$lpjId'";

    if (mysqli_query($conn, $deleteQuery)) {
        echo "LPJ deleted successfully";
    } else {
        echo "Error deleting LPJ: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    echo "Invalid request";
}
?>
