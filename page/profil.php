<?php 
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menonaktifkan pesan error
error_reporting(0);

// Mendapatkan nama depan dari session
$nama_depan = $_SESSION["nama_depan"];
$level = $_SESSION["level"];

// Inisialisasi variabel pesan error
$error = '';
// Retrieve data from the database
$query = "SELECT nama_ukm, logo_ukm FROM tab_ukm";
$result = mysqli_query($conn, $query);

// Initialize an array to store the names and logos of UKMs
$ukm_data = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Add each UKM name and logo to the array
        $ukm_data[] = $row;
    }
}

// Close the database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Profil - SIUKM STMIK KOMPUTAMA MAJENANG</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
  <style>
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
	h1 {
		padding-top: 40px;
	}
    .jumbotron {
      background-color: #AFD3E2;
      padding: 20px;
      margin-bottom: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
	.card {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 10px;
    }
	.left-align li {
        text-align: left;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark fixed-top">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
			<span class="navbar-toggler-icon"></span>
		</button>
	        	<div class="collapse navbar-collapse" id="collapsibleNavbar">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" href="beranda.php">Beranda</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="profil.php">Profil</a>
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
			<div class="container">
				<h1>Profil</h1>
				<div class="jumbotron">
					<img src="..\assets\images\siukm-logo.png" alt="Logo SIUKM" class="logo-siukm">
					<h2>Sistem Informasi Unit Kegiatan Mahasiswa</h2>
					<p>STMIK Komputama Majenang</p>
				</div>

				<div class="jumbotron">
				<h3>Deskripsi Singkat</h3>
				<p>SIUKM STMIK Komputama Majenang merupakan wadah yang didedikasikan untuk penyebaran informasi kepada Unit Kegiatan Mahasiswa (UKM) di lingkungan kampus. Kami berkomitmen untuk menyediakan platform yang memungkinkan UKM untuk mengkomunikasikan kegiatan, berbagi pengetahuan, dan memperluas jaringan melalui sistem informasi yang efektif dan efisien.</p>
			
				<h3>Visi</h3>
				<p>Menjadi wadah utama yang memperkuat kolaborasi antar-UKM dan menyediakan platform yang memungkinkan penyebaran informasi yang luas, terbuka, dan mudah diakses.</p>
				<h3>Misi</h3>
				<ul class="left-align">
				<li>Mengembangkan sistem informasi yang inovatif dan user-friendly untuk kebutuhan UKM.</li>
				<li>Mendorong pertukaran pengetahuan dan pengalaman antar-UKM melalui platform SIUKM.</li>
				<li>Menyediakan akses yang mudah dan cepat terhadap informasi seputar kegiatan UKM.</li>
				<li>Memperluas jaringan kolaborasi dengan institusi dan organisasi terkait.</li>
			</ul>
			</div>
			
		
	<!-- Display the UKM names and logos -->
	<div class="jumbotron ukm-grid">
    <h3>Daftar Nama Unit Kegiatan Mahasiswa</h3>
    <div class="card-deck">
        <?php foreach ($ukm_data as $ukm) : ?>
            <div class="card">
                <img src="../assets/images/logoukm/<?= $ukm['logo_ukm'] ?>" class="card-img-top" alt="<?= $ukm['nama_ukm'] ?> Logo">
                <div class="card-body">
                    <h5 class="card-title"><?= $ukm['nama_ukm'] ?></h5>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
		</div>
		</body>
		<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
		<path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
		</svg> Website</a> 
		| Connect with us on <a href="https://www.facebook.com/stmikkomputama"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
		<path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
		</svg> Facebook</a></footer>
		</html>
