<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menandai halaman yang aktif
$active_page = 'calon_anggota';


// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Calon Anggota - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">

    <script>
    // Fungsi untuk menampilkan form update
    function showUpdateForm(id) {
        // Buat AJAX request untuk mengambil data calabar berdasarkan ID
        $.ajax({
            url: 'get_calabar.php', // Ganti dengan file PHP yang mengambil data calabar berdasarkan ID
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                // Tampilkan form update dengan data calabar yang diterima
                $('#updateForm').show();
                $('#updateForm #id_calabar').val(response.id_calabar);
                // Setel nilai dropdown sesuai dengan status diterima atau tidak diterima
                $('#updateForm #status').val(response.status);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Fungsi untuk menghapus data calabar
    function deleteData(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            // Buat AJAX request untuk menghapus data calabar berdasarkan ID
            $.ajax({
                url: 'delete_calabar.php', // Ganti dengan file PHP yang menghapus data calabar berdasarkan ID
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    // Refresh halaman setelah data berhasil dihapus
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    }
</script>
  
</head>
<body>
<div class="sidebar">
    <h2>Daftar Calon Anggota</h2>
    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
    <a href="update.php" class="btn btn-primary <?php if($active_page == 'update') echo 'active'; ?>">Update</a>
    <a href="?logout=true" class="btn btn-primary <?php if($active_page == 'logout') echo 'active'; ?>">Logout</a>
    <a href="proses_struktur.php" class="btn btn-primary <?php if($active_page == 'struktur') echo 'active'; ?>">Kepengurusan</a>
    <a href="proses_prestasi.php" class="btn btn-primary <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="visi_misi.php" class="btn btn-primary <?php if($active_page == 'visi_misi') echo 'active'; ?>">Visi Misi</a>
    <a href="galeri.php" class="btn btn-primary <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="kegiatan.php" class="btn btn-primary <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
</div>

<div class="content">
    <div class="container">
        <h1>Daftar Calon Anggota</h1>
        <table class="table">
        <thead>
        <tr>
            <th>ID Calabar</th>
            <th>ID User</th>
            <th>Nama</th>
            <th>NIM</th>
            <th>Prodi</th>
            <th>ID UKM</th>
            <th>Nama UKM</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Pasfoto</th>
            <th>Foto UKM</th>
            <th>Alasan</th>
            <th>Nilai TPA</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>
    </thead>
            <tbody>
                <?php
                
                // Mengambil data calabar dari database
                $query = "SELECT * FROM tab_pacab";
                $result = mysqli_query($conn, $query);

                // Menampilkan data dalam tabel
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['id_calabar'] . "</td>";
                    echo "<td>" . $row['id_user'] . "</td>";
                    echo "<td>" . $row['nama_depan'] . " " . $row['nama_belakang'] . "</td>";
                    echo "<td>" . $row['nim'] . "</td>";
                    echo "<td>" . $row['prodi'] . "</td>";
                    echo "<td>" . $row['id_ukm'] . "</td>";
                    echo "<td>" . $row['nama_ukm'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['no_hp'] . "</td>";
                    echo "<td><img src='../assets/images/" . $row['pasfoto'] . "' width='100'></td>";
                    echo "<td><img src='../assets/images/" . $row['foto_ktm'] . "' width='100'></td>";
                    echo "<td>" . $row['alasan'] . "</td>";
                    echo "<td>" . $row['nilai_tpa'] . "</td>";
                    echo "<td><button class='btn btn-primary' onclick='showUpdateForm(" . $row['id_calabar'] . ")'>Update</button></td>";
                    echo "<td><button class='btn btn-danger' onclick='deleteData(" . $row['id_calabar'] . ")'>Delete</button></td>";
                    echo "</tr>";
                }

                // Menutup koneksi
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script>
$(document).ready(function() {
    // Menangani klik tombol delete
    $('.btn-delete').click(function() {
        // Mengambil ID calabar dari atribut data
        var idCalabar = $(this).data('id');
        
        // Konfirmasi penghapusan
        if (confirm("Apakah Anda yakin ingin menghapus calabar ini?")) {
            // Mengirim permintaan AJAX ke delete_calabar.php
            $.ajax({
                url: 'delete_calabar.php',
                method: 'POST',
                data: {id_calabar: idCalabar},
                dataType: 'json',
                success: function(response) {
                    // Menampilkan pesan hasil penghapusan
                    alert(response.message);
                    
                    // Menghapus baris dari tabel setelah penghapusan berhasil
                    if (response.status === 'success') {
                        $(this).closest('tr').remove();
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus calabar.');
                }
            });
        }
    });
});
</script>

</body>
<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
            <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z" />
        </svg> Website</a> | Connect with us on <a href="https://www.facebook.com/stmikkomputama"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
            <path d="M16 8.049c0-4.423-3.576-8-7.999-8C3.575.049 0 3.626 0 8.049c0 3.892 2.84 7.11 6.57 7.807v-5.509H4.429V8.048h2.142V6.311c0-2.117 1.26-3.293 3.19-3.293.92 0 1.82.165 1.82.165v1.997h-1.03c-1.009 0-1.324.628-1.324 1.27v1.52h2.25l-.361 2.252h-1.89v5.51C13.159 15.16 16 11.942 16 8.05z" />
        </svg> Facebook</a></footer>
</html>
