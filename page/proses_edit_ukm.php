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

    $sql = "UPDATE tab_ukm SET nama_ukm=?, sejarah=?, instagram=?, facebook=?, visi=?, misi=? WHERE id_ukm=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssss", $nama_ukm, $sejarah, $instagram, $facebook, $visi, $misi, $id_ukm);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: proses_ukm.php?editSuccess=true&showSnackbar=true"); // Add &showSnackbar=true
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
?>
