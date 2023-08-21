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

// Check if the id_foto parameter is provided
if (isset($_GET['id_foto'])) {
    $id_foto = mysqli_real_escape_string($conn, $_GET['id_foto']);

    // Fetch the filename of the image before deleting the data from tab_galeri table
    $sql_select_image = "SELECT foto_kegiatan FROM tab_galeri WHERE id_foto = ?";
    $stmt_select_image = $conn->prepare($sql_select_image);
    $stmt_select_image->bind_param("s", $id_foto);
    $stmt_select_image->execute();
    $stmt_select_image->bind_result($filename);
    $stmt_select_image->fetch();
    $stmt_select_image->close();

    // Prepare the SQL query to delete the data from tab_galeri table
    $sql_delete_data = "DELETE FROM tab_galeri WHERE id_foto = ?";
    $stmt_delete_data = $conn->prepare($sql_delete_data);
    $stmt_delete_data->bind_param("s", $id_foto);

    // Execute the query to delete data from tab_galeri table
    if ($stmt_delete_data->execute()) {
        // Delete the corresponding image file from ./assets/images/kegiatan/ directory
        $image_path = "./assets/images/kegiatan/" . $filename;
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // Redirect to the page where you want to show the success message (e.g., galeri.php)
        header("Location: proses_galeri.php?success=1");
        exit();
    } else {
        // Handle the error condition, for example:
        echo "Sorry, there was an error deleting the data.";
        exit();
    }
} else {
    // If the id_foto parameter is not provided, redirect to the page where you want to show an error message (e.g., galeri.php)
    header("Location: galeri.php?error=1");
    exit();
}
