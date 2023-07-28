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
$active_page = 'prestasi';

// Memeriksa apakah tombol logout diklik
if (isset($_GET['logout'])) {
    // Memanggil fungsi logout
    logout();
}

/// Memeriksa apakah parameter id_prestasi telah diberikan
if (!isset($_GET['id_prestasi'])) {
    // Jika parameter id_prestasi tidak diberikan, redirect to an error page or take appropriate action
    exit();
}

$id_prestasi = $_GET['id_prestasi'];

// Query untuk mengambil data prestasi berdasarkan id_prestasi
$sql = "SELECT * FROM tab_prestasi WHERE id_prestasi = '$id_prestasi'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // Mengambil data prestasi dari hasil query
    $prestasi = $result->fetch_assoc();
} else {
    // Jika data prestasi tidak ditemukan
    echo "Data prestasi tidak ditemukan";
    exit();
}

// Mendapatkan data ID UKM dan nama UKM dari tabel tab_ukm
$query = "SELECT id_ukm, nama_ukm, logo_ukm, nama_ketua, nim_ketua, sejarah, visi, misi FROM tab_ukm";
$result = mysqli_query($conn, $query);

// Inisialisasi variabel untuk opsi combobox
$options = "";

// Buat array untuk menyimpan data nama_ukm berdasarkan id_ukm
$namaUKM = array();
while ($row = mysqli_fetch_assoc($result)) {
    $id_ukm = $row['id_ukm'];
    $nama_ukm = $row['nama_ukm'];
    $namaUKM[$id_ukm] = $nama_ukm;
}

// Memeriksa apakah form edit prestasi telah di-submit
if (isset($_POST['submit'])) {
    // Mengambil data dari form dan melakukan sanitasi
    $id_prestasi = $_POST['id_prestasi'];
    $nama_prestasi = mysqli_real_escape_string($conn, $_POST['nama_prestasi']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara']);
    $tgl_prestasi = $_POST['tgl_prestasi'];
    $id_ukm = $_POST['id_ukm'];
    $nama_ukm = mysqli_real_escape_string($conn, $_POST['nama_ukm']);

    // Memperbarui data prestasi di database
    $sql = "UPDATE tab_prestasi SET nama_prestasi = '$nama_prestasi', penyelenggara = '$penyelenggara', tgl_prestasi = '$tgl_prestasi', id_ukm = '$id_ukm', nama_ukm = '$nama_ukm' WHERE id_prestasi = '$id_prestasi'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Redirect back to the user list after update
        header("Location: proses_prestasi.php");
        exit();
    } else {
        // If an error occurs during the update
        echo "Error: " . mysqli_error($conn);
        exit();
    }
}

// Define the logout() function if not already defined
function logout() {
    // Add your logout logic here, such as clearing session data, etc.
    // For example, you can use session_destroy() to clear the session data
    // and then redirect the user to the login page.
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prestasi - SIUKM</title>
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
    .delete-button {
        background-color: red;
    }
</style>
<script>
    // Define the updateNamaUKM function
    function updateNamaUKM(selectElement) {
        var selectedIdUkm = selectElement.value;
        var namaUkmField = document.getElementById("nama_ukm");
        
        // Set the value of the "nama_ukm" field based on the selected "id_ukm"
        if (selectedIdUkm in <?php echo json_encode($namaUKM); ?>) {
            namaUkmField.value = <?php echo json_encode($namaUKM); ?>[selectedIdUkm];
        } else {
            namaUkmField.value = '';
        }
    }
</script>

<script>
  function generateIdPrestasi() {
    var namaPrestasi = document.getElementById("nama_prestasi").value;
    var akronim = "";
    
    // Mengambil akronim dari nama prestasi
    var words = namaPrestasi.split(" ");
    for (var i = 0; i < words.length; i++) {
      akronim += words[i].charAt(0);
    }
    
    // Mendapatkan 3 digit angka acak
    var angka = Math.floor(Math.random() * 1000);
    var angkaStr = ("000" + angka).slice(-3);
    
    // Mengisi textfield id_prestasi dengan hasil generate
    var idPrestasi = akronim.toUpperCase() + angkaStr;
    document.getElementById("id_prestasi").value = idPrestasi;
  }
</script>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
    <h2>Manajemen Prestasi</h2>
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
    
  <!-- Form Edit Prestasi -->
<div class="content">
    <div class="card">
        <h2>Edit Prestasi</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <!-- Menambahkan input field hidden untuk id_prestasi -->
            <input type="hidden" name="id_prestasi" value="<?php echo $prestasi['id_prestasi']; ?>">
            <div class="form-group">
                <label for="nama_prestasi">Nama Prestasi:</label>
                <input type="text" class="form-control" id="nama_prestasi" value="<?php echo $prestasi['nama_prestasi']; ?>" name="nama_prestasi" required>
            </div>
            <div class="form-group">
                <label for="penyelenggara">Penyelenggara:</label>
                <input type="text" class="form-control" id="penyelenggara" value="<?php echo $prestasi['penyelenggara']; ?>" name="penyelenggara" required>
            </div>
            <div class="form-group">
                <label for="tgl_prestasi">Tanggal Prestasi:</label>
                <input type="date" class="form-control" id="tgl_prestasi" name="tgl_prestasi" value="<?php echo $prestasi['tgl_prestasi']; ?>" required>
            </div>
            <div class="form-group">
                <label for="id_ukm">ID UKM:</label>
                <select id="id_ukm" class="form-control" name="id_ukm" required onchange="updateNamaUKM(this)">
                    <option value="" selected disabled>Pilih ID UKM</option>
                    <?php
                    // Membuat opsi combobox dari hasil query
                    foreach ($namaUKM as $id_ukm => $nama_ukm) {
                        $selected = ($prestasi['id_ukm'] == $id_ukm) ? 'selected' : '';
                        echo "<option value='$id_ukm' $selected>$id_ukm</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nama_ukm">Nama UKM:</label>
                <input type="text" class="form-control" id="nama_ukm" name="nama_ukm" value="<?php echo $prestasi['nama_ukm']; ?>" readonly>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Update</button>
        </form>
    </div>
</div>
</body>
</html>