<?php
require_once "db_connect.php";
session_start();

// Check user authentication and roles
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['level'] == "3" || $_SESSION['level'] == "2") {
    header("Location: beranda.php");
    exit();
}

function sendError($message) {
    echo $message;
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_ukm = $_POST["id_ukm_edit"];
    $nama_ukm = $_POST["nama_ukm_edit"];
    $sejarah = $_POST["sejarah_edit"];
    $instagram = $_POST["instagram_edit"];
    $facebook = $_POST["facebook_edit"];
    $visi = $_POST["visi_edit"];
    $misi = $_POST["misi_edit"];

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Handle SK file upload
    $targetDirSK = "../assets/images/sk/";
    $sk_filename = "";

    if ($_FILES["sk_edit"]["error"] === UPLOAD_ERR_OK) {
        $sk_name = $_FILES["sk_edit"]["name"];
        $sk_extension = strtolower(pathinfo($sk_name, PATHINFO_EXTENSION));

        if ($sk_extension !== 'pdf') {
            sendError("Sorry, only PDF files are allowed for SK.");
        }

        $sk_filename = generateSKFilename($id_ukm, $sk_extension);
        $sk_path = $targetDirSK . $sk_filename;

        // Remove existing SK file if exists
        if (file_exists($sk_path)) {
            unlink($sk_path);
        }

        if (!move_uploaded_file($_FILES["sk_edit"]["tmp_name"], $sk_path)) {
            sendError("Sorry, there was an error uploading the SK file.");
        }
    }

    // Update database
    $sql = "UPDATE tab_ukm SET nama_ukm=?, sejarah=?, instagram=?, facebook=?, visi=?, misi=?, sk=? WHERE id_ukm=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $nama_ukm, $sejarah, $instagram, $facebook, $visi, $misi, $sk_filename, $id_ukm);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: proses_ukm.php?editSuccess=true&showSnackbar=true");
        exit();
    } else {
        echo "Error updating data: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    header("Location: beranda.php");
    exit();
}

function generateSKFilename($id_ukm, $extension) {
    return $id_ukm . "-sk." . $extension;
}
?>
