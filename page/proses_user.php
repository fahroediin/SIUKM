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
// Memeriksa apakah form tambah user telah di-submit
if (isset($_POST['submit'])) {
    // Mengambil data dari form
    $id_user = $_POST['id_user'];
    $password = $_POST['password'];
    $nama_depan = $_POST['nama_depan'];
    $nama_belakang = $_POST['nama_belakang'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $level = $_POST['level'];

    // Menyimpan data ke database
    $sql = "INSERT INTO tab_user (id_user, password, nama_depan, nama_belakang, email, no_hp, level) VALUES ('$id_user', '$password', '$nama_depan', '$nama_belakang', '$email', '$no_hp', '$level')";
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
</style>
<body>
<div class="sidebar">
    <h2>Manajemen Pengguna</h2>
    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
    <a href="proses_struktur.php" class="btn btn-primary <?php if($active_page == 'struktur') echo 'active'; ?>">Kepengurusan</a>
    <a href="proses_prestasi.php" class="btn btn-primary <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_visimisi.php" class="btn btn-primary <?php if($active_page == 'visi_misi') echo 'active'; ?>">Data UKM</a>
    <a href="galeri.php" class="btn btn-primary <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="kegiatan.php" class="btn btn-primary <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
</div>


<!-- Data User -->
<div class="content">
    <h2>Daftar User</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID User</th>
                <th>Nama Depan</th>
                <th>Nama Belakang</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Level</th>
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
            echo "<td>" . $row["nama_depan"] . "</td>";
            echo "<td>" . $row["nama_belakang"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["no_hp"] . "</td>";
            echo "<td>" . $row["level"] . "</td>"; // Display the level's name based on the value in the $userLevels array

            // Menambahkan kondisi jika ID user adalah "admin"
            if ($row["id_user"] == "admin") {
                echo "<td>Tidak dapat dihapus</td>";
            } else {
                // Update link to open edit_user.php with the user ID as a query parameter
                echo "<td><a href='edit_user.php?id=" . $row["id_user"] . "'>Edit</a> | <a href='proses_delete_user.php?id=" . $row["id_user"] . "' onclick='return confirmDelete()'>Hapus</a></td>";

                echo "</tr>";
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
        <form method="POST" action="proses_user.php" onsubmit="return validateForm();">
            <div class="form-group">
                <label for="id_user">ID User:</label>
                <input type="text" class="form-control" id="id_user" name="id_user" required>
            </div>
            <div class="form-group">
            <label for="password">Password:</label>
            <div class="password-input">
                <input type="password" class="form-control" id="password" name="password" required>
                <i class="fas fa-eye" id="passwordToggle"></i>
            </div>
            </div>
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password:</label>
                <div class="password-input">
                <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                <i class="fas fa-eye" id="passwordToggle"></i>
            </div>
            <div class="form-group">
                <label for="nama_depan">Nama Depan:</label>
                <input type="text" class="form-control" id="nama_depan" name="nama_depan" required>
            </div>
            <div class="form-group">
                <label for="nama_belakang">Nama Belakang:</label>
                <input type="text" class="form-control" id="nama_belakang" name="nama_belakang">
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
    const passwordInput = document.getElementById("password");
    const passwordToggle = document.getElementById("passwordToggle");

    passwordToggle.addEventListener("click", function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordToggle.classList.remove("fa-eye");
            passwordToggle.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            passwordToggle.classList.remove("fa-eye-slash");
            passwordToggle.classList.add("fa-eye");
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