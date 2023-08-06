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


// Menandai halaman yang aktif
$active_page = 'view_dau';

// Memperoleh data anggota UKM dari tabel tab_dau
$query = "SELECT id_anggota, id_user, nama_lengkap, no_hp, email, prodi, semester, pasfoto, foto_ktm, id_ukm, nama_ukm, sjk_bergabung FROM tab_dau";
$result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota UKM - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
</head>
<style>
        /* Tambahkan gaya CSS berikut untuk mengatur layout sidebar dan konten */
        .container {
            display: flex;
            flex-wrap: wrap;
        }

        .sidebar {
            flex: 0 0 20%; /* Lebar sidebar 20% dari container */
        }

        .content {
            flex: 0 0 80%; /* Lebar konten 80% dari container */

        }

        /* Gaya CSS tambahan untuk mengatur tampilan tabel dan form */
        .table {
            width: 100%;
        }
        th {
        white-space: nowrap;
        }
        .delete-button {
        background-color: red;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .form-row .form-control {
            flex: 1;
            margin-right: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }
        .sidebar img {
        display: block;
        margin: 0 auto;
        margin-bottom: 20px;
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .sidebar {
        text-align: center; /* Center the contents horizontally */
    }
    </style>


<div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Anggota UKM</i></h2>
<a href="kemahasiswaan.php" class="btn btn-primary <?php if($active_page == 'kemahasiswaan') echo 'active'; ?>">Dashboard</a>
<p style="text-align: center;">--Monitoring--</p>
            <a href="view_struktur.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_struktur') echo 'active'; ?>">Pengurus</a>
    <a href="view_dau.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_dau') echo 'active'; ?>">Data Anggota</a>
    <a href="view_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="view_user.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_user') echo 'active'; ?>">User Manager</a>
    <a href="view_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_ukm') echo 'active'; ?>">Data UKM</a>
    <a href="view_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_galeri') echo 'active'; ?>">Galeri</a>
    <a href="view_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="view_calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
    <a href="#" class="btn btn-primary" id="logout-btn" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>
<script>
    // Function to wrap buttons with a border, except for the Logout button
    function wrapButtonsWithBorder() {
        const buttons = document.querySelectorAll('.btn-manajemen');
        buttons.forEach((button) => {
            if (!button.getAttribute('id') || button.getAttribute('id') !== 'logout-btn') {
                button.style.border = '1px solid #ccc';
                button.style.borderRadius = '5px';
                button.style.padding = '8px';
                button.style.margin = '5px';
            }
        });
    }

    // Call the function to apply the border to the buttons
    wrapButtonsWithBorder();
</script>
<body>
<div class="content">
    <h2>Data Anggota UKM</h2>
    <div class="form-group">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID Anggota</th>
                <th>ID User</th>
                <th>Nama Lengkap</th>
                <th>No. HP</th>
                <th>Email</th>
                <th>Program Studi</th>
                <th>Semester</th>
                <th>Pasfoto</th>
                <th>Foto KTM</th>
                <th>Nama UKM</th>
                <th>Bergabung</th>
            </tr>
        </thead>
        <tbody>
    <?php
    // Loop melalui hasil query untuk menampilkan data anggota UKM
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_anggota'] . "</td>";
        echo "<td>" . $row['id_user'] . "</td>";
        echo "<td>" . $row['nama_lengkap'] . "</td>";
        echo "<td>" . $row['no_hp'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['prodi'] . "</td>";
        echo "<td>" . $row['semester'] . "</td>";
        // Display the "Pasfoto" image
        echo "<td><img src='../assets/images/pasfoto/" . $row['pasfoto'] . "' alt='Pasfoto' class='img-thumbnail' style='max-height: 100px;'></td>";
        // Display the "Foto_KTM" image
        echo "<td><img src='../assets/images/ktm/" . $row['foto_ktm'] . "' alt='Foto KTM' class='img-thumbnail' style='max-height: 100px;'></td>";
        echo "<td>" . $row['nama_ukm'] . "</td>";
        echo "<td>" . date('d-m-Y', strtotime($row['sjk_bergabung'])) . "</td>";
        echo "</tr>";
    }
    ?>
</tbody>

    </table>
        </div>

</body>
</html>
