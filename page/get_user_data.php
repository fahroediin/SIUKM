<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_user"])) {
    // Mengambil nilai id_user dari form
    $id_user = $_POST["id_user"];

    // Fetch user data based on the selected id_user
    $query = "SELECT nama_lengkap, no_hp, email, prodi, semester FROM tab_user WHERE id_user = $id_user";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);
        echo json_encode($userData);
    } else {
        echo json_encode(array("error" => "User not found"));
    }
} else {
    echo json_encode(array("error" => "Invalid request"));
}
?>
