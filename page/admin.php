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
$active_page = 'dashboard';

// Memeriksa apakah query berhasil dieksekusi
// Assuming you have a table named 'tab_user'
if ($conn) {
    // Get the user ID from the session
    $userId = $_SESSION['id_user'];
    
    // Prepare the SQL query
    $sql = "SELECT * FROM tab_user WHERE id_user = '$userId'";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Mengambil data pengguna
        $user = mysqli_fetch_assoc($result);

        // Menyimpan data pengguna ke dalam variabel session
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['no_hp'] = $user['no_hp'];

        // Menyimpan foto profil ke dalam variabel session
        $_SESSION['pasfoto'] = $user['pasfoto'];
    } else {
        // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // If the connection to the database failed, handle the error accordingly
    die("Connection failed: " . mysqli_connect_error());
}
// Create a function to generate the profile picture path based on id_user
function getProfilePicturePath($userId) {
    $extensions = array('jpg', 'jpeg', 'png');
    $baseDir = "../assets/images/pasfoto/";
    
    foreach ($extensions as $extension) {
        $filePath = $baseDir . $userId . "." . $extension;
        if (file_exists($filePath)) {
            return $filePath;
        }
    }
    
    // If no matching file is found, return the default profile picture path
    return "../assets/images/pasfoto/default_profile_picture.png";
}
// Prepare the SQL query to get the number of snapshots for the current user
$sqlSnapshotCount = "SELECT COUNT(*) AS snapshot_count FROM tab_user";

// Execute the query to get the snapshot count
$resultSnapshotCount = mysqli_query($conn, $sqlSnapshotCount);

if ($resultSnapshotCount) {
    // Fetch the snapshot count
    $snapshotCount = mysqli_fetch_assoc($resultSnapshotCount)['snapshot_count'];
} else {
    // If the query fails, handle the error accordingly
    $snapshotCount = 0;
    echo "Error: " . mysqli_error($conn);
}

// Prepare the SQL query to get the number of snapshots for tab_ukm
$sqlUkmSnapshotCount = "SELECT COUNT(*) AS ukm_snapshot_count FROM tab_ukm";

// Execute the query to get the ukm snapshot count
$resultUkmSnapshotCount = mysqli_query($conn, $sqlUkmSnapshotCount);

if ($resultUkmSnapshotCount) {
    // Fetch the ukm snapshot count
    $ukmSnapshotCount = mysqli_fetch_assoc($resultUkmSnapshotCount)['ukm_snapshot_count'];
} else {
    // If the query fails, handle the error accordingly
    $ukmSnapshotCount = 0;
    echo "Error: " . mysqli_error($conn);
}


// Prepare the SQL query to get the number of snapshots for the current user
$pacabSnapshotCount = "SELECT COUNT(*) AS pacab_snapshot_count FROM tab_pacab";

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
// Prepare the SQL query to get the number of snapshots for a specific id_foto
$galeriSnapshotCountQuery = "SELECT COUNT(*) AS galeri_snapshot_count FROM tab_galeri";

// Execute the query to get the snapshot count
$resultGaleriSnapshotCount = mysqli_query($conn, $galeriSnapshotCountQuery);

if ($resultGaleriSnapshotCount) {
    // Fetch the snapshot count for the specified id_foto
    $galeriSnapshotCount = mysqli_fetch_assoc($resultGaleriSnapshotCount)['galeri_snapshot_count'];
} else {
    // If the query fails, handle the error accordingly
    $galeriSnapshotCount = 0;
    echo "Error: " . mysqli_error($conn);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
          .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
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
    .card-wrapper {
    display: flex;
    justify-content: space-between;
    width: 100%;
}


/* CSS untuk garis pembatas vertikal */
.divider-vertical {
    height: 100px; /* Sesuaikan tinggi dengan konten yang ada */
 
}

/* CSS untuk card */
.white-box {
    background-color: #fff; /* Atur warna latar belakang card */
    padding: 15px; /* Sesuaikan padding dengan kebutuhan */
    border-radius: 5px; /* Atur radius border sesuai keinginan */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Atur bayangan card sesuai keinginan */
}

/* CSS untuk gambar */
.col-in img {
    display: nowrap; /* Agar gambar berada di tengah kolom */
    margin: 0 auto; /* Agar gambar berada di tengah vertikal */
    vertical-align: middle;
  }
.col-in p {
    display: inline-block;
    vertical-align: middle;
    font-size: 18px;
  }
  .custom-card {
        height: 200px;
    }
  
    </style>
</head>
<body>
<div class="navbar">
    <div class="navbar-brand">.</div>
    <div class="logout-btn" onclick="logout()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm1.354 4.646a.5.5 0 0 1 .146.354L10.5 8l-1.646 1.646a.5.5 0 0 1-.708-.708L9.793 8.5l-1.647-1.646a.5.5 0 0 1 .708-.708L10.5 7.293l1.646-1.647a.5.5 0 0 1 .354-.147zM8 4.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 1 0v-3a.5.5 0 0 0-.5-.5z"/>
        </svg>
        Logout
    </div>
</div>


    <div class="sidebar">
    <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
    <h2>Dashboard</h2>
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

        
        <div class="content">
            <h1>Dashboard</h1>
            <hr class="divider">
            <div class="wrapper">
            <div class="col-md-12col-sm-12">
            <div class="white-box">
                <div class="row row-in">

                
        <div class="col-md-3 col-sm-6">
            <div class="card custom-card" style="background-color: #91C8E4;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 col-sm-6">
                            <img src="../assets/images/dashboard/user.png" alt="User Snapshot" style="width: 60px; height: 60px;">
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <p style="font-size: 55px;"><?php echo $snapshotCount; ?></p>
                        </div>
                        <div class="col-md-12 col-sm-12">
                        <p style="font-size: 20px; margin-top: 10px; font-weight: bold; font-style: italic;">Total User</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

     
        <div class="col-md-3 col-sm-6">
            <div class="card custom-card" style="background-color: #F6F6C9;">
                <div class="card-body">
                    <div class="row align-items-center"> 
                        <div class="col-md-6 col-sm-6">
                            <img src="../assets/images/dashboard/ukm.png" alt="UKM Snapshot" style="width: 60px; height: 60px;">
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <p style="font-size: 55px;"><?php echo $ukmSnapshotCount; ?></p>
                        </div>
                        <div class="col-md-12 col-sm-12"> 
                        <p style="font-size: 20px; margin-top: 10px; font-weight: bold; font-style: italic;">Total UKM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
        <div class="card custom-card" style="background-color: #E8AA42;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <img src="../assets/images/dashboard/calabar.png" alt="Pacab Snapshot" style="width: 60px; height: 60px;">
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <p style="font-size: 55px;"><?php echo $pacabSnapshotCount; ?></p>
                        </div>
                        <div class="col-md-12">
                        <p style="font-size: 20px; margin-top: 10px; font-weight: bold; font-style: italic;">Pendaftar Baru</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-3 col-sm-6">
            <div class="card custom-card" style="background-color: #5B9A8B;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <img src="../assets/images/dashboard/gallery.png" alt="Gallery Snapshot" style="width: 60px; height: 60px;">
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <p style="font-size: 55px;"><?php echo $galeriSnapshotCount; ?></p>
                        </div>
                        <div class="col-md-12 col-sm-6">                           
                        <p style="font-size: 20px; margin-top: 10px; font-weight: bold; font-style: italic;">Foto Gallery</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
   <script>
    // Fungsi untuk logout
    function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
</script>
</body>

</html>
