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
        // Assuming the path to the pasfoto directory is "../assets/images/pasfoto/"
        $pasfoto = "../assets/images/pasfoto/" . $pasfotoFilename;
    } else {
        // If pasfoto field is empty or not set, provide a default image path
        $pasfoto = "../assets/images/default_profile_picture.png"; // Change this to your desired default image path
    }
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

   /* Style for the user card container */
.card.user-card {
  background-color: #f9f9f9;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
}

/* Style for the profile picture */
.profil-picture {
  width: 100%;
  max-width: 150px;
  height: auto;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
}

/* Style for the user information details */
.profile-details {
  padding-left: 15px;
}

/* Style for the label and value pairs */
.profile-details p {
  margin: 5px 0;
  font-size: 16px;
  line-height: 1.5;
}

/* Style for the label */
.profile-details .label {
  font-weight: bold;
}

/* Style for the value */
.profile-details .value {
  font-weight: normal;
  color: #777;
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
    </style>
</head>
<body>

    <div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Kemahasiswaan</i></h2>
            <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
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

        
        <div class="content">
            <h1>Kemahasiswaan</h1>
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
    </div>
</div>
<br>
            <div class="col-md-6">
                    <div class="card user-card">
                        <div class="row">
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
                                </div>
                                <!-- Tombol Ganti Password -->
                                <a href="ganti_password_admin.php" class="btn btn-primary mt-2"><i class="fas fa-key"></i> Ganti Password</a>
                                <!-- Tombol Update Data Diri -->
                                <a href="update_admin.php" class="btn btn-primary mt-2"><i class="fas fa-user-edit"></i> Update Data Diri</a>
                            </div>
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
