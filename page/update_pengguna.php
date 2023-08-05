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
    $_SESSION['prodi'] = $user['prodi'];
    $_SESSION['semester'] = $user['semester'];
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
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $prodi = $_POST['prodi'];
    $semester = $_POST['semester'];

    // Handle file uploads for pasfoto and foto_ktm
    if (isset($_FILES['pasfoto']) && isset($_FILES['foto_ktm'])) {
        $pasfoto = $_FILES['pasfoto'];
        $foto_ktm = $_FILES['foto_ktm'];

        // Generate new filenames based on id_user and nama_lengkap
        $pasfotoFileName = $_SESSION['id_user'] . "_" . $_SESSION['nama_lengkap'] . "_" . uniqid() . "." . pathinfo($pasfoto['name'], PATHINFO_EXTENSION);
        $fotoKtmFileName = $_SESSION['id_user'] . "_" . $_SESSION['nama_lengkap'] . "_" . uniqid() . "." . pathinfo($foto_ktm['name'], PATHINFO_EXTENSION);

        // Move uploaded files to the respective directories
        $pasfotoDestination = "../assets/images/pasfoto/" . $pasfotoFileName;
        $fotoKtmDestination = "../assets/images/ktm/" . $fotoKtmFileName;

        if (move_uploaded_file($pasfoto['tmp_name'], $pasfotoDestination) && move_uploaded_file($foto_ktm['tmp_name'], $fotoKtmDestination)) {
            // File uploads successful, update the database with the new filenames
            $query = "UPDATE tab_user SET nama_lengkap=?, email=?, no_hp=?, prodi=?, semester=?, pasfoto=?, foto_ktm=? WHERE id_user=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssssssi", $nama_lengkap, $email, $no_hp, $prodi, $semester, $pasfotoFileName, $fotoKtmFileName, $id_user);
            $updateResult = mysqli_stmt_execute($stmt);

            if ($updateResult) {
                // Mengupdate data di dalam session
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['email'] = $email;
                $_SESSION['no_hp'] = $no_hp;
                $_SESSION['prodi'] = $prodi;
                $_SESSION['semester'] = $semester;

                // Redirect ke halaman dashboard.php
                header("Location: dashboard.php");
                exit();
            } else {
                // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
                $error = "Error: " . mysqli_error($conn);
                echo "<script>alert('$error');</script>";
            }
        } else {
            // File uploads failed, handle the error accordingly
            $error = "Error uploading files. Please try again.";
            echo "<script>alert('$error');</script>";
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
    </div>
    <div class="container">
<h2 class="text-center">UPDATE DATA</h2>
<div class="container">
    <div class="row">
    <div class="col-md-12">

</div>
    </div>
    <form class="form-container" method="POST" action="" enctype="multipart/form-data">
    <div>
        <label for="id_user">ID User (NIM):</label>
        <input type="text" id="id_user" class="form-control" name="id_user" required placeholder="Masukkan ID User (NIM)" value="<?php echo $_SESSION['id_user']; ?>" oninput="validasiIdUser(event, 10)">
        </div>
        <script>
        function validasiIdUser(event, maxLength) {
            const input = event.target;
            const filteredValue = input.value.replace(/[^0-9]/g, '').slice(0, maxLength);
            input.value = filteredValue;
        }
        </script>
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required value="<?php echo $_SESSION['nama_lengkap']; ?>">
        </div>
        <div class="form-group">
        <label for="prodi">Program Studi:</label>
        <select class="form-control" id="prodi" name="prodi" required>
            <option value="Teknik Informatika">Teknik Informatika</option>
            <option value="Sistem Informasi">Sistem Informasi</option>
        </select>
    </div>
    <div class="form-group">
        <label for="semester">Semester:</label>
        <select class="form-control" id="semester" name="semester" required>
            <?php
            for ($i = 1; $i <= 14; $i++) {
                echo "<option value=\"$i\">$i</option>";
            }
            ?>
        </select>
    </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo $_SESSION['email']; ?>">
        </div>
        <div class="form-group">
            <label for="no_hp">Nomor Telepon:</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" required value="<?php echo $_SESSION['no_hp']; ?>">
        </div>
        <!-- Pasfoto input -->
    <div class="form-group">
        <label for="pasfoto">Pasfoto:</label>
        <input type="file" class="form-control-file" id="pasfoto" name="pasfoto" accept="image/*" required>
    </div>

    <!-- Foto KTM input -->
    <div class="form-group">
        <label for="foto_ktm">Foto KTM:</label>
        <input type="file" class="form-control-file" id="foto_ktm" name="foto_ktm" accept="image/*" required>
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