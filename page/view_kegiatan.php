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
$active_page = 'view_kegiatan';

// Get the id_user from the session
$id_user = $_SESSION['id_user'];

// Query to fetch id_ukm based on the logged-in user's id_user
$queryIdUkm = "SELECT id_ukm FROM tab_dau WHERE id_user = '$id_user'";
$resultIdUkm = mysqli_query($conn, $queryIdUkm);

// Fetch the id_ukm from the result
$rowIdUkm = mysqli_fetch_assoc($resultIdUkm);
$id_ukm = $rowIdUkm['id_ukm'];

// Query to retrieve events from tab_kegiatan for the logged-in user's id_ukm
$query_kegiatan = "SELECT id_kegiatan, nama_kegiatan, id_ukm, nama_ukm, tgl FROM tab_kegiatan WHERE id_ukm = '$id_ukm'";
$result_kegiatan = mysqli_query($conn, $query_kegiatan);

?>

<!DOCTYPE html>
<html>

<head>
<title>Kegiatan - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
       

        .card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .card-body div {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        select,
        input[type="text"],
        input[type="date"],
        button {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        select {
            width: 100%;
        }

        button {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
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
<h2><i>Jadwal Kegiatan</i></h2>
<a href="dashboard.php" class="btn btn-primary <?php if ($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Informasi--</p>
            <a href="view_struktur.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_struktur') echo 'active'; ?>">Pengurus</a>
    <a href="view_dau.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_dau') echo 'active'; ?>">Data Anggota</a>
    <a href="view_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_kegiatan') echo 'active'; ?>">Kegiatan</a>
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
        <h2>Data Kegiatan</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Kegiatan</th>
                    <th>ID UKM</th>
                    <th>Nama UKM</th>
                    <th>Nama Kegiatan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Define Indonesian month names
                $indonesianMonths = array(
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                    'Agustus', 'September', 'Oktober', 'November', 'Desember'
                );

                // Loop through the retrieved events and display them in the table
                while ($row_kegiatan = mysqli_fetch_assoc($result_kegiatan)) {
                    // Output table rows
                    echo "<tr>";
                    echo "<td>" . $row_kegiatan['id_kegiatan'] . "</td>";
                    echo "<td>" . $row_kegiatan['id_ukm'] . "</td>";
                    echo "<td>" . $row_kegiatan['nama_ukm'] . "</td>";
                    echo "<td>" . $row_kegiatan['nama_kegiatan'] . "</td>";
                    echo "<td>" . date('d', strtotime($row_kegiatan['tgl'])) . " " . $indonesianMonths[intval(date('m', strtotime($row_kegiatan['tgl']))) - 1] . " " . date('Y', strtotime($row_kegiatan['tgl'])) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
    // Fungsi untuk logout dengan konfirmasi
    function logout() {
        // Tampilkan dialog konfirmasi menggunakan SweetAlert
        Swal.fire({
            title: 'Apakah Anda yakin ingin keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengklik "Ya", maka lakukan proses logout
                window.location.href = "?logout=true";
            }
        });
    }
</script>
</body>
</html>