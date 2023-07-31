<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();
        
// Menonaktifkan pesan error
error_reporting(0);


// Mendapatkan nama depan dan level dari session
$nama_depan = $_SESSION["nama_lengkap"];
$level = $_SESSION["level"];


?>

<!DOCTYPE html>
<html>
<head>
	<title>Beranda - SIUKM STMIK Komputama Majenang</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="../assets/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
	<style>
        .carousel-item img {
            height: 400px; /* Atur tinggi sesuai kebutuhan */
            object-fit: cover; /* Pastikan gambar mengisi seluruh area */
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
					<a class="nav-link active" href="beranda.php">Beranda</a>
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
						<a class="dropdown-item" href="racana.php">Pramuka</a>
						<a class="dropdown-item" href="wanacetta.php">Wanaceta</a>
						<a class="dropdown-item" href="agrogreen.php">Agro Green</a>
						<a class="dropdown-item" href="ecc.php">ECC</a>
						<a class="dropdown-item" href="riset.php">Riset</a>
						<a class="dropdown-item" href="kwu.php">Kewirausahaan</a>
						<a class="dropdown-item" href="hsr.php">HSR</a>
					</div>
				</li>
			</ul>
			<ul class="navbar-nav ml-auto ">
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
						echo '<a class="nav-link btn btn-signin" href="dashboard.php"><p class="nav-greeting">Hi! ' . $_SESSION['nama_depan'] . '</p></a>';
					} elseif ($_SESSION['level'] == "1" || $_SESSION['level'] == "Admin") {
						// Jika level 1 atau admin, arahkan ke halaman admin.php
						echo '<a class="nav-link btn btn-signin" href="admin.php"><p class="nav-greeting">Hi! ' . $_SESSION['nama_depan'] . '</p></a>';
					} elseif ($_SESSION['level'] == "2") {
						// Jika level 2, arahkan ke halaman kemahasiswaan.php
						echo '<a class="nav-link btn btn-signin" href="kemahasiswaan.php"><p class="nav-greeting">Hi! ' . $_SESSION['nama_depan'] . '</p></a>';
					}
				}
			?>


 		</li>
		</ul>
		</div>
	</nav>
	
	<div class="jumbotron">
		<div class="container my-4">
		<h1 class="text-center">SELAMAT DATANG DI (SIUKM)<br> STMIK KOMPUTAMA MAJENANG</h1>
		<div class="row">
		<div class="col-md-7">
   		<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="2000">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block img-fluid" src="../assets/images/carousel-1.png" alt="First slide">
            </div>
            <div class="carousel-item">
                <img class="d-block img-fluid" src="../assets/images/carousel-2.png" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img class="d-block img-fluid" src="../assets/images/carousel-3.png" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    	</div>
		</div>

			<div class="col-md-5" style="text-align: justify;">
				<p>
				SIUKM (Sistem Informasi Unit Kegiatan Mahasiswa) STMIK Komputama Majenang merupakan sebuah platform web yang didedikasikan untuk mendukung kegiatan
				 Unit Kegiatan Mahasiswa (UKM) di STMIK Komputama Majenang. Platform ini memberikan kemudahan dalam mengelola kegiatan UKM serta menyediakan berbagai informasi yang dibutuhkan oleh anggota dan calon anggota UKM.
				 Salah satu fitur utama yang disediakan oleh SIUKM adalah pendaftaran calon anggota baru untuk UKM yang diminati. Calon anggota dapat mendaftar melalui platform ini dengan mengisi formulir pendaftaran yang telah disediakan. 
				 Hal ini memudahkan proses penerimaan anggota baru dan memastikan data yang tercatat lebih terstruktur.	

				</p>
			</div>
		</div>
		<div class="row mx-7">
    <div class="col-md-12 text-right">
				<?php
			// Cek apakah pengguna sudah login
			if (isset($_SESSION['id_user'])) {
				// Jika sudah login, arahkan ke halaman register-ukm.php
				echo '<button class="btn btn-lg btn-primary" onclick="location.href=\'register-ukm.php\';">DAFTAR SEKARANG</button>';
			} else {
				// Jika belum login, arahkan ke halaman login.php
				echo '<button class="btn btn-lg btn-primary" onclick="location.href=\'login.php\';">DAFTAR SEKARANG</button>';
			}
			?>
		</div>
</div>
	</div>
</body>
<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
</svg> Website</a> 
| Connect with us on <a href="https://www.facebook.com/stmikkomputama"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
  <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
</svg> Facebook</a></footer>
</html>