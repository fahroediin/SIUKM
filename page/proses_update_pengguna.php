<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menandai halaman yang aktif
$active_page = 'proses_update_pengguna';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
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
    $_SESSION['id_user'] = $user['id_user']; // Perubahan: Menyimpan ID User
    $_SESSION['nama_depan'] = $user['nama_depan'];
    $_SESSION['nama_belakang'] = $user['nama_belakang'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['no_hp'] = $user['no_hp'];
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
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

// Memeriksa apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $id_user = $_POST['id_user'];
    $password = $_POST['password'];
    $nama_depan = $_POST['nama_depan'];
    $nama_belakang = $_POST['nama_belakang'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];

    // Update data pengguna di tabel tab_user
    $query = "UPDATE tab_user SET password='$password', nama_depan='$nama_depan', nama_belakang='$nama_belakang', email='$email', no_hp='$no_hp' WHERE id_user='$id_user'";
    $updateResult = mysqli_query($conn, $query);

    // Memeriksa apakah query update berhasil dieksekusi
    if ($updateResult) {
        // Mengupdate data pengguna di session
        $_SESSION['nama_depan'] = $nama_depan;
        $_SESSION['nama_belakang'] = $nama_belakang;
        $_SESSION['email'] = $email;
        $_SESSION['no_hp'] = $no_hp;

        // Redirect ke halaman dashboard.php
        header("Location: dashboard.php");
        exit();
    } else {
        // Jika query update gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
        echo "Error: " . mysqli_error($conn);
    }
}
// Memeriksa apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $id_user = $_POST['id_user'];
    $password = $_POST['password'];
    $nama_depan = $_POST['nama_depan'];
    $nama_belakang = $_POST['nama_belakang'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];

    // Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
    $userId = $_SESSION['id_user'];
    $query = "SELECT * FROM tab_user WHERE id_user = '$userId'";

    // Mengeksekusi query
    $result = mysqli_query($conn, $query);

    // Memeriksa apakah query berhasil dieksekusi
    if ($result) {
        // Mengambil data pengguna
        $user = mysqli_fetch_assoc($result);

        // Memeriksa apakah password yang dimasukkan cocok dengan password di database
        if (password_verify($password, $user['password'])) {
            // Update data pengguna di tabel tab_user
            $query = "UPDATE tab_user SET nama_depan='$nama_depan', nama_belakang='$nama_belakang', email='$email', no_hp='$no_hp' WHERE id_user='$id_user'";
            $updateResult = mysqli_query($conn, $query);

            // Memeriksa apakah query update berhasil dieksekusi
            if ($updateResult) {
                // Mengupdate data pengguna di session
                $_SESSION['nama_depan'] = $nama_depan;
                $_SESSION['nama_belakang'] = $nama_belakang;
                $_SESSION['email'] = $email;
                $_SESSION['no_hp'] = $no_hp;

                // Redirect ke halaman dashboard.php
                header("Location: dashboard.php");
                exit();
            } else {
                // Jika query update gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            // Password tidak cocok, tampilkan snackbar "password salah!"
            echo '<script>$(document).ready(function() { $("#snackbar").html("Password salah!").addClass("show"); setTimeout(function(){ $("#snackbar").removeClass("show"); }, 3000); });</script>';
        }
    } else {
        // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pengguna</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
        /* Style untuk snackbar */
#snackbar {
  visibility: hidden; /* Menyembunyikan snackbar secara default */
  min-width: 250px; /* Lebar minimum snackbar */
  margin-left: -125px; /* Mengatur posisi snackbar di tengah */
  background-color: #333; /* Warna latar belakang snackbar */
  color: #fff; /* Warna teks snackbar */
  text-align: center; /* Posisi teks di tengah snackbar */
  border-radius: 2px; /* Mengatur sudut bulat snackbar */
  padding: 16px; /* Ruang dalam snackbar */
  position: fixed; /* Menempatkan snackbar di posisi tetap di atas elemen lain */
  z-index: 1; /* Mengatur tingkat tumpukan snackbar */
  left: 50%; /* Mengatur posisi horizontal snackbar di tengah */
  bottom: 30px; /* Mengatur posisi vertikal snackbar 30px dari bawah */
  font-size: 14px; /* Ukuran font teks snackbar */
}

/* Tampilkan snackbar */
#snackbar.show {
  visibility: visible;
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s; /* Animasi muncul dan menghilang */
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}

/* Animasi muncul */
@-webkit-keyframes fadein {
  from {bottom: 0; opacity: 0;} 
  to {bottom: 30px; opacity: 1;}
}

@keyframes fadein {
  from {bottom: 0; opacity: 0;}
  to {bottom: 30px; opacity: 1;}
}

/* Animasi menghilang */
@-webkit-keyframes fadeout {
  from {bottom: 30px; opacity: 1;} 
  to {bottom: 0; opacity: 0;}
}

@keyframes fadeout {
  from {bottom: 30px; opacity: 1;}
  to {bottom: 0; opacity: 0;}
}
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard</h2>
        <a href="dashboard.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
        <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
        <a href="proses_update_pengguna.php" class="btn btn-primary <?php if($active_page == 'proses_update_pengguna') echo 'active'; ?>">Update</a>
        <a href="?logout=true" class="btn btn-primary <?php if($active_page == 'logout') echo 'active'; ?>">Logout</a>
    </div>
    <div class="content">
        <h2>Update Pengguna</h2>
        <form action="proses_update_pengguna.php" method="POST">
            <div class="form-group">
                <label for="id_user">ID User:</label>
                <input type="text" class="form-control" id="id_user" name="id_user" value="<?php echo $_SESSION['id_user']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="nama_depan">Nama Depan:</label>
                <input type="text" class="form-control" id="nama_depan" name="nama_depan" value="<?php echo $_SESSION['nama_depan']; ?>" required>
            </div>
            <div class="form-group">
                <label for="nama_belakang">Nama Belakang:</label>
                <input type="text" class="form-control" id="nama_belakang" name="nama_belakang" value="<?php echo $_SESSION['nama_belakang']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $_SESSION['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="no_hp">No. HP:</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo $_SESSION['no_hp']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Mendapatkan elemen snackbar
        var snackbar = document.getElementById("snackbar");

        // Menambahkan class "show" pada snackbar untuk menampilkannya
        snackbar.className = "show";

        // Mengatur waktu 3 detik.
        setTimeout(function(){
            // Menghapus class "show" dari snackbar untuk menyembunyikannya
            snackbar.className = snackbar.className.replace("show", "");
        }, 3000);
    });
</script>
</body>
<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49c-.173.256-.33.514-.468.932A6.698 6.698 0 0 1 12.18 11zM1.674 8.5h2.99c.036.403.109.791.216 1.162A6.701 6.701 0 0 1 2.255 12H1.674A6.956 6.956 0 0 0 1 8.5h.674zM2.255 4a6.698 6.698 0 0 1 .597.933c.107.371.18.759.216 1.162H1.674A6.955 6.955 0 0 0 1 4h1.255zM4.09 12h1.836a6.692 6.692 0 0 1-.468.932A12.5 12.5 0 0 1 4.847 13H4.09zm.458-1.932a6.689 6.689 0 0 1 .468-.932h2.146c-.062.89-.291 1.733-.656 2.5H4.548a6.704 6.704 0 0 1-.468-.932z"/>
</svg> STMIK Komputama Teknologi</a></footer>
</html>
