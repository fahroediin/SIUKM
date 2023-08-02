<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menghindari SQL injection
$oldPassword = mysqli_real_escape_string($conn, $_POST['old_password']);
$userId = $_SESSION['id_user'];

// Mengecek kebenaran password lama
$query = "SELECT * FROM tab_user WHERE id_user = '$userId' AND password = '$oldPassword'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    // Password lama tidak sesuai
    $response = array('success' => false);
} else {
    // Password lama sesuai
    $response = array('success' => true);
}

// Mengembalikan response dalam format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
