<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

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
// Menandai halaman yang aktif
$active_page = 'pengurus';
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

$userId = $_SESSION['id_admin'];
$id_ukm = $_SESSION['id_ukm'];
$query = "SELECT * FROM tab_admin WHERE id_admin = '$userId'";

// Mengeksekusi query
$result = mysqli_query($conn, $query);

if ($result) {
    // Mengambil data pengguna
    $user = mysqli_fetch_assoc($result);

    // Menyimpan data pengguna ke dalam variabel
    $nama_lengkap = $user['nama_lengkap'];
    $email = $user['email'];
    $id_ukm = $user['id_ukm'];

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
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
}


// Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
$userId = $_SESSION['id_admin'];
$query = "SELECT * FROM tab_user WHERE id_admin = '$userId'";

// Mengeksekusi query
$result = mysqli_query($conn, $query);
// Prepare the SQL query to get the number of snapshots for tab_ukm
$sqlMemberSnapshotCount = "SELECT COUNT(*) AS member_snapshot_count FROM tab_dau WHERE id_ukm = '$id_ukm'";

// Execute the query to get the ukm snapshot count
$resultMemberSnapshotCount = mysqli_query($conn, $sqlMemberSnapshotCount);

if ($resultMemberSnapshotCount) {
    // Fetch the ukm snapshot count
    $memberSnapshotCount = mysqli_fetch_assoc($resultMemberSnapshotCount)['member_snapshot_count'];
} else {
    // If the query fails, handle the error accordingly
    $memberSnapshotCount = 0;
    echo "Error: " . mysqli_error($conn);
}


// Prepare the SQL query to get the number of snapshots for the current user
$pacabSnapshotCount = "SELECT COUNT(*) AS pacab_snapshot_count FROM tab_pacab WHERE id_ukm = '$id_ukm'";

// Execute the query to get the snapshot count
$resultPacabSnapshotCount = mysqli_query($conn, $pacabSnapshotCount);

if ($resultPacabSnapshotCount) {
    // Fetch the snapshot count for the user's id_pacab
    $pacabSnapshotCount = mysqli_fetch_assoc($resultPacabSnapshotCount)['pacab_snapshot_count'];
} else {
    // If the query fails, handle the error accordingly
    $pacabSnapshotCount = 0;
    echo "Error: " . mysqli_error($conn);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin UKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .user-info {
             /* Add spacing between sidebar and user-info */
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
<a href="pengurus.php" class="btn btn-primary <?php if($active_page == 'pengurus') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
    <a href="proses_dau_pengurus.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_dau') echo 'active'; ?>">Data Anggota</a>
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
        <div class="content">
            <h1>Admin UKM</h1>
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
                                </div>
                                <!-- Tombol Ganti Password -->
                                <a href="ganti_password_pengurus.php" class="btn btn-primary mt-2"><i class="fas fa-key"></i> Ganti Password</a>
                                <!-- Tombol Update Data Diri -->
                                <a href="update_pengurus.php" class="btn btn-primary mt-2"><i class="fas fa-user-edit"></i> Update Data</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4"> 
                <div class="row row-in">
                    <div class="card shadow">
                        <h4 class="mb-4">Jumlah Pendaftar Baru</h4>
                        <p>Menunggu diterima oleh pengurus</p>
                        <p class="font-weight-bold" style="font-size: 50px; text-align: center;"><?php echo $pacabSnapshotCount; ?></p>
                    </div>
                    </div>
                    <br>
                <div class="col-md-12">
                <div class="card shadow">
                    <h4 class="mb-4">Total Anggota UKM</h4>
                    <p class="font-weight-bold" style="font-size: 50px; text-align: center;"><?php echo $memberSnapshotCount; ?></p>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
