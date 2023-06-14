<?php
// Mendefinisikan informasi koneksi database
$servername ="localhost";
$username = "root";
$password = "";
$database = "dbsiukm";

// Membuat koneksi ke database
$conn = mysqli_connect($servername, $username, $password, $database);

// Memeriksa apakah koneksi berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
