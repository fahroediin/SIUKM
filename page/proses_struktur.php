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

function isJabatanExists($conn, $id_ukm, $id_jabatan)
{
    $sql = "SELECT COUNT(*) as count FROM tab_strukm WHERE id_ukm = ? AND id_jabatan = ? AND id_jabatan != 6";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $id_ukm, $id_jabatan);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

// Check if "nama_ukm" is selected
if (isset($_POST['submit'])) {
    $id_ukm = $_POST['id_ukm'];
    $id_jabatan = $_POST['id_jabatan'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $nim = $_POST['nim'];

 // Check if the selected id_jabatan already exists for the chosen id_ukm
if ($id_jabatan != '6' && isJabatanExists($conn, $id_ukm, $id_jabatan)) {
    // Use JavaScript to display the alert message
    echo '<script>alert("Jabatan Sudah ada");</script>';
    // Use JavaScript to reset the form fields
    echo '<script>resetFormFields();</script>';
    // Exit to prevent further processing
    exit();
}


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
        <form method="post" action="proses_struktur.php">
            <div class="form-group">
                <select class="form-control" id="id_ukm" name="id_ukm" required onchange="this.form.submit()">
                    <option value="">-- Pilih Nama UKM --</option>
                    <?php
                    // Query untuk mendapatkan data tab_ukm
                    $sql_ukm = "SELECT id_ukm, nama_ukm FROM tab_ukm";
                    $result_ukm = $conn->query($sql_ukm);

                    // Menampilkan opsi untuk setiap baris data
                    while ($row_ukm = $result_ukm->fetch_assoc()) {
                        $id_ukm = $row_ukm['id_ukm'];
                        $nama_ukm = $row_ukm['nama_ukm'];
                        $selected = isset($_POST['id_ukm']) && $_POST['id_ukm'] == $id_ukm ? 'selected' : '';
                        echo "<option value='$id_ukm' $selected>$nama_ukm</option>";
                    }
                    ?>
                </select>
            </div>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>ID UKM</th>
                    <th>Jabatan</th>
                    <th>Nama Lengkap</th>
                    <th>NIM</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Check if "id_ukm" is selected
                if (isset($_POST['id_ukm'])) {
                    // Query to retrieve "tab_strukm" data based on "id_ukm" and sort by "id_jabatan"
                    $id_ukm_selected = $_POST['id_ukm'];
                    $sql_strukm = "SELECT * FROM tab_strukm WHERE id_ukm = ? ORDER BY id_jabatan ASC";
                    $stmt_strukm = $conn->prepare($sql_strukm);
                    $stmt_strukm->bind_param("s", $id_ukm_selected);
                    $stmt_strukm->execute();
                    $result_strukm = $stmt_strukm->get_result();

                    // Display data in table rows
                    while ($row_strukm = $result_strukm->fetch_assoc()) {
                        $id_jabatan = $row_strukm['id_jabatan'];
                        $nama_lengkap = $row_strukm['nama_lengkap'];
                        $nim = $row_strukm['nim'];

                        // Mendapatkan nama UKM berdasarkan id_ukm dari tabel tab_ukm
                        $sql_ukm_name = "SELECT id_ukm FROM tab_ukm WHERE id_ukm = ?";
                        $stmt_ukm_name = $conn->prepare($sql_ukm_name);
                        $stmt_ukm_name->bind_param("s", $id_ukm_selected);
                        $stmt_ukm_name->execute();
                        $result_ukm_name = $stmt_ukm_name->get_result();
                        $ukm_name = $result_ukm_name->fetch_assoc()['id_ukm'];

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
                }
                ?>
            </tbody>
        </table>
        <form method="post" action="proses_struktur.php" onsubmit="return checkJabatan()">
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
    <script>
    function checkJabatan() {
        // Get the selected id_jabatan value from the form
        var idJabatan = document.getElementById('id_jabatan').value;

        // Check if the selected id_jabatan is not 'Anggota' (value '6') and show the alert
        if (idJabatan !== '6') {
            alert("Jabatan Sudah ada");
            return false; // Return false to prevent form submission
        }

        return true; // Return true to allow form submission
    }
</script>
<script>
    function resetFormFields() {
        // Reset the value of id_jabatan dropdown to '6' (Anggota)
        document.getElementById('id_jabatan').value = '6';
        // Reset the values of nama_lengkap and nim fields to empty
        document.getElementById('nama_lengkap').value = '';
        document.getElementById('nim').value = '';
    }

    function checkJabatan() {
        // Get the selected id_jabatan value from the form
        var idJabatan = document.getElementById('id_jabatan').value;

        // Check if the selected id_jabatan is not 'Anggota' (value '6') and show the alert
        if (idJabatan !== '6') {
            alert("Jabatan Sudah ada");
            resetFormFields(); // Reset the form fields
            return false; // Return false to prevent form submission
        }

        return true; // Return true to allow form submission
    }
</script>
</body>
</html>