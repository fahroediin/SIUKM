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

function generateUniqueFilename($filename, $extension) {
    $filename = preg_replace("/[^a-zA-Z0-9]/", "", $filename); // Remove non-alphanumeric characters
    $filename = str_replace(" ", "", $filename); // Remove spaces
    return $filename . "." . $extension;
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
    // Convert the date to a DateTime object
    $date = new DateTime($tgl);
    
    // Extract the year, month, and day from the DateTime object
    $year = $date->format('Y');
    $month = $date->format('m');
    $day = $date->format('d');

    // Generate a unique random number
    $randomNumber = sprintf("%04d", mt_rand(1, 9999));

    // Concatenate the year, month, day, and random number without hyphens
    return $year . $month . $day . $randomNumber;
}

// Function to generate id_kegiatan based on tgl and random number
function generateIdKegiatan($tgl)
{
    // Convert the date to a DateTime object
    $date = new DateTime($tgl);
    
    // Extract the year, month, and day from the DateTime object
    $year = $date->format('Y');
    $month = $date->format('m');
    $day = $date->format('d');

    // Generate a unique random number
    $randomNumber = sprintf("%04d", mt_rand(1, 9999));

    // Concatenate the year, month, day, and random number without hyphens
    return $year . $month . $day . $randomNumber;
}


// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $id_ukm = mysqli_real_escape_string($conn, $_POST["id_ukm"]);
    $nama_ukm = mysqli_real_escape_string($conn, $_POST["nama_ukm"]);
    $nama_kegiatan = mysqli_real_escape_string($conn, $_POST["nama_kegiatan"]);
    $tgl = mysqli_real_escape_string($conn, $_POST["tgl"]);
    // Define the maximum file size in bytes (5MB)
    $maxFileSize = 5 * 1024 * 1024;

    // Check if the file size exceeds the maximum limit
    if ($_FILES["foto_kegiatan"]["size"] > $maxFileSize) {
        // Handle the error condition, for example:
        echo "Sorry, your file exceeds the maximum allowed size (5MB).";
        exit();
    }

    // Handle file upload
    $targetDir = "../assets/images/kegiatan/";

    if (isset($_FILES["foto_kegiatan"]["name"]) && $_FILES["foto_kegiatan"]["name"] != "") {
        $foto_kegiatan = $_FILES["foto_kegiatan"]["name"];
        $foto_kegiatan_extension = strtolower(pathinfo($foto_kegiatan, PATHINFO_EXTENSION));
        $nama_kegiatan = $_POST["nama_kegiatan"];
    
        // Generate a unique filename based on the id_foto and the extension
        $id_foto = generateIdFoto($tgl);
        $uniqueFilename = $id_foto . "." . $foto_kegiatan_extension;
    
        $targetFilePath = $targetDir . $uniqueFilename;

        // Check if the image file is an actual image or a fake image
        $check = getimagesize($_FILES["foto_kegiatan"]["tmp_name"]);
        if ($check === false) {
            // Handle the error condition, for example:
            echo "Sorry, your file is not a valid image.";
            exit();
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES["foto_kegiatan"]["tmp_name"], $targetFilePath)) {
            // Handle the error condition, for example:
            echo "Sorry, there was an error uploading your file.";
            exit();
        }

                // Generate a unique id_foto based on tgl and random number
                $id_foto = generateIdFoto($tgl);

            
                // Generate a unique id_kegiatan based on tgl and random number
                $id_kegiatan = generateIdKegiatan($tgl);

        // Prepare the SQL query to insert data into tab_galeri table
    $sql = "INSERT INTO tab_galeri (id_foto, id_ukm, nama_ukm, id_kegiatan, nama_kegiatan, foto_kegiatan, tgl) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters
    $stmt->bind_param("sssssss", $id_foto, $id_ukm, $nama_ukm, $id_kegiatan, $nama_kegiatan, $uniqueFilename, $tgl);

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
// Mendapatkan data ID UKM dan nama UKM dari tabel tab_ukm
$query_ukm = "SELECT id_ukm, nama_ukm FROM tab_ukm";
$result_ukm = mysqli_query($conn, $query_ukm);

// Mendapatkan data ID kegiatan dan nama kegiatan dari tabel tab_kegiatan
$query_kegiatan = "SELECT id_kegiatan, nama_kegiatan FROM tab_kegiatan";
$result_kegiatan = mysqli_query($conn, $query_kegiatan);

// Fetch data from the tab_galeri table
$query_galeri = "SELECT id_foto, id_kegiatan, id_ukm, nama_ukm, nama_kegiatan, foto_kegiatan, tgl FROM tab_galeri";
$result_galeri = mysqli_query($conn, $query_galeri);

