<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memeriksa apakah ada parameter id_ukm yang dikirim melalui GET
if (isset($_GET['id_ukm'])) {
    // Mengambil nilai id_ukm dari parameter GET
    $id_ukm = $_GET['id_ukm'];

    // Membuat query untuk mengambil nama_ukm berdasarkan id_ukm
    $query = "SELECT nama_ukm FROM tab_ukm WHERE id_ukm = '$id_ukm'";

    // Mengeksekusi query
    $result = mysqli_query($conn, $query);

    // Memeriksa apakah ada hasil dari query
    if (mysqli_num_rows($result) > 0) {
        // Jika ada hasil, ambil nama_ukm dari hasil query
        $row = mysqli_fetch_assoc($result);
        $nama_ukm = $row['nama_ukm'];

        // Mengirimkan nama_ukm sebagai respons ke pemanggil (file utama)
        echo $nama_ukm;
    } else {
        // Jika tidak ada hasil, kirimkan pesan error sebagai respons
        echo "Nama UKM tidak ditemukan.";
    }
} else {
    // Jika parameter id_ukm tidak diberikan, kirimkan pesan error sebagai respons
    echo "ID UKM tidak ditemukan.";
}

// Menutup koneksi database
mysqli_close($conn);
?>
