<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

error_reporting(0);

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
    // Mengarahkan pengguna ke index.php setelah logout
    header("Location: index.php");
    exit();
}

// Memeriksa apakah tombol logout diklik
if (isset($_GET['logout'])) {
    // Memanggil fungsi logout
    logout();
}

// Menandai halaman yang aktif
$active_page = 'dashboard';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}
// Memeriksa apakah id_user ada pada session dan terdapat dalam tab_dau
$id_user_session = $_SESSION['id_user'];
$queryCheckIdUser = "SELECT COUNT(*) AS user_exists FROM tab_dau WHERE id_user = '$id_user_session'";
$resultCheckIdUser = mysqli_query($conn, $queryCheckIdUser);

if ($resultCheckIdUser) {
    $userExistsData = mysqli_fetch_assoc($resultCheckIdUser);
    $userExists = $userExistsData['user_exists'];

    if ($userExists > 0) {
        // Jika user ditemukan dalam tab_dau, tampilkan tombol-tombol
        $showButtons = true;
    } else {
        // Jika user tidak ditemukan dalam tab_dau, sembunyikan tombol-tombol
        $showButtons = false;
    }
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
}

// Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM tab_user WHERE id_user = '$userId'";

// Mengeksekusi query
$result = mysqli_query($conn, $query);

if ($result) {
    // Mengambil data pengguna
    $user = mysqli_fetch_assoc($result);

    // Menyimpan data pengguna ke dalam variabel
    $nama_lengkap = $user['nama_lengkap'];
    $email = $user['email'];
    $no_hp = $user['no_hp'];
    $prodi = $user['prodi']; // Assuming "prodi" is the field name in the database
    $semester = $user['semester']; // Assuming "semester" is the field name in the database

    // Check if the pasfoto field is not empty
    if (!empty($user['pasfoto'])) {
        // Assuming the "pasfoto" field contains only the filename (e.g., "sanji.jpg")
        $pasfotoFilename = $user['pasfoto'];
        // Assuming the path to the pasfoto directory is "./assets/images/pasfoto/"
        $pasfoto = "./assets/images/pasfoto/" . $pasfotoFilename;
    } else {
        // If pasfoto field is empty or not set, provide a default image path
        $pasfoto = "./assets/images/pasfoto/default_profile_picture.png"; // Change this to your desired default image path
    }
    
    // Check if semester and prodi are empty
    if (empty($semester) || empty($prodi)) {
        $semester = "Lengkapi data dirimu";
        $prodi = "Lengkapi data dirimu";
    }
    
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
}

// Query to count data in tab_pacab based on id_user
$queryPacabCount = "SELECT COUNT(*) AS pacab_count FROM tab_pacab WHERE id_user = '$userId'";
$resultPacabCount = mysqli_query($conn, $queryPacabCount);

$pacabCount = 0; // Default value if the query doesn't yield results
if ($resultPacabCount) {
    $pacabData = mysqli_fetch_assoc($resultPacabCount);
    $pacabCount = $pacabData['pacab_count'];
} else {
    // Handle query error as needed
    echo "Error: " . mysqli_error($conn);
}
// Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM tab_user WHERE id_user = '$userId'";

// Mengeksekusi query
$result = mysqli_query($conn, $query);

// Tambahkan query untuk menghitung jumlah snapshot dari tab_dau berdasarkan id_user
$querySnapshot = "SELECT COUNT(*) AS total_snapshot FROM tab_dau WHERE id_user = '$userId'";
$resultSnapshot = mysqli_query($conn, $querySnapshot);

$totalSnapshot = 0; // Default value jika query tidak menghasilkan hasil
if ($resultSnapshot) {
    $snapshotData = mysqli_fetch_assoc($resultSnapshot);
    $totalSnapshot = $snapshotData['total_snapshot'];
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
}
// Query to fetch data from tab_dau based on id_user
$querySnapshotData = "SELECT nama_ukm, id_anggota, sjk_bergabung FROM tab_dau WHERE id_user = '$userId'";

