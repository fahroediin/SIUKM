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
if ($_SESSION['level'] == "3" || $_SESSION['level'] == "2") {
    // Jika level adalah "3" atau "2", redirect ke halaman index.php
    header("Location: index.php");
    exit();
}

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


function generateUniqueFilename($filename, $extension) {
    $filename = preg_replace("/[^a-zA-Z0-9]/", "", $filename); // Remove non-alphanumeric characters
    $filename = str_replace(" ", "", $filename); // Remove spaces
    return $filename . "." . $extension;
}

// Menandai halaman yang aktif
$active_page = 'galeri';

// Function to generate a random 4-digit number
function generateRandomNumber()
{
    return sprintf("%04d", mt_rand(1, 9999));
}

// Function to generate id_foto based on tgl and random number
function generateIdFoto($tgl)
{
    // Convert the date to a DateTime object
    $date = new DateTime($tgl);
    
    // Extract the year, month, and day from the DateTime object
    $year = $date->format('Y');
    $month = $date->format('m');
    $day = $date->format('d');

    // Generate a unique random number
    $randomNumber = sprintf("%04d", mt_rand(1, 9999));

    // Concatenate the year, month, day, and random number without hyphens
    return $year . $month . $day . $randomNumber;
}

// Function to generate id_kegiatan based on tgl and random number
function generateIdKegiatan($tgl)
{
    // Convert the date to a DateTime object
    $date = new DateTime($tgl);
    
    // Extract the year, month, and day from the DateTime object
    $year = $date->format('Y');
    $month = $date->format('m');
    $day = $date->format('d');

    // Generate a unique random number
    $randomNumber = sprintf("%04d", mt_rand(1, 9999));

    // Concatenate the year, month, day, and random number without hyphens
    return $year . $month . $day . $randomNumber;
}


// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $id_kegiatan = mysqli_real_escape_string($conn, $_POST["id_kegiatan"]);
    $id_ukm = mysqli_real_escape_string($conn, $_POST["id_ukm"]);
    $nama_ukm = mysqli_real_escape_string($conn, $_POST["nama_ukm"]);
    $nama_kegiatan = mysqli_real_escape_string($conn, $_POST["nama_kegiatan"]);
    $jenis = mysqli_real_escape_string($conn, $_POST["jenis"]);
    $deskripsi = mysqli_real_escape_string($conn, $_POST["deskripsi"]);
    $tgl = mysqli_real_escape_string($conn, $_POST["tgl"]);
    // Define the maximum file size in bytes (5MB)
    $maxFileSize = 5 * 1024 * 1024;

    // Check if the file size exceeds the maximum limit
    if ($_FILES["foto_kegiatan"]["size"] > $maxFileSize) {
        // Handle the error condition, for example:
        echo "Sorry, your file exceeds the maximum allowed size (5MB).";
        exit();
    }

    // Handle file upload
    $targetDir = "./assets/images/kegiatan/";

    if (isset($_FILES["foto_kegiatan"]["name"]) && $_FILES["foto_kegiatan"]["name"] != "") {
        $foto_kegiatan = $_FILES["foto_kegiatan"]["name"];
        $foto_kegiatan_extension = strtolower(pathinfo($foto_kegiatan, PATHINFO_EXTENSION));
        $nama_kegiatan = $_POST["nama_kegiatan"];
    
        // Generate a unique filename based on the id_foto and the extension
        $id_foto = generateIdFoto($tgl);
        $uniqueFilename = $id_foto . "." . $foto_kegiatan_extension;
    
        $targetFilePath = $targetDir . $uniqueFilename;

        // Check if the image file is an actual image or a fake image
        $check = getimagesize($_FILES["foto_kegiatan"]["tmp_name"]);
        if ($check === false) {
            // Handle the error condition, for example:
            echo "Sorry, your file is not a valid image.";
            exit();
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES["foto_kegiatan"]["tmp_name"], $targetFilePath)) {
            // Handle the error condition, for example:
            echo "Sorry, there was an error uploading your file.";
            exit();
        }

                // Generate a unique id_foto based on tgl and random number
                $id_foto = generateIdFoto($tgl);

            
                // Generate a unique id_kegiatan based on tgl and random number
                $id_kegiatan = generateIdKegiatan($tgl);

        // Prepare the SQL query to insert data into tab_galeri table
    $sql = "INSERT INTO tab_galeri (id_foto, id_kegiatan, id_ukm, nama_ukm, nama_kegiatan, jenis, deskripsi, foto_kegiatan, tgl) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters
    $stmt->bind_param("sssssssss", $id_kegiatan, $id_foto, $id_ukm, $nama_ukm, $nama_kegiatan, $jenis, $deskripsi, $uniqueFilename, $tgl);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the same page with a success parameter
            header("Location: proses_galeri.php?success=1");
            exit();
        } else {
            // Handle the error condition, for example:
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }
}
// Mendapatkan data ID UKM dan nama UKM dari tabel tab_ukm
$query_ukm = "SELECT id_ukm, nama_ukm FROM tab_ukm";
$result_ukm = mysqli_query($conn, $query_ukm);

