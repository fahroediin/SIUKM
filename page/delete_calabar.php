<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memeriksa apakah request datang dari metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memeriksa apakah parameter ID calabar telah diterima dari request
    if (isset($_POST["id"])) {
        // Melakukan sanitasi data ID calabar yang diterima dari request
        $id_calabar = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);

        // Mengeksekusi query untuk menghapus data calabar dari database berdasarkan ID calabar
        $query = "DELETE FROM tab_pacab WHERE id_calabar = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_calabar);

        if (mysqli_stmt_execute($stmt)) {
            // Jika data berhasil dihapus, kirimkan pesan sukses ke AJAX
            echo "Data berhasil dihapus";
        } else {
            // Jika terjadi kesalahan saat menghapus data, kirimkan pesan error ke AJAX
            echo "Gagal menghapus data: " . mysqli_error($conn);
        }

        // Menutup statement
        mysqli_stmt_close($stmt);
    } else {
        // Jika parameter ID calabar tidak diterima, kirimkan pesan error ke AJAX
        echo "ID calabar tidak ditemukan dalam request";
    }
} else {
    // Jika request bukan dari metode POST, kirimkan pesan error ke AJAX
    echo "Metode request tidak diizinkan";
}

// Menutup koneksi
mysqli_close($conn);
?>
