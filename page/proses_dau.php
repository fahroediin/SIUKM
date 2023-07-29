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
$query = "SELECT id_anggota, id_user, nama_depan, nama_belakang, nim, no_hp, email, prodi, semester, pasfoto, id_ukm, nama_ukm, sjk_bergabung FROM tab_dau";
$result = mysqli_query($conn, $query);

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai-nilai dari form
    $id_anggota = $_POST["id_anggota"];
    $id_user = $_POST["id_user"];
    $nama_depan = $_POST["nama_depan"];
    $nama_belakang = $_POST["nama_belakang"];
    $nim = $_POST["nim"];
    $no_hp = $_POST["no_hp"];
    $email = $_POST["email"];
    $prodi = $_POST["prodi"];
    $semester = $_POST["semester"];
    $id_ukm = $_POST["id_ukm"];
    $sjk_bergabung = $_POST["sjk_bergabung"];

    // Simpan data ke database
    $sql = "INSERT INTO tab_dau (id_anggota, id_user, nama_depan, nama_belakang, nim, no_hp, email, prodi, semester, id_ukm, sjk_bergabung) 
            VALUES ('$id_anggota', '$id_user', '$nama_depan', '$nama_belakang', '$nim', '$no_hp', '$email', '$prodi', '$semester', '$id_ukm', '$sjk_bergabung')";
    
    if (mysqli_query($conn, $sql)) {
        // Redirect ke halaman data anggota setelah penyimpanan berhasil
        header("Location: data_anggota_ukm.php?success=1");
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

    <h1>Data Anggota UKM</h1>
    <table>
        <thead>
            <tr>
                <th>ID Anggota</th>
                <th>ID User</th>
                <th>Nama Depan</th>
                <th>Nama Belakang</th>
                <th>NIM</th>
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
                echo "<td>" . $row['nim'] . "</td>";
                echo "<td>" . $row['no_hp'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['prodi'] . "</td>";
                echo "<td>" . $row['semester'] . "</td>";
                echo "<td><img src='" . $row['pasfoto'] . "' alt='Pasfoto'></td>";
                echo "<td>" . $row['id_ukm'] . "</td>";
                echo "<td>" . $row['nama_ukm'] . "</td>";
                echo "<td>" . $row['sjk_bergabung'] . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    
    <h1>Data Anggota UKM</h1>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <!-- Tambahkan input fields untuk data anggota -->
        <label for="id_anggota">ID Anggota:</label>
        <input type="text" name="id_anggota" required>

        <label for="id_user">ID User:</label>
        <input type="text" name="id_user" required>

        <label for="nama_depan">Nama Depan:</label>
        <input type="text" name="nama_depan" required>

        <label for="nama_belakang">Nama Belakang:</label>
        <input type="text" name="nama_belakang" required>

        <label for="nim">NIM:</label>
        <input type="text" name="nim" required>

        <label for="no_hp">No. HP:</label>
        <input type="text" name="no_hp" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="prodi">Program Studi:</label>
        <input type="text" name="prodi" required>

        <label for="semester">Semester:</label>
        <input type="text" name="semester" required>

        <label for="id_ukm">ID UKM:</label>
        <input type="text" name="id_ukm" required>

        <label for="sjk_bergabung">SJK Bergabung:</label>
        <input type="text" name="sjk_bergabung" required>

        <button type="submit">Simpan Data</button>
    </form>

</body>
</html>
