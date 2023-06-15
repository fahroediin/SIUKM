<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memeriksa apakah tombol submit di klik
if (isset($_POST['submit'])) {
    // Mengambil nilai id_prestasi dari form
    $id_prestasi = $_POST['id_prestasi'];

    // Query dan perintah SQL untuk menghapus data prestasi berdasarkan id_prestasi
    $sql = "DELETE FROM tab_prestasi WHERE id_prestasi = '$id_prestasi'";
    $result = $conn->query($sql);

    if ($result) {
        // Redirect ke halaman daftar prestasi setelah penghapusan berhasil
        header("Location: proses_prestasi.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menghapus prestasi
        echo "Error: " . $conn->error;
        exit();
    }
}

// Menutup koneksi database
$conn->close();
?>
