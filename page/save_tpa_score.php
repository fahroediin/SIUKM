<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Mendapatkan data jawaban dari request
$data = json_decode(file_get_contents('php://input'), true);
$id_calabar = $_SESSION['id_calabar'];

// Fungsi untuk menghitung skor TPA berdasarkan jawaban
function calculateTPAScore($answers)
{
    $skorBenar = 1; // Skor untuk jawaban benar
    $skorSalah = 0; // Skor untuk jawaban salah
    $nilaiTPA = 0; // Nilai total TPA

    // Mendapatkan jawaban benar untuk setiap nomor soal dari database (misalnya dari tabel soal_tpa)
    // $jawabanBenar = array(1 => 1, 2 => 0, 3 => 1, ...); // Contoh data jawaban benar

    // Iterasi untuk setiap nomor soal
    for ($nomorSoal = 1; $nomorSoal <= 50; $nomorSoal++) {
        // Ambil nilai jawaban yang dipilih oleh pengguna
        $jawabanPengguna = isset($answers[$nomorSoal]) ? $answers[$nomorSoal] : null;

        // Periksa apakah jawaban pengguna benar
        if ($jawabanPengguna !== null) {
            $jawabanBenar = getCorrectAnswerFromDatabase($nomorSoal); // Ganti dengan kode untuk mendapatkan jawaban benar dari database
            if ($jawabanPengguna == $jawabanBenar) {
                $nilaiTPA += $skorBenar;
            } else {
                $nilaiTPA += $skorSalah;
            }
        }
    }

    return $nilaiTPA;
}

// Fungsi untuk menyimpan skor TPA ke database
function saveTPAScoreToDatabase($id_calabar, $nilaiTPA)
{
    global $conn; // Variabel koneksi database dari db_connect.php

    // Periksa apakah data dengan id_calabar ini sudah ada di database atau belum
    $query = "SELECT * FROM tab_pacab WHERE id_calabar = $id_calabar";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Jika data sudah ada, lakukan UPDATE untuk mengupdate nilai_tpa
        $query = "UPDATE tab_pacab SET nilai_tpa = $nilaiTPA WHERE id_calabar = $id_calabar";
    } else {
        // Jika data belum ada, lakukan INSERT untuk memasukkan data baru
        $query = "INSERT INTO tab_pacab (id_calabar, nilai_tpa) VALUES ($id_calabar, $nilaiTPA)";
    }

    // Eksekusi query untuk menyimpan data skor TPA ke database
    if (mysqli_query($conn, $query)) {
        return true; // Berhasil menyimpan skor TPA ke database
    } else {
        return false; // Gagal menyimpan skor TPA ke database
    }
}

// Fungsi untuk mendapatkan jawaban benar dari database (digunakan dalam calculateTPAScore())
function getCorrectAnswerFromDatabase($nomorSoal)
{
    // Ganti ini dengan kode untuk mendapatkan jawaban benar untuk nomor soal dari tabel soal_tpa
    // Contoh:
    // $correctAnswer = 1; // Misalnya, 1 menandakan jawaban benar
    // return $correctAnswer;

    // Implementasikan sesuai struktur tabel soal_tpa dan peroleh jawaban benar dari database.
    // Pastikan sudah mendapatkan jawaban benar dari database berdasarkan nomor soal.
    // Metode ini akan bergantung pada bagaimana Anda mengatur struktur tabel soal_tpa dan menyimpan jawaban benar untuk setiap nomor soal.
}

// Memeriksa apakah data jawaban ada dalam request
if (isset($data['answers'])) {
    $answers = $data['answers'];

    // Menghitung nilai TPA berdasarkan jawaban
    $nilaiTPA = calculateTPAScore($answers);

   // Menyimpan nilai TPA ke database
if (saveTPAScoreToDatabase($id_calabar, $nilaiTPA)) {
    // Jika penyimpanan berhasil, redirect to beranda.php
    header("Location: beranda.php");
    exit();
} else {
    // Jika penyimpanan gagal, kirimkan respons ke klien
    $response = array(
        'status' => 'error',
        'message' => 'Gagal menyimpan skor TPA ke database.'
    );
    // Handle the error response accordingly if needed
}

} else {
    // Jika data jawaban tidak ada dalam request, kirimkan respons ke klien
    $response = array(
        'status' => 'error',
        'message' => 'Data jawaban tidak ditemukan dalam request.'
    );
}

// Mengirimkan respons sebagai JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
