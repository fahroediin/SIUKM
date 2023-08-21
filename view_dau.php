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
// Memeriksa apakah id_user ada pada session dan terdapat dalam tab_dau
$id_user_session = $_SESSION['id_user'];
$queryCheckIdUser = "SELECT COUNT(*) AS user_exists FROM tab_dau WHERE id_user = '$id_user_session'";
$resultCheckIdUser = mysqli_query($conn, $queryCheckIdUser);

if ($resultCheckIdUser) {
    $userExistsData = mysqli_fetch_assoc($resultCheckIdUser);
    $userExists = $userExistsData['user_exists'];

    if ($userExists > 0) {
        // Jika user ditemukan dalam tab_dau, tampilkan tombol-tombol
        $showButtons = true;
    } else {
        // Jika user tidak ditemukan dalam tab_dau, sembunyikan tombol-tombol
        $showButtons = false;
    }
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
}

// Menambahkan parameter placeholder pada query
$query = "SELECT id_anggota, id_user, nama_lengkap, no_hp, email, prodi, semester, pasfoto, foto_ktm, id_ukm, nama_ukm, sjk_bergabung FROM tab_dau WHERE id_anggota = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_anggota_session);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fungsi logout
function logout() {
    // Menghapus semua data session
    session_unset();
    // Menghancurkan session
    session_destroy();
    // Mengarahkan pengguna ke index.php setelah logout
    header("Location: index.php");
    exit();
}

// Memeriksa apakah tombol logout diklik
if (isset($_GET['logout'])) {
    // Memanggil fungsi logout
    logout();
}


// Menandai halaman yang aktif
$active_page = 'view_dau';

// Fetch the id_ukm associated with the current session user
$queryIdUkm = "SELECT id_ukm FROM tab_dau WHERE id_user = ?";
$stmtIdUkm = mysqli_prepare($conn, $queryIdUkm);
mysqli_stmt_bind_param($stmtIdUkm, "s", $id_user_session);
mysqli_stmt_execute($stmtIdUkm);
$resultIdUkm = mysqli_stmt_get_result($stmtIdUkm);
$rowIdUkm = mysqli_fetch_assoc($resultIdUkm);
$id_ukm_user = $rowIdUkm['id_ukm'];

// Query to retrieve members with matching id_ukm
$queryMembers = "SELECT * FROM tab_dau WHERE id_ukm = ?";
$stmtMembers = mysqli_prepare($conn, $queryMembers);
mysqli_stmt_bind_param($stmtMembers, "s", $id_ukm_user);
mysqli_stmt_execute($stmtMembers);
$resultMembers = mysqli_stmt_get_result($stmtMembers);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota UKM - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

       /* Gaya tambahan untuk tampilan tabel */
.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
}

.table th {
    background-color: #f2f2f2;
}

.table img {
    max-height: 100px;
    object-fit: cover;
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
    <a href="index.php">
  <img src="./assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Anggota UKM</i></h2>
<a href="dashboard.php" class="btn btn-primary <?php if ($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
<?php if ($showButtons): ?>
    <p style="text-align: center;">--Informasi--</p>
<?php endif; ?>
            <a href="view_struktur.php" class="btn btn-primary btn-manajemen <?php if ($active_page == 'view_struktur') echo 'active'; ?>" <?php if (!$showButtons) echo 'style="display: none;"'; ?>>Pengurus</a>
    <a href="view_dau.php" class="btn btn-primary btn-manajemen <?php if ($active_page == 'view_dau') echo 'active'; ?>" <?php if (!$showButtons) echo 'style="display: none;"'; ?>>Data Anggota</a>
    <a href="view_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_kegiatan') echo 'active'; ?>" <?php if (!$showButtons) echo 'style="display: none;"'; ?>>Kegiatan</a>
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
                    // Define Indonesian month names
                    $indonesianMonths = array(
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                        'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    );
                    // Loop through the fetched members and display them in the table
                    while ($rowMember = mysqli_fetch_assoc($resultMembers)) {
                        echo "<tr>";
                        echo "<td>" . $rowMember['id_anggota'] . "</td>";
                        echo "<td>" . $rowMember['id_user'] . "</td>";
                        echo "<td>" . $rowMember['nama_lengkap'] . "</td>";
                        echo "<td>" . $rowMember['no_hp'] . "</td>";
                        echo "<td>" . $rowMember['email'] . "</td>";
                        echo "<td>" . $rowMember['prodi'] . "</td>";
                        echo "<td>" . $rowMember['semester'] . "</td>";
                        echo "<td><img src='./assets/images/pasfoto/" . $rowMember['pasfoto'] . "' alt='Pasfoto' class='img-thumbnail' style='max-height: 100px;'></td>";
                        echo "<td><img src='./assets/images/ktm/" . $rowMember['foto_ktm'] . "' alt='Foto KTM' class='img-thumbnail' style='max-height: 100px;'></td>";
                        echo "<td>" . $rowMember['nama_ukm'] . "</td>";
                        echo "<td>" . date('d', strtotime($rowMember['sjk_bergabung'])) . " " . $indonesianMonths[intval(date('m', strtotime($rowMember['sjk_bergabung']))) - 1] . " " . date('Y', strtotime($rowMember['sjk_bergabung'])) . "</td>";
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
