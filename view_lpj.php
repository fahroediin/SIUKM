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

$file = $_GET['file'];

$file_extension = pathinfo($file, PATHINFO_EXTENSION);

if ($file_extension === 'pdf') {
    header('Location: ./assets/images/lpj/' . $file);
    exit();
} elseif ($file_extension === 'docx') {
    $google_docs_url = 'https://docs.google.com/viewer?url=' . urlencode('./assets/images/lpj/' . $file);
    header('Location: ' . $google_docs_url);
    exit();
} else {
    echo 'Unsupported file format';
}
?>
