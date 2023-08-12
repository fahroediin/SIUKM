<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

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
