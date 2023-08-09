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
function uploadImage($fileInputName, $targetDir, $carouselNumber) {
    if (isset($_FILES[$fileInputName]["name"]) && $_FILES[$fileInputName]["name"] != "") {
        $imageFileName = $_FILES[$fileInputName]["name"];
        $imageFileExtension = strtolower(pathinfo($imageFileName, PATHINFO_EXTENSION));

        // Generate a unique filename for the image based on carousel number
        $uniqueFilename = "carousel" . $carouselNumber . "." . $imageFileExtension;
        $targetFilePath = $targetDir . $uniqueFilename;

          // Delete the existing file (if any)
          $existingFilePath = $targetDir . $uniqueFilename;
          if (file_exists($existingFilePath)) {
              unlink($existingFilePath);
          }
  
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
    $informasi = mysqli_real_escape_string($conn, $_POST["informasi"]);

    // Define the target directory for image uploads
    $targetDir = "../assets/images/carousel/";

    // Upload and replace images
    $foto1 = uploadImage("foto1", $targetDir, 1);
    $foto2 = uploadImage("foto2", $targetDir, 2);
    $foto3 = uploadImage("foto3", $targetDir, 3);

    // Prepare the SQL query to insert/update data into tab_beranda table
    if ($_POST["action"] === "edit") {
        // Edit existing data in tab_beranda
        $sql = "UPDATE tab_beranda SET informasi = ?, foto1 = ?, foto2 = ?, foto3 = ?";
    } else {
        // Insert new data into tab_beranda
        $sql = "INSERT INTO tab_beranda (informasi, foto1, foto2, foto3) VALUES (?, ?, ?, ?)";
    }

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters
    $stmt->bind_param("ssss", $informasi, $foto1, $foto2, $foto3);

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

// Fetch existing data from the tab_beranda table
$query_beranda = "SELECT * FROM tab_beranda";
$result_beranda = mysqli_query($conn, $query_beranda);
$row_beranda = mysqli_fetch_assoc($result_beranda);

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
.is-invalid {
        border-color: #dc3545;
    }
   </style>

 <!-- Sidebar -->
 <div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
    <h2><i>Beranda</i></h2>
    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
            <a href="proses_beranda.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_beranda') echo 'active'; ?>">Beranda</a>
            <a href="proses_profil.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_profil') echo 'active'; ?>">Profil</a>
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
                <h2 style="text-align: center;">Data Beranda</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" class="form">
        <input type="hidden" name="action" value="<?php echo isset($row_beranda) ? 'edit' : 'add'; ?>">
        <div class="form-group">
    <label for="informasi">Informasi:</label>
    <textarea id="informasi" name="informasi" rows="4" class="form-control"><?php echo isset($row_beranda) ? $row_beranda['informasi'] : ''; ?></textarea>
    <div id="validation-message" class="invalid-feedback"></div>
</div>

<script>
    const informasiTextarea = document.getElementById('informasi');
    const validationMessage = document.getElementById('validation-message');

    informasiTextarea.addEventListener('input', function () {
        const informasiValue = informasiTextarea.value;
        if (informasiValue.length < 500) {
            validationMessage.textContent = 'Minimal 500 karakter diperlukan.';
            informasiTextarea.classList.add('is-invalid');
        } else if (informasiValue.length > 800) {
            validationMessage.textContent = 'Maksimal 800 karakter diizinkan.';
            informasiTextarea.classList.add('is-invalid');
        } else {
            validationMessage.textContent = '';
            informasiTextarea.classList.remove('is-invalid');
        }
    });
</script>
<div class="form-group">
    <label for="foto1">Slide 1:</label>
    <input type="file" id="foto1" name="foto1" accept=".jpeg, .jpg, .png" class="form-control-file">
    <div class="image-preview" id="foto1-preview">
        <?php if(isset($row_beranda['foto1']) && !empty($row_beranda['foto1'])): ?>
            <img src="../assets/images/carousel/<?php echo $row_beranda['foto1']; ?>" class="preview-image" alt="Foto 1">
            <p style="font-size: 14px;">Foto saat ini</p>
        <?php endif; ?>
    </div>
    <p style="font-size: 14px; color: #888;">Unggah gambar dengan resolusi minimal 1440x720</p>
</div>

<div class="form-group">
    <label for="foto2">Slide 2:</label>
    <input type="file" id="foto2" name="foto2" accept=".jpeg, .jpg, .png" class="form-control-file">
    <div class="image-preview" id="foto2-preview">
        <?php if(isset($row_beranda['foto2']) && !empty($row_beranda['foto2'])): ?>
            <img src="../assets/images/carousel/<?php echo $row_beranda['foto2']; ?>" class="preview-image" alt="Foto 2">
            <p style="font-size: 14px;">Foto saat ini</p>
        <?php endif; ?>
    </div>
    <p style="font-size: 14px; color: #888;">Unggah gambar dengan resolusi minimal 1440x720</p>
</div>

<div class="form-group">
    <label for="foto3">Slide 3:</label>
    <input type="file" id="foto3" name="foto3" accept=".jpeg, .jpg, .png" class="form-control-file">
    <div class="image-preview" id="foto3-preview">
        <?php if(isset($row_beranda['foto3']) && !empty($row_beranda['foto3'])): ?>
            <img src="../assets/images/carousel/<?php echo $row_beranda['foto3']; ?>" class="preview-image" alt="Foto 3">
            <p style="font-size: 14px;">Foto saat ini</p>
        <?php endif; ?>
    </div>
    <p style="font-size: 14px; color: #888;">Unggah gambar dengan resolusi minimal 1440x720</p>
</div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
</div>
<script>
    // Function to handle image preview
    function handleImagePreview(input, previewElementId) {
        const previewElement = document.getElementById(previewElementId);
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const imagePreview = document.createElement('img');
                imagePreview.src = event.target.result;
                imagePreview.classList.add('preview-image');
                previewElement.innerHTML = ''; // Clear previous preview
                previewElement.appendChild(imagePreview);
            };
            reader.readAsDataURL(file);
        } else {
            previewElement.innerHTML = ''; // Clear preview when no file is selected
        }
    }

    // Attach event listeners to file input fields
    document.getElementById('foto1').addEventListener('change', function() {
        handleImagePreview(this, 'foto1-preview');
    });

    document.getElementById('foto2').addEventListener('change', function() {
        handleImagePreview(this, 'foto2-preview');
    });

    document.getElementById('foto3').addEventListener('change', function() {
        handleImagePreview(this, 'foto3-preview');
    });
</script>

</body>
</html>
