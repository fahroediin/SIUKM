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
$active_page = 'lpj';

// Memeriksa level pengguna
if ($_SESSION['level'] == "3" || $_SESSION['level'] == "2") {
    // Jika level adalah "3" atau "2", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}
// Directory to save LPJ files
$lpjFileDirectory = '../assets/images/lpj/';
function generateUniqueId() {
    $year = date('Y');
    $randomNumber = mt_rand(100000, 999999);
    return $year . $randomNumber;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    // Sanitize and validate form inputs
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $id_ukm = mysqli_real_escape_string($conn, $_POST['id_ukm']);
    $nama_ukm = mysqli_real_escape_string($conn, $_POST['nama_ukm']);
    $tgl_laporan = mysqli_real_escape_string($conn, $_POST['tgl_laporan']);
    $laporan_bulan = mysqli_real_escape_string($conn, $_POST['laporan_bulan']);
    $laporan_tahun = mysqli_real_escape_string($conn, $_POST['laporan_tahun']);
    $saran = mysqli_real_escape_string($conn, $_POST['saran']);

    // Generate id_laporan
    $id_laporan = generateUniqueId();

    // Directory to save LPJ files
    $lpjFileDirectory = '../assets/images/lpj/';

    // Process file upload
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $tempFilePath = $_FILES['file']['tmp_name'];
        $originalFileName = $_FILES['file']['name'];

        // Generate a unique file name
        $newFileName = 'lpj' . $nama_ukm . $id_laporan . '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);

        // Move uploaded file to the desired directory
        move_uploaded_file($tempFilePath, $lpjFileDirectory . $newFileName);

        // Save the file name to the database
        $file_lpj = $newFileName;
    }

    // Insert data into the database
    $insertQuery = "INSERT INTO tab_lpj (id_laporan, jenis, id_ukm, nama_ukm, tgl_laporan, file_lpj, laporan_bulan_tahun, saran)
                    VALUES ('$id_laporan', '$jenis', '$id_ukm', '$nama_ukm', '$tgl_laporan', '$file_lpj', '$laporan_bulan-$laporan_tahun', '$saran')";
    
    if (mysqli_query($conn, $insertQuery)) {
        // Redirect to the page after successful insertion
        header("Location: proses_lpj.php");
        exit();
    } else {
        // Handle insertion error
        // You can display an error message or log the error for debugging
        echo "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html>

<head>
<title>LPJ - SIUKM</title>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
           .sidebar {
        text-align: center; /* Center the contents horizontally */
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
       .preview-image {
    max-width: 100%;
    max-height: 200px;
    margin-top: 10px;
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
.content {
    /* Atur tata letak (layout) untuk kontainer utama */
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* Penyesuaian padding atau margin sesuai kebutuhan */
}
#file-preview-label {
    display: none; /* Hide the label by default */
    margin-top: 10px;
    font-weight: bold;
}

#file-preview-container a {
    color: #007bff;
    text-decoration: underline;
    margin-top: 5px;
}
.preview-image {
    max-width: 100%;
    max-height: 200px;
    margin-top: 10px;
}
   </style>
 <!-- Sidebar -->
 <div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
    <h2><i>Profil</i></h2>
    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <p style="text-align: center;">--Manajemen--</p>
    <a href="proses_beranda.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_beranda') echo 'active'; ?>">Beranda</a>
    <a href="proses_profil.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_profil') echo 'active'; ?>">Profil</a>
    <a href="proses_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="proses_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="proses_user.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_dau.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_struktur.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'struktur') echo 'active'; ?>">Pengurus</a>
    <a href="proses_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'ukm') echo 'active'; ?>">Data UKM</a>
    <a href="calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
    <a href="proses_lpj.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'lpj') echo 'active'; ?>">LPJ</a>
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
    <div class="header">
    <h2>Data LPJ</h2>
    <button type="button" class="btn btn-primary" onclick="openLaporModal()">Tambah LPJ</button>
    </div>
</div>

<div class="content">
     <!-- Table to display LPJ data -->
    <table class="table">
            <thead>
            <tr>
                <th>ID Laporan</th>
                <th>Jenis</th>
                <th>Nama UKM</th>
                <th>Tanggal Laporan</th>
                <th>File</th>
            </tr>
        </thead>

        <tbody>
    <?php
    // Fetch data from the tab_lpj table
    $lpjQuery = "SELECT * FROM tab_lpj";
    $lpjResult = mysqli_query($conn, $lpjQuery);

    function formatTanggalIndonesia($tanggal) {
        $bulan = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
    
        // Ambil bagian tanggal dari format datetime
        $tanggal_array = explode(' ', $tanggal);
        $date_part = $tanggal_array[0];
        $tanggal_parts = explode('-', $date_part);
        $day = $tanggal_parts[2];
        $month = intval($tanggal_parts[1]);
        $year = $tanggal_parts[0];
    
        return $day . ' ' . $bulan[$month] . ' ' . $year;
    }
    

    while ($lpjRow = mysqli_fetch_assoc($lpjResult)) {
        echo '<tr>';
        echo '<td>' . $lpjRow['id_laporan'] . '</td>';
        echo '<td>' . $lpjRow['jenis'] . '</td>';
        echo '<td>' . $lpjRow['nama_ukm'] . '</td>';
        echo '<td>' . formatTanggalIndonesia($lpjRow['tgl_laporan']) . '</td>';
        echo '<td><a href="view_lpj.php?file=' . $lpjRow['file_lpj'] . '">Download</a></td>';
        echo '</tr>';                
    }
    ?>
</tbody>

    </table>
</div>
<!-- Modal for Lapor LPJ -->
<div class="modal fade" id="laporModal" tabindex="-1" role="dialog" aria-labelledby="laporModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="laporModalLabel">Lapor LPJ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <div class="row justify-content-center">
            <div class="card">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                    enctype="multipart/form-data" class="form">
                    <input type="hidden" name="action" value="add">
                <div class="form-group">
                            <label for="jenis">*Jenis Laporan</label>
                             <!-- Input for id_laporan -->
                    <input type="hidden" id="id_laporan" name="id_laporan" value="<?php echo generateUniqueId(); ?>" readonly>
                            <select id="jenis" name="jenis" class="form-control">
                                <!-- Options for jenis values -->
                                <option value="Kegiatan">Kegiatan</option>
                                <option value="Semester">Semester</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jenis">*Nama UKM</label>
                        <select class="form-control" name="id_ukm" id="id_ukm_dropdown" required onchange="updateNamaUKM(this)">
                        <option value="">Pilih Nama UKM</option>
                        <?php
                        // Fetch data from the tab_ukm table and populate the dropdown options
                        $ukmQuery = "SELECT id_ukm, nama_ukm FROM tab_ukm";
                        $ukmResult = mysqli_query($conn, $ukmQuery);

                        while ($ukmRow = mysqli_fetch_assoc($ukmResult)) {
                            echo '<option value="' . $ukmRow['id_ukm'] . '">' . $ukmRow['nama_ukm'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" id="nama_ukm" name="nama_ukm" class="form-control">
                    <input type="hidden" id="tgl_laporan" name="tgl_laporan" class="form-control" value="<?php echo date("Y-m-d"); ?>" readonly>
                <div class="form-group">
                    <label for="file">*File</label>
                    <input type="file" id="file" name="file" accept=".pdf" class="form-control-file">
                    <!-- Add a link to the existing file for editing -->
                    <?php if(isset($row_lpj['file']) && !empty($row_lpj['file'])): ?>
                        <p>Existing File: <a href="../assets/lpj/<?php echo $row_lpj['file']; ?>" target="_blank"><?php echo $row_lpj['file']; ?></a></p>
                    <?php endif; ?>
                     <div id="file-preview-container"></div>
                </div>
                <div class="form-group">
    <label for="laporan_bulan_tahun">Laporan Bulan/Tahun</label>
    <div class="row">
        <div class="col-md-6">
            <select id="laporan_bulan" name="laporan_bulan" class="form-control">
                <option value="">Pilih Bulan</option>
                <?php
                // Generate options for months
                $months = array(
                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                );

                foreach ($months as $monthNumber => $monthName) {
                    echo '<option value="' . $monthNumber . '">' . $monthName . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <select id="laporan_tahun" name="laporan_tahun" class="form-control">
                <option value="">Pilih Tahun</option>
                <?php
                // Generate options for years (you can customize the range)
                $currentYear = date('Y');
                $startYear = $currentYear - 10; // Example: Show options from 10 years ago
                $endYear = $currentYear + 10;   // Example: Show options up to 10 years in the future

                for ($year = $startYear; $year <= $endYear; $year++) {
                    echo '<option value="' . $year . '">' . $year . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="saran">Saran</label>
    <textarea id="saran" name="saran" class="form-control"></textarea>
</div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>
<script>
function previewImage(inputId, previewContainerId) {
    const input = document.getElementById(inputId);
    const previewContainer = document.getElementById(previewContainerId);
    
    while (previewContainer.firstChild) {
        previewContainer.removeChild(previewContainer.firstChild);
    }

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(event) {
            const image = document.createElement('img');
            image.setAttribute('src', event.target.result);
            image.setAttribute('class', 'preview-image');
            previewContainer.appendChild(image);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
</script>       
<script>
function updateFilePreview(inputElement) {
    const previewContainer = document.getElementById('file-preview-container');
    const previewLabel = document.getElementById('file-preview-label');

    // Clear previous preview
    previewContainer.innerHTML = '';

    if (inputElement.files && inputElement.files[0]) {
        const file = inputElement.files[0];
        const filePreviewLink = document.createElement('a');
        filePreviewLink.href = URL.createObjectURL(file);
        filePreviewLink.target = '_blank';
        filePreviewLink.textContent = 'Preview File';

        // Append the link to the preview container
        previewContainer.appendChild(filePreviewLink);
        previewLabel.style.display = 'block'; // Display the preview label
    } else {
        previewLabel.style.display = 'none'; // Hide the preview label if no file selected
    }
}

// Attach the updateFilePreview function to the file input change event
document.getElementById('file').addEventListener('change', function() {
    updateFilePreview(this);
});

    // Function to open the Lapor LPJ modal
    function openLaporModal() {
        $('#laporModal').modal('show');
    }
</script>

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
<script>
    const idUkmSelect = document.getElementById("id_ukm_dropdown");
    const namaUkmField = document.getElementById("nama_ukm");

    idUkmSelect.addEventListener("change", function() {
        const selectedOption = idUkmSelect.options[idUkmSelect.selectedIndex];
        const namaUkm = selectedOption.text; // Get the text of the selected option
        namaUkmField.value = namaUkm; // Set the value of the hidden input field
    });

     // Function to confirm the delete action
     function confirmDelete() {
        return confirm("Apakah Anda yakin akan menghapus data kegiatan?");
    }
</script>
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
