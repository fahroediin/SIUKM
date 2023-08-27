<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menandai halaman yang aktif
$active_page = 'view_prestasi';


// Check if id_admin is sent through the form submission
if (isset($_POST['id_admin'])) {
    $id_admin = $_POST['id_admin'];
}
// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_admin'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}
$id_ukm = $_SESSION['id_ukm'];

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

// Memeriksa apakah form tambah prestasi telah di-submit
if (isset($_POST['submit'])) {
    // Mengambil data dari form
    $nama_prestasi = $_POST['nama_prestasi'];
    $penyelenggara = $_POST['penyelenggara'];
    $tingkat = $_POST['tingkat'];
    $tgl_prestasi = $_POST['tgl_prestasi'];
    $id_ukm = $_POST['id_ukm'];
    $nama_ukm = $_POST['nama_ukm'];
    $sertifikat = $_POST['sertifikat'];

   // Generate ID Prestasi
$id_prestasi = generateIdPrestasi($id_ukm, $nama_prestasi, $penyelenggara, $tgl_prestasi);

 // Check if the certificate file is uploaded
 if (isset($_FILES['sertifikat']) && $_FILES['sertifikat']['error'] === UPLOAD_ERR_OK) {
    $targetDir = './assets/images/sertifikat/';
    $imageFileType = strtolower(pathinfo($_FILES['sertifikat']['name'], PATHINFO_EXTENSION));

    // Generate the certificate file name
    $certificateFilename = "sertifikat" . $id_ukm . "_" . $nama_prestasi . "." . $imageFileType;
    $targetFile = $targetDir . $certificateFilename;

    // Check file size
    if ($_FILES['sertifikat']['size'] > 5000000) {
        echo "Sorry, your file is too large.";
        exit();
    }

    // Allow certain file formats
    $allowedFormats = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($imageFileType, $allowedFormats)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        exit();
    }

    if (move_uploaded_file($_FILES['sertifikat']['tmp_name'], $targetFile)) {
        $sertifikatFilename = $certificateFilename;
        // Insert sertifikatFilename into the database query
        $sql = "INSERT INTO tab_prestasi (id_prestasi, nama_prestasi, penyelenggara, tingkat, tgl_prestasi, id_ukm, nama_ukm, sertifikat) VALUES ('$id_prestasi', '$nama_prestasi', '$penyelenggara', '$tingkat', '$tgl_prestasi', '$id_ukm', '$nama_ukm', '$sertifikatFilename')";
        // . (Bagian kode setelahnya)
    } else {
        echo "Sorry, there was an error uploading your file.";
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
       $sql = "INSERT INTO tab_prestasi (id_prestasi, nama_prestasi, penyelenggara, tingkat, tgl_prestasi, id_ukm, nama_ukm, sertifikat) VALUES ('$id_prestasi', '$nama_prestasi', '$penyelenggara', '$tingkat', '$tgl_prestasi', '$id_ukm', '$nama_ukm', '$sertifikatFilename')";
        $result = $conn->query($sql);

        if ($result) {
            // Redirect ke halaman daftar prestasi setelah penyimpanan berhasil
            header("Location: proses_prestasi.php");
            exit();
        } else {
            // Jika terjadi kesalahan saat menyimpan prestasi
            exit();
        }
    }
}

function generateIdPrestasi($id_ukm, $nama_prestasi, $penyelenggara, $tgl_prestasi)
{
    // Generate 6-digit random number
    $random_digits = mt_rand(100000, 999999);

    // Format the date as Ymd (YearMonthDay)
    $formatted_date = date('Ymd', strtotime($tgl_prestasi));

    // Combine the formatted date with the random digits
    $id_prestasi = $formatted_date . $random_digits;

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
            exit();
        }
    }
}

   // Mendapatkan data ID UKM dan nama UKM dari tabel tab_ukm
$query = "SELECT id_ukm, nama_ukm, logo_ukm, instagram, facebook, sejarah, visi, misi FROM tab_ukm WHERE id_ukm = '$id_ukm'";
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

$sql = "SELECT * FROM tab_prestasi WHERE id_ukm = '$id_ukm'";

