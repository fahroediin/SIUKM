<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memeriksa apakah tombol submit di klik
if (isset($_POST['submit'])) {
    // Mengambil nilai id_prestasi dari form
    $id_prestasi = $_POST['id_prestasi'];

   // Get the sertifikat filename for the achievement being deleted
$sertifikatFilename = ''; // Initialize the filename variable
$sertifikatQuery = "SELECT sertifikat FROM tab_prestasi WHERE id_prestasi = '$id_prestasi'";
$sertifikatResult = $conn->query($sertifikatQuery);
if ($sertifikatResult->num_rows > 0) {
    $sertifikatRow = $sertifikatResult->fetch_assoc();
    $sertifikatFilename = $sertifikatRow['sertifikat'];
}

// Query and SQL command to delete achievement data based on id_prestasi
$deleteQuery = "DELETE FROM tab_prestasi WHERE id_prestasi = '$id_prestasi'";
$deleteResult = $conn->query($deleteQuery);

if ($deleteResult) {
    // Delete the associated certificate image file
    if (!empty($sertifikatFilename)) {
        $sertifikatFilePath = '../assets/images/sertifikat/' . $sertifikatFilename;
        if (file_exists($sertifikatFilePath)) {
            unlink($sertifikatFilePath); // Delete the file
        }
    }

    // Redirect to the desired page after successful deletion
    header("Location: proses_prestasi.php");
    exit();
} else {
   
}    
    exit();
    }

// Menutup koneksi database
$conn->close();
?>
