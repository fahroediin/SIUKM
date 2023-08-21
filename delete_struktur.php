<?php
require_once "db_connect.php"; // Include your database connection

if (isset($_GET['id_jabatan'])) {
    $id_jabatan_to_delete = $_GET['id_jabatan'];

    // Perform the deletion operation using a DELETE query
    $sql_delete = "DELETE FROM tab_strukm WHERE id_jabatan = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("s", $id_jabatan_to_delete);
    $result_delete = $stmt_delete->execute();

    if ($result_delete) {
        // Deletion successful, redirect back to the page
        header("Location: proses_struktur.php");
        exit();
    } else {
        // Deletion failed, display an error message
        echo "Error deleting data: " . $stmt_delete->error;
        exit();
    }
}
?>
