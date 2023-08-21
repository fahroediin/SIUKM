<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_kegiatan"])) {
    $id_kegiatan = $_POST["id_kegiatan"];

    // Query the database to retrieve details based on the provided id_kegiatan
    // Replace this with your actual database query
    $query = "SELECT id_ukm, nama_ukm, nama_kegiatan, jenis, deskripsi, tgl FROM tab_kegiatan WHERE id_kegiatan = ?";
    
    // Prepare the statement and execute the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id_kegiatan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(array("error" => "Data not found"));
    }
} else {
    echo json_encode(array("error" => "Invalid request"));
}
?>
