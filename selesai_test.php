<?php
 // Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}
if(isset($_SESSION['id_calabar'])) {
  $id_calabar = $_SESSION['id_calabar'];
    // Retrieve data from the tab_pacab table based on the stored id_calabar
    $query = "SELECT * FROM tab_pacab WHERE id_calabar = $id_calabar";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // Menyimpan data ke dalam variabel
    $id_calabar = $row['id_calabar'];
    $id_user = $row['id_user'];
    $nama_lengkap = $row['nama_lengkap'];
    $prodi = $row['prodi'];
    $semester = $row['semester'];
    $email = $row['email'];
    $no_hp = $row['no_hp'];
    $id_ukm = $row['id_ukm'];
    $nama_ukm = $row['nama_ukm'];
    $alasan = $row['alasan'];

    // Mengatur path untuk pasfoto dan foto KTM
    $pasfoto_path = "./assets/images/pasfoto/" . $row['pasfoto'];
    $foto_ktm_path = "./assets/images/ktm/" . $row['foto_ktm'];
}
    ?>
<!DOCTYPE html>
<html>
<head>
	<title>Sukses Daftar - SIUKM STMIK KOMPUTAMA MAJENANG</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="./assets/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="./assets/images/favicon-siukm.png">
    <style>

   /* Additional CSS styles */
   .center-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }
        .center-text {
            text-align: center;
        }
        .center-image {
            width: 150px;
            height: 150px;
        }
        .frame {
        border: 3px solid #ddd; /* Set the border style */
        padding: 5px; /* Add some padding around the image */
    }
    .frame.rounded {
        border-radius: 50%; /* Set the border radius to create a circular frame */
    }
    .image-container {
        margin-bottom: 20px; /* Add margin to create distance between images and table */
    }
</style>

<nav class="navbar navbar-expand-md navbar-dark fixed-top">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Beranda</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="profil.php">Profil</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="prestasi.php">Prestasi</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="galeri.php">Galeri</a>
      </li>
      <li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
						Pilih UKM
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						<a class="dropdown-item" href="pramuka.php">Pramuka</a>
						<a class="dropdown-item" href="mapala.php">Mapala</a>
						<a class="dropdown-item" href="pertanian.php">Pertanian</a>
						<a class="dropdown-item" href="english.php">Bahasa Inggris</a>
						<a class="dropdown-item" href="penelitian.php">Penelitian</a>
						<a class="dropdown-item" href="kewirausahaan.php">Kewirausahaan</a>
						<a class="dropdown-item" href="keagamaan.php">Keagamaan</a>
					</div>
				</li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
      <?php
				// Cek apakah pengguna sudah login
				if (!isset($_SESSION['level'])) {
					// Jika belum login, arahkan ke halaman login.php
					echo '<a class="nav-link btn btn-signin" href="login.php">Sign In</a>';
				} else {
					// Jika sudah login, cek level pengguna
          if ($_SESSION['level'] == "3") {
            // Jika level 3, arahkan ke halaman dashboard.php
            echo '<a class="nav-link btn btn-signin" href="dashboard.php"><p class="nav-greeting">Hi! ' . $_SESSION['nama_lengkap'] . '</p></a>';
          } elseif ($_SESSION['level'] == "1" || $_SESSION['level'] == "Admin") {
            // Jika level 1 atau admin, arahkan ke halaman admin.php
            echo '<a class="nav-link btn btn-signin" href="admin.php"><p class="nav-greeting">Hi! ' . $_SESSION['nama_lengkap'] . '</p></a>';
          } elseif ($_SESSION['level'] == "2") {
            // Jika level 2, arahkan ke halaman kemahasiswaan.php
            echo '<a class="nav-link btn btn-signin" href="kemahasiswaan.php"><p class="nav-greeting">Hi! ' . $_SESSION['nama_lengkap'] . '</p></a>';
          }
				}
          ?>
          </li>
        </ul>
      </div>
    </nav>
    <body>
    <div class="container center-container" style="margin-top: 75px;">
        <img src="./assets/images/good.png" alt="Sukses" class="center-image">
        <h1 class="center-text">Terima Kasih, <?php echo $nama_lengkap; ?></h1>
        <p  class="center-text">Anda telah mengerjakan Tes Potensi Akademik,</p>
        <p  class="center-text">Mohon tunggu untuk informasi lebih lanjut, hasil akan dikirim melalui email <b><?php echo $email; ?></b>.</p>
        <p  class="center-text">ID Pendaftaran : <b><?php echo $id_calabar; ?></b> </p>
        </div>
        </div>
        </div>
    </div>
</div>
</body>
</html>
