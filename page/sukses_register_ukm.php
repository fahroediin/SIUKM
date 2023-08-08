<?php
 // Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}

    // Mendapatkan data terakhir dari tabel tab_pacab
    $query = "SELECT * FROM tab_pacab ORDER BY id_calabar DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // Menyimpan data ke dalam variabel
    $id_calabar = $row['id_calabar'];
    $id_user = $row['id_user'];
    $nama_lengkap = $row['nama_lengkap'];
    $prodi = $row['prodi'];
    $semester = $row['semester'];
    $email = $row['email'];
    $no_hp = $row['no_hp'];
    $id_ukm = $row['id_ukm'];
    $nama_ukm = $row['nama_ukm'];
    $alasan = $row['alasan'];

    // Mengatur path untuk pasfoto dan foto KTM
    $pasfoto_path = "../assets/images/pasfoto/" . $row['pasfoto'];
    $foto_ktm_path = "../assets/images/ktm/" . $row['foto_ktm'];
    ?>
<!DOCTYPE html>
<html>
<head>
    <title>Halaman Sukses Registrasi UKM</title>
</head>
<body>

    <h1>Registrasi UKM Sukses</h1>
    <p>Berikut adalah data pendaftaran Anda:</p>
    <table>
        <tr>
            <td>ID Calon Anggota</td>
            <td>: <?php echo $id_calabar; ?></td>
        </tr>
        <tr>
            <td>ID User</td>
            <td>: <?php echo $id_user; ?></td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td>: <?php echo $nama_lengkap; ?></td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>: <?php echo $prodi; ?></td>
        </tr>
        <tr>
            <td>Semester</td>
            <td>: <?php echo $semester; ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td>: <?php echo $email; ?></td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>: <?php echo $no_hp; ?></td>
        </tr>
        <tr>
            <td>ID UKM</td>
            <td>: <?php echo $id_ukm; ?></td>
        </tr>
        <tr>
            <td>Nama UKM</td>
            <td>: <?php echo $nama_ukm; ?></td>
        </tr>
    </table>

    <p>Alasan bergabung: <?php echo $alasan; ?></p>

    <p>Apakah data sudah sesuai? Jika belum, tekan tombol kembali ke halaman pendaftaran.</p>
    <a href="register-ukm.php">Kembali ke halaman pendaftaran</a>

    <p>Selanjutnya, Anda akan mengerjakan soal Tes Potensi Akademik dengan jumlah soal 50 butir. Kerjakan soal sebaik mungkin dalam waktu 30 menit.</p>
</body>
</html>