?>

<!DOCTYPE html>
<html>

<head>
<title>Manajemen Galeri - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
       

        .card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .card-body div {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        select,
        input[type="text"],
        input[type="date"],
        button {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        select {
            width: 100%;
        }

        button {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

    </style>
 <!-- Sidebar -->
 <div class="sidebar">
    <h2>Manajemen Galeri</h2>
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
<body>
<div class="content">
    <h2>Data Galeri</h2>
    <table class="table">
        <thead>
            <tr>
            <th>ID Foto</th>
            <th>ID Kegiatan</th>
            <th>ID UKM</th>
            <th>Nama UKM</th>
            <th>Nama Kegiatan</th>
            <th>Foto Kegiatan</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
            <?php
                    // Define Indonesian month names
            $indonesianMonths = array(
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                'Agustus', 'September', 'Oktober', 'November', 'Desember'
            );
            while ($row_galeri = mysqli_fetch_assoc($result_galeri)) {
                // Output table rows
                echo "<tr>";
                echo "<td>" . $row_galeri['id_foto'] . "</td>";
                echo "<td>" . $row_galeri['id_kegiatan'] . "</td>";
                echo "<td>" . $row_galeri['id_ukm'] . "</td>";
                echo "<td>" . $row_galeri['nama_ukm'] . "</td>";
                echo "<td>" . $row_galeri['nama_kegiatan'] . "</td>";
                echo "<td><img src='../assets/images/kegiatan/" . $row_galeri['foto_kegiatan'] . "' width='100'></td>";
                echo "<td>" . date('d', strtotime($row_galeri['tgl'])) . " " . $indonesianMonths[intval(date('m', strtotime($row_galeri['tgl']))) - 1] . " " . date('Y', strtotime($row_galeri['tgl'])) . "</td>";
                echo "<td>
                        <a href='edit_galeri.php?id_foto=" . $row_galeri['id_foto'] . "'>Edit</a>
                        <a href='delete_galeri.php?id_foto=" . $row_galeri['id_foto'] . "' onclick='return confirmDelete(\"" . $row_galeri['nama_kegiatan'] . "\");'>Hapus</a>
                    </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
</table>
<div class="container">
            <div class="row justify-content-center">
                <!-- Wrap the form with a card component -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                            enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="id_ukm">ID UKM:</label>
                                <select id="id_ukm" name="id_ukm" class="form-control" required>
                                    <option value="" selected disabled>Pilih ID UKM</option>
                                    <?php
                                        // Membuat opsi combobox dari hasil query
                                        while ($row_ukm = mysqli_fetch_assoc($result_ukm)) {
                                            echo "<option value='" . $row_ukm['id_ukm'] . "'>" . $row_ukm['id_ukm'] . "</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nama_ukm">Nama UKM:</label>
                                <input type="text" id="nama_ukm" name="nama_ukm" class="form-control" readonly>
                            </div>
                           
                                <input type="hidden" id="id_kegiatan" name="id_kegiatan" class="form-control" readonly>
                          
                            <div class="form-group">
                                <label for="nama_kegiatan">Nama Kegiatan:</label>
                                <input type="text" id="nama_kegiatan" name="nama_kegiatan" class="form-control"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="foto_kegiatan">Foto Kegiatan:</label>
                                <input type="file" id="foto_kegiatan" name="foto_kegiatan" accept="image/*" required
                                    class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="tgl">Tanggal:</label>
                                <input type="date" id="tgl" name="tgl" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <!-- Add your JavaScript code here to populate the nama_ukm field -->
<script>
    const idUkmSelect = document.getElementById("id_ukm");
    const namaUkmField = document.getElementById("nama_ukm");

    idUkmSelect.addEventListener("change", function() {
        const selectedOption = idUkmSelect.options[idUkmSelect.selectedIndex];
        const idUkm = selectedOption.value;
        if (idUkm) {
            // Make an AJAX request to get the nama_ukm based on the selected id_ukm
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `get_nama_ukm.php?id_ukm=${idUkm}`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    // Update the nama_ukm field with the fetched value
                    namaUkmField.value = xhr.responseText;
                }
            };
            xhr.send();
        } else {
            // If no id_ukm is selected, reset the nama_ukm field
            namaUkmField.value = "";
        }
    });
</script>
<script>
    function confirmDelete(namaKegiatan) {
        // Show the confirmation alert and ask for user confirmation
        const confirmMessage = `Apakah yakin akan menghapus foto kegiatan "${namaKegiatan}"?`;
        return confirm(confirmMessage);
    }
</script>
</body>
</html>