<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}

// Memeriksa level pengguna
if ($_SESSION['level'] == "2" || $_SESSION['level'] == "3") {
    // Jika level adalah "2" atau "3", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}

// Check if the 'id_kegiatan' parameter is provided via GET request
if (isset($_GET['id_kegiatan'])) {
    // Validate and sanitize the 'id_kegiatan'
    $id_kegiatan = mysqli_real_escape_string($conn, $_GET['id_kegiatan']);

    // Prepare the SQL query to delete the kegiatan
    $sql = "DELETE FROM tab_kegiatan WHERE id_kegiatan = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameter
    $stmt->bind_param("s", $id_kegiatan);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to the kegiatan.php page with a success parameter
        header("Location: proses_kegiatan.php?success=2");
        exit();
    } else {
        // Handle the error condition, for example:
        echo "Sorry, there was an error deleting the kegiatan.";
        exit();
    }
} else {
    // If 'id_kegiatan' parameter is not provided, redirect to kegiatan.php
    header("Location: kegiatan.php");
    exit();
}
?>
