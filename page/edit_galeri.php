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


// Memeriksa apakah tombol logout diklik
if (isset($_GET['logout'])) {
    // Memanggil fungsi logout
    logout();
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

// Check if the id_foto query parameter is set in the URL
if (isset($_GET['id_foto'])) {
    // Retrieve the id_foto from the query parameter
    $id_foto = $_GET['id_foto'];

    // Fetch the corresponding data from the database based on the id_foto
    $query_single_galeri = "SELECT id_foto, id_kegiatan, id_ukm, nama_ukm, nama_kegiatan, foto_kegiatan, tgl FROM tab_galeri WHERE id_foto = '$id_foto'";
    $result_single_galeri = mysqli_query($conn, $query_single_galeri);

    // Check if a record is found
    if ($result_single_galeri && mysqli_num_rows($result_single_galeri) > 0) {
        // Fetch the data from the result
        $row_single_galeri = mysqli_fetch_assoc($result_single_galeri);
        
        // Populate the fields with the fetched data
        updateFields(
            $row_single_galeri['id_foto'],
            $row_single_galeri['id_ukm'],
            $row_single_galeri['nama_ukm'],
            $row_single_galeri['id_kegiatan'],
            $row_single_galeri['nama_kegiatan'],
            $row_single_galeri['tgl']
        );
    } else {
        // Handle the case when the id_foto doesn't match any records (e.g., redirect to an error page)
        echo "Error: Data not found.";
        exit();
    }
}
function updateFields($id_foto, $id_ukm, $nama_ukm, $id_kegiatan, $nama_kegiatan, $tgl) {
    // Populate the form fields with the fetched data
    echo "
        <script>
            updateFields('$id_foto', '$id_ukm', '$nama_ukm', '$id_kegiatan', '$nama_kegiatan', '$tgl');
        </script>
    ";
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $id_foto = mysqli_real_escape_string($conn, $_POST["id_foto"]);
    $id_ukm = mysqli_real_escape_string($conn, $_POST["id_ukm"]);
    $nama_ukm = mysqli_real_escape_string($conn, $_POST["nama_ukm"]);
    $id_kegiatan = mysqli_real_escape_string($conn, $_POST["id_kegiatan"]);
    $nama_kegiatan = mysqli_real_escape_string($conn, $_POST["nama_kegiatan"]);
    $tgl = mysqli_real_escape_string($conn, $_POST["tgl"]);

    // Check if the file size exceeds the maximum limit
    if ($_FILES["foto_kegiatan"]["size"] > $maxFileSize) {
        // Handle the error condition, for example:
        echo "Sorry, your file exceeds the maximum allowed size (5MB).";
        exit();
    }

    // Handle file upload (if a new file is selected)
    if (isset($_FILES["foto_kegiatan"]["name"]) && $_FILES["foto_kegiatan"]["name"] != "") {
        // Existing image filename (to be used in updating the record later)
        $existingFilename = "";

        // Check if there's an existing record with the given id_foto
        $query_existing_foto = "SELECT foto_kegiatan FROM tab_galeri WHERE id_foto = '$id_foto'";
        $result_existing_foto = mysqli_query($conn, $query_existing_foto);
        if ($result_existing_foto && mysqli_num_rows($result_existing_foto) > 0) {
            $row_existing_foto = mysqli_fetch_assoc($result_existing_foto);
            $existingFilename = $row_existing_foto["foto_kegiatan"];
        }

        // Generate a unique filename based on the nama_kegiatan and the extension
        $foto_kegiatan = $_FILES["foto_kegiatan"]["name"];
        $foto_kegiatan_extension = strtolower(pathinfo($foto_kegiatan, PATHINFO_EXTENSION));
        $nama_kegiatan = $_POST["nama_kegiatan"];
        $uniqueFilename = generateUniqueFilename($nama_kegiatan, $foto_kegiatan_extension);
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

        // Delete the old image file if it exists and the new image is uploaded
        if (!empty($existingFilename) && file_exists($targetDir . $existingFilename)) {
            unlink($targetDir . $existingFilename);
        }
    } else {
        // No new file is uploaded, keep the existing filename (no need to update the image)
        $uniqueFilename = $existingFilename;
    }

    // Prepare the SQL query to update data in the tab_galeri table
    $sql = "UPDATE tab_galeri SET id_ukm = ?, nama_ukm = ?, id_kegiatan = ?, nama_kegiatan = ?, foto_kegiatan = ?, tgl = ? WHERE id_foto = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters
    $stmt->bind_param("sssssss", $id_ukm, $nama_ukm, $id_kegiatan, $nama_kegiatan, $uniqueFilename, $tgl, $id_foto);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to the same page with a success parameter
        header("Location: proses_galeri.php?success=1");
        exit();
    } else {
        // Handle the error condition, for example:
        echo "Sorry, there was an error updating the record.";
        exit();
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
    <title>Edit Galeri - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
           .sidebar {
        text-align: center; /* Center the contents horizontally */
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
    <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
    <h2><i>Galeri</i></h2>
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
    <a href="lpj_upload.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'lpj') echo 'active'; ?>">LPJ</a>
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
<div class="content">
        <div class="row justify-content-center">
            <!-- Wrap the form with a card component -->
            <div class="card">
    
                <div class="card-body">
                    <h2>Edit Data Galeri</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
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
    </div>

  <!-- Add your JavaScript code here to populate the nama_ukm field -->
<script>
 // Update the fields with data based on the selected row
 function updateFields(id_foto, id_ukm, nama_ukm, id_kegiatan, nama_kegiatan, tgl) {
        const idUkmSelect = document.getElementById("id_ukm");
        const namaUkmField = document.getElementById("nama_ukm");
        const idKegiatanField = document.getElementById("id_kegiatan");
        const namaKegiatanField = document.getElementById("nama_kegiatan");
        const tglField = document.getElementById("tgl");

        idUkmSelect.value = id_ukm;
        namaUkmField.value = nama_ukm;
        idKegiatanField.value = id_kegiatan;
        namaKegiatanField.value = nama_kegiatan;
        tglField.value = tgl;
    }

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
    // Fungsi untuk logout
    function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
</script>

</body>
</html>