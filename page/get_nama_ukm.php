<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

if (isset($_POST['id_ukm'])) {
    $id_ukm = $_POST['id_ukm'];
    $query = "SELECT nama_ukm FROM tab_ukm WHERE id_ukm = '$id_ukm'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $nama_ukm = $row['nama_ukm'];

        // Create an associative array to send the data back as JSON
        $data = array('nama_ukm' => $nama_ukm);
        echo json_encode($data);
    } else {
        echo json_encode(array('nama_ukm' => ''));
    }
}
?>
