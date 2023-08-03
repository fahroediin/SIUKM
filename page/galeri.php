<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menonaktifkan pesan error
error_reporting(0);


$nama_lengkap = $_SESSION["nama_lengkap"];
$level = $_SESSION["level"];

// Inisialisasi variabel pesan error
$error = '';
// Function to get all image files from a directory
function getImagesFromDir($dir) {
    $images = array();
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (!is_dir($dir . $file)) {
                    $images[] = $dir . $file;
                }
            }
            closedir($dh);
        }
    }
    return $images;
}

// Get the image files from the 'kegiatan' directory
$image_files = getImagesFromDir('../assets/images/kegiatan/');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Galeri</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>
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
        .gallery {
            display: flex;
            flex-wrap: wrap;
        }
        .gallery-item {
            width: 25%;
            padding: 10px;
        }
        .gallery-item img {
            width: 100%;
            height: auto;
        }
        .gallery-item .caption {
            margin-top: 5px;
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
					<a class="nav-link" href="profil.php">Profil</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="prestasi.php">Prestasi</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="galeri.php">Galeri</a>
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
	<div class="container">
        <h1>Galeri</h1>
        <div class="gallery">
            <?php
            // Loop through the image files and display them
            foreach ($image_files as $index => $image_file) {
                // Calculate the index of the image to display (0 to 3)
                $image_index = $index % 4;
                ?>
                <div class="gallery-item">
                    <img src="<?php echo $image_file; ?>" alt="Image <?php echo ($index + 1); ?>" />
                    <div class="caption">Caption for Image <?php echo ($index + 1); ?></div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
            <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z" />
        </svg> Website</a> | Connect with us on <a href="https://www.facebook.com/stmikkomputama"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
            <path d="M16 8.049c0-4.423-3.576-8-7.999-8C3.575.049 0 3.626 0 8.049c0 3.892 2.84 7.11 6.57 7.807v-5.509H4.429V8.048h2.142V6.311c0-2.117 1.26-3.293 3.19-3.293.92 0 1.82.165 1.82.165v1.997h-1.03c-1.009 0-1.324.628-1.324 1.27v1.52h2.25l-.361 2.252h-1.89v5.51C13.159 15.16 16 11.942 16 8.05z" />
        </svg> Facebook</a></footer>
</html>
