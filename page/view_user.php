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
$active_page = 'view_user';

?>


<!DOCTYPE html>
<html>
<head>
    <title>User Manager - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
</head>
<style>
    .card {
        width: 100%; /* Set the width to 100% to make the card responsive */
        max-width: 400px; /* Add max-width to limit the card's width */
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
     th {
        white-space: nowrap;
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
    .delete-button {
        background-color: red;
    }
        /* Tambahkan gaya CSS berikut untuk mengatur tata letak tombol */
        .action-buttons {
        display: flex;
        justify-content: space-between;
    }

    .action-buttons button {
        flex: 1;
        margin-right: 5px;
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
<h2><i>Pengguna</i></h2>
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
<!-- Data User -->
<div class="content">
    <h2>Data User</h2>
    <table class="table">
    <thead>
            <tr>
                <th>ID User</th>
                <th>Nama Lengkap</th>
                <th>Prodi</th>
                <th>Semester</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Level</th>
                <th>Foto</th>
                <th>Foto KTM</th>
            </tr>
        </thead>

        <tbody>
    <?php
    // Fetch users from the database
    $sql = "SELECT * FROM tab_user";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id_user"] . "</td>";
            echo "<td>" . $row["nama_lengkap"] . "</td>";
            echo "<td>" . $row["prodi"] . "</td>";
            echo "<td>" . $row["semester"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["no_hp"] . "</td>";
            echo "<td>" . $row["level"] . "</td>";
                    // Pasfoto
            $pasfoto_filename = $row["pasfoto"];
            $pasfoto_path = "../assets/images/pasfoto/" . $pasfoto_filename;
            echo "<td><img src='$pasfoto_path' alt='Pasfoto' width='100'></td>";

            // Foto KTM
            $foto_ktm_filename = $row["foto_ktm"];
            $foto_ktm_path = "../assets/images/ktm/" . $foto_ktm_filename;
            echo "<td><img src='$foto_ktm_path' alt='Foto KTM' width='100'></td>";
            echo "</tr>";
            }
        }

    ?>
</tbody>
    </table>
</body>
</html>