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

// Menandai halaman yang aktif
$active_page = 'data_anggota_ukm';

// Memperoleh data anggota UKM dari tabel tab_dau
$query = "SELECT id_anggota, id_user, nama_depan, nama_belakang, no_hp, email, prodi, semester, pasfoto, id_ukm, nama_ukm, sjk_bergabung FROM tab_dau";
$result = mysqli_query($conn, $query);

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai-nilai dari form
    $id_anggota = $_POST["id_anggota"];
    $id_user = $_POST["id_user"];
    $nama_depan = $_POST["nama_depan"];
    $nama_belakang = $_POST["nama_belakang"];
    $no_hp = $_POST["no_hp"];
    $email = $_POST["email"];
    $prodi = $_POST["prodi"];
    $semester = $_POST["semester"];
    $id_ukm = $_POST["id_ukm"];
    $sjk_bergabung = $_POST["sjk_bergabung"];

    // Simpan data ke database
    $sql = "INSERT INTO tab_dau (id_anggota, id_user, nama_depan, nama_belakang, no_hp, email, prodi, semester, id_ukm, sjk_bergabung) 
            VALUES ('$id_anggota', '$id_user', '$nama_depan', '$nama_belakang', '$no_hp', '$email', '$prodi', '$semester', '$id_ukm', '$sjk_bergabung')";

    if (mysqli_query($conn, $sql)) {
        // Redirect ke halaman data anggota setelah penyimpanan berhasil
        header("Location: proses_dau.php");
        exit();
    } else {
        // Jika terjadi kesalahan saat menyimpan data
        echo "Error: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota UKM - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
</head>
<style>
        /* Tambahkan gaya CSS berikut untuk mengatur layout sidebar dan konten */
        .container {
            display: flex;
            flex-wrap: wrap;
        }

        .sidebar {
            flex: 0 0 20%; /* Lebar sidebar 20% dari container */
        }

        .content {
            flex: 0 0 80%; /* Lebar konten 80% dari container */

        }

        /* Gaya CSS tambahan untuk mengatur tampilan tabel dan form */
        .table {
            width: 100%;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .form-row .form-control {
            flex: 1;
            margin-right: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }
    </style>

<body>
 <!-- Sidebar -->
 <div class="sidebar">
    <h2>Manajemen Data UKM</h2>
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
    <h2>Data Anggota UKM</h2>
    <div class="form-group">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID Anggota</th>
                <th>ID User</th>
                <th>Nama Depan</th>
                <th>Nama Belakang</th>
                <th>No. HP</th>
                <th>Email</th>
                <th>Program Studi</th>
                <th>Semester</th>
                <th>Pasfoto</th>
                <th>ID UKM</th>
                <th>Nama UKM</th>
                <th>SJK Bergabung</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop melalui hasil query untuk menampilkan data anggota UKM
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id_anggota'] . "</td>";
                echo "<td>" . $row['id_user'] . "</td>";
                echo "<td>" . $row['nama_depan'] . "</td>";
                echo "<td>" . $row['nama_belakang'] . "</td>";
                echo "<td>" . $row['no_hp'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['prodi'] . "</td>";
                echo "<td>" . $row['semester'] . "</td>";
                echo "<td><img src='" . $row['pasfoto'] . "' alt='Pasfoto' class='img-thumbnail' style='max-height: 100px;'></td>";
                echo "<td>" . $row['id_ukm'] . "</td>";
                echo "<td>" . $row['nama_ukm'] . "</td>";
                echo "<td>" . $row['sjk_bergabung'] . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
        </div>
    
            <div class="card">
            <h2>Tambah Data Anggota UKM</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-row">
                    <div class="col-md-6">
                        <label for="id_anggota">ID Anggota:</label>
                        <input type="text" class="form-control" name="id_anggota" required>
                    </div>
                    <div class="col-md-6">
                    <label for="id_user">ID User:</label>
                    <select class="form-control" name="id_user" id="id_user_dropdown" required>
    <option value="">------------Pilih ID User------------</option>
    <?php
    // Fetch data from the tab_user table and populate the dropdown options
    $userQuery = "SELECT id_user FROM tab_user"; // Select only the id_user column
    $userResult = mysqli_query($conn, $userQuery);

    while ($userRow = mysqli_fetch_assoc($userResult)) {
        // Use a regular expression to check if the id_user contains only digits (numbers)
        if (preg_match('/^\d+$/', $userRow['id_user'])) {
            echo '<option value="' . $userRow['id_user'] . '">' . $userRow['id_user'] . '</option>';
        }
    }
    ?>
</select>

<script>
    // Event listener for the dropdown (id_user)
    document.getElementById("id_user_dropdown").addEventListener("change", function () {
        var selectedUserId = this.value;
        var namaDepanField = document.getElementsByName("nama_depan")[0];
        var namaBelakangField = document.getElementsByName("nama_belakang")[0];
        var noHpField = document.getElementsByName("no_hp")[0];
        var emailField = document.getElementsByName("email")[0];

        if (selectedUserId === "") {
            // Reset the text fields
            namaDepanField.value = "";
            namaBelakangField.value = "";
            noHpField.value = "";
            emailField.value = "";

            // Disable the text fields
            namaDepanField.disabled = true;
            namaBelakangField.disabled = true;
            noHpField.disabled = true;
            emailField.disabled = true;
        } else {
        }
    });
</script>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            // Function to fetch user data based on the selected id_user
            function fetchUserData(userId) {
                $.ajax({
                    type: "POST",
                    url: "get_user_data.php", // Create a separate PHP file to handle AJAX request and database query
                    data: { id_user: userId },
                    dataType: "json",
                    success: function (data) {
                        // Update the text fields with the fetched data
                        $("input[name='nama_depan']").val(data.nama_depan);
                        $("input[name='nama_belakang']").val(data.nama_belakang);
                        $("input[name='no_hp']").val(data.no_hp);
                        $("input[name='email']").val(data.email);
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    }
                });
            }

            // Event listener for the dropdown (id_user)
            $("select[name='id_user']").on("change", function () {
                var selectedUserId = $(this).val();
                fetchUserData(selectedUserId);
            });
        </script>
        </div>

        <div class="form-row">
        <div class="col-md-6">
            <label for="nama_depan">Nama Depan:</label>
            <input type="text" class="form-control" name="nama_depan" required readonly>
        </div>
        <div class="col-md-6">
            <label for="nama_belakang">Nama Belakang:</label>
            <input type="text" class="form-control" name="nama_belakang" readonly>
        </div>
        </div>


        <div class="form-row">
            <div class="col-md-6">
                <label for="no_hp">No. HP:</label>
                <input type="text" class="form-control" name="no_hp" required readonly>
            </div>
            <div class="col-md-6">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" required readonly>
            </div>
        </div>


        <div class="form-row">
        <div class="col-md-6">
            <label for="prodi">Program Studi:</label>
            <select class="form-control" name="prodi" required>
                <option value="">Pilih Program Studi</option>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="semester">Semester:</label>
            <select class="form-control" name="semester" required>
                <option value="">Pilih Semester</option>
                <?php
                // Generate options for semester from 1 to 14
                for ($i = 1; $i <= 14; $i++) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
                ?>
            </select>
            </div>
        </div>


        <div class="form-row">
        <div class="col-md-6">
        <label for="id_ukm">ID UKM:</label>
        <select class="form-control" name="id_ukm" required>
            <option value="">Pilih ID UKM</option>
        <?php
        // Fetch data from the tab_ukm table and populate the dropdown options
        $ukmQuery = "SELECT id_ukm FROM tab_ukm";
        $ukmResult = mysqli_query($conn, $ukmQuery);

        while ($ukmRow = mysqli_fetch_assoc($ukmResult)) {
            echo '<option value="' . $ukmRow['id_ukm'] . '">' . $ukmRow['id_ukm'] . '</option>';
        }
        ?>
    </select>
</div>

            <div class="col-md-6">
        <label for="sjk_bergabung">SJK Bergabung:</label>
        <input type="datetime-local" class="form-control" name="sjk_bergabung" required>
    </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Data</button>
    </form>
</div>

</body>
</html>
