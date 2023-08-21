<?php
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_calabar = $_POST["id_calabar"];
    $selectedStatus = $_POST["status"];

    // Update the status in tab_pacab
    $updateQuery = "UPDATE tab_pacab SET status_cab = '$selectedStatus' WHERE id_calabar = $id_calabar";
    if (mysqli_query($conn, $updateQuery)) {
        // If status is "Diterima", insert data into tab_dau
        if ($selectedStatus == "Diterima") {
            $query = "SELECT * FROM tab_pacab WHERE id_calabar = $id_calabar";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);

            // Generate id_anggota
            $id_user = $row['id_user'];
            $programCode = ""; // Initialize program code
            $currentSemester = $row['semester']; // Get semester value from $row
            $currentMonth = date("m");
            $tahun_bergabung = substr($row['tanggal_bergabung'], 0, 4); // Get year from joining date
            $randomDigits = mt_rand(10, 99);

            // Assign program code based on program_studi value
            $programCode = ($row['program_studi'] == "01") ? "IT" : ($row['program_studi'] == "02" ? "SI" : "");

            // Generate id_anggota
            $id_anggota = substr($id_user, -2) . $programCode . $currentSemester . $currentMonth . substr($tahun_bergabung, -2) . $randomDigits;

            // Insert into tab_dau
            $insertQuery = "INSERT INTO tab_dau (id_anggota, id_user, nama_lengkap, prodi, id_ukm, nama_ukm, email, no_hp, semester, prodi, pasfoto, foto_ktm, sjk_bergabung) VALUES ('$id_anggota', '{$row['id_user']}', '{$row['nama_lengkap']}', '{$row['prodi']}', '{$row['id_ukm']}', '{$row['nama_ukm']}', '{$row['email']}', '{$row['no_hp']}', '{$row['semester']}', '{$row['prodi']}', '{$row['pasfoto']}', '{$row['foto_ktm']}', '{$row['sjk_bergabung']}')";

            if (mysqli_query($conn, $insertQuery)) {
                echo "Status updated and data inserted into tab_dau successfully";
            } else {
                echo "Error inserting data into tab_dau: " . mysqli_error($conn);
            }
        } else {
            echo "Status updated successfully";
        }
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