// Cek apakah ada parameter pencarian
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " WHERE id_prestasi LIKE '%$search%' OR nama_prestasi LIKE '%$search%' OR id_ukm LIKE '%$search%' OR penyelenggara LIKE '%$search%'";
}

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
    <title>Manajemen Prestasi - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<style>
            .password-input {
    position: relative;
    }

    .password-input input {
    padding-right: 30px; /* To make space for the icon */
    }

    .password-input i {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    }
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
    .content {
    /* Atur tata letak (layout) untuk kontainer utama */
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* Penyesuaian padding atau margin sesuai kebutuhan */
}

.header {
    /* Atur tata letak (layout) untuk header */
    display: flex;
    align-items: center;
}

.header h2 {
    /* Atur gaya untuk elemen H2 pada header */
    margin-right: 10px; /* Jarak antara H2 dan tombol tambah */
}
.is-invalid {
    border-color: red;
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
    <a href="index.php">
  <img src="./assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Prestasi</i></h2>
<a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
    <a href="proses_dau_pengurus.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_dau_pengurus') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_struktur_pengurus.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'struktur') echo 'active'; ?>">Pengurus</a>
    <a href="view_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="view_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_ukm') echo 'active'; ?>">Data UKM</a>
    <a href="view_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_galeri') echo 'active'; ?>">Galeri</a>
    <a href="view_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="view_calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
    <a href="view_lpj.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_lpj') echo 'active'; ?>">Unggah LPJ</a>
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
        <div class="header">
            <h2>Data Prestasi</h2>
            <button type="button" class="btn btn-primary btn-sm btn-medium" data-toggle="modal" data-target="#tambahPrestasiModal">
                <i class="fas fa-plus"></i> Tambah Prestasi
            </button>
        </div>
        <form class="form-inline mt-2 mt-md-0 float-right" method="get">
        <input class="form-control mr-sm-2" type="text" placeholder="Cari." name="search" aria-label="Search">
        <button type="submit" class="btn btn-outline-primary">Search</button>
    <a href="proses_prestasi.php" class="btn btn-outline-secondary ml-2">
  <i class="fas fa-sync-alt"></i>
</a>
    </div>
</form>

<div class="content">
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Prestasi</th>
                <th>Nama Prestasi</th>
                <th>Penyelenggara</th>
                <th>Tingkat</th>
                <th>Tanggal Prestasi</th>
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
    if (count($prestasi_data) > 0) {
        $counter = 1;
        foreach ($prestasi_data as $prestasi) {
    ?>
        <tr>
        <td><?php echo $counter; ?></td>
            <td><?php echo $prestasi['id_prestasi']; ?></td>
            <td><?php echo $prestasi['nama_prestasi']; ?></td>
            <td><?php echo $prestasi['penyelenggara']; ?></td>
            <td><?php echo $prestasi['tingkat']; ?></td>
            <td><?php echo date('d', strtotime($prestasi['tgl_prestasi'])); ?> <?php echo $indonesianMonths[intval(date('m', strtotime($prestasi['tgl_prestasi']))) - 1]; ?> <?php echo date('Y', strtotime($prestasi['tgl_prestasi'])); ?></td>
            <td><?php echo $prestasi['nama_ukm']; ?></td>
            <td>
            <img src="./assets/images/sertifikat/<?php echo htmlspecialchars($prestasi['sertifikat']); ?>" alt="Sertifikat" class="preview-image" onclick="#sertifikatPreview('./assets/images/sertifikat/<?php echo htmlspecialchars($prestasi['sertifikat']); ?>')">
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
        }
    } else {
        echo '<tr><td colspan="9" style="text-align: center;">Tidak ada data prestasi</td></tr>';
    }
    ?>
    </tbody>
</table>
</div>
          <!-- Modal for Tambah Prestasi -->
          <div class="modal fade" id="tambahPrestasiModal" tabindex="-1" role="dialog" aria-labelledby="tambahPrestasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <h2 style="text-align: center;">Tambah Prestasi</h2>
            <form method="post" action="proses_prestasi.php" enctype="multipart/form-data">
              <!-- Menambahkan input field hidden untuk id_prestasi -->
              <input type="hidden" name="id_prestasi" value="<?php echo $prestasi['id_prestasi']; ?>">
              <div class="form-group">
                <label for="nama_prestasi">*Nama Prestasi:</label>
                <input type="text" class="form-control" id="nama_prestasi" placeholder="Masukan nama prestasi maksimal 30 karakter" maxlength="30" name="nama_prestasi" required>
                <div class="invalid-feedback">
                    Nama prestasi tidak boleh lebih dari 30 karakter.
                </div>
            </div>

            <div class="form-group">
                <label for="penyelenggara">*Penyelenggara:</label>
                <input type="text" class="form-control" id="penyelenggara" placeholder="Masukan nama penyelenggara maksimal 30 karakter" maxlength="30" name="penyelenggara" required>
                <div class="invalid-feedback">
                    Nama prestasi tidak boleh lebih dari 30 karakter.
            </div>
            <div class="form-group">
                <label for="tingkat">*Tingkat:</label>
                <select class="form-control" id="tingkat" name="tingkat" required>
                    <option value="Nasional">Nasional</option>
                    <option value="Internasional">Internasional</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tgl_prestasi">*Tanggal Prestasi:</label>
                <input type="date" class="form-control" id="tgl_prestasi" name="tgl_prestasi" value="<?php echo $prestasi['tgl_prestasi']; ?>" required>
            </div>
            <div class="form-group">
    <label for="id_ukm">*Nama UKM:</label>
    <select id="id_ukm" class="form-control" name="id_ukm" required onchange="updateNamaUKM(this)">
        <option value="" selected disabled>Pilih UKM</option>
        <?php
        // Membuat opsi combobox dari hasil query
        foreach ($namaUKM as $id_ukm => $nama_ukm) {
            $selected = ($prestasi['id_ukm'] == $id_ukm) ? 'selected' : '';
            echo "<option value='$id_ukm' $selected>$nama_ukm</option>";
        }
        ?>
    </select>
    <!-- Hidden input field to store the nama_ukm value -->
    <input type="hidden" class="form-control" id="nama_ukm" name="nama_ukm" value="<?php echo $prestasi['nama_ukm']; ?>" readonly>
</div>
<script>
    function updateNamaUKM(selectElement) {
    var selectedIdUkm = selectElement.value;
    var namaUkmField = document.getElementById("nama_ukm");
    var namaUkmHiddenField = document.getElementById("nama_ukm_hidden");

    // Set the value of the "nama_ukm" field based on the selected "id_ukm"
    if (selectedIdUkm in <?php echo json_encode($namaUKM); ?>) {
        namaUkmField.value = <?php echo json_encode($namaUKM); ?>[selectedIdUkm];
        namaUkmHiddenField.value = <?php echo json_encode($namaUKM); ?>[selectedIdUkm]; // Set the hidden field value
    } else {
        namaUkmField.value = '';
        namaUkmHiddenField.value = '';
    }
}
</script>
            <div class="form-group">
                <label for="sertifikat">Sertifikat:</label>
                <input type="file" class="form-control-file" id="sertifikat" name="sertifikat" accept=".jpg, .jpeg, .png, .gif">
                <img id="sertifikatPreview" src="" alt="Sertifikat Preview" width="100">
            </div>
            <div class="text-center"> <!-- Wrap the button in a div with the "text-center" class -->
            <button type="submit" class="btn btn-primary btn-sm btn-medium" name="submit">
    <i class="fas fa-plus"></i> Tambah Prestasi
</button>
    </div>
        </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to confirm the delete action
    function confirmDelete() {
        return confirm("Apakah Anda yakin akan menghapus data prestasi?");
    }
</script>
<script>
    // Function to update certificate preview
    function updateCertificatePreview(input) {
        const preview = document.getElementById("sertifikatPreview");
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    // Add event listener for the sertifikat input
    const sertifikatInput = document.getElementById('sertifikat');
    sertifikatInput.addEventListener('change', function () {
        updateCertificatePreview(this);
    });

    // Function to open the certificate preview modal
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
