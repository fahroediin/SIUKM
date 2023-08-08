<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menandai halaman yang aktif
$active_page = 'prestasi';


// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}

// Memeriksa level pengguna
if ($_SESSION['level'] == "3" || $_SESSION['level'] == "2") {
    // Jika level adalah "3" atau "2", redirect ke halaman beranda.php
    header("Location: beranda.php");
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

// Memeriksa apakah form tambah prestasi telah di-submit
if (isset($_POST['submit'])) {
    // Mengambil data dari form
    $nama_prestasi = $_POST['nama_prestasi'];
    $penyelenggara = $_POST['penyelenggara'];
    $tgl_prestasi = $_POST['tgl_prestasi'];
    $id_ukm = $_POST['id_ukm'];
    $nama_ukm = $_POST['nama_ukm'];
    $sertifikat = $_POST['sertifikat'];

   // Generate ID Prestasi
    $id_prestasi = generateIdPrestasi($id_ukm, $nama_prestasi, $penyelenggara, $tgl_prestasi);
    if (isset($_FILES['sertifikat'])) {
        $targetDir = '../assets/images/sertifikat/';
        $targetFile = $targetDir . basename($_FILES['sertifikat']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['sertifikat']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    
        // Check file size
        if ($_FILES['sertifikat']['size'] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
    
        // Allow certain file formats
        if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
    
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES['sertifikat']['tmp_name'], $targetFile)) {
                $sertifikatFilename = htmlspecialchars(basename($_FILES['sertifikat']['name']));
                echo "The file $sertifikatFilename has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
    $sertifikatFilename = isset($sertifikatFilename) ? $sertifikatFilename : ''; // Initialize with empty string if not set

    // Memeriksa apakah ID Prestasi sudah ada di database
    $check_query = "SELECT COUNT(*) AS count FROM tab_prestasi WHERE id_prestasi = '$id_prestasi'";
    $check_result = $conn->query($check_query);
    $check_data = $check_result->fetch_assoc();

    if ($check_data['count'] > 0) {
        // ID Prestasi sudah ada, tampilkan pesan alert
        echo '<script>alert("ID Prestasi tidak boleh sama");</script>';
    } else {
       // Menyimpan data ke database
        $sql = "INSERT INTO tab_prestasi (id_prestasi, nama_prestasi, penyelenggara, tgl_prestasi, id_ukm, nama_ukm, sertifikat) VALUES ('$id_prestasi', '$nama_prestasi', '$penyelenggara', '$tgl_prestasi', '$id_ukm', '$nama_ukm', '$sertifikatFilename')";
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

function generateIdPrestasi($id_ukm, $nama_prestasi, $penyelenggara, $tgl_prestasi)
{
    // Menghapus karakter non-alfanumerik dari id_ukm
    $clean_id_ukm = preg_replace("/[^a-zA-Z0-9]/", "", $id_ukm);

   
    $id_prestasi = substr($clean_id_ukm, 0, 3);

    // Menghapus karakter non-alfanumerik dari nama prestasi
    $clean_nama_prestasi = preg_replace("/[^a-zA-Z0-9]/", "", $nama_prestasi);

    // Mengambil 4 huruf pertama dari nama prestasi
    $id_prestasi .= substr($clean_nama_prestasi, 0, 4);

    // Menghapus karakter non-alfanumerik dari penyelenggara
    $clean_penyelenggara = preg_replace("/[^a-zA-Z0-9]/", "", $penyelenggara);

    // Mengambil 4 huruf pertama dari penyelenggara
    $id_prestasi .= substr($clean_penyelenggara, 0, 4);

    // Mengubah format tanggal prestasi menjadi Y
    $tahun_prestasi = date('Y', strtotime($tgl_prestasi));

    // Mengambil tahun dari tanggal prestasi
    $id_prestasi .= $tahun_prestasi;

    // Generate 4 digit angka acak
    $random_digits = mt_rand(1000, 9999);

    // Append the 4 random digits to the ID Prestasi
    $id_prestasi .= $random_digits;

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
$query = "SELECT id_ukm, nama_ukm, logo_ukm, instagram, facebook, sejarah, visi, misi FROM tab_ukm";
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
    .certificate-preview {
    margin-top: 10px;
}

.preview-image {
    max-width: 100%;
    height: auto;
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

    <!-- Sidebar -->
    
<div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Prestasi</i></h2>
            <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
            <a href="proses_beranda.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_beranda') echo 'active'; ?>">Beranda</a>
            <a href="proses_profil.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_profil') echo 'active'; ?>">Profil</a>
            <a href="proses_struktur.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'struktur') echo 'active'; ?>">Pengurus</a>
    <a href="proses_dau.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'ukm') echo 'active'; ?>">Data UKM</a>
    <a href="proses_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="proses_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
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
  <!-- Data Prestasi -->
  
<div class="content">
    <h2>Data Prestasi</h2>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Prestasi</th>
                <th>Nama Prestasi</th>
                <th>Penyelenggara</th>
                <th>Tanggal Prestasi</th>
                <th>ID UKM</th>
                <th>Nama UKM</th>
                <th>Sertifikat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
    <?php
    // Define Indonesian month names
    $indonesianMonths = array(
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
        'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $counter = 1;
    foreach ($prestasi_data as $prestasi) :
    ?>
        <tr>
        <td><?php echo $counter; ?></td>
            <td><?php echo $prestasi['id_prestasi']; ?></td>
            <td><?php echo $prestasi['nama_prestasi']; ?></td>
            <td><?php echo $prestasi['penyelenggara']; ?></td>
            <td><?php echo date('d', strtotime($prestasi['tgl_prestasi'])); ?> <?php echo $indonesianMonths[intval(date('m', strtotime($prestasi['tgl_prestasi']))) - 1]; ?> <?php echo date('Y', strtotime($prestasi['tgl_prestasi'])); ?></td>
            <td><?php echo $prestasi['id_ukm']; ?></td>
            <td><?php echo $prestasi['nama_ukm']; ?></td>
            <td>
            <img src="../assets/images/sertifikat/<?php echo htmlspecialchars($prestasi['sertifikat']); ?>" alt="Sertifikat" class="preview-image" onclick="openCertificateModal('../assets/images/sertifikat/<?php echo htmlspecialchars($prestasi['sertifikat']); ?>')">
            </td>
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
        
    <?php 
       $counter++;
endforeach; ?>
</tbody>
    </table>
</div>
    <!-- Form Tambah Prestasi -->
    <div class="content">
    <div class="card">
    <h2 style="text-align: center;">Tambah Prestasi</h2>
    <form method="post" action="proses_prestasi.php" enctype="multipart/form-data">
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
            <div class="form-group">
                <label for="sertifikat">Sertifikat:</label>
                <input type="file" class="form-control-file" id="sertifikat" name="sertifikat" accept=".jpg, .jpeg, .png, .gif">
                <?php if (isset($_FILES['sertifikat'])): ?>
                    <div class="certificate-preview">
                        <img src="../assets/images/sertifikat/<?php echo htmlspecialchars(basename($_FILES['sertifikat']['name'])); ?>" alt="Certificate Preview" class="preview-image">
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center"> <!-- Wrap the button in a div with the "text-center" class -->
            <button type="submit" class="btn btn-primary btn-sm btn-medium" name="submit">
    <i class="fas fa-plus"></i> Tambah Prestasi
</button>
    </div>
        </form>
    </div>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Function to confirm the delete action
    function confirmDelete() {
        return confirm("Apakah Anda yakin akan menghapus data prestasi?");
    }
    // Fungsi untuk logout
    function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
</script>
<script>
  // Function to open the modal with the clicked certificate image
  function openCertificateModal(imageSrc) {
    const modal = document.getElementById("certificateModal");
    const certificateImage = document.getElementById("certificateImage");
    
    // Set the source of the modal image to the clicked image's source
    certificateImage.src = imageSrc;
    
    // Open the modal
    $(modal).modal('show');
  }
</script>

<!-- Modal for Certificate Preview -->
<div class="modal fade" id="certificateModal" tabindex="-1" role="dialog" aria-labelledby="certificateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="certificateModalLabel">Certificate Preview</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" alt="Certificate" id="certificateImage" class="img-fluid">
      </div>
    </div>
  </div>
</div>
</body>
</html>
