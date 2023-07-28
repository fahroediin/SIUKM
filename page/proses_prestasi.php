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

// Menandai halaman yang aktif
$active_page = 'prestasi';

// Memeriksa apakah tombol logout diklik
if (isset($_GET['logout'])) {
    // Memanggil fungsi logout
    logout();
}

// Memeriksa apakah form tambah prestasi telah di-submit
if (isset($_POST['submit'])) {
    // Mengambil data dari form
    $nama_prestasi = $_POST['nama_prestasi'];
    $penyelenggara = $_POST['penyelenggara'];
    $tgl_prestasi = $_POST['tgl_prestasi'];
    $id_ukm = $_POST['id_ukm'];
    $nama_ukm = $_POST['nama_ukm'];

    // Generate ID Prestasi
    $id_prestasi = generateIdPrestasi($nama_prestasi, $tgl_prestasi);

    // Memeriksa apakah ID Prestasi sudah ada di database
    $check_query = "SELECT COUNT(*) AS count FROM tab_prestasi WHERE id_prestasi = '$id_prestasi'";
    $check_result = $conn->query($check_query);
    $check_data = $check_result->fetch_assoc();

    if ($check_data['count'] > 0) {
        // ID Prestasi sudah ada, tampilkan pesan alert
        echo '<script>alert("ID Prestasi tidak boleh sama");</script>';
    } else {
        // Menyimpan data ke database
        $sql = "INSERT INTO tab_prestasi (id_prestasi, nama_prestasi, penyelenggara, tgl_prestasi, id_ukm, nama_ukm) VALUES ('$id_prestasi', '$nama_prestasi', '$penyelenggara', '$tgl_prestasi', '$id_ukm', '$nama_ukm')";
        $result = $conn->query($sql);

        if ($result) {
            // Redirect ke halaman daftar prestasi setelah penyimpanan berhasil
            header("Location: proses_prestasi.php");
            exit();
        } else {
            // Jika terjadi kesalahan saat menyimpan prestasi
            echo "Error: " . $conn->error;
            exit();
        }
    }
}

function generateIdPrestasi($nama_prestasi, $tgl_prestasi)
{
    // Menghapus karakter non-alfanumerik dari nama prestasi
    $clean_nama_prestasi = preg_replace("/[^a-zA-Z0-9]/", "", $nama_prestasi);

    // Mengambil 6 digit pertama dari nama prestasi
    $id_prestasi = substr($clean_nama_prestasi, 0, 6);

    // Mengubah format tanggal prestasi menjadi Ymd (misal: 2023-06-15 menjadi 20230615)
    $tanggal_prestasi = date('Ymd', strtotime($tgl_prestasi));

    // Mengambil 6 digit terakhir dari tanggal prestasi
    $id_prestasi .= substr($tanggal_prestasi, -6);

    // Jika panjang ID Prestasi kurang dari 12 digit, tambahkan digit acak
    while (strlen($id_prestasi) < 12) {
        $id_prestasi .= mt_rand(0, 9);
    }

    return $id_prestasi;
}


