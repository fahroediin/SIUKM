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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $oldPassword = $_POST['old_password'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_POST['email'];
    $namaDepan = $_POST['nama_depan'];
    $namaBelakang = $_POST['nama_belakang'];
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
        $namaDepan = mysqli_real_escape_string($conn, $namaDepan);
        $namaBelakang = mysqli_real_escape_string($conn, $namaBelakang);
        $noHp = mysqli_real_escape_string($conn, $noHp);

        // Mengecek kebenaran password lama
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM tab_user WHERE id_user = '$userId' AND password = '$oldPassword'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    // Password lama tidak cocok
    echo "<script>alert('Password lama salah.');</script>";
} else {
    // Membuat query update
    $query = "UPDATE tab_user SET password = '$password', email = '$email', nama_depan = '$namaDepan', nama_belakang = '$namaBelakang', no_hp = '$noHp' WHERE id_user = '$userId'";

    // Mengeksekusi query update
    $updateResult = mysqli_query($conn, $query);

    // Tampilkan snackbar jika data berhasil diubah
    if ($updateResult) {
        // Mengupdate data di dalam session
        $_SESSION['password'] = $password;
        $_SESSION['email'] = $email;
        $_SESSION['nama_depan'] = $namaDepan;
        $_SESSION['nama_belakang'] = $namaBelakang;
        $_SESSION['no_hp'] = $noHp;

        // Tampilkan snackbar jika data berhasil diubah
        echo "<script>showSnackbar('Data berhasil diubah.');</script>";
    } else {
        // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
        $error = "Error: " . mysqli_error($conn);
        echo "<script>alert('$error');</script>";
    }
}
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Data Pengguna - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
    }

    .container h1 {
        text-align: center;
        margin-bottom: 30px;
    }

    .form-container {
        margin-bottom: 20px;
    }

    .form-container .form-group label {
        font-weight: bold;
    }

    .form-container .form-group input {
        border-radius: 5px;
    }

    .form-container .btn-primary {
        width: 100%;
    }

    /* Style for snackbar */
    #snackbar {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
    }

    #snackbar.show {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }

    @-webkit-keyframes fadein {
        from {bottom: 0; opacity: 0;}
        to {bottom: 30px; opacity: 1;}
    }

    @keyframes fadein {
        from {bottom: 0; opacity: 0;}
        to {bottom: 30px; opacity: 1;}
    }

    @-webkit-keyframes fadeout {
        from {bottom: 30px; opacity: 1;}
        to {bottom: 0; opacity: 0;}
    }

    @keyframes fadeout {
        from {bottom: 30px; opacity: 1;}
        to {bottom: 0; opacity: 0;}
    }
</style>
<script>
        function showSnackbar(message) {
            var snackbar = document.getElementById("snackbar");
            snackbar.innerHTML = message;
            snackbar.className = "show";
            setTimeout(function() {
                snackbar.className = snackbar.className.replace("show", "");
            }, 3000);
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Manajemen Pengguna</h2>
        <a href="dashboard.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
        <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
        <a href="proses_update_pengguna.php" class="btn btn-primary <?php if($active_page == 'proses_update_pengguna') echo 'active'; ?>">Update</a>
        <a href="?logout=true" class="btn btn-primary <?php if($active_page == 'logout') echo 'active'; ?>">Logout</a>
    </div>
    <div class="container">
<h2 class="text-center">UPDATE DATA</h2>
<div class="container">
    <div class="row">
    <div class="col-md-12">

</div>
    </div>
    <form class="form-container" method="POST" action="">
        <div class="form-group">
            <label for="id_user">ID User (NIM):</label>
            <input type="text" class="form-control" id="id_user" name="id_user" required value="<?php echo $_SESSION['id_user']; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="old_password">Password Lama:</label>
            <input type="password" class="form-control" id="old_password" name="old_password">
        </div>
        <div class="form-group">
            <label for="password">Password Baru:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo $_SESSION['email']; ?>">
        </div>
        <div class="form-group">
            <label for="nama_depan">Nama Depan:</label>
            <input type="text" class="form-control" id="nama_depan" name="nama_depan" required value="<?php echo $_SESSION['nama_depan']; ?>">
        </div>
        <div class="form-group">
            <label for="nama_belakang">Nama Belakang:</label>
            <input type="text" class="form-control" id="nama_belakang" name="nama_belakang" required value="<?php echo $_SESSION['nama_belakang']; ?>">
        </div>
        <div class="form-group">
            <label for="no_hp">Nomor Telepon:</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" required value="<?php echo $_SESSION['no_hp']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
    </form>
    </div>
    <!-- snackbar jika password tidak cocok-->
    <div id="snackbar"></div>

<!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
<script src="script.js"></script>
<script>
        function showSnackbar(message) {
            var snackbar = document.getElementById("snackbar");
            snackbar.innerHTML = message;
            snackbar.className = "show";
            setTimeout(function() {
                snackbar.className = snackbar.className.replace("show", "");
            }, 3000);
        }

        document.addEventListener("DOMContentLoaded", function() {
            var form = document.querySelector("form");
            form.addEventListener("submit", function(event) {
                var oldPassword = document.getElementById("old_password").value;
                var password = document.getElementById("password").value;
                var confirmPassword = document.getElementById("confirm_password").value;

                if (oldPassword !== "" && password !== confirmPassword) {
                    event.preventDefault();
                    showSnackbar("Password tidak cocok");
                }
            });
        });
    </script>
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