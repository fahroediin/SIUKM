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
$active_page = 'view_struktur';

// Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM tab_user WHERE id_user = '$userId'";
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struktur Organisasi - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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


   <!-- Sidebar -->

   <div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Struktur</i></h2>
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
 <!-- Konten -->
 <div class="content">
    <div class="card">
        <h2 style="text-align: center;">Struktur Organisasi</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID UKM</th>
                    <th>Jabatan</th>
                    <th>Nama Lengkap</th>
                    <th>NIM/NIDN</th>
                </tr>
            </thead>
        <!-- Display the data from tab_strukm -->
<tbody>
    <?php
    // Check if "id_ukm" is selected
    if (isset($_POST['id_ukm'])) {
        // Get the selected id_ukm
        $id_ukm_selected = $_POST['id_ukm'];

        // Query to retrieve data from tab_strukm based on id_ukm and id_user
        $sql_strukm = "SELECT * FROM tab_strukm WHERE id_ukm = ? AND id_user = ?";
        $stmt_strukm = $conn->prepare($sql_strukm);
        $stmt_strukm->bind_param("ss", $id_ukm_selected, $userId);
        $stmt_strukm->execute();
        $result_strukm = $stmt_strukm->get_result();

        // Display data in table rows
        while ($row_strukm = $result_strukm->fetch_assoc()) {
            $id_jabatan = $row_strukm['id_jabatan'];
            $nama_lengkap = $row_strukm['nama_lengkap'];
            $nim = $row_strukm['nim'];

            // Convert id_jabatan to corresponding text
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
                }
                ?>
            </tbody>
        </table>

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