// Memeriksa apakah form edit atau hapus telah di-submit
if (isset($_POST['action'])) {
    $id_prestasi = $_POST['id_prestasi'];
    $action = $_POST['action'];

    if ($action === 'edit') {
        // Redirect to the edit_prestasi.php page with the id_prestasi as a query parameter
        header("Location: edit_prestasi.php?id_prestasi=$id_prestasi");
        exit();
    } elseif ($action === 'delete') {
        // Query dan perintah SQL untuk menghapus data prestasi berdasarkan id_prestasi
        $sql = "DELETE FROM tab_prestasi WHERE id_prestasi = '$id_prestasi'";
        $result = $conn->query($sql);

        if ($result) {
            // Redirect ke halaman daftar prestasi setelah penghapusan berhasil
            header("Location: proses_prestasi.php");
            exit();
        } else {
            // Jika terjadi kesalahan saat menghapus prestasi
            echo "Error: " . $conn->error;
            exit();
        }
    }
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
// Mengambil data dari tabel tab_prestasi
$sql = "SELECT * FROM tab_prestasi";
$result = $conn->query($sql);

// Memeriksa apakah terdapat data prestasi
if ($result->num_rows > 0) {
    // Mengubah data hasil query menjadi array asosiatif
    $prestasi_data = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Jika tidak ada data prestasi
    $prestasi_data = [];
}

// Menutup koneksi database
$conn->close();
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
        /* Tambahkan gaya CSS berikut untuk mengatur tata letak tombol */
        .action-buttons {
        display: flex;
        justify-content: space-between;
    }

    .action-buttons button {
        flex: 1;
        margin-right: 5px;
    }
</style>
<script>
 function generateIdPrestasi($nama_prestasi, $tgl_prestasi)
{
    global $conn; // Assuming $conn is the database connection object

    // Menghapus karakter non-alfanumerik dari nama prestasi
    $clean_nama_prestasi = preg_replace("/[^a-zA-Z0-9]/", "", $nama_prestasi);

    // Mengambil 6 digit pertama dari nama prestasi
    $id_prestasi = substr($clean_nama_prestasi, 0, 6);

    // Mengubah format tanggal prestasi menjadi Ymd (misal: 2023-06-15 menjadi 20230615)
    $tanggal_prestasi = date('Ymd', strtotime($tgl_prestasi));

    // Mengambil 6 digit terakhir dari tanggal prestasi
    $id_prestasi .= substr($tanggal_prestasi, -6);

    // Generate ID Prestasi until it is unique
    do {
        // Jika panjang ID Prestasi kurang dari 12 digit, tambahkan digit acak
        while (strlen($id_prestasi) < 12) {
            $id_prestasi .= mt_rand(0, 9);
        }

        // Check if ID Prestasi already exists in the database
        $check_query = "SELECT COUNT(*) AS count FROM tab_prestasi WHERE id_prestasi = '$id_prestasi'";
        $check_result = $conn->query($check_query);
        $check_data = $check_result->fetch_assoc();

        if ($check_data['count'] > 0) {
            // ID Prestasi already exists, reset the ID and generate again
            $id_prestasi = substr($id_prestasi, 0, -6); // Remove the last 6 digits
            $id_prestasi .= mt_rand(0, 9);
        } else {
            // ID Prestasi is unique, exit the loop
            break;
        }
    } while (true);

    return $id_prestasi;
}

</script>

<script>
    // Mendefinisikan fungsi JavaScript untuk memperbarui field nama_ukm
    function updateNamaUKM(select) {
      var id_ukm = select.value;
      var nama_ukmField = document.getElementById("nama_ukm");

      // Mengirim permintaan AJAX ke server
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          // Mengambil respons dari server
          var nama_ukm = this.responseText;

          // Mengatur nilai field nama_ukm dengan respons dari server
          nama_ukmField.value = nama_ukm;
        }
      };
      xhttp.open("GET", "get_nama_ukm.php?id_ukm=" + id_ukm, true);
      xhttp.send();
    }
    </script>
    <script>
    function confirmDelete() {
        if (confirm("Apakah yakin ingin menghapus prestasi?")) {
            return true;
        } else {
            return false;
        }
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
    
  <!-- Data Prestasi -->
<div class="content">
    <h2>Data Prestasi</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID Prestasi</th>
                <th>Nama Prestasi</th>
                <th>Penyelenggara</th>
                <th>Tanggal Prestasi</th>
                <th>ID UKM</th>
                <th>Nama UKM</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prestasi_data as $prestasi) : ?>
                <tr>
                    <td><?php echo $prestasi['id_prestasi']; ?></td>
                    <td><?php echo $prestasi['nama_prestasi']; ?></td>
                    <td><?php echo $prestasi['penyelenggara']; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($prestasi['tgl_prestasi'])); ?></td>
                    <td><?php echo $prestasi['id_ukm']; ?></td>
                    <td><?php echo $prestasi['nama_ukm']; ?></td>
                    <td class="action-buttons">
                        <!-- Menggunakan form dengan method GET untuk mengarahkan ke halaman edit_prestasi.php -->
                        <form method="get" action="edit_prestasi.php">
                            <input type="hidden" name="id_prestasi" value="<?php echo $prestasi['id_prestasi']; ?>">
                            <input type="hidden" name="action" value="edit">
                            <button type="submit" class="btn btn-primary btn-sm" name="submit">Edit</button>
                        </form>
                        <!-- Menggunakan form dengan method POST untuk menghapus prestasi -->
                        <form method="post" action="proses_delete_prestasi.php" onsubmit="return confirmDelete();">
                            <input type="hidden" name="id_prestasi" value="<?php echo $prestasi['id_prestasi']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger btn-sm delete-button" name="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <!-- Form Tambah Prestasi -->
    <div class="content">
    <div class="card">
    <h2>Tambah Prestasi</h2>
        <form method="post" action="proses_prestasi.php">
              <!-- Menambahkan input field hidden untuk id_prestasi -->
              <input type="hidden" name="id_prestasi" value="<?php echo $prestasi['id_prestasi']; ?>">
            <div class="form-group">
                <label for="nama_prestasi">Nama Prestasi:</label>
                <input type="text" class="form-control" id="nama_prestasi" name="nama_prestasi" required>
            </div>
            <div class="form-group">
                <label for="penyelenggara">Penyelenggara:</label>
                <input type="text" class="form-control" id="penyelenggara" name="penyelenggara" required>
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
                        echo "<option value='$id_ukm'>$id_ukm</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nama_ukm">Nama UKM:</label>
                <input type="text" class="form-control" id="nama_ukm" name="nama_ukm" readonly>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Tambah</button>
        </form>
    </div>

</body>
</html>
