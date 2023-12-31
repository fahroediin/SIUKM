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
    // Jika level adalah "3" atau "2", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}


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

// Menandai halaman yang aktif
$active_page = 'prestasi';

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

// Check if form is submitted for update
if (isset($_POST['update'])) {
    // Mengambil data dari form dan melakukan sanitasi
    $id_prestasi = $_POST['id_prestasi'];
    $nama_prestasi = $_POST['nama_prestasi'];
    $penyelenggara = $_POST['penyelenggara'];
    $tgl_prestasi = $_POST['tgl_prestasi'];
    $id_ukm = $_POST['id_ukm'];

    // Get the corresponding 'nama_ukm' based on 'id_ukm' from the $namaUKM array
    $nama_ukm = $namaUKM[$id_ukm];

    // Memperbarui data prestasi di database
    $sql = "UPDATE tab_prestasi SET nama_prestasi = ?, penyelenggara = ?, tgl_prestasi = ?, id_ukm = ?, nama_ukm = ? WHERE id_prestasi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nama_prestasi, $penyelenggara, $tgl_prestasi, $id_ukm, $nama_ukm, $id_prestasi);
    // Check if a certificate file was uploaded
        if (isset($_FILES['certificateFile']) && $_FILES['certificateFile']['error'] === UPLOAD_ERR_OK) {
            $certificateFileName = $_FILES['certificateFile']['name'];
            $certificateFilePath = "./assets/images/sertifikat/" . $certificateFileName;
            
            // Move the uploaded file to the desired directory
            if (move_uploaded_file($_FILES['certificateFile']['tmp_name'], $certificateFilePath)) {
                // Update the certificate image path in the database
                $sql = "UPDATE tab_prestasi SET certificate_image = ? WHERE id_prestasi = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $certificateFilePath, $id_prestasi);
                $stmt->execute();
            }
        }
    if ($stmt->execute()) {
        // Redirect back to the user list after update
        header("Location: proses_prestasi.php");
        exit();
    } else {
        // If an error occurs during the update
        echo "Error: " . $conn->error;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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


    <div class="sidebar">
    <a href="index.php">
  <img src="./assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Prestasi</i></h2>
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
  <!-- Form Edit Prestasi -->
<div class="content">
    <div class="card">
        <h2>Edit Prestasi</h2>
        <form method="POST" onsubmit="return validateForm();">
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
            <div class="form-group">
                <label for="certificateFile">Upload Sertifikat:</label>
                <input type="file" class="form-control-file" id="certificateFile" name="certificateFile">
            </div>
            <button type="submit" class="btn btn-primary" name="update">Update</button>
        </form>
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