<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

if (isset($_GET['id_anggota'])) {
    $id_anggota = $_GET['id_anggota'];
    
    // Query to retrieve the details of the selected id_anggota from tab_dau
    $sql = "SELECT nama_lengkap, id_user FROM tab_dau WHERE id_anggota = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id_anggota);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Data found, return as JSON
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        // Data not found, return null
        echo json_encode(null);
    }
}
?>
