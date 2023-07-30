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

// Menandai halaman yang aktif
$active_page = 'dashboard';

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

// Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM tab_user WHERE id_user = '$userId'";

// Mengeksekusi query
$result = mysqli_query($conn, $query);

// Memeriksa apakah query berhasil dieksekusi
if ($result) {
    // Mengambil data pengguna
    $user = mysqli_fetch_assoc($result);

    // Menyimpan data pengguna ke dalam variabel session
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['no_hp'] = $user['no_hp'];
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
}

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $oldPassword = $_POST['old_password'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_POST['email'];
    $namaLengkap = $_POST['nama_lengkap'];
    $noHp = $_POST['no_hp'];

    // Memeriksa apakah password baru dan konfirmasi password cocok
    if ($password !== $confirmPassword) {
        // Password baru dan konfirmasi password tidak cocok
        $error = "Error: Password baru dan konfirmasi password tidak cocok.";
    } else {
        // Menghindari SQL injection
        $oldPassword = mysqli_real_escape_string($conn, $oldPassword);
        $password = mysqli_real_escape_string($conn, $password);
        $email = mysqli_real_escape_string($conn, $email);
        $namaLengkap = mysqli_real_escape_string($conn, $namaLengkap);
        $noHp = mysqli_real_escape_string($conn, $noHp);

        // Mengecek kebenaran password lama
        $userId = $_SESSION['id_user'];
        $query = "SELECT * FROM tab_user WHERE id_user = '$userId' AND password = '$oldPassword'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 0) {
            // Password lama tidak cocok
            $error = "Error: Password lama tidak cocok.";
        } else {
            // Membuat query update
            $query = "UPDATE tab_user SET password = '$password', email = '$email', nama_lengkap = '$namaLengkap', no_hp = '$noHp' WHERE id_user = '$userId'";

            // Mengeksekusi query update
            $updateResult = mysqli_query($conn, $query);

            // Tampilkan snackbar jika data berhasil diubah
            if ($updateResult) {
                // Mengupdate data di dalam session
                $_SESSION['password'] = $password;
                $_SESSION['email'] = $email;
                $_SESSION['nama_lengkap'] = $namaLengkap;
                $_SESSION['no_hp'] = $noHp;

                // Tampilkan snackbar jika data berhasil diubah
                echo "<script>showSnackbar('Data berhasil diubah.');</script>";
            } else {
                // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #007bff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            color: #fff;
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

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .sidebar a {
            display: block;
            margin-bottom: 10px;
            color: #000;
            text-decoration: none;
        }

        .sidebar a.active {
            font-weight: bold;
        }

        .toggle-btn {
            display: none;
        }

        @media (max-width: 768px) {
            .toggle-btn {
                display: block;
                font-size: 20px;
                cursor: pointer;
            }
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-container {
            margin-right: 20px;
        }

        .profil-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-details {
            display: flex;
            flex-direction: column;
        }
        
        .profile-details p {
            margin-bottom: 5px;
        }

        .profile-details .label {
            font-weight: bold;
        }
    </style>
</head>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
       
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-brand">Dashboard</div>
        <div class="logout-btn" onclick="logout()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm1.354 4.646a.5.5 0 0 1 .146.354L10.5 8l-1.646 1.646a.5.5 0 0 1-.708-.708L9.793 8.5l-1.647-1.646a.5.5 0 0 1 .708-.708L10.5 7.293l1.646-1.647a.5.5 0 0 1 .354-.147zM8 4.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 1 0v-3a.5.5 0 0 0-.5-.5z"/>
            </svg>
            Logout
        </div>
    </div>
    <div class="container">
        <div class="sidebar">
        <h2>Dashboard Admin</h2>
            <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
            <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
            <a href="proses_struktur.php" class="btn btn-primary <?php if($active_page == 'struktur') echo 'active'; ?>">Kepengurusan</a>
            <a href="proses_dau.php" class="btn btn-primary <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
            <a href="proses_prestasi.php" class="btn btn-primary <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
            <a href="proses_user.php" class="btn btn-primary <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
            <a href="proses_visimisi.php" class="btn btn-primary <?php if($active_page == 'visi_misi') echo 'active'; ?>">Data UKM</a>
            <a href="galeri.php" class="btn btn-primary <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
            <a href="kegiatan.php" class="btn btn-primary <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
            <a href="calon_anggota.php" class="btn btn-primary <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
        </div>
        
        <div class="content">
            <h1>Dashboard Admin</h1>
            <hr class="divider">
            <div class="user-info">
    <div class="profile-container">
        <img src="../assets/images/sanji.jpg" alt="Foto Profil" class="profil-picture">
    </div>
    <div class="profile-details">
        <p><span class="label">Nama:</span> <span class="value"><?php echo $_SESSION['nama_lengkap']; ?></span></p>
        <p><span class="label">Email:</span> <span class="value"><?php echo $_SESSION['email']; ?></span></p>
        <p><span class="label">Nomor Telepon:</span> <span class="value"><?php echo $_SESSION['no_hp']; ?></span></p>
        <!-- Add the "Update Data" button here -->
        <a href="update_data.php" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                <path d="M6.146 13.354a.5.5 0 0 0 .708 0l8-8a.5.5 0 0 0 0-.708l-1.5-1.5a.5.5 0 0 0-.708 0L6 10.293 2.854 7.146a.5.5 0 1 0-.708.708l3 3zM2 14a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V4.414l-2 2V13a.5.5 0 0 1-1 0V6.414l-8-8L3.414 1H2v12z"/>
            </svg>
            Update Data
        </a>
        <!-- End of "Update Data" button -->
    </div>
</div>
    </div>
    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Ambil elemen toggle button dan sidebar
        const toggleBtn = document.querySelector('.toggle-btn');
        const sidebar = document.querySelector('.sidebar');

        // Tambahkan event listener untuk toggle button
        toggleBtn.addEventListener('click', () => {
            // Toggle class 'collapsed' pada sidebar
            sidebar.classList.toggle('collapsed');
        });

        // Fungsi untuk logout
        function logout() {
            // Redirect ke halaman logout
            window.location.href = "?logout=true";
        }
    </script>
</body>

</html>
