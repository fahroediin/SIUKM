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
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
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
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];

    // Update data pengguna di tabel tab_user
    $query = "UPDATE tab_user SET password='$password', nama_lengkap='$nama_lengkap', email='$email', no_hp='$no_hp' WHERE id_user='$id_user'";
    $updateResult = mysqli_query($conn, $query);

    // Memeriksa apakah query update berhasil dieksekusi
    if ($updateResult) {
        // Mengupdate data pengguna di session
        $_SESSION['nama_lengkap'] = $nama_lengkap;
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
    echo "<script>alert('Password lama salah.');</script>";
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
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required value="<?php echo $_SESSION['nama_lengkap']; ?>">
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
</html>