// Mendapatkan data ID kegiatan dan nama kegiatan dari tabel tab_kegiatan
$query_kegiatan = "SELECT id_kegiatan, nama_kegiatan FROM tab_kegiatan";
$result_kegiatan = mysqli_query($conn, $query_kegiatan);

// Fetch data from the tab_galeri table with search filter
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query_galeri = "SELECT id_foto, id_kegiatan, id_ukm, nama_ukm, nama_kegiatan, jenis, deskripsi, foto_kegiatan, tgl FROM tab_galeri";

if (!empty($searchTerm)) {
    $query_galeri .= " WHERE id_foto LIKE '%$searchTerm%' OR id_kegiatan LIKE '%$searchTerm%' OR nama_ukm LIKE '%$searchTerm%'";
}

$result_galeri = mysqli_query($conn, $query_galeri);

?>

<!DOCTYPE html>
<html>

<head>
<title>Manajemen Galeri - SIUKM</title>
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
</style>

 <!-- Sidebar -->
 <div class="sidebar">
    <a href="index.php">
  <img src="./assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
    <h2><i>Galeri</i></h2>
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
    <h2>Data Galeri</h2>
    <button type="button" class="btn btn-primary btn-sm btn-medium" data-toggle="modal" data-target="#addModal">
        <i class="fas fa-plus"></i> Tambah Foto
    </button>
    </div>
        <form class="form-inline mt-2 mt-md-0 float-right" method="get">
        <input class="form-control mr-sm-2" type="text" placeholder="Cari berdasarkan ID Foto, ID Kegiatan, atau Nama UKM">
        <button type="submit" class="btn btn-primary btn-sm">Search</button>
        <a href="proses_galeri.php" class="btn btn-outline-secondary ml-2">
  <i class="fas fa-sync-alt"></i>
</a>
    </div>
</form>
   

<div class="content">
    <table class="table">
        <thead>
            <tr>
            <th>ID Foto</th>
            <th>ID Kegiatan</th>
            <th>Nama UKM</th>
            <th>Nama Kegiatan</th>
            <th>Jenis Kegiatan</th>
            <th>Deskripsi</th>
            <th>Foto Kegiatan</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
            if (mysqli_num_rows($result_galeri) > 0) {
                    // Define Indonesian month names
            $indonesianMonths = array(
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                'Agustus', 'September', 'Oktober', 'November', 'Desember'
            );
            while ($row_galeri = mysqli_fetch_assoc($result_galeri)) {
                // Output table rows
                echo "<tr>";
                echo "<td>" . $row_galeri['id_foto'] . "</td>";
                echo "<td>" . $row_galeri['id_kegiatan'] . "</td>";
                echo "<td>" . $row_galeri['nama_ukm'] . "</td>";
                echo "<td>" . $row_galeri['nama_kegiatan'] . "</td>";
                echo "<td>" . $row_galeri['jenis'] . "</td>";
                echo "<td>" . $row_galeri['deskripsi'] . "</td>";
                echo "<td><img src='./assets/images/kegiatan/" . $row_galeri['foto_kegiatan'] . "' width='100'></td>";
                echo "<td>" . date('d', strtotime($row_galeri['tgl'])) . " " . $indonesianMonths[intval(date('m', strtotime($row_galeri['tgl']))) - 1] . " " . date('Y', strtotime($row_galeri['tgl'])) . "</td>";
                echo "<td>
                        <a href='edit_galeri.php?id_foto=" . $row_galeri['id_foto'] . "'>Edit</a>
                        <a href='delete_galeri.php?id_foto=" . $row_galeri['id_foto'] . "' onclick='return confirmDelete(\"" . $row_galeri['nama_kegiatan'] . "\");'>Hapus</a>
                    </td>";
                echo "</tr>";
            }
        } else {
            // Display a message when there is no gallery data
            echo '<tr><td colspan="9" style="text-align: center;">Tidak ada data galeri yang ditemukan</td></tr>';
        }
        ?>
    </tbody>
</table>
</div>

<script>
    function confirmDelete(namaKegiatan) {
        // Show the confirmation alert and ask for user confirmation
        const confirmMessage = `Apakah yakin akan menghapus foto kegiatan "${namaKegiatan}"?`;
        return confirm(confirmMessage);
    }
    // Fungsi untuk logout
    function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
