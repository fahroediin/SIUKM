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
$active_page = 'ukm';

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

function generateLogoFilename($id_ukm, $extension)
{
    // Concatenate the id_ukm with the "-logo" suffix and the extension
    $logoFilename = $id_ukm . "-logo." . $extension;
    return $logoFilename;
}

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai-nilai dari form
    $id_ukm = $_POST["id_ukm"];
    $nama_ukm = $_POST["nama_ukm"];
    $sejarah = $_POST["sejarah"];
    $instagram = $_POST["instagram"];
    $facebook = $_POST["facebook"];
    $visi = $_POST["visi"];
    $misi = $_POST["misi"];

    // Check if a logo file is uploaded
    if ($_FILES["logo_ukm"]["name"] != "") {
        // Define the target directory for the logo file
        $targetDir = "../assets/images/logoukm/";

        // Get the original filename and extension
        $logo_ukm_name = $_FILES["logo_ukm"]["name"];
        $logo_ukm_extension = strtolower(pathinfo($logo_ukm_name, PATHINFO_EXTENSION));

        // Check if the file format is allowed
        if (!in_array($logo_ukm_extension, ['jpeg', 'jpg', 'png'])) {
            echo "Sorry, only JPEG, JPG, and PNG files are allowed.";
            exit();
        }

        // Generate the logo filename based on id_ukm and the validated extension
        $logo_ukm_filename = generateLogoFilename($id_ukm, $logo_ukm_extension);

        // Move the uploaded logo file to the target directory
        if (!move_uploaded_file($_FILES["logo_ukm"]["tmp_name"], $targetDir . $logo_ukm_filename)) {
            // Handle the error condition, for example:
            echo "Sorry, there was an error uploading the logo file.";
            exit();
        }
    } else {
        // If no logo file is uploaded, use the existing logo filename
        $logo_ukm_filename = $logo_ukm;
    }

     // Menyimpan data ke database
     $sql = "UPDATE tab_ukm SET nama_ukm='$nama_ukm', sejarah='$sejarah', logo_ukm='$logo_ukm_filename', instagram='$instagram', facebook='$facebook', visi='$visi', misi='$misi' WHERE id_ukm='$id_ukm'";
     $result = $conn->query($sql);

    if ($result) {
        // Redirect ke halaman daftar struktur setelah penyimpanan berhasil
        header("Location: proses_ukm.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menyimpan struktur
        echo "Error: " . $conn->error;
        exit();
    }
}

// Mendapatkan data ID UKM dan nama UKM dari tabel tab_ukm
$query = "SELECT id_ukm, nama_ukm, logo_ukm, instagram, facebook, sejarah, visi, misi FROM tab_ukm";
$result = mysqli_query($conn, $query);

// Inisialisasi variabel untuk opsi combobox
$options = "";

// Buat array untuk menyimpan data nama_ukm berdasarkan id_ukm
$namaUKM = array();
while ($row = mysqli_fetch_assoc($result)) {
  $id_ukm = $row['id_ukm'];
  $nama_ukm = $row['nama_ukm'];
  $logo_ukm = $row['logo_ukm'];
  $instagram = $row['instagram'];
  $facebook = $row['facebook'];
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
      var instagramField = document.getElementById("instagram");
      var facebookField = document.getElementById("facebook");
      var visiField = document.getElementById("visi");
      var misiField = document.getElementById("misi");
      var logo_ukmField = document.getElementById("logo_ukm_preview");

      // Mengirim permintaan AJAX ke server
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // Mengambil respons dari server dalam bentuk JSON
            var data = JSON.parse(this.responseText);

            // Mengatur nilai field-field yang sesuai dengan respons dari server
            nama_ukmField.value = data.nama_ukm;
            sejarahField.value = data.sejarah;
            instagramField.value = data.instagram;
            facebookField.value = data.facebook;
            visiField.value = data.visi;
            misiField.value = data.misi;

            // Update the logo preview image
            logo_ukmField.src = "../assets/images/logoukm/" + data.logo_ukm;
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
    <a href="proses_dau.php" class="btn btn-primary <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_prestasi.php" class="btn btn-primary <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_ukm.php" class="btn btn-primary <?php if($active_page == 'ukm') echo 'active'; ?>">Data UKM</a>
    <a href="proses_galeri.php" class="btn btn-primary <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="proses_kegiatan.php" class="btn btn-primary <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
</div>
<body>
<!-- Main content -->
<div class="content">
    <div class="card">
    <h2>Data UKM</h2>
    <form id="dataForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
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
                <input type="text" class="form-control" id="nama_ukm" name="nama_ukm" readonly required>
            </div>
            <div class="form-group">
                <label for="sejarah">Sejarah:</label>
                <textarea class="form-control" placeholder="Isikan sejarah UKM" id="sejarah" name="sejarah" rows="5"></textarea>
            </div>
                    <div class="form-group">
                <label for="logo_ukm">Logo UKM:</label>
                <input type="file" class="form-control-file" id="logo_ukm" name="logo_ukm">
                <!-- Add this img tag to display the logo preview -->
                <img id="logo_ukm_preview" src="" alt="Logo UKM" style="max-width: 100px; max-height: 100px; margin-top: 10px;">

                <!-- Add the message for logo upload -->
                <p class="mt-2" style="font-size: 12px; color: #777;">Upload logo UKM dengan resolusi 512x512 pixel.</p>
            </div>
            <div class="form-group">
            <label for="instagram">Instagram:</label>
            <input type="text" placeholder="Masukkan akun instagram ukm tanpa @" class="form-control" id="instagram" name="instagram" pattern="[a-zA-Z]+">
        </div>
        <div class="form-group">
            <label for="facebook">Facebook:</label>
            <input type="text" placeholder="Masukkan akun facebook ukm tanpa @" class="form-control" id="facebook" name="facebook" pattern="[a-zA-Z]+">
        </div>

            <div class="form-group">
                <label for="visi">Visi:</label>
                <textarea class="form-control" placeholder="Sebaiknya buka dan tutup kalimat dengan tanda petik" id="visi" name="visi" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="misi">Misi:</label>
                <textarea class="form-control" placeholder="Sebaiknya buka dan tutup kalimat dengan tanda petik" id="misi" name="misi" rows="3"></textarea>
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