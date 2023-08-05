<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Check if the id_anggota parameter is set in the request
if (isset($_GET['id_anggota'])) {
    // Sanitize the input
    $id_anggota = mysqli_real_escape_string($conn, $_GET['id_anggota']);
    
    // Query untuk mendapatkan data user berdasarkan id_anggota
    $query = "SELECT td.nama_lengkap, tm.nim FROM tab_dau td 
              JOIN tab_mahasiswa tm ON td.id_user = tm.id_user
              WHERE td.id_anggota = '$id_anggota'";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if ($result) {
        // Fetch the row containing the nama_lengkap and nim
        $row = mysqli_fetch_assoc($result);

        // Check if the row was found
        if ($row) {
            // Return the nama_lengkap and nim as a response
            echo "Nama Lengkap: " . $row['nama_lengkap'] . "<br>";
            echo "NIM: " . $row['nim'];
        } else {
            // If no matching id_anggota was found, return an empty string as the response
            echo "Data tidak ditemukan.";
        }
    } else {
        // If there was an error executing the query, return an empty string as the response
        echo "Error executing the query.";
    }
} else {
    // If the id_anggota parameter is not set, return an empty string as the response
    echo "Parameter id_anggota tidak ditemukan.";
}
