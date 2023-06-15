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
if ($_SESSION['level'] == "3" || $_SESSION['level'] == "2") {
    // Jika level adalah "3" atau "2", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}
// Menandai halaman yang aktif
$active_page = 'dashboard';


// Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM tab_user WHERE id_user = '$userId'";

// Mengeksekusi query
$result = mysqli_query($conn, $query);

// Memeriksa apakah query berhasil dieksekusi
if ($result) {
    // Mengambil data pengguna
    $user = mysqli_fetch_assoc($result);

    // Menyimpan data pengguna ke dalam variabel session
    $_SESSION['nama_depan'] = $user['nama_depan'];
    $_SESSION['nama_belakang'] = $user['nama_belakang'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['no_hp'] = $user['no_hp'];
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
}

// Fungsi logout
function logout() {
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
// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $oldPassword = $_POST['old_password'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_POST['email'];
    $namaDepan = $_POST['nama_depan'];
    $namaBelakang = $_POST['nama_belakang'];
    $noHp = $_POST['no_hp'];

    // Memeriksa apakah password baru dan konfirmasi password cocok
    if ($password !== $confirmPassword) {
        // Password baru dan konfirmasi password tidak cocok
        $error = "Error: Password baru dan konfirmasi password tidak cocok.";
    } else {
        // Menghindari SQL injection
        $oldPassword = mysqli_real_escape_string($conn, $oldPassword);
        $password = mysqli_real_escape_string($conn, $password);
        $email = mysqli_real_escape_string($conn, $email);
        $namaDepan = mysqli_real_escape_string($conn, $namaDepan);
        $namaBelakang = mysqli_real_escape_string($conn, $namaBelakang);
        $noHp = mysqli_real_escape_string($conn, $noHp);

        // Mengecek kebenaran password lama
        $userId = $_SESSION['id_user'];
        $query = "SELECT * FROM tab_user WHERE id_user = '$userId' AND password = '$oldPassword'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 0) {
            // Password lama tidak cocok
            $error = "Error: Password lama tidak cocok.";
        } else {
            // Membuat query update
            $query = "UPDATE tab_user SET password = '$password', email = '$email', nama_depan = '$namaDepan', nama_belakang = '$namaBelakang', no_hp = '$noHp' WHERE id_user = '$userId'";

            // Mengeksekusi query update
            $updateResult = mysqli_query($conn, $query);

            // Tampilkan snackbar jika data berhasil diubah
            if ($updateResult) {
                // Mengupdate data di dalam session
                $_SESSION['password'] = $password;
                $_SESSION['email'] = $email;
                $_SESSION['nama_depan'] = $namaDepan;
                $_SESSION['nama_belakang'] = $namaBelakang;
                $_SESSION['no_hp'] = $noHp;

                // Tampilkan snackbar jika data berhasil diubah
                echo "<script>showSnackbar('Data berhasil diubah.');</script>";
            } else {
                // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">

    <style>
       
        .user-info {
            display: flex;
            align-items: center;
        }

        .profile-container {
            margin-right: 20px;
        }

        .profil-picture {
            width: 150px;
            height: 150px;
        }

        .profile-container {
            margin-right: 20px;
        }

        .profil-picture {
            width: 150px;
            height: 150px;
        }

    
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Dashboard</h2>
    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
    <a href="update.php" class="btn btn-primary <?php if($active_page == 'update') echo 'active'; ?>">Update</a>
    <a href="?logout=true" class="btn btn-primary <?php if($active_page == 'logout') echo 'active'; ?>">Logout</a>
    <a href="proses_struktur.php" class="btn btn-primary <?php if($active_page == 'struktur') echo 'active'; ?>">Kepengurusan</a>
    <a href="proses_prestasi.php" class="btn btn-primary <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_visimisi.php" class="btn btn-primary <?php if($active_page == 'visi_misi') echo 'active'; ?>">Data UKM</a>
    <a href="galeri.php" class="btn btn-primary <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="kegiatan.php" class="btn btn-primary <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
</div>


<div class="content">
    <h1>Dashboard Admin</h1>
    <hr class="divider">
    <div class="user-info">
        <div class="profile-container">
            <img src="../assets/images/sanji.jpg" alt="Foto Profil" class="profil-picture">
        </div>
        <div class="profile-details">
            <p><span class="label">Nama Depan:</span> <span class="value"><?php echo $_SESSION['nama_depan']; ?></span></p>
            <p><span class="label">Nama Belakang:</span> <span class="value"><?php echo $_SESSION['nama_belakang']; ?></span></p>
            <p><span class="label">Email:</span> <span class="value"><?php echo $_SESSION['email']; ?></span></p>
            <p><span class="label">Nomor Telepon:</span> <span class="value"><?php echo $_SESSION['no_hp']; ?></span></p>
        </div>

</div>
<!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
<script src="script.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>// Ambil elemen toggle button dan sidebar
const toggleBtn = document.querySelector('.toggle-btn');
const sidebar = document.querySelector('.sidebar');

// Tambahkan event listener untuk toggle button
toggleBtn.addEventListener('click', () => {
  // Toggle class 'collapsed' pada sidebar
  sidebar.classList.toggle('collapsed');
});
</script>
</body>
<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
            <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z" />
        </svg> Website</a> | Connect with us on <a href="https://www.facebook.com/stmikkomputama"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
            <path d="M16 8.049c0-4.423-3.576-8-7.999-8C3.575.049 0 3.626 0 8.049c0 3.892 2.84 7.11 6.57 7.807v-5.509H4.429V8.048h2.142V6.311c0-2.117 1.26-3.293 3.19-3.293.92 0 1.82.165 1.82.165v1.997h-1.03c-1.009 0-1.324.628-1.324 1.27v1.52h2.25l-.361 2.252h-1.89v5.51C13.159 15.16 16 11.942 16 8.05z" />
        </svg> Facebook</a></footer>
</html>