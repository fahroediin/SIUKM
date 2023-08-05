<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Check if the id_anggota parameter is set in the request
if (isset($_GET['id_anggota'])) {
    // Sanitize the input
    $id_anggota = mysqli_real_escape_string($conn, $_GET['id_anggota']);

    // Prepare the SQL query to fetch the nama_lengkap and id_user based on the given id_anggota
    $query = "SELECT nama_lengkap, id_user FROM tab_dau WHERE id_anggota = ?";

    // Prepare the statement
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "s", $id_anggota);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Bind the result variables
        mysqli_stmt_bind_result($stmt, $nama_lengkap, $id_user);

        // Fetch the row containing the nama_lengkap and id_user
        if (mysqli_stmt_fetch($stmt)) {
            // Return the nama_lengkap and id_user values as a response
            $response = array(
                'nama_lengkap' => $nama_lengkap,
                'id_user' => $id_user
            );
            echo json_encode($response);
        } else {
            // If no matching id_anggota was found, return an empty response
            echo json_encode(array());
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // If there was an error preparing the statement, return an empty response
        echo json_encode(array());
    }
}
