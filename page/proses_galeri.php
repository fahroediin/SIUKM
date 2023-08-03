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
if ($_SESSION['level'] == "2" || $_SESSION['level'] == "3") {
    // Jika level adalah "2" atau "3", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}

// Menandai halaman yang aktif
$active_page = 'galeri';

// Function to generate a random 4-digit number
function generateRandomNumber()
{
    return sprintf("%04d", mt_rand(1, 9999));
}

// Function to generate id_foto based on tgl and random number
function generateIdFoto($tgl)
{
    return $tgl . generateRandomNumber();
}

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai-nilai dari form
    $id_ukm = $_POST["id_ukm"];
    $nama_ukm = $_POST["nama_ukm"];
    $nama_kegiatan = $_POST["nama_kegiatan"];
    $tgl = $_POST["tgl"];

    // Generate id_kegiatan
    $id_kegiatan = $nama_kegiatan . $id_ukm . generateRandomNumber();

    // Generate id_foto
    $id_foto = generateIdFoto($tgl);

}
// Upload and save the photo
$targetDir = "../assets/images/kegiatan/";

if (isset($_FILES["foto_kegiatan"]["name"]) && $_FILES["foto_kegiatan"]["name"] != "") {
    $foto_kegiatan = basename($_FILES["foto_kegiatan"]["name"]);
    $targetFilePath = $targetDir . $foto_kegiatan;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Check if the image file is an actual image or a fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["foto_kegiatan"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($targetFilePath)) {
        $uploadOk = 0;
    }

    // Allow certain image file formats
    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        // Handle the error condition, for example:
        echo "Sorry, your file was not uploaded.";
        exit();
    } else {
        // If everything is OK, try to upload the file
        if (move_uploaded_file($_FILES["foto_kegiatan"]["tmp_name"], $targetFilePath)) {
            // File has been uploaded successfully, continue with the database insertion
            // Rest of the code...

         // Create the SQL query
    $sql = "INSERT INTO tab_galeri (id_foto, id_ukm, nama_ukm, id_kegiatan, nama_kegiatan, foto_kegiatan, tgl) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters
    $stmt->bind_param("sssssss", $id_foto, $id_ukm, $nama_ukm, $id_kegiatan, $nama_kegiatan, $foto_kegiatan, $tgl);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to the same page with a success parameter
        header("Location: proses_galeri.php?success=1");
        exit();
    } else {
        // Handle the error condition, for example:
        echo "Sorry, there was an error uploading your file.";
        exit();
    }
}
}
}
// Mendapatkan data ID UKM dan nama UKM dari tabel tab_ukm
$query_ukm = "SELECT id_ukm, nama_ukm FROM tab_ukm";
$result_ukm = mysqli_query($conn, $query_ukm);

// Mendapatkan data ID kegiatan dan nama kegiatan dari tabel tab_kegiatan
$query_kegiatan = "SELECT id_kegiatan, nama_kegiatan FROM tab_kegiatan";
$result_kegiatan = mysqli_query($conn, $query_kegiatan);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Proses Galeri - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    .card {
        /* Set max-width and margin auto to center the card on larger screens */
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>
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
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="card">
                <h1>Proses Galeri</h1>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="id_ukm">ID UKM:</label>
            <select id="id_ukm" name="id_ukm" required>
                <option value="" selected disabled>Pilih ID UKM</option>
                <?php
                // Membuat opsi combobox dari hasil query
                while ($row_ukm = mysqli_fetch_assoc($result_ukm)) {
                    echo "<option value='" . $row_ukm['id_ukm'] . "'>" . $row_ukm['id_ukm'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <label for="nama_ukm">Nama UKM:</label>
            <input type="text" id="nama_ukm" name="nama_ukm" readonly>
        </div>
        <div>
            <label for="id_kegiatan">ID Kegiatan:</label>
            <input type="text" id="id_kegiatan" name="id_kegiatan" readonly>
        </div>
        <div>
    <label for="nama_kegiatan">Nama Kegiatan:</label>
    <input type="text" id="nama_kegiatan" name="nama_kegiatan" required>
</div>
<div>
    <label for="foto_kegiatan">Foto Kegiatan:</label>
    <input type="file" id="foto_kegiatan" name="foto_kegiatan" accept="image/*" required>
</div>
        <div>
            <label for="tgl">Tanggal:</label>
            <input type="date" id="tgl" name="tgl" required>
        </div>
        <div>
            <button type="submit">Simpan</button>
            </form>
            </div>
        </div>
    </div>

    <!-- Add your JavaScript code here to populate the nama_ukm field -->
    <script>
        const idUkmSelect = document.getElementById("id_ukm");
        const namaUkmField = document.getElementById("nama_ukm");

        idUkmSelect.addEventListener("change", function() {
            const selectedOption = idUkmSelect.options[idUkmSelect.selectedIndex];
            const namaUkm = selectedOption.text;
            namaUkmField.value = namaUkm;
        });
    </script>
</body>

</html>
