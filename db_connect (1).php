<?php
// Mendefinisikan informasi koneksi database
$servername ="localhost";
$username = "siur5746_siukm";
$password = "whosyourdaddy1343";
$database = "siur5746_dbsiukm";

// Membuat koneksi ke database
$conn = mysqli_connect($servername, $username, $password, $database);

// Memeriksa apakah koneksi berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
