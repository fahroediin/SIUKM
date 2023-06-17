<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();
// Mendapatkan ID calabar dari permintaan POST
$idCalabar = $_POST['id_calabar'];

// Hapus calabar dari database berdasarkan ID
$sql = "DELETE FROM calabar WHERE id_calabar = '$idCalabar'";
if (mysqli_query($conn, $sql)) {
    // Jika penghapusan berhasil
    echo json_encode(array('status' => 'success', 'message' => 'Calabar berhasil dihapus'));
} else {
    // Jika terjadi kesalahan saat menghapus
    echo json_encode(array('status' => 'error', 'message' => 'Gagal menghapus calabar'));
}

// Tutup koneksi ke database
mysqli_close($conn);
?>