<?php
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id_ukm"])) {
    $id_ukm = $_GET["id_ukm"];

    $sql = "SELECT logo_ukm, sk FROM tab_ukm WHERE id_ukm = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id_ukm);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $logo_ukm, $sk);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $deleteSql = "DELETE FROM tab_ukm WHERE id_ukm = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);
    mysqli_stmt_bind_param($deleteStmt, "s", $id_ukm);

    if (mysqli_stmt_execute($deleteStmt)) {
        // Delete logo_ukm file
        if (!empty($logo_ukm)) {
            $logoPath = "../assets/images/logoukm/" . $logo_ukm;
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        }

        // Delete sk file
        if (!empty($sk)) {
            $skPath = "../assets/images/sk/" . $sk;
            if (file_exists($skPath)) {
                unlink($skPath);
            }
        }

        mysqli_stmt_close($deleteStmt);
        mysqli_close($conn);
        header("Location: proses_ukm.php?deleteSuccess=true");
        exit();

    } else {
        echo "Error deleting data: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>