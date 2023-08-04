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
if ($_SESSION['level'] == "3") {
    // Jika level adalah "3", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}

// Menandai halaman yang aktif
$active_page = 'user_manager';

// Fungsi logout
function logout()
{
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
            echo "Error: " . $conn->error;
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
            VALUES ('$id_user', '$password', '$nama_lengkap', '$email', '$no_hp', '$level', '$prodi', $semester, '$pasfoto_filename', '$foto_ktm_filename')";

    $result = $conn->query($sql);

    if ($result) {
        // Redirect ke halaman daftar user setelah penyimpanan berhasil
        header("Location: proses_user.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menyimpan user
        echo "Error: " . $conn->error;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
</head>
<style>
    .card {
        width: 50%;
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
     /* Update the Edit button styles */
     .btn.btn-edit {
        padding: 8px 12px;
        background-color: #007bff; /* Blue color */
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none; /* Remove underline from links */
        margin-right: 8px; /* Add a little space between the buttons */
    }

    /* Update the Delete button styles */
    .btn.btn-delete {
        padding: 8px 12px;
        background-color: red;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none; /* Remove underline from links */
    }
           /* Tambahkan gaya CSS berikut untuk mengatur tata letak tombol */
        .action-buttons {
        display: flex;
        justify-content: space-between;
    }
</style>
<body>
<div class="sidebar">
    <h2>Manajemen Pengguna</h2>
    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
    <a href="proses_struktur.php" class="btn btn-primary <?php if($active_page == 'struktur') echo 'active'; ?>">Kepengurusan</a>
    <a href="proses_dau.php" class="btn btn-primary <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_prestasi.php" class="btn btn-primary <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_ukm.php" class="btn btn-primary <?php if($active_page == 'visi_misi') echo 'active'; ?>">Data UKM</a>
    <a href="proses_galeri.php" class="btn btn-primary <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="proses_kegiatan.php" class="btn btn-primary <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
</div>


<!-- Data User -->
<div class="content">
    <h2>Daftar User</h2>
    <table class="table">
    <thead>
            <tr>
                <th>ID User</th>
                <th>Nama Lengkap</th>
                <th>Prodi</th>
                <th>Semester</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Level</th>
                <th>Pasfoto</th>
                <th>Foto KTM</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
    <?php
    // Fetch users from the database
    $sql = "SELECT * FROM tab_user";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
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
            echo "<td><img src='$foto_ktm_path' alt='Foto KTM' width='100'></td>";
            // Menambahkan kondisi jika ID user adalah "admin"
    // Update link to open edit_user.php with the user ID as a query parameter
        if ($row["id_user"] == "admin") {
            // For the admin user, show only the Edit button
            echo "<td><a class='btn btn-edit' href='edit_user.php?id=" . $row["id_user"] . "'>Edit</a></td>";
        } else {
            // For other users, show both Edit and Hapus (Delete) buttons
                // Update link to open edit_user.php with the user ID as a query parameter
                echo "<td>
                <a class='btn btn-edit' href='edit_user.php?id=" . $row["id_user"] . "'>Edit</a>
                <a class='btn btn-delete' href='proses_delete_user.php?id=" . $row["id_user"] . "' onclick='return confirmDelete()'>Hapus</a>
              </td>";
            }
        }
    }
    ?>
</tbody>
    </table>
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


<div class="content">
    <div class="card">
        <h2>Tambah User Baru</h2>
        <form method="POST" action="proses_user.php" onsubmit="return validateForm();" enctype="multipart/form-data">
            <div class="form-group">
                <label for="id_user">ID User:</label>
                <input type="text" class="form-control" id="id_user" name="id_user" required>
            </div>
            <div class="form-group">
            <label for="password">Password:</label>
            <div class="password-input">
                <input type="password" class="form-control" id="password" name="password" required>
                <i class="fas fa-eye" id="passwordToggle1"></i>
            </div>
            </div>
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password:</label>
                <div class="password-input">
                <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                <i class="fas fa-eye" id="passwordToggle2"></i>
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
            </div>
            <div class="form-group">
                <label for="prodi">Program Studi:</label>
                <select id="prodi" name="prodi" class="form-control" required>
                    <option value="" selected>Pilih Program Studi</option>
                    <option value="Teknik Informatika">Teknik Informatika</option>
                    <option value="Sistem Informasi">Sistem Informasi</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="semester">Semester:</label>
                <select id="semester" name="semester" class="form-control" required>
                    <option value="" selected>Pilih Semester</option>
                    <?php
                    for ($i = 1; $i <= 14; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
            <label for="no_hp">No. HP:</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" required>
            </div>
            <div class="form-group">
                <label for="pasfoto">Pasfoto:</label>
                <input type="file" class="form-control" id="pasfoto" name="pasfoto" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="foto_ktm">Foto KTM:</label>
                <input type="file" class="form-control" id="foto_ktm" name="foto_ktm" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="level">Level:</label>
                <select id="level" name="level" class="form-control">
                    <option value="3">User</option>
                    <option value="2">Kemahasiswaan</option>
                    <option value="1">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" name="submit">Tambah</button>
            </div>
        </form>
    </div>
    
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


    <script>
    // Function to allow only numeric input in the phone number field and limit to 13 digits
    document.getElementById("no_hp").addEventListener("input", function (e) {
        var value = e.target.value;
        var numericValue = value.replace(/\D/g, ""); // Remove non-numeric characters
        var maxLength = 13; // Maximum length for the phone number

        // Limit the input to the maximum length
        if (numericValue.length > maxLength) {
            numericValue = numericValue.slice(0, maxLength);
        }

        e.target.value = numericValue;
    });
</script>



<script>
    function validateForm() {
        var password = document.getElementById("password").value;
        var konfirmasiPassword = document.getElementById("konfirmasi_password").value;

        if (password !== konfirmasiPassword) {
            alert("Password tidak cocok!");
            return false;
        }

        return true;
    }
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

</script>
</body>
</html>