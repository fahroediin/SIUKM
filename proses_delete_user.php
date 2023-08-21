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
if ($_SESSION['level'] == "3") {
    // Jika level adalah "3", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}

// Memeriksa apakah parameter id_user telah diberikan
if (isset($_GET['id'])) {
    $id_user = $_GET['id'];

    // Menghapus user dari database
    $sql = "DELETE FROM tab_user WHERE id_user = '$id_user'";
    $result = $conn->query($sql);

    if ($result) {
        // Redirect ke halaman daftar user setelah penghapusan berhasil
        header("Location: proses_user.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menghapus user
        echo "Error: " . $conn->error;
        exit();
    }
} else {
    // Jika parameter id_user tidak diberikan
    echo "Invalid user ID";
    exit();
}
?>
