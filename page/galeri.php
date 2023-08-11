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

// Function to get additional data (nama_kegiatan, tgl, and nama_ukm) from the database for a given image file
function getDataForImage($conn, $image_file) {
    $query = "SELECT nama_kegiatan, tgl, nama_ukm, jenis FROM tab_galeri WHERE foto_kegiatan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $image_file);
    $stmt->execute();
    $stmt->bind_result($nama_kegiatan, $tgl, $nama_ukm, $jenis); // Include jenis in bind_result
    $stmt->fetch();
    $stmt->close();

    return array(
        "nama_kegiatan" => $nama_kegiatan,
        "tgl" => $tgl,
        "nama_ukm" => $nama_ukm,
        "jenis" => $jenis // Include jenis in the returned array
    );
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Galeri</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">

	</head>
    <style>
     h1 {
		padding-top: 40px;
		     }
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    .divider {
        border: none;
        border-top: 1px solid #ccc;
        margin: 20px 0;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f2f2f2;
    }
    .filter-button {
        border-radius: 0.25rem;
        padding: 6px 12px;
    }

    /* Style the search icon */
    .filter-button i {
        margin-right: 5px;
    }

    /* Style the filter form */
    .filter-form {
        margin-bottom: 20px;
    }
    /* Center the modal dialog vertically and horizontally */
  .custom-modal-dialog {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh; /* Ensure the modal covers the entire viewport height */
    padding: 0;
  }

    .gallery {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .gallery-item {
        width: 23%; /* Adjust the width for four items per row */
        margin-bottom: 10px; /* Reduce spacing between rows */
    }

    .gallery-item img {
        max-width: 720px; /* Limit the width to 720 pixels */
        height: 220px; /* Set a fixed height of 360 pixels */
        cursor: pointer;
        border-radius: 10px; /* Add border radius for a more rounded appearance */
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2); /* Add a subtle shadow */
    }
    .card {
    border: 1px solid #e1e1e1;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.card-body {
    padding: 15px;
}

.card-title {
    font-size: 18px;
    margin-bottom: 8px;
}

.card-text {
    font-size: 14px;
    color: #555;
    margin-bottom: 5px;
}

.card-jenis {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 10px;
    font-style: italic;
    background-color: #f1f1f1;
    padding: 3px 8px;
    border-radius: 5px;
}
    </style>
	 <script>
        // Add jQuery click event handler to handle the zoom effect
        $(document).ready(function() {
            $(".gallery-item img").click(function() {
                $(this).toggleClass("zoomed"); // Toggle the zoomed class when an image is clicked
            });
        });
    </script>
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
						<a class="dropdown-item" href="pramuka.php">Racana</a>
						<a class="dropdown-item" href="mapala.php">Wanacetta</a>
						<a class="dropdown-item" href="pertanian.php">Agro Green</a>
						<a class="dropdown-item" href="english.php">English Conversation Club</a>
						<a class="dropdown-item" href="penelitian.php">Mahasiswa Community Riset</a>
						<a class="dropdown-item" href="kewirausahaan.php">Kewirausahaan</a>
						<a class="dropdown-item" href="keagamaan.php">Human Social Religion</a>
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
        <div class="divider"></div>
      <!-- Filter form -->
      <form class="filter-form">
        <label for="jenis">Jenis Kegiatan:</label>
        <select id="jenis" name="jenis">
            <option value="all">All</option>
            <option value="Rutin">Rutin</option>
            <option value="Tidak Rutin">Tidak Rutin</option>
            <!-- Add more options based on your actual types -->
        </select>
        <button type="submit" class="btn btn-primary filter-button"><i class="fas fa-filter"></i> Filter</button>
    </form>

        <div class="container">
        <div class="gallery">
        <?php
// Loop through the image files and display them
foreach ($image_files as $index => $image_file) {
    // Calculate the index of the image to display (0 to 3)
    $image_index = $index % 4;

    // Get the original dimensions of the image
    list($width, $height) = getimagesize($image_file);

    // Get additional data (nama_kegiatan, tgl, and nama_ukm) from the database
    $data = getDataForImage($conn, basename($image_file));
    $nama_kegiatan = $data["nama_kegiatan"];
    $tgl = $data["tgl"];
    $nama_ukm = $data["nama_ukm"];
    $jenis = $data["jenis"];

    // Get the selected filter type
    $filterType = isset($_GET['jenis']) ? $_GET['jenis'] : 'all';
    
    // Get the jenis (type) for the current image from the database
    $imageJenis = $data["jenis"];

    // Check if the image matches the filter type
    if ($filterType === 'all' || $imageJenis === $filterType) {
        // Display the gallery item with the filtered images
        ?>
        <div class="gallery-item">
            <!-- Add data attributes for the modal -->
            <div class="card">
                <img src="<?php echo $image_file; ?>" alt="Image <?php echo ($index + 1); ?>" class="card-img-top"
                     data-toggle="modal" data-target="#imageModal" />
                <div class="card-body">
                    <h3 class="card-title"><?php echo $nama_kegiatan; ?></h3>
                    <p class="card-text"><?php echo date('d F Y', strtotime($tgl)); ?></p>
                    <p class="card-text"><?php echo $nama_ukm; ?></p>
                </div>
                <p class="card-jenis"><?php echo $jenis; ?></p>
            </div>
        </div>
        <?php
    }
}
?>

    </div>
	</div>
    </div>
	<!-- Modal to display enlarged image -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <img src="" alt="" class="enlarged-image img-fluid">
            </div>
        </div>
    </div>
</div>

	</body>
	<script>
    $(document).ready(function() {
        $(".gallery-item img").click(function() {
            // Get the image source and ID from the clicked image
            var src = $(this).attr('src');
            var imageID = $(this).data('image-id');

            // Update the modal's image source and show the modal
            $("#imageModal .enlarged-image").attr('src', src);
            $("#imageModal").modal('show');
        });
    });
</script>

<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
</svg> Website</a> 
| Connect with us on <a href="https://www.facebook.com/stmikkomputama"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
  <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
</svg> Facebook</a></footer>
</html>
