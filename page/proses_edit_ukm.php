<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";
if (isset($_GET['edit_id'])) {
    $ukmId = $_GET['edit_id'];


    // Query untuk mengambil data berdasarkan ukmId
    $query = "SELECT id_ukm, nama_ukm, logo_ukm, instagram, facebook, sejarah, visi, misi, sk FROM tab_ukm WHERE id_ukm = '$ukmId'";
    $result = mysqli_query($conn, $query);

    // Pastikan data ditemukan sebelum menggunakan mysqli_fetch_assoc
    if ($result && mysqli_num_rows($result) > 0) {
        $ukmData = mysqli_fetch_assoc($result);
    } else {
        // Handle ketika data tidak ditemukan
        echo "Data UKM tidak ditemukan.";
        exit(); // Keluar dari skrip
    }
} else {
    // Handle ketika edit_id tidak ada
    echo "Parameter edit_id tidak ditemukan.";
    exit(); // Keluar dari skrip
}
$ukmId = $_GET['edit_id']; // Assuming you have an edit_id parameter in the URL
$query = "SELECT id_ukm, nama_ukm, logo_ukm, instagram, facebook, sejarah, visi, misi, sk FROM tab_ukm WHERE id_ukm = '$ukmId'";
$result = mysqli_query($conn, $query);

$ukmData = mysqli_fetch_assoc($result);

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai-nilai dari form
    $id_ukm = $_POST["id_ukm"];
    $nama_ukm = $_POST["nama_ukm"];
    $sejarah = $_POST["sejarah"];
    $instagram = $_POST["instagram"];
    $facebook = $_POST["facebook"];
    $visi = $_POST["visi"];
    $misi = $_POST["misi"];

    // SQL query to update data in tab_ukm table
    $sql = "UPDATE tab_ukm SET nama_ukm=?, sejarah=?, instagram=?, facebook=?, visi=?, misi=? WHERE id_ukm=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssi", $nama_ukm, $sejarah, $instagram, $facebook, $visi, $misi, $id_ukm);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: proses_ukm.php?success=1");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
        exit();
    }
}
?>
