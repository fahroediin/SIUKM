<?php
// ...

// Memasukkan file db_connect.php
require_once "db_connect.php";

if (isset($_GET['id_ukm'])) {
  $id_ukm = $_GET['id_ukm'];

  // Buat query untuk mengambil nama_ukm berdasarkan id_ukm
  $query = "SELECT nama_ukm FROM tab_ukm WHERE id_ukm = '$id_ukm'";
  $result = mysqli_query($conn, $query);

  if ($row = mysqli_fetch_assoc($result)) {
    $nama_ukm = $row['nama_ukm'];

    // Mengirimkan nama_ukm sebagai respons
    echo $nama_ukm;
  }
}

// ...
?>
