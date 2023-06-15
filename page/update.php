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
            $error = "Error: Password lama tidak cocok.";
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
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perbarui Informasi</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

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
<body>
<div class="container">
<h1 class="text-center">UPDATE DATA</h1>
<div class="container">
    <div class="row">
    <div class="col-md-12">
    <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Dashboard</a>
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
        
    </form>

    <!-- snackbar jika password tidak cocok-->
    <div id="snackbar"></div>

</div>

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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>