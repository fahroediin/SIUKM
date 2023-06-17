<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memeriksa apakah parameter id_ukm telah diterima
if (isset($_GET["id_ukm"])) {
    $id_ukm = $_GET["id_ukm"];

    // Query untuk mengambil data nama_ukm, sejarah, nama_ketua, nim_ketua, visi, misi berdasarkan id_ukm
    $query = "SELECT nama_ukm, sejarah, nama_ketua, nim_ketua, visi, misi FROM tab_ukm WHERE id_ukm='$id_ukm'";
    $result = mysqli_query($conn, $query);

    // Memeriksa apakah data ditemukan
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $nama_ukm = $row["nama_ukm"];
        $sejarah = $row["sejarah"];
        $nama_ketua = $row["nama_ketua"];
        $nim_ketua = $row["nim_ketua"];
        $visi = $row["visi"];
        $misi = $row["misi"];

        // Periksa apakah parameter "data" ada pada URL
        if (isset($_GET['data'])) {
            $data = $_GET['data'];

            // Periksa nilai parameter "data" untuk menentukan jenis data yang akan dikembalikan
            if ($data == 'keseluruhan') {
                // Mengembalikan data keseluruhan dalam format JSON
                $data = array(
                    "nama_ukm" => $nama_ukm,
                    "sejarah" => $sejarah,
                    "nama_ketua" => $nama_ketua,
                    "nim_ketua" => $nim_ketua,
                    "visi" => $visi,
                    "misi" => $misi
                );
                echo json_encode($data);
            } elseif ($data == 'nama_ukm') {
                // Mengembalikan data nama_ukm saja dalam format JSON
                $data = array("nama_ukm" => $nama_ukm);
                echo json_encode($data);
            } else {
                echo "Parameter 'data' tidak valid.";
            }
        } else {
            // Jika parameter "data" tidak ada, mengembalikan data keseluruhan dalam format JSON
            $data = array(
                "nama_ukm" => $nama_ukm,
                "sejarah" => $sejarah,
                "nama_ketua" => $nama_ketua,
                "nim_ketua" => $nim_ketua,
                "visi" => $visi,
                "misi" => $misi
            );
            echo json_encode($data);
        }
    } else {
        // Jika data tidak ditemukan
        echo "Data not found.";
    }
} else {
    // Jika parameter id_ukm tidak diterima
    echo "Invalid request.";
}
?>
