<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menandai halaman yang aktif
$active_page = 'dashboard';


// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}

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
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
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

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pengguna</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">

    <style>
        body {
            display: flex;
            min-height: 100vh;
        }

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
    
    .divider {
        border: none;
        border-top: 2px solid #ccc;
        margin-bottom: 20px;
    }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Dashboard</h2>
    <a href="dashboard.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
    <a href="?logout=true" class="btn btn-primary <?php if($active_page == 'logout') echo 'active'; ?>">Logout</a>
</div>

<div class="content">
    <h1>Informasi Pengguna</h1>
 <!-- Tombol Ganti Password -->
 <a href="ganti_password_pengguna.php" class="btn btn-primary">Ganti Password</a>
 <a href="proses_update_pengguna.php" class="btn btn-primary">Update Data Diri</a>
    <hr class="divider">
    <div class="user-info">
    <div class="profile-container">
        <img src="../assets/images/sanji.jpg" alt="Foto Profil" class="profil-picture">
    </div>
    <div class="profile-details">
        <p><span class="label">Nama Lengkap:</span> <span class="value"><?php echo $_SESSION['nama_lengkap']; ?></span></p>
        <p><span class="label">Email:</span> <span class="value"><?php echo $_SESSION['email']; ?></span></p>
        <p><span class="label">Nomor Telepon:</span> <span class="value"><?php echo $_SESSION['no_hp']; ?></span></p>
    </div>
</div>
    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
