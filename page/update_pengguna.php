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
$active_page = 'update_pengguna';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}
// Memeriksa apakah id_anggota ada pada session
if (isset($_SESSION['id_anggota'])) {
    $id_anggota_session = $_SESSION['id_anggota'];
    // Jika id_anggota ada pada session, tampilkan tombol-tombol
    $showButtons = true;
} else {
    // Jika id_anggota tidak ada pada session, sembunyikan tombol-tombol
    $showButtons = false;
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
    $_SESSION['pasfoto'] = $user['pasfoto'];
    $_SESSION['foto_ktm'] = $user['foto_ktm'];
    $_SESSION['semester'] = $user['semester'];
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
}

// Memeriksa apakah tombol update diklik
if (isset($_POST['update'])) {
    // Memeriksa apakah parameter id_user telah diberikan
    if (isset($_POST['id_user'])) {
        // Get the ID and other user data from the POST request
    $id_user = $_POST['id_user'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $prodi = $_POST['prodi'];
    $semester = $_POST['semester'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $level = $_POST['level'];

    // File upload handling for pasfoto
    if ($_FILES['pasfoto']['error'] === 0) {
        $pasfoto_tmp_name = $_FILES['pasfoto']['tmp_name'];
        $pasfoto_extension = pathinfo($_FILES['pasfoto']['name'], PATHINFO_EXTENSION);
        $pasfoto_filename = $id_user . "_" . $nama_lengkap . "." . $pasfoto_extension; // Format the filename
        $pasfoto_destination = "../assets/images/pasfoto/" . $pasfoto_filename;
        move_uploaded_file($pasfoto_tmp_name, $pasfoto_destination);
    } else {
        // If no new pasfoto is uploaded, keep the existing filename
        $pasfoto_filename = $row['pasfoto']; // Assuming 'pasfoto' is the column in the 'tab_user' table that stores the pasfoto filename
    }

    // File upload handling for foto_ktm
    if ($_FILES['foto_ktm']['error'] === 0) {
        $foto_ktm_tmp_name = $_FILES['foto_ktm']['tmp_name'];
        $foto_ktm_extension = pathinfo($_FILES['foto_ktm']['name'], PATHINFO_EXTENSION);
        $foto_ktm_filename = $id_user . "_" . $nama_lengkap . "." . $foto_ktm_extension; // Format the filename
        $foto_ktm_destination = "../assets/images/ktm/" . $foto_ktm_filename;
        move_uploaded_file($foto_ktm_tmp_name, $foto_ktm_destination);
    } else {
        // If no new foto_ktm is uploaded, keep the existing filename
        $foto_ktm_filename = $row['foto_ktm']; // Assuming 'foto_ktm' is the column in the 'tab_user' table that stores the foto_ktm filename
    }

    // Prepare the update query using prepared statements to avoid SQL injection
    $stmt = $conn->prepare("UPDATE tab_user SET nama_lengkap = ?, prodi = ?, semester = ?, email = ?, no_hp = ?, level = ?, pasfoto = ?, foto_ktm = ? WHERE id_user = ?");
    $stmt->bind_param("ssisssssi", $nama_lengkap, $prodi, $semester, $email, $no_hp, $level, $pasfoto_filename, $foto_ktm_filename, $id_user);

    // Execute the update query
    if ($stmt->execute()) {
        // Redirect back to the user list after update
        header("Location: dashboard.php");
        exit();
    } else {
        // If an error occurs during the update
        echo "Error: " . $stmt->error;
        exit();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
<div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
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
    <div class="container">
<h2 class="text-center">UPDATE DATA</h2>
<div class="container">
    <div class="row">
    <div class="col-md-12">

</div>
    </div>
    <form class="form-container" method="POST" action="" enctype="multipart/form-data">
    <div>
        <label for="id_user">*ID User (NIM)</label>
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
            <label for="nama_lengkap">*Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required value="<?php echo $_SESSION['nama_lengkap']; ?>">
        </div>
        <div class="form-group">
        <label for="prodi">*Program Studi</label>
            <select class="form-control" id="prodi" name="prodi" required>
                <option value="Teknik Informatika" <?php if ($user['prodi'] === 'Teknik Informatika') echo 'selected'; ?>>Teknik Informatika</option>
                <option value="Sistem Informasi" <?php if ($user['prodi'] === 'Sistem Informasi') echo 'selected'; ?>>Sistem Informasi</option>
            </select>
    </div>
    <div class="form-group">
        <label for="semester">*Semester</label>
        <select class="form-control" id="semester" name="semester" required>
        <?php
        for ($i = 1; $i <= 14; $i++) {
            $selected = ($user['semester'] == $i) ? 'selected' : '';
            echo "<option value=\"$i\" $selected>$i</option>";
        }
        ?>
    </select>
    </div>
        <div class="form-group">
            <label for="email">*Email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo $_SESSION['email']; ?>">
        </div>
        <div class="form-group">
    <label for="no_hp">*Nomor HP</label>
    <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo $_SESSION['no_hp']; ?>" required>
</div>

<script>
    document.getElementById('no_hp').addEventListener('input', function () {
        // Hapus semua karakter selain angka
        this.value = this.value.replace(/\D/g, '');

        // Batasi panjang input menjadi maksimal 13 karakter
        if (this.value.length > 13) {
            this.value = this.value.slice(0, 13);
        }
    });
</script>

  <!-- Pasfoto preview -->
<div class="form-group">
    <label for="pasfoto">*Pasfoto</label>
    <input type="file" class="form-control-file" id="pasfoto" name="pasfoto" accept="image/*">
    <?php if ($user['pasfoto']) { ?>
        <img id="pasfoto-preview" src="../assets/images/pasfoto/<?php echo $user['pasfoto']; ?>" alt="Pasfoto Preview" style="max-width: 100px; max-height: 100px;">
    <?php } ?>
</div>

<!-- Foto KTM preview -->
<div class="form-group">
    <label for="foto_ktm">*Foto KTM</label>
    <input type="file" class="form-control-file" id="foto_ktm" name="foto_ktm" accept="image/*">
    <?php if ($user['foto_ktm']) { ?>
        <img id="foto-ktm-preview" src="../assets/images/ktm/<?php echo $user['foto_ktm']; ?>" alt="Foto KTM Preview" style="max-width: 100px; max-height: 100px;">
    <?php } ?>
</div>



    
        <button type="submit" class="btn btn-primary" name="update">Simpan Perubahan</button>
            </div>
    </form>
    </div>

    


<!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
<script src="script.js"></script>
<script>
    // Function to update image preview based on selected file
    function updateImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Call the function to update the preview when a file is selected
    document.getElementById("pasfoto").addEventListener("change", function() {
        updateImagePreview("pasfoto", "pasfoto-preview");
    });

    document.getElementById("foto_ktm").addEventListener("change", function() {
        updateImagePreview("foto_ktm", "foto-ktm-preview");
    });
</script>

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