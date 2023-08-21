<?php
require_once "db_connect.php";

$id_user = $_GET['id_user'];
$id_user = mysqli_real_escape_string($conn, $id_user);

$query = "SELECT id_user FROM tab_user WHERE id_user = '$id_user'";
$result = mysqli_query($conn, $query);

$response = array();
$response['exists'] = false;

if ($result && mysqli_num_rows($result) > 0) {
    $response['exists'] = true;
}

echo json_encode($response);
?>
