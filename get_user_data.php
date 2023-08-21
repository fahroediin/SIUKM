<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Cek apakah ada permintaan POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai id_user dari permintaan POST
    $id_user = $_POST["id_user"];

    // Query untuk mendapatkan data user berdasarkan id_user
    $query = "SELECT nama_lengkap, no_hp, email, prodi, semester, pasfoto, foto_ktm FROM tab_user WHERE id_user = '$id_user'";
    $result = mysqli_query($conn, $query);

    // Cek apakah query berhasil dijalankan dan mengembalikan data
    if ($result && mysqli_num_rows($result) > 0) {
        // Ambil data dari hasil query
        $row = mysqli_fetch_assoc($result);

        // Kirim data dalam format JSON
        header("Content-Type: application/json");
        echo json_encode($row);
    } else {
        // Jika tidak ada data, kirimkan pesan kesalahan
        echo json_encode(["error" => "User not found"]);
    }
} else {
    // Jika bukan permintaan POST, kirimkan pesan kesalahan
    echo json_encode(["error" => "Invalid request"]);
}
