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
$active_page = 'user_manager';


// Memeriksa apakah tombol delete diklik
if (isset($_GET['delete'])) {
    // Memeriksa apakah parameter id_user telah diberikan
    if (isset($_GET['id'])) {
        $id_user = $_GET['id'];

        // Menghapus user dari database
        $sql = "DELETE FROM tab_user WHERE id_user = '$id_user'";
        $result = $conn->query($sql);

        if ($result) {
            // Redirect ke halaman daftar user setelah penghapusan berhasil
            header("Location: proses_user.php");
            exit();
        } else {
            // Jika terjadi kesalahan saat menghapus user
            exit();
        }
    } else {
        // Jika parameter id_user tidak diberikan
        echo "Invalid user ID";
        exit();
    }
}

// Function to check if id_user is already registered and return a boolean value
function isIdUserRegistered($id_user, $conn)
{
    $sql = "SELECT id_user FROM tab_user WHERE id_user = '$id_user'";
    $result = $conn->query($sql);
    return ($result->num_rows > 0);
}

// Memeriksa apakah form tambah user telah di-submit
if (isset($_POST['submit'])) {
    // Mengambil data dari form
    $id_user = $_POST['id_user'];

    // Check if the id_user is already registered
    if (isIdUserRegistered($id_user, $conn)) {
        echo "<script>alert('NIM Sudah Terdaftar');</script>";
        // Kosongkan textfield id_user
        header("Location: proses_user.php");
        $_POST['id_user'] = "";
        // Exit to prevent further processing
        exit();
    }

    $password = $_POST['password'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $level = $_POST['level'];
    $prodi = $_POST['prodi'];
    $semester = $_POST['semester'];

    // Handle file uploads for pasfoto and foto_ktm
    $pasfoto_filename = ""; // Variable to store pasfoto file name
    $foto_ktm_filename = ""; // Variable to store foto_ktm file name

   // Memeriksa apakah form tambah user telah di-submit
if (isset($_POST['submit'])) {
    // Mengambil data dari form
    $id_user = $_POST['id_user'];
    $password = $_POST['password'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $level = $_POST['level'];
    $prodi = $_POST['prodi']; 
    $semester = $_POST['semester']; 

    // Handle file uploads for pasfoto and foto_ktm
    $pasfoto_filename = ""; // Variable to store pasfoto file name
    $foto_ktm_filename = ""; // Variable to store foto_ktm file name

    if ($_FILES['pasfoto']['error'] === 0) {
        // File is uploaded successfully, move it to a desired directory
        $pasfoto_tmp_name = $_FILES['pasfoto']['tmp_name'];
        $pasfoto_extension = pathinfo($_FILES['pasfoto']['name'], PATHINFO_EXTENSION);
        $pasfoto_filename = $id_user . "_" . $nama_lengkap . "." . $pasfoto_extension; // Format the filename
        move_uploaded_file($pasfoto_tmp_name, "../assets/images/pasfoto/" . $pasfoto_filename);
    }

    if ($_FILES['foto_ktm']['error'] === 0) {
        // File is uploaded successfully, move it to a desired directory
        $foto_ktm_tmp_name = $_FILES['foto_ktm']['tmp_name'];
        $foto_ktm_extension = pathinfo($_FILES['foto_ktm']['name'], PATHINFO_EXTENSION);
        $foto_ktm_filename = $id_user . "_" . $nama_lengkap . "." . $foto_ktm_extension; // Format the filename
        move_uploaded_file($foto_ktm_tmp_name, "../assets/images/ktm/" . $foto_ktm_filename);
    }

    // Menyimpan data ke database
$sql = "INSERT INTO tab_user (id_user, password, nama_lengkap, email, no_hp, level, prodi, semester, pasfoto, foto_ktm)
VALUES ('$id_user', '$password', '$nama_lengkap', '$email', '$no_hp', '$level', '$prodi', '$semester', '$pasfoto_filename', '$foto_ktm_filename')";


    $result = $conn->query($sql);

    if ($result) {
        // Redirect ke halaman daftar user setelah penyimpanan berhasil
        header("Location: proses_user.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menyimpan user
        exit();
    }
}
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>User Manager - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
</head>
<style>
            .password-input {
    position: relative;
    }

    .password-input input {
    padding-right: 30px; /* To make space for the icon */
    }

    .password-input i {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    }
    .card {
        width: 100%; /* Set the width to 100% to make the card responsive */
        max-width: 400px; /* Add max-width to limit the card's width */
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
     th {
        white-space: nowrap;
    }

    .btn {
        padding: 8px 12px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #0056b3;
    }
    .delete-button {
        background-color: red;
    }
        /* Tambahkan gaya CSS berikut untuk mengatur tata letak tombol */
        .action-buttons {
        display: flex;
        justify-content: space-between;
    }

    .action-buttons button {
        flex: 1;
        margin-right: 5px;
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
    .content {
    /* Atur tata letak (layout) untuk kontainer utama */
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* Penyesuaian padding atau margin sesuai kebutuhan */
}

.header {
    /* Atur tata letak (layout) untuk header */
    display: flex;
    align-items: center;
}

.header h2 {
    /* Atur gaya untuk elemen H2 pada header */
    margin-right: 10px; /* Jarak antara H2 dan tombol tambah */
}
.is-invalid {
    border-color: red;
}
</style>


<div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Pengguna</i></h2>
<a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <p style="text-align: center;">--Manajemen--</p>
    <a href="proses_beranda.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_beranda') echo 'active'; ?>">Beranda</a>
    <a href="proses_profil.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_profil') echo 'active'; ?>">Profil</a>
    <a href="proses_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="proses_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="proses_user.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_dau.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_struktur.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'struktur') echo 'active'; ?>">Pengurus</a>
    <a href="proses_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'ukm') echo 'active'; ?>">Data UKM</a>
    <a href="calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
    <a href="proses_lpj.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'lpj') echo 'active'; ?>">LPJ</a>
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
<!-- Data User -->
<div class="content">
    <div class="header">
        <h2>User Manager</h2>
        <button type="button" class="btn btn-primary btn-sm btn-medium" data-toggle="modal" data-target="#userModal">
            <i class="fas fa-plus"></i> Tambah Pengguna
        </button>
    </div>
    <form class="form-inline mb-3" method="GET" action="">
    <input type="text" class="form-control mr-sm-2" placeholder="Search by ID User" name="search_id_user">
    <button type="submit" class="btn btn-outline-primary">Search</button>
    <a href="proses_user.php" class="btn btn-outline-secondary ml-2">
  <i class="fas fa-sync-alt"></i>
</a>
</div>
</form>
<div class="content">
    <table class="table">
        <thead>
            <tr>
                <th>No.</th>
                <th>ID User</th>
                <th>Nama Lengkap</th>
                <th>Prodi</th>
                <th>Semester</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Level</th>
                <th>Foto</th>
                <th>Foto Identitas</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php
         // Fetch users from the database
         $searchCondition = isset($_GET['search_id_user']) ? "WHERE id_user LIKE '%" . $_GET['search_id_user'] . "%'" : "";
         $sql = "SELECT * FROM tab_user $searchCondition";
         $result = $conn->query($sql);
         if ($result->num_rows > 0) {
                $serialNumber = 1; // Initialize the serial number
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $serialNumber . "</td>"; // Display the serial number
                    echo "<td>" . $row["id_user"] . "</td>";
                    echo "<td>" . $row["nama_lengkap"] . "</td>";
                    echo "<td>" . $row["prodi"] . "</td>";
                    echo "<td>" . $row["semester"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "<td>" . $row["no_hp"] . "</td>";
                    echo "<td>" . $row["level"] . "</td>";
                    // Pasfoto
                    $pasfoto_filename = $row["pasfoto"];
                    $pasfoto_path = "../assets/images/pasfoto/" . $pasfoto_filename;
                    echo "<td><img src='$pasfoto_path' alt='Pasfoto' width='100'></td>";

                    // Foto KTM
                    $foto_ktm_filename = $row["foto_ktm"];
                    $foto_ktm_path = "../assets/images/ktm/" . $foto_ktm_filename;
                    echo "<td><img src='$foto_ktm_path' alt='Foto Identitas' width='100'></td>";

                    // Menambahkan kondisi jika ID user adalah "admin"
                    // Action buttons for each row
                    echo "<td>";
                    if ($row["id_user"] == "admin") {
                        // For the admin user, show only the Edit button
                        echo "<a class='btn btn-edit' href='edit_user.php?id=" . $row["id_user"] . "'>Edit</a>";
                    } else {
                        // For other users, show both Edit and Delete buttons
                        echo "<div class='action-buttons'>
                                <a class='btn btn-edit' href='edit_user.php?id=" . $row["id_user"] . "'>Edit</a>
                                <a class='btn delete-button' href='proses_delete_user.php?id=" . $row["id_user"] . "' onclick='return confirmDelete()'>Hapus</a>
                              </div>";
                    }
                    echo "</td>";

                    // Increment the serial number for the next row
                    $serialNumber++;
                }
            } else {
                echo "<tr><td colspan='11' class='text-center'>No results found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>

<!-- Add a script to handle level dropdown updates -->
<script>
    // Function to handle level dropdown updates
    document.querySelectorAll('.level-dropdown').forEach(function (dropdown) {
        dropdown.addEventListener('change', function () {
            var userId = this.getAttribute('data-user-id');
            var newLevel = this.value;

            // Perform an AJAX request to update the user's level in the database
            // You can use fetch or jQuery.ajax to make the request

            // Example using fetch:
            fetch('update_user_level.php', {
                method: 'POST',
                body: JSON.stringify({ userId: userId, level: newLevel }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response if needed
                console.log(data);
            })
            .catch(error => {
                // Handle any errors if they occur
                console.error('Error:', error);
            });
        });
    });
</script>

<script>
    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus data ini?");
    }
</script>

    <script>
    const passwordInput1 = document.getElementById("password");
    const passwordToggle1 = document.getElementById("passwordToggle1");
    const passwordInput2 = document.getElementById("konfirmasi_password");
    const passwordToggle2 = document.getElementById("passwordToggle2");

    passwordToggle1.addEventListener("click", function () {
        if (passwordInput1.type === "password") {
            passwordInput1.type = "text";
            passwordToggle1.classList.remove("fa-eye");
            passwordToggle1.classList.add("fa-eye-slash");
        } else {
            passwordInput1.type = "password";
            passwordToggle1.classList.remove("fa-eye-slash");
            passwordToggle1.classList.add("fa-eye");
        }
    });

    passwordToggle2.addEventListener("click", function () {
        if (passwordInput2.type === "password") {
            passwordInput2.type = "text";
            passwordToggle2.classList.remove("fa-eye");
            passwordToggle2.classList.add("fa-eye-slash");
        } else {
            passwordInput2.type = "password";
            passwordToggle2.classList.remove("fa-eye-slash");
            passwordToggle2.classList.add("fa-eye");
        }
    });
    </script>


<!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>// Ambil elemen toggle button dan sidebar
const toggleBtn = document.querySelector('.toggle-btn');
const sidebar = document.querySelector('.sidebar');

// Tambahkan event listener untuk toggle button
toggleBtn.addEventListener('click', () => {
  // Toggle class 'collapsed' pada sidebar
  sidebar.classList.toggle('collapsed');
});

// Function to handle delete user
function deleteUser(userId) {
  // Prompt the user for confirmation
  var confirmDelete = confirm("Are you sure you want to delete this user?");

  // If the user confirms the deletion
  if (confirmDelete) {
    // Delete the user from the user list (assuming you have an array or object to store the user list)
    // Example code:
    // userList.splice(userId, 1);

    // Refresh the user list table
    populateUserList();
  }
}

// Add event listener to delete buttons
var deleteButtons = document.getElementsByClassName('deleteBtn');
for (var i = 0; i < deleteButtons.length; i++) {
  deleteButtons[i].addEventListener('click', function() {
    var userId = this.getAttribute('data-id');
    deleteUser(userId);
  });
}

// Fungsi untuk logout
function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
</script>
<!-- Add a modal structure at the end of the body tag -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
       
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Place your form here -->
        <h2 style="text-align: center;">Tambah Pengguna</h2>
        <form method="POST" action="proses_user.php" onsubmit="return validateForm();" enctype="multipart/form-data">
            <div class="form-group">
            <div class="form-group">
    <label for="id_user">*ID User:</label>
    <input type="text" class="form-control" id="id_user" maxlength="10" name="id_user" required>
    <div class="invalid-feedback" id="id-user-error" style="color: red;"></div>
</div>

<script>
    document.getElementById("id_user").addEventListener("input", function(event) {
        let input = event.target.value;
        
        // Remove characters that are not letters or numbers using a regular expression
        input = input.replace(/[^a-zA-Z0-9]/g, '');

        // Limit the length to a maximum of 10 characters
        input = input.slice(0, 10);

        event.target.value = input;

        let errorElement = document.getElementById("id-user-error");
        if (input.length < 5 || input.length > 10) {
            errorElement.textContent = "ID User harus memiliki minimal 5 dan maksimal 10 karakter!";
            event.target.classList.add("is-invalid");
        } else {
            errorElement.textContent = "";
            event.target.classList.remove("is-invalid");
        }
    });
</script>

  <div class="form-group">
    <label for="password">*Password:</label>
    <div class="password-input">
        <input type="password" class="form-control" placeholder="Wajib diisi" id="password" maxlength="30" name="password" required>
        <i class="fas fa-eye" id="passwordToggle1"></i>
    </div>
</div>
<div class="form-group">
    <label for="konfirmasi_password">*Konfirmasi Password:</label>
    <div class="password-input">
        <input type="password" class="form-control" placeholder="Pastikan konfirmasi password sesuai" id="konfirmasi_password" maxlength="30" name="konfirmasi_password" required>
        <i class="fas fa-eye" id="passwordToggle2"></i>
    </div>
    <div class="invalid-feedback" id="password-match-error" style="color: red;"></div>
</div>
<script>
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("konfirmasi_password");
    const passwordMatchError = document.getElementById("password-match-error");

    passwordInput.addEventListener("input", validatePassword);
    confirmPasswordInput.addEventListener("input", validatePassword);

    function validatePassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        // Check if passwords match and have a minimum length of 6 characters
        if (password === confirmPassword && password.length >= 6) {
            passwordMatchError.textContent = "";
            passwordInput.classList.remove("is-invalid");
            confirmPasswordInput.classList.remove("is-invalid");
        } else {
            passwordMatchError.textContent = "Password harus sesuai dan memiliki minimal 6 karakter!";
            passwordInput.classList.add("is-invalid");
            confirmPasswordInput.classList.add("is-invalid");
        }
    }

    // Toggle password visibility
    document.getElementById("passwordToggle1").addEventListener("click", function() {
        togglePasswordVisibility("password");
    });

    document.getElementById("passwordToggle2").addEventListener("click", function() {
        togglePasswordVisibility("konfirmasi_password");
    });

    function togglePasswordVisibility(inputId) {
        const passwordInput = document.getElementById(inputId);
        const icon = passwordInput.nextElementSibling;

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>
<div class="form-group">
    <label for="nama_lengkap">*Nama Lengkap:</label>
    <input type="text" class="form-control" placeholder="Wajib diisi" id="nama_lengkap" name="nama_lengkap" required>
    <div class="invalid-feedback" id="nama-lengkap-error" style="color: red;"></div>
</div>

<script>
    document.getElementById("nama_lengkap").addEventListener("input", function(event) {
        let input = event.target.value;
        
        // Remove non-letter characters using a regular expression
        input = input.replace(/[^a-zA-Z ]/g, '');

        // Limit the length to a maximum of 70 characters
        input = input.slice(0, 70);

        event.target.value = input;

        let errorElement = document.getElementById("nama-lengkap-error");
        if (input.length < 1) {
            errorElement.textContent = "Nama Lengkap harus memiliki minimal 1 huruf!";
            event.target.classList.add("is-invalid");
        } else {
            errorElement.textContent = "";
            event.target.classList.remove("is-invalid");
        }
    });
</script>


            <div class="form-group">
                <label for="prodi">Program Studi:</label>
                <select id="prodi" name="prodi" class="form-control">
                    <option value="" selected>Pilih Program Studi</option>
                    <option value="Teknik Informatika">Teknik Informatika</option>
                    <option value="Sistem Informasi">Sistem Informasi</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="semester">Semester:</label>
                <select id="semester" name="semester" class="form-control">
                    <option value="" selected>Pilih Semester</option>
                    <?php
                    for ($i = 1; $i <= 14; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
        <label for="email">*Email:</label>
        <input type="text" class="form-control" placeholder="Wajib diisi dengan format email yang sesuai" id="email" name="email">
        <div class="invalid-feedback" id="email-error" style="color: red;"></div>
    </div>

    <script>
    document.getElementById("email").addEventListener("blur", function(event) {
        let input = event.target.value;
        let errorElement = document.getElementById("email-error");
        if (!isValidEmail(input)) {
            errorElement.textContent = "Format email tidak valid!";
            event.target.classList.add("is-invalid"); // Tambahkan class is-invalid untuk merahkan input
        } else {
            errorElement.textContent = "";
            event.target.classList.remove("is-invalid"); // Hapus class is-invalid jika valid
        }
    });

    function isValidEmail(email) {
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        return emailPattern.test(email);
    }
    </script>
<div class="form-group">
    <label for="no_hp">*No. HP:</label>
    <input type="text" class="form-control" id="no_hp" placeholder="Wajib diisi" name="no_hp" maxlength="13" required>
    <div class="invalid-feedback" id="no-hp-error" style="color: red;"></div>
</div>


<script>
   document.getElementById("no_hp").addEventListener("input", function(event) {
    let input = event.target.value;
    input = input.replace(/\D/g, ''); // Menghapus karakter non-angka
    input = input.slice(0, 13); // Membatasi panjang maksimal menjadi 13 karakter
    event.target.value = input;

    let errorElement = document.getElementById("no-hp-error");
    if (input.length < 10) {
        errorElement.textContent = "No. HP harus memiliki minimal 10 karakter!";
        event.target.classList.add("is-invalid"); // Tambahkan class is-invalid untuk merahkan input
    } else {
        errorElement.textContent = "";
        event.target.classList.remove("is-invalid"); // Hapus class is-invalid jika valid
    }
});

</script>

            <div class="form-group">
                <label for="pasfoto">Pasfoto:</label>
                <input type="file" class="form-control-file" id="pasfoto" name="pasfoto" accept="image/*">
                <img id="pasfotoPreview" src="" alt="Pasfoto Preview" width="100">
            </div>
            <div class="form-group">
                <label for="foto_ktm">Foto Identitas:</label>
                <input type="file" class="form-control-file" id="foto_ktm" name="foto_ktm" accept="image/*">
                <img id="fotoKtmPreview" src="" alt="Foto Identitas Preview" width="100">
            </div>
            <div class="form-group" required>
                <label for="level">Level:</label>
                <select id="level" name="level" class="form-control">
                    <option value="3">User</option>
                    <option value="2">Kemahasiswaan</option>
                    <option value="1">Admin</option>
                </select>
            </div>
            <div class="text-center"> <!-- Wrap the button in a div with the "text-center" class -->
            <button type="submit" class="btn btn-primary btn-sm btn-medium" name="submit">
    <i class="fas fa-plus"></i> Tambah Pengguna
</button>
    </div>
        </form>
    </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
    // Function to update photo preview
    function updatePreview(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    // Add event listeners for file inputs
    const pasfotoInput = document.getElementById('pasfoto');
    pasfotoInput.addEventListener('change', function () {
        updatePreview(this, 'pasfotoPreview');
    });

    const fotoKtmInput = document.getElementById('foto_ktm');
    fotoKtmInput.addEventListener('change', function () {
        updatePreview(this, 'fotoKtmPreview');
    });
</script>

</body>
</html>