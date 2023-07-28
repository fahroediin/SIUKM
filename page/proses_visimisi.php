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
if ($_SESSION['level'] == "2" || $_SESSION['level'] == "3") {
    // Jika level adalah "2" atau "3", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}


// Menandai halaman yang aktif
$active_page = 'visi_misi';

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

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai-nilai dari form
    $id_ukm = $_POST["id_ukm"];
    $nama_ukm = $_POST["nama_ukm"];
    $sejarah = $_POST["sejarah"];
    $logo_ukm = $_POST["logo_ukm"];
    $nama_ketua = $_POST["nama_ketua"];
    $nim_ketua = $_POST["nim_ketua"];
    $visi = $_POST["visi"];
    $misi = $_POST["misi"];

     // Menyimpan data ke database
    $sql = "UPDATE tab_ukm SET nama_ukm='$nama_ukm', sejarah='$sejarah', logo_ukm='$logo_ukm', nama_ketua='$nama_ketua', nim_ketua='$nim_ketua', visi='$visi', misi='$misi' WHERE id_ukm='$id_ukm'";
    $result = $conn->query($sql);

    if ($result) {
        // Redirect ke halaman daftar struktur setelah penyimpanan berhasil
        header("Location: proses_visimisi.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menyimpan struktur
        echo "Error: " . $conn->error;
        exit();
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
  $logo_ukm = $row['logo_ukm'];
  $nama_ketua = $row['nama_ketua'];
  $nim_ketua = $row['nim_ketua'];
  $sejarah = $row['sejarah'];
  $visi = $row['visi'];
  $misi = $row['misi'];
  $namaUKM[$id_ukm] = $nama_ukm;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Data UKM - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <script>
    // Mendefinisikan fungsi JavaScript untuk memperbarui field nama_ukm, sejarah, nama_ketua, nim_ketua, visi, dan misi
    function updateFormData(select) {
      var id_ukm = select.value;
      var nama_ukmField = document.getElementById("nama_ukm");
      var sejarahField = document.getElementById("sejarah");
      var nama_ketuaField = document.getElementById("nama_ketua");
      var nim_ketuaField = document.getElementById("nim_ketua");
      var visiField = document.getElementById("visi");
      var misiField = document.getElementById("misi");

      // Mengirim permintaan AJAX ke server
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          // Mengambil respons dari server dalam bentuk JSON
          var data = JSON.parse(this.responseText);

          // Mengatur nilai field-field yang sesuai dengan respons dari server
          nama_ukmField.value = data.nama_ukm;
          sejarahField.value = data.sejarah;
          nama_ketuaField.value = data.nama_ketua;
          nim_ketuaField.value = data.nim_ketua;
          visiField.value = data.visi;
          misiField.value = data.misi;
        }
      };
      xhttp.open("GET", "get_data_ukm.php?id_ukm=" + id_ukm, true);
      xhttp.send();
    }
</script>

    
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

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Manajemen Data UKM</h2>
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

<!-- Main content -->
<div class="content">
    <div class="card">
    <h2>Edit Data UKM</h2>
    <form id="dataForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="id_ukm">ID UKM:</label>
                <select id="id_ukm" class="form-control" name="id_ukm" required onchange="updateFormData(this)">
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
                <input type="text" class="form-control" id="nama_ukm" name="nama_ukm">
            </div>
            <div class="form-group">
                <label for="sejarah">Sejarah:</label>
                <textarea class="form-control" id="sejarah" name="sejarah" rows="5"></textarea>
            </div>
            <div class="form-group">
                <label for="logo_ukm">Logo UKM:</label>
                <input type="file" class="form-control-file" id="logo_ukm" name="logo_ukm">
            </div>
            <div class="form-group">
                <label for="nama_ketua">Nama Ketua:</label>
                <input type="text" class="form-control" id="nama_ketua" name="nama_ketua">
            </div>
            <div class="form-group">
                <label for="nim_ketua">NIM Ketua:</label>
                <input type="text" class="form-control" id="nim_ketua" name="nim_ketua">
            </div>
            <div class="form-group">
                <label for="visi">Visi:</label>
                <textarea class="form-control" id="visi" name="visi" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="misi">Misi:</label>
                <textarea class="form-control" id="misi" name="misi" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" onclick="showConfirmation(event)">Simpan</button>
        </form>
    </div>

    <!-- Snackbar -->
<div id="snackbar"></div>
<script>
  function showConfirmation(event) {
    event.preventDefault(); // Menghentikan pengiriman form secara langsung

    // Menampilkan konfirmasi dengan fungsi confirm()
    var confirmation = confirm("Apakah Anda yakin ingin menyimpan data?");

    if (confirmation) {
      // Mengirim form jika pengguna menekan tombol "OK"
      document.getElementById("dataForm").submit();
    }
  }
</script>

<!-- Add the following script to show the alert after the page loads -->
<script>
    // Wait for the page to load
    window.addEventListener('DOMContentLoaded', (event) => {
        // Check if the URL contains a success query parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            // Show the success message
            showSnackbar('Data berhasil disimpan');
        }
    });

    // Function to show the snackbar with a message
    function showSnackbar(message) {
        const snackbar = document.getElementById('snackbar');
        snackbar.textContent = message;
        snackbar.className = 'show';

        // Hide the snackbar after 3 seconds
        setTimeout(() => {
            snackbar.className = snackbar.className.replace('show', '');
        }, 3000);
    }
</script>


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
    
        </body>
        </html>