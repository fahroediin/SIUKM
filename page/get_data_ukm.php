<?php
// get_data_ukm.php
require_once "db_connect.php";

if (isset($_GET['id_ukm'])) {
    $id_ukm = $_GET['id_ukm'];
    $query = "SELECT * FROM tab_ukm WHERE id_ukm='$id_ukm'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    } else {
        echo json_encode(array("error" => "Data not found"));
    }
}
?>
