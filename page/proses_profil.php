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

// Menandai halaman yang aktif
$active_page = 'proses_beranda';

// Memeriksa level pengguna
if ($_SESSION['level'] == "3" || $_SESSION['level'] == "2") {
    // Jika level adalah "3" atau "2", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}

// Function to generate a unique filename for image uploads
function generateUniqueFilename($filename, $extension) {
    $filename = preg_replace("/[^a-zA-Z0-9]/", "", $filename); // Remove non-alphanumeric characters
    $filename = str_replace(" ", "", $filename); // Remove spaces
    return $filename . "." . $extension;
}

// Update the uploadImage function
function uploadImage($fileInputName, $targetDir) {
    if (isset($_FILES[$fileInputName]["name"]) && $_FILES[$fileInputName]["name"] != "") {
        $imageFileName = $_FILES[$fileInputName]["name"];
        $imageFileExtension = strtolower(pathinfo($imageFileName, PATHINFO_EXTENSION));

        // Generate a unique filename for the image
        $uniqueFilename = generateUniqueFilename('logo_siukm', $imageFileExtension);
        $targetFilePath = $targetDir . $uniqueFilename;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFilePath)) {
            return $uniqueFilename;
        } else {
            return false; // File upload failed
        }
    }
    return null; // File not uploaded
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $deskripsi = mysqli_real_escape_string($conn, $_POST["deskripsi"]);
    $visi = mysqli_real_escape_string($conn, $_POST["visi"]);
    $misi = mysqli_real_escape_string($conn, $_POST["misi"]);

    // Define the target directory for image uploads
    $targetDir = "../assets/images/logo/";

    $logo_siukm = uploadImage("logo_siukm", $targetDir);
    
    // Prepare the SQL query to insert/update data into tab_profil table
    if ($_POST["action"] === "edit") {
        // Edit existing data in tab_profil
        $sql = "UPDATE tab_profil SET deskripsi = ?, visi = ?, misi = ?, logo_siukm = ?";
    } else {
        // Insert new data into tab_profil
        $sql = "INSERT INTO tab_profil (deskripsi, visi, misi, logo_siukm) VALUES (?, ?, ?, ?)";
    }

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters
    $stmt->bind_param("ssss", $deskripsi, $visi, $misi, $logo_siukm);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to the same page with a success parameter
        header("Location: proses_beranda.php?success=1");
        exit();
    } else {
        // Handle the error condition, for example:
        echo "Sorry, there was an error saving the data.";
        exit();
    }
}

// Fetch existing data from the tab_profil table
$query_profil = "SELECT * FROM tab_profil";
$result_profil = mysqli_query($conn, $query_profil);
$row_profil = mysqli_fetch_assoc($result_profil);

?>

<!DOCTYPE html>
<html>

<head>
<title>Manajemen Beranda - SIUKM</title>
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
       .preview-image {
    max-width: 100%;
    max-height: 200px;
    margin-top: 10px;
}

   </style>

 <!-- Sidebar -->
 <div class="sidebar">
    <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
    <h2><i>Beranda</i></h2>
    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
            <a href="proses_beranda.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_beranda') echo 'active'; ?>">Beranda</a>
            <a href="proses_struktur.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'struktur') echo 'active'; ?>">Pengurus</a>
    <a href="proses_dau.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'ukm') echo 'active'; ?>">Data UKM</a>
    <a href="proses_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="proses_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
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
            <h2 style="text-align: center;">Data Profil</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" class="form">
                <input type="hidden" name="action" value="<?php echo isset($row_profil) ? 'edit' : 'add'; ?>">
                <div class="form-group">
                    <label for="deskripsi">Deskripsi:</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control"><?php echo isset($row_profil) ? $row_profil['deskripsi'] : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="visi">Visi:</label>
                    <textarea id="visi" name="visi" rows="4" class="form-control"><?php echo isset($row_profil) ? $row_profil['visi'] : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="misi">Misi:</label>
                    <textarea id="misi" name="misi" rows="4" class="form-control"><?php echo isset($row_profil) ? $row_profil['misi'] : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="logo_siukm">Logo SIUKM:</label>
                    <input type="file" id="logo_siukm" name="logo_siukm" accept="image/*" class="form-control-file">
                    <div class="image-preview" id="logo_siukm-preview">
                        <?php if(isset($row_profil['logo_siukm'])): ?>
                            <img src="../assets/images/logo/<?php echo $row_profil['logo_siukm']; ?>" class="preview-image" alt="Logo SIUKM">
                        <?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
    <script>
        // ... (Script section remains unchanged) ...
    </script>
</div>
<!-- ... (Sidebar and other parts remain unchanged) ... -->
</body>
</html>
