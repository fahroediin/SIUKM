<?php
require_once "db_connect.php";

error_reporting(E_ALL);
ini_set('display_errors', '1');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_calabar     = $_POST["id_calabar"];
    $selectedStatus = $_POST["status"];

    // Update the status in tab_pacab
    $updateQuery = "UPDATE tab_pacab SET status_cab = '$selectedStatus' WHERE id_calabar = $id_calabar";
    if (mysqli_query($conn, $updateQuery)) {
        // If status is "Diterima", insert data into tab_dau
        if ($selectedStatus == "Diterima") {
            $query  = "SELECT * FROM tab_pacab WHERE id_calabar = $id_calabar";
            $result = mysqli_query($conn, $query);
            $row    = mysqli_fetch_assoc($result);

            // Generate id_anggota
            $id_user      = $row['id_user'];
            $nama_lengkap = $row['nama_lengkap'];
            $no_hp        = $row['no_hp'];
            $email        = $row['email'];
            $prodi        = $row['prodi'];
            $semester     = $row['semester'];
            $pasfoto      = $row['pasfoto'];
            $foto_ktm     = $row['foto_ktm'];
            $id_ukm       = $row['id_ukm'];
            $nama_ukm     = $row['nama_ukm'];

            $programAbbreviation = "00"; // Initialize program code
            $currentSemester     = $row['semester'];

            // Get semester value from $row
            $currentMonth    = date("m");
            $dateTime        = new DateTime();
            $sjk_bergabung   = $dateTime->format('Y-m-d');
            $tahun_bergabung = substr($sjk_bergabung, 0, 4); // Get year from joining date
            $randomDigits    = mt_rand(10, 99);

            if ($prodi == "Teknik Informatika") {
                $programAbbreviation = "01";
            } elseif ($prodi == "Sistem Informasi") {
                $programAbbreviation = "02";
            }

            // Generate id_anggota
            $id_anggota = substr($id_user, -2) . $programAbbreviation . $currentSemester . $currentMonth . substr($tahun_bergabung, -2) . $randomDigits;


            $deleteQuery = "DELETE from tab_pacab WHERE id_calabar = '$id_calabar'";
            $del         = mysqli_query($conn, $deleteQuery);

            // Insert into tab_dau
            $insertQuery = "INSERT INTO tab_dau(id_anggota, id_user, nama_lengkap, no_hp, email, prodi, semester, pasfoto, foto_ktm, id_ukm, nama_ukm, sjk_bergabung) VALUES ('$id_anggota', '$id_user', '$nama_lengkap', '$no_hp', '$email', '$prodi', '$semester', '$pasfoto', '$foto_ktm', '$id_ukm', '$nama_ukm', '$sjk_bergabung') ";

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