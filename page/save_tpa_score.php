<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Menerima data JSON dari client
$data = json_decode(file_get_contents('php://input'), true);

// Mendapatkan nilai TPA dari data JSON
$nilaiTPA = $data['nilaiTPA'];
$id_calabar = $data['id_calabar'];

// Memperbarui nilai TPA ke tabel tab_pacab berdasarkan id_calabar
$query = "UPDATE tab_pacab SET nilai_tpa = $nilaiTPA WHERE id_calabar = '$id_calabar'";

if (mysqli_query($conn, $query)) {
    // Mengembalikan respons JSON berhasil
    $response = [
        'status' => 'success',
        'message' => 'Nilai TPA berhasil disimpan.',
        'nilaiTPA' => $nilaiTPA
    ];
} else {
    // Mengembalikan respons JSON gagal
    $response = [
        'status' => 'error',
        'message' => 'Gagal menyimpan nilai TPA: ' . mysqli_error($conn),
        'nilaiTPA' => null
    ];
}

// Mengirim respons JSON ke client
header('Content-Type: application/json');
echo json_encode($response);

// Menutup koneksi database
mysqli_close($conn);
?>
