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

// Memeriksa level pengguna
if ($_SESSION['level'] == "3") {
    // Jika level adalah "3", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}

// Menandai halaman yang aktif
$active_page = 'struktur';

// Fungsi logout
function logout()
{
    // Menghapus semua data session
    session_unset();
    // Menghancurkan session
    session_destroy();
    // Mengarahkan pengguna ke beranda.php setelah logout
    header("Location: beranda.php");
    exit();
}

// Memeriksa apakah tombol logout diklik
if (isset($_GET['logout'])) {
    // Memanggil fungsi logout
    logout();
}

// Memeriksa apakah form tambah struktur telah di-submit
if (isset($_POST['submit'])) {
    // Mengambil data dari form
    $id_ukm = $_POST['id_ukm'];
    $id_jabatan = $_POST['id_jabatan'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $nim = $_POST['nim'];

    // Menyimpan data ke database menggunakan prepared statements
    $sql = "INSERT INTO tab_strukm (id_ukm, id_jabatan, nama_lengkap, nim) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $id_ukm, $id_jabatan, $nama_lengkap, $nim);
    $result = $stmt->execute();

    if ($result) {
        // Redirect ke halaman daftar struktur setelah penyimpanan berhasil
        header("Location: proses_struktur.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menyimpan struktur
        echo "Error: " . $stmt->error;
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struktur Organisasi - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
</head>
<style>
    .card {
        width: 50%;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .btn {
        padding: 8px 12px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #0056b3;
    }
</style>
<body>
   <!-- Sidebar -->
<div class="sidebar">
    <h2>Manajemen Struktur</h2>
    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
    <a href="proses_struktur.php" class="btn btn-primary <?php if($active_page == 'struktur') echo 'active'; ?>">Kepengurusan</a>
    <a href="proses_prestasi.php" class="btn btn-primary <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_visimisi.php" class="btn btn-primary <?php if($active_page == 'visi_misi') echo 'active'; ?>">Data UKM</a>
    <a href="galeri.php" class="btn btn-primary <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="kegiatan.php" class="btn btn-primary <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
</div>


    <!-- Konten -->
    <div class="content">
    <div class="card">
        <h2>Kelola Struktur Organisasi</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama UKM</th>
                    <th>Jabatan</th>
                    <th>Nama Lengkap</th>
                    <th>NIM</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query untuk mendapatkan data tab_strukm
                $sql_strukm = "SELECT * FROM tab_strukm";
                $result_strukm = $conn->query($sql_strukm);

                // Menampilkan data struktur dalam bentuk baris tabel
                while ($row_strukm = $result_strukm->fetch_assoc()) {
                    $id_ukm = $row_strukm['id_ukm'];
                    $id_jabatan = $row_strukm['id_jabatan'];
                    $nama_lengkap = $row_strukm['nama_lengkap'];
                    $nim = $row_strukm['nim'];

                    // Mendapatkan nama UKM berdasarkan id_ukm dari tabel tab_ukm
                    $sql_ukm_name = "SELECT nama_ukm FROM tab_ukm WHERE id_ukm = $id_ukm";
                    $result_ukm_name = $conn->query($sql_ukm_name);
                    $ukm_name = $result_ukm_name->fetch_assoc()['nama_ukm'];

                    // Mengonversi id_jabatan menjadi teks jabatan
                    $jabatan = "";
                    switch ($id_jabatan) {
                        case 0:
                            $jabatan = "Pembimbing";
                            break;
                        case 1:
                            $jabatan = "Ketua";
                            break;
                        case 2:
                            $jabatan = "Wakil Ketua";
                            break;
                        case 3:
                            $jabatan = "Sekretaris";
                            break;
                        case 4:
                            $jabatan = "Bendahara";
                            break;
                        case 5:
                            $jabatan = "Koordinator";
                            break;
                        case 6:
                            $jabatan = "Anggota";
                            break;
                        default:
                            $jabatan = "Tidak diketahui";
                            break;
                    }

                    // Menampilkan data dalam baris tabel
                    echo "<tr>";
                    echo "<td>$ukm_name</td>";
                    echo "<td>$jabatan</td>";
                    echo "<td>$nama_lengkap</td>";
                    echo "<td>$nim</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <form method="post" action="proses_struktur.php">
            <div class="form-group">
                <label for="id_ukm">Nama UKM:</label>
                <select class="form-control" id="id_ukm" name="id_ukm" required>
                    <?php
                    // Query untuk mendapatkan data tab_ukm
                    $sql_ukm = "SELECT id_ukm, nama_ukm FROM tab_ukm";
                    $result_ukm = $conn->query($sql_ukm);

                    // Menampilkan opsi untuk setiap baris data
                    while ($row_ukm = $result_ukm->fetch_assoc()) {
                        $id_ukm = $row_ukm['id_ukm'];
                        $nama_ukm = $row_ukm['nama_ukm'];
                        echo "<option value='$id_ukm'>$nama_ukm</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_jabatan">ID Jabatan:</label>
                <select class="form-control" id="id_jabatan" name="id_jabatan" required>
                    <option value="6">Anggota</option>
                    <option value="5">Koordinator</option>
                    <option value="4">Bendahara</option>
                    <option value="3">Sekretaris</option>
                    <option value="2">Wakil Ketua</option>
                    <option value="1">Ketua</option>
                    <option value="0">Pembimbing</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
            </div>
            <div class="form-group">
                <label for="nim">NIM:</label>
                <input type="text" class="form-control" id="nim" name="nim" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" name="submit">Tambah</button>
            </div>
        </form>
    </div>

    <!-- Script untuk mengatur perubahan lebar sidebar -->
    <script>
        const sidebar = document.querySelector('.sidebar');
        document.addEventListener('DOMContentLoaded', function() {
            // Menambahkan event listener pada tombol collapse
            document.querySelector('#collapse-button').addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        });
    </script>
</body>
</html>