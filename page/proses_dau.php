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


// Menandai halaman yang aktif
$active_page = 'data_anggota_ukm';

// Construct the initial query to get data from the database
$query = "SELECT id_anggota, id_user, nama_lengkap, no_hp, email, prodi, semester, pasfoto, foto_ktm, id_ukm, nama_ukm, sjk_bergabung FROM tab_dau";

// Check if a search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
    $query .= " WHERE id_anggota LIKE '%$searchTerm%' OR id_user LIKE '%$searchTerm%' OR nama_lengkap LIKE '%$searchTerm%'";
}

// Execute the query
$result = mysqli_query($conn, $query);



// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai-nilai dari form
    $id_anggota = $_POST["id_anggota"];
    $id_user = $_POST["id_user"];
    $nama_lengkap = $_POST["nama_lengkap"];
    $no_hp = $_POST["no_hp"];
    $email = $_POST["email"];
    $prodi = $_POST["prodi"];
    $semester = $_POST["semester"];
    $id_ukm = $_POST["id_ukm"];
    $nama_ukm = $_POST["nama_ukm"];
    $sjk_bergabung = $_POST["sjk_bergabung"];
    $tahun_bergabung = substr($_POST["sjk_bergabung"], 2, 2);

    $pasfoto = $_POST["pasfoto"];
    $fotoKtm = $_POST["foto_ktm"];

    // Generate the ID Anggota based on the rules
    $randomDigits = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT); // 4-digit random number
    $tahun_bergabung = substr($sjk_bergabung, 2, 2); // Extract the last 2 digits of the year

    // Determine the abbreviation for the program based on the prodi value
    if ($prodi == "Teknik Informatika") {
        $programAbbreviation = "01";
    } elseif ($prodi == "Sistem Informasi") {
        $programAbbreviation = "02";
    } else {
        $programAbbreviation = "00"; // Default if not Teknik Informatika or Sistem Informasi
    }

    // Get the current semester
    $currentSemester = intval($semester);

    // Get the current month in 2-digit format
    $currentMonth = date("m");

    // Combine the parts to create the ID Anggota
    $id_anggota = substr($id_user, -2) . $programAbbreviation . $currentSemester . $currentMonth . substr($tahun_bergabung, -2) . $randomDigits;


    // Simpan data ke database
    $sql = "INSERT INTO tab_dau (id_anggota, id_user, nama_lengkap, no_hp, email, prodi, semester, pasfoto, foto_ktm, id_ukm, nama_ukm, sjk_bergabung) 
            VALUES ('$id_anggota', '$id_user', '$nama_lengkap', '$no_hp', '$email', '$prodi', '$semester', '$pasfoto', '$fotoKtm', '$id_ukm', '$nama_ukm', '$sjk_bergabung')";

    if (mysqli_query($conn, $sql)) {
        echo "Berhasil menambahkan anggota";
        // Redirect ke halaman data anggota setelah penyimpanan berhasil
        header("Location: proses_dau.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menyimpan data
        echo "Error: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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


<div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Anggota UKM</i></h2>
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
            <h2>Data Anggota</h2>
            <button type="button" class="btn btn-primary btn-sm btn-medium" data-toggle="modal" data-target="#tambahAnggotaModal">
                <i class="fas fa-plus"></i> Tambah Anggota
            </button>
            </div>
            <form class="form-inline mt-2 mt-md-0 float-right" method="get">
    <input class="form-control mr-sm-2" type="text" placeholder="Cari id anggota/id user/nama" name="search" aria-label="Search">
    <button type="submit" class="btn btn-outline-primary">Search</button>
    <a href="proses_dau.php" class="btn btn-outline-secondary ml-2">
        <i class="fas fa-sync-alt"></i>
    </a>
    </div>
</form>

<div class="content">
<table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Anggota</th>
                <th>ID User</th>
                <th>Nama Lengkap</th>
                <th>No. HP</th>
                <th>Email</th>
                <th>Program Studi</th>
                <th>Semester</th>
                <th>Pasfoto</th>
                <th>Foto Identitas</th>
                <th>Nama UKM</th>
                <th>Bergabung</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
    <?php
      $counter = 1;
    // Loop melalui hasil query untuk menampilkan data anggota UKM
    while ($row = mysqli_fetch_assoc($result)) {
    
        echo "<tr>";
        echo "<td>" . $counter . "</td>"; // Display the counter value
        echo "<td>" . $row['id_anggota'] . "</td>";
        echo "<td>" . $row['id_user'] . "</td>";
        echo "<td>" . $row['nama_lengkap'] . "</td>";
        echo "<td>" . $row['no_hp'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['prodi'] . "</td>";
        echo "<td>" . $row['semester'] . "</td>";
        // Display the "Pasfoto" image
        echo "<td><img src='../assets/images/pasfoto/" . $row['pasfoto'] . "' alt='Pasfoto' class='img-thumbnail' style='max-height: 100px;'></td>";
        // Display the "Foto_KTM" image
        echo "<td><img src='../assets/images/ktm/" . $row['foto_ktm'] . "' alt='Foto KTM' class='img-thumbnail' style='max-height: 100px;'></td>";
        echo "<td>" . $row['nama_ukm'] . "</td>";
        echo "<td>" . date('d-m-Y', strtotime($row['sjk_bergabung'])) . "</td>";
        echo "<td><a href='delete_anggota.php?id_anggota=" . $row['id_anggota'] . "' class='btn btn-danger btn-sm delete-button' onclick='return confirmDelete()'>Hapus</a></td>";
        echo "</tr>";
        $counter++;
    }
    ?>
</tbody>
    </table>
        </div>
    
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        function confirmDelete() {
                return confirm("Apakah yakin ingin menghapus data anggota ini?");
            }
</script>
<!-- Script for handling file uploads -->
<script>
    // Function to handle pasfoto file upload
    function openPasfotoUploader() {
        document.getElementById("pasfoto").click();
    }

    document.getElementById("pasfoto").addEventListener("change", function() {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
            document.getElementsByName("hidden_pasfoto")[0].value = reader.result;
        };
        if (file) {
            reader.readAsDataURL(file);
        }
    });

    // Function to handle foto_ktm file upload
    function openFotoKTMUploader() {
        document.getElementById("foto_ktm").click();
    }

    document.getElementById("foto_ktm").addEventListener("change", function() {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
            document.getElementsByName("hidden_foto_ktm")[0].value = reader.result;
        };
        if (file) {
            reader.readAsDataURL(file);
        }
    });
</script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
$(document).ready(function() {
    $("#id_ukm_dropdown").change(function() {
        // Ambil nilai ID UKM yang dipilih oleh pengguna
        var id_ukm = $(this).val();

        // Kirim permintaan AJAX ke server untuk mendapatkan nama UKM berdasarkan ID UKM
        $.ajax({
            url: "get_nama_ukm.php", // Ganti dengan alamat file PHP yang akan memproses permintaan ini
            method: "POST",
            data: { id_ukm: id_ukm },
            success: function(response) {
                // Isi nilai nama UKM ke dalam input text dengan id "nama_ukm"
                $("#nama_ukm").val(response);
            },
            error: function(xhr, status, error) {
                // Tangani error jika ada
                console.error(error);
            }
        });
    });
});
// Fungsi untuk logout
function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
</script>
<!-- Modal for adding a new anggota -->
<div class="modal fade" id="tambahAnggotaModal" tabindex="-1" role="dialog" aria-labelledby="tambahAnggotaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <h2 style="text-align: center;">Tambah Data Anggota UKM</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-row">
                    <div class="col-md-6">
                    <label for="id_user">*ID User:</label>
                    <select class="form-control" name="id_user" id="id_user_dropdown" required>
                    <option value="">------------Pilih ID User------------</option>
                    <?php
                    // Fetch data from the tab_user table and populate the dropdown options
                    $userQuery = "SELECT id_user FROM tab_user"; // Select only the id_user column
                    $userResult = mysqli_query($conn, $userQuery);

                    while ($userRow = mysqli_fetch_assoc($userResult)) {
                        // Use a regular expression to check if the id_user contains only digits (numbers)
                        if (preg_match('/^\d+$/', $userRow['id_user'])) {
                            echo '<option value="' . $userRow['id_user'] . '">' . $userRow['id_user'] . '</option>';
                        }
                    }
                    ?>
                </select>

        <script>
            // Event listener for the dropdown (id_user)
            document.getElementById("id_user_dropdown").addEventListener("change", function () {
                var selectedUserId = this.value;
                var namaLengkapField = document.getElementsByName("nama_lengkap")[0];
                var prodiField = document.getElementsByName("prodi")[0];
                var semesterField = document.getElementsByName("semester")[0];
                var pasfotoField = document.getElementsByName("pasfoto")[0];
                var fotoKtmField = document.getElementsByName("foto_ktm")[0];
                var noHpField = document.getElementsByName("no_hp")[0];
                var emailField = document.getElementsByName("email")[0];

                if (selectedUserId === "") {
                    // Reset the text fields
                    namaLengkapField.value = "";
                    prodiField.disabled = "";
                    semesterField.disabled = "";
                    noHpField.value = "";
                    emailField.value = "";

                    // Disable the text fields
                    namaLengkapField.disabled = true;
                    prodiField.disabled = true;
                    semesterField.disabled = true;
                    noHpField.disabled = true;
                    emailField.disabled = true;
                } else {
                }
            });
            </script>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
    // Function to fetch user data based on the selected id_user
    function fetchUserData(userId) {
        $.ajax({
            type: "POST",
            url: "get_user_data.php", // Create a separate PHP file to handle AJAX request and database query
            data: { id_user: userId },
            dataType: "json",
            success: function(data) {
                // Update the text fields with the fetched data
                $("input[name='nama_lengkap']").val(data.nama_lengkap);
                $("input[name='prodi']").val(data.prodi);
                $("input[name='semester']").val(data.semester);
                $("input[name='pasfoto']").val(data.pasfoto);
                $("input[name='foto_ktm']").val(data.foto_ktm);
                $("input[name='no_hp']").val(data.no_hp);
                $("input[name='email']").val(data.email);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }

    // Event listener for the dropdown (id_user)
    $("select[name='id_user']").on("change", function() {
        var selectedUserId = $(this).val();
        fetchUserData(selectedUserId);
    });
</script>

         <div class="col-md-6">
            <label for="nama_lengkap">*Nama Lengkap:</label>
            <input type="text" class="form-control" name="nama_lengkap" required readonly>
        </div>
        </div>
        <div class="form-row">
        <div class="col-md-6">
                <label for="no_hp">*No. HP:</label>
                <input type="text" class="form-control" name="no_hp" required readonly>
            </div>
            <div class="col-md-6">
                    <label for="email">*Email:</label>
                    <input type="email" class="form-control" name="email" required readonly>
                </div>
        </div>


        <div class="form-row">
                <div class="col-md-6">
                    <label for="prodi">Prodi:</label>
                    <input type="prodi" class="form-control" name="prodi" required readonly>
                </div>
                <div class="col-md-6">
                <label for="semester">Semester:</label>
                <input type="semester" class="form-control" name="semester" required readonly>
            </div>
            </div>

                <div class="form-row">
            <div class="col-md-6">
                <label for="sjk_bergabung">*Bergabung:</label>
                <input type="date" class="form-control" id="sjk_bergabung"  name="sjk_bergabung" required>
            </div>
            <div class="col-md-6">
    <label for="id_ukm">*Nama UKM:</label>
    <select class="form-control" name="id_ukm" id="id_ukm_dropdown" required>
        <option value="">Pilih Nama UKM</option>
        <?php
        // Fetch data from the tab_ukm table and populate the dropdown options
        $ukmQuery = "SELECT id_ukm, nama_ukm FROM tab_ukm"; // Add 'nama_ukm' to the SELECT query
        $ukmResult = mysqli_query($conn, $ukmQuery);

        while ($ukmRow = mysqli_fetch_assoc($ukmResult)) {
            echo '<option value="' . $ukmRow['id_ukm'] . '">' . $ukmRow['nama_ukm'] . '</option>';
        }
        ?>
    </select>
</div>
            </div>
        <div class="form-row">
            <div class="col-md-6">
                <input type="text" class="form-control" name="nama_ukm" id="nama_ukm" style="display: none;" required readonly>
            </div>
            <input type="text" class="form-control" name="pasfoto" id="pasfoto" style="display: none;">
            <input type="text" name="foto_ktm" id="foto_ktm" style="display: none;">
<!-- Move the "Tambah Anggota" button to the right side -->
<div class="col-md-6 d-flex align-items-end justify-content-end">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Anggota
    </button>
</div>
    <input type="text" class="form-control" placeholder="Akan terisi secara otomatis" name="id_anggota" readonly style="display: none;">
        </form>
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
