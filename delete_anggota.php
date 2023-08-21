<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memeriksa apakah tombol hapus diklik
if (isset($_GET['id_anggota'])) {
    $id_anggota = $_GET['id_anggota'];

    // Query untuk menghapus data anggota berdasarkan id_anggota
    $deleteQuery = "DELETE FROM tab_dau WHERE id_anggota='$id_anggota'";

    if (mysqli_query($conn, $deleteQuery)) {
        // Redirect ke halaman data anggota setelah data dihapus
        header("Location: proses_dau.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menghapus data
        echo "Error: " . mysqli_error($conn);
    }
}
?>