</script>
<!-- Modal structure -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <!-- Move your form here -->
                    <h2 style="text-align: center;">Tambah Foto</h2>
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                            enctype="multipart/form-data">
                            <div class="form-group">
                            <label for="nama_ukm">Pilih Kegiatan</label>
                            <select class="form-control" name="id_kegiatan" id="id_kegiatan_dropdown" required>
                            <option value="">Pilih Kegiatan</option>
                            <?php
                            // Fetch data from the tab_kegiatan table and populate the dropdown options
                            while ($kegiatanRow = mysqli_fetch_assoc($result_kegiatan)) {
                                echo '<option value="' . $kegiatanRow['id_kegiatan'] . '" data-id_ukm="' . $kegiatanRow['id_ukm'] . '" data-nama_ukm="' . $kegiatanRow['nama_ukm'] . '" data-tgl="' . $kegiatanRow['tgl'] . '">' . $kegiatanRow['id_kegiatan'] . ' - ' . $kegiatanRow['nama_kegiatan'] . '</option>';
                            }
                            ?>
                        </select>
                        </div>
                            <input type="hidden" id="id_ukm" name="id_ukm" class="form-control" required>
                        
                        <div class="form-group">
                            <label for="nama_ukm">Nama Ukm</label>
                            <input type="text" id="nama_ukm" name="nama_ukm" class="form-control" readonly  required>
                        </div>
                        <div class="form-group">
                            <label for="nama_kegiatan">Nama Kegiatan</label>
                            <input type="text" id="nama_kegiatan" name="nama_kegiatan" class="form-control" readonly  required>
                        </div>
                        <div class="form-group">
                            <label for="jenis">Jenis Kegiatan</label>
                            <input type="text" id="jenis" name="jenis" class="form-control" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <input type="text" id="deskripsi" name="deskripsi" class="form-control" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="foto_kegiatan">Foto Kegiatan</label>
                            <input type="file" id="foto_kegiatan" name="foto_kegiatan" accept="image/*" required class="form-control-file">
                            <img id="image-preview" src="#" alt="Foto Kegiatan" style="display: none; max-width: 100%; height: auto;">
                        </div>
                        <div class="form-group">
                            <label for="tgl">Tanggal</label>
                            <input type="text" id="tgl" name="tgl" class="form-control" readonly required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-sm btn-medium" name="submit">
                                <i class="fas fa-plus"></i> Tambah Foto
                            </button>
                        </div>
                    </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Check if the photo was successfully added (using the 'success' parameter)
    if (getUrlParameter('success') === '1') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Foto berhasil ditambahkan ke Galeri',
        });
    }

    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
</script>

<script>
$(document).ready(function() {
    $("#foto_kegiatan").change(function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#image-preview').attr('src', e.target.result);
            $('#image-preview').css('display', 'block');
        }
        reader.readAsDataURL(this.files[0]);
    });
});
</script>

<script>
$(document).ready(function() {
    $("#id_kegiatan_dropdown").change(function() {
        var selectedOption = $(this).find("option:selected");

        // Update the "id_ukm" field
        $("#id_ukm").val(selectedOption.data("id_ukm"));

        // Update the "nama_ukm" field
        $("#nama_ukm").val(selectedOption.data("nama_ukm"));

        // Update the "nama_kegiatan" field
        $("#nama_kegiatan").val(selectedOption.text());

        // Get the date value from the selected option's data-tgl attribute
        var rawDate = selectedOption.data("tgl");

        // Parse the rawDate and create a new Date object
        var dateObject = new Date(rawDate);

        // Format the date as Tanggal-Bulan-Tahun
        var formattedDate = formatDate(dateObject);

        // Update the "tgl" field with the formatted date
        $("#tgl").val(formattedDate);
    });

    // Function to format date as Tanggal-Bulan-Tahun
    function formatDate(date) {
        var day = date.getDate();
        var monthIndex = date.getMonth();
        var year = date.getFullYear();

        // Array of Indonesian month names
        var indonesianMonths = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        // Format the date as Tanggal-Bulan-Tahun
        var formattedDate = day + " " + indonesianMonths[monthIndex] + " " + year;

        return formattedDate;
    }
});
</script>
<script>
    $("#id_kegiatan_dropdown").change(function() {
    var id_kegiatan = $(this).val();

    $.ajax({
        url: "get_kegiatan_details.php", // Replace with the actual URL to your PHP script
        method: "POST",
        data: { id_kegiatan: id_kegiatan },
        success: function(response) {
            var data = JSON.parse(response);
            // Update the fields using the retrieved data
            $("#id_ukm").val(data.id_ukm);
            $("#nama_ukm").val(data.nama_ukm);
            $("#nama_kegiatan").val(data.nama_kegiatan);
            $("#jenis").val(data.jenis);
            $("#deskripsi").val(data.deskripsi);
            $("#tgl").val(data.tgl);
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
});
</script>
<script>
    // Listen for the click event on delete buttons
    $(".delete-button").click(function() {
        var idFoto = $(this).data("id-foto");
        var namaKegiatan = $(this).data("nama-kegiatan");

        // Show a confirmation dialog using SweetAlert
        Swal.fire({
            title: 'Apakah Anda yakin ingin menghapus foto kegiatan "' + namaKegiatan + '"?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // If the user confirms deletion, proceed with the actual deletion
                window.location.href = 'delete_galeri.php?id_foto=' + idFoto;
            }
        });
    });

    // Check if the photo was successfully deleted (using the 'success' parameter)
    if (getUrlParameter('success') === '1') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Foto berhasil dihapus',
        });
    }

    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
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