// Execute the query to fetch snapshot data
$resultSnapshotData = mysqli_query($conn, $querySnapshotData);
// Get the id_user from the session
$id_user = $_SESSION['id_user'];

// Query to fetch id_ukm based on the logged-in user's id_user
$queryIdUkm = "SELECT id_ukm FROM tab_dau WHERE id_user = '$id_user'";
$resultIdUkm = mysqli_query($conn, $queryIdUkm);

if ($resultIdUkm) {
    $rowIdUkm = mysqli_fetch_assoc($resultIdUkm);
    $id_ukm = $rowIdUkm['id_ukm'];

    // Query to fetch schedule of events based on id_ukm
    $querySchedule = "SELECT nama_kegiatan, tgl, deskripsi FROM tab_kegiatan WHERE id_ukm = '$id_ukm'";
    $resultSchedule = mysqli_query($conn, $querySchedule);

    if ($resultSchedule && mysqli_num_rows($resultSchedule) > 0) {
        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function() {';
        echo '    Swal.fire({';
        echo '        icon: "info",';
        echo '        title: "Jadwal Kegiatan",';
        echo '        html: `';

        while ($rowSchedule = mysqli_fetch_assoc($resultSchedule)) {
            echo '<p><span class="label">Nama Kegiatan:</span> ' . $rowSchedule['nama_kegiatan'] . '</p>';
            echo '<p><span class="label">Tanggal:</span> ' . date('d M Y', strtotime($rowSchedule['tgl'])) . '</p>';
            echo '<p><span class="label">Deskripsi:</span> ' . $rowSchedule['deskripsi'] . '</p>';
            echo '<hr class="divider">';
        }

        echo '        `,';
        echo '        confirmButtonText: "Tutup",';
        echo '    });';
        echo '});';
        echo '</script>';
    }
} else {
    echo "Error fetching id_ukm: " . mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Pengguna</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
  .navbar .logout-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            color: #fff;
        }

        .navbar .logout-btn:hover {
            text-decoration: underline;
        }

 .card {
            background-color: #007bff;
            color: #fff;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            background-color: #4213;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

                .profil-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: 4px solid #fff; /* Tambahkan border putih di sekitar foto */
        }

        .profile-details {
            flex: 1; /* Allow the details section to take up remaining space */
           
        }

                .label {
            font-weight: bold;
            color: #333;
        }

        .value {
            color: #555;
        }

        .divider {
            border: none;
            border-top: 2px solid #ccc;
            margin-bottom: 20px;
        }
        .btn {
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 10px 20px;
    cursor: pointer;
}

.btn:hover {
    background-color: #218838;
}
.wrapper {
            flex: 1 0 auto; /* Buat wrapper "sticky" dengan flex-grow: 1 dan flex-shrink: 0 */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-bottom: 100px; /* Atur padding-bottom agar ada ruang di antara konten dan footer */
        }
        .white-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    } .sidebar img {
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
    p {
        color: black;
    }
    .scrollable-content {
        height: 300px; /* Set the desired fixed height for the scrollable area */
        overflow-y: auto; /* Enable vertical scrolling when content overflows */
        /* Optional: Add padding to the scrollable area */
    }
    </style>
</head>


<div class="sidebar">
    <a href="index.php">
  <img src="./assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Dashboard</i></h2>
<a href="dashboard.php" class="btn btn-primary <?php if ($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
<?php if ($showButtons): ?>
    <p style="text-align: center;">--Informasi--</p>
<?php endif; ?>
            <a href="view_struktur.php" class="btn btn-primary btn-manajemen <?php if ($active_page == 'view_struktur') echo 'active'; ?>" <?php if (!$showButtons) echo 'style="display: none;"'; ?>>Pengurus</a>
    <a href="view_dau.php" class="btn btn-primary btn-manajemen <?php if ($active_page == 'view_dau') echo 'active'; ?>" <?php if (!$showButtons) echo 'style="display: none;"'; ?>>Data Anggota</a>
    <a href="view_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_kegiatan') echo 'active'; ?>" <?php if (!$showButtons) echo 'style="display: none;"'; ?>>Kegiatan</a>
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
            <h1>Informasi Pengguna</h1>
            <hr class="divider">
            <div class="wrapper">
    <div class="col-md-12 col-sm-12">
        <div class="white-box">
            <div class="row row-in">
                <div class="col-md-8">
                    <div class="card shadow user-info">
                        <div class="row">
                            <!-- Left column for profile picture -->
                            <div class="col-md-4">
                                <div class="profile-container">
                                    <img src="<?php echo $pasfoto; ?>" alt="Foto Profil" class="profil-picture">
                                </div>
                            </div>
                            <!-- Right column for user information -->
                            <div class="col-md-8">
                                <div class="profile-details">
                                    <p><span class="label">Nama Lengkap:</span> <span class="value"><?php echo $nama_lengkap; ?></span></p>
                                    <p><span class="label">Email:</span> <span class="value"><?php echo $email; ?></span></p>
                                    <p><span class="label">Nomor Telepon:</span> <span class="value"><?php echo $no_hp; ?></span></p>
                                    <p><span class="label">Prodi:</span> <span class="value"><?php echo $prodi; ?></span></p>
                                    <p><span class="label">Semester:</span> <span class="value"><?php echo $semester; ?></span></p>
                                </div>
                                <!-- Tombol Ganti Password -->
                                <a href="ganti_password_pengguna.php" class="btn btn-primary mt-2"><i class="fas fa-key"></i> Ganti Password</a>
                                <!-- Tombol Update Data Diri -->
                                <a href="update_pengguna.php" class="btn btn-primary mt-2"><i class="fas fa-user-edit"></i> Update Data Diri</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"> <!-- Adjust the column size according to your needs -->
                    <!-- Content for jumlah UKM yang diikuti -->
                    <div class="card shadow">
                        <h4 class="mb-3">Jumlah UKM yang diikuti</h4>
                        <p class="font-weight-bold" style="font-size: 50px;"><?php echo $totalSnapshot; ?></p>
                        <?php
                        if ($pacabCount > 0) {
                            echo '<p>Menunggu diproses oleh pengurus: ' . $pacabCount . '</p>';
                        } else {
                            echo '<p>Tidak ada data yang perlu diproses saat ini.</p>';
                        }
                        ?>
                    </div>          
                    <!-- Content for UKM yang diikuti -->
                    <div class="card shadow mt-4 scrollable-content">
                        <h4 class="mb-3">Daftar UKM yang diikuti</h4>
                        <?php
                    // Array of Indonesian month names
                    $indonesianMonths = array(
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    );

                    // Check if there are any snapshots for the user
                    if (mysqli_num_rows($resultSnapshotData) > 0) {
                        while ($snapshot = mysqli_fetch_assoc($resultSnapshotData)) {
                            echo '<p><span class="label">UKM:</span> ' . $snapshot['nama_ukm'] . '</p>';
                            echo '<p><span class="label">ID Anggota:</span> ' . $snapshot['id_anggota'] . '</p>';
                    
                            // Convert sjk_bergabung to the desired date format
                            $dateComponents = explode('-', $snapshot['sjk_bergabung']);
                            $day = intval($dateComponents[2]);
                            $monthIndex = intval($dateComponents[1]) - 1; // Since the array is 0-based index
                            $year = intval($dateComponents[0]);
                    
                            $sjk_bergabung_formatted = $day . ' ' . $indonesianMonths[$monthIndex] . ' ' . $year;
                            echo '<p><span class="label">Bergabung:</span> ' . $sjk_bergabung_formatted . '</p>';
                    
                            echo '<hr class="divider">';
                        }
                    } else {
                        echo '<p>Kamu belum mengikuti UKM manapun, ekspresikan dirimu dengan bergabung UKM.</p>';
                    }                    
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="script.js"></script>

    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
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