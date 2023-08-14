<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

session_start();

$error = '';

$id_ukm = $_GET['id_ukm'];

$query = "SELECT * FROM tab_strukm";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit();
}

$logoDirectory = '../assets/images/logoukm/';
$defaultLogo = $logoDirectory . 'logo-default.png';

$query = "SELECT logo_ukm FROM tab_ukm WHERE id_ukm = '$id_ukm'";
$logoResult = mysqli_query($conn, $query);

if (!$logoResult) {
  echo "Error: " . mysqli_error($conn);
  exit();
}
// Get the logo URL
$row = mysqli_fetch_assoc($logoResult);
$logo_ukm = $row['logo_ukm'];

if (!empty($logo_ukm) && file_exists($logoDirectory . $logo_ukm)) {
  $logo_src = $logoDirectory . $logo_ukm;
} else {
  $logo_src = $defaultLogo;
}

$query = "SELECT visi, misi, sejarah, nama_ukm FROM tab_ukm WHERE id_ukm = '$id_ukm'";
$infoResult = mysqli_query($conn, $query);

// Memeriksa apakah query berhasil dieksekusi
if (!$infoResult) {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
    exit();
}
// Mengambil visi dan misi dari hasil query
$row = mysqli_fetch_assoc($infoResult);
$visi = $row['visi'];
$misi = $row['misi'];
$sejarah = $row['sejarah'];
$nama_ukm = $row['nama_ukm'];

// Menghitung jumlah anggota pada setiap jabatan berdasarkan id_ukm
$jabatan_count = array();
while ($row = mysqli_fetch_assoc($result)) {
    $id_ukm = $row['id_ukm'];
    $id_jabatan = $row['id_jabatan'];

    if (!isset($jabatan_count[$id_ukm])) {
        $jabatan_count[$id_ukm] = array();
    }

    if (!isset($jabatan_count[$id_ukm][$id_jabatan])) {
        $jabatan_count[$id_ukm][$id_jabatan] = 0;
    }

    $jabatan_count[$id_ukm][$id_jabatan]++;
}

// Reset pointer hasil query
mysqli_data_seek($result, 0);

// Inisialisasi array jabatan
$jabatan = array(
    0 => "Pembimbing",
    1 => "Ketua",
    2 => "Wakil Ketua",
    3 => "Sekretaris",
    4 => "Bendahara",
    5 => "Koordinator",
    6 => "Anggota"
);

// Membuat array kosong untuk setiap jabatan berdasarkan id_ukm
$struktur = array();
foreach ($jabatan as $id_jabatan => $nama_jabatan) {
    $struktur[$id_jabatan] = array();
}

// Mengisi array struktur dengan data dari tabel tab_strukm
while ($row = mysqli_fetch_assoc($result)) {
    $id_ukm = $row['id_ukm'];
    $id_jabatan = $row['id_jabatan'];
    $nim = $row['nim'];
    $nama_lengkap = $row['nama_lengkap'];

    // Menambahkan data ke array struktur berdasarkan id_ukm dan id_jabatan
    $struktur[$id_jabatan][$id_ukm][] = array("nim" => $nim, "nama_lengkap" => $nama_lengkap);
}
// Query to get kegiatan data for the "pramuka" UKM
$query = "SELECT nama_kegiatan, tgl FROM tab_kegiatan WHERE id_ukm = '$id_ukm'";
$kegiatanResult = mysqli_query($conn, $query);

// Memeriksa apakah query berhasil dieksekusi
if (!$kegiatanResult) {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
    exit();
}

$kegiatanData = array();
while ($row = mysqli_fetch_assoc($kegiatanResult)) {
    $kegiatanData[] = array("nama_kegiatan" => $row['nama_kegiatan'], "tgl" => $row['tgl']);
}

$indonesianMonths = array(
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
  'Agustus', 'September', 'Oktober', 'November', 'Desember'
);

// Function to format date in Indonesian format
function formatDateIndonesia($date) {
  global $indonesianMonths;
  return date('d', strtotime($date)) . " " . $indonesianMonths[intval(date('m', strtotime($date))) - 1] . " " . date('Y', strtotime($date));
}
$query = "SELECT instagram, facebook FROM tab_ukm WHERE id_ukm = '$id_ukm'";
$socialMediaResult = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($socialMediaResult);
$instagram = $row['instagram'];
$facebook = $row['facebook'];

$instagram = "https://www.instagram.com/" . $instagram;
$facebook = "https://www.facebook.com/" . $facebook;
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $nama_ukm; ?></title>
  <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <script src="../assets/js/script.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">

</head>
<style>
  .divider {
  border-top: 1px solid #ddd;
  margin-top: 20px;
  margin-bottom: 20px;
}

.jumbotron {
  padding: 20px;
  background-color: #AFD3E2; /* Ganti dengan warna yang Anda inginkan */
        color: #ffffff; /* Ganti dengan warna teks yang kontras */
}

.jumbotron h2 {
  margin-bottom: 10px;
}

.jumbotron p {
  margin-bottom: 20px;
}

.jumbotron hr {
  margin-top: 20px;
  margin-bottom: 20px;
}


.ukm-logo {
  float: left;
  margin-right: 20px;
  width: 200px;
  height: 200px;
  border-radius: 10%;
  overflow: hidden;
  max-width: 512px; 
  max-height: 512px;
}

.ukm-logo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.ukm-info {
  overflow: hidden;
}

.ukm-info h2 {
  margin-bottom: 10px;
}

.container {
  margin-top: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  padding-top: 40px;
}
h2 {
    text-align: left;
  }
.h2-struktur {
  text-align: center;
}

.organisasi {
    display: flex;
    flex-wrap: wrap;
    justify-content: center; /* Pusatkan elemen dalam div organisasi */
  }

  .posisi {
    flex-basis: 25%;
    padding: 10px;
    text-align: center; /* Pusatkan teks dalam setiap posisi */
    box-sizing: border-box; /* Hitung padding dalam perhitungan lebar elemen */
  }

  .posisi h3 {
    margin: 0;
  }

  .posisi img {
    width: 100px; /* Atur ukuran gambar sesuai kebutuhan */
    border-radius: 50%; /* Membulatkan sudut gambar */
    margin-bottom: 10px; /* Beri sedikit ruang di bawah gambar */
  }

  table {
        width: 25%;
        border-collapse: collapse;
        margin-left: auto;
        margin-right: auto;
    }

    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        white-space: nowrap;
    }

    th.table-heading {
        background-color: #f2f2f2;
        text-align: center;
    }
    .h2-kegiatan {
    text-align: center;
    margin-bottom: 20px;
    color: #333; /* Set the desired color */
}

.kegiatan-container {
    padding: 20px;
    background-color: #f9f9f9; /* Set the desired background color */
}

.card {
        position: relative;
        overflow: hidden;
    }

    .card-img-right {
        width: 100px; /* Set the fixed width to 100px */
        height: auto;
        position: absolute;
        top: 50%;
        right: 10px; /* Adjust the right spacing as needed */
        transform: translateY(-50%);
        opacity: 0.8;
        /* Add other styles such as border-radius, box-shadow, etc. for visual appeal */
    }

    .card-title {
        font-size: 24px; /* Increase the font size for the card title */
        white-space: nowrap; /* Prevent text wrapping */
        overflow: hidden; /* Hide overflowing text */
        text-overflow: ellipsis; /* Add ellipsis (...) for long texts */
    }

    .card-text {
        font-size: 18px; /* Increase the font size for the card text */
         text-align: left;
    }
.card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}
p {
    text-align: center; 
    font-size: 18px; 
  }
    /* Style for the "Lihat SK" button */
.sk-button {
    float: right;
    margin-top: -40px; /* Adjust this value as needed */
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}
    </style>
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
        <a class="nav-link" href="galeri.php">Galeri</a>
      </li>
      <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
        Pilih UKM
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <?php
        $ukmQuery = "SELECT id_ukm, nama_ukm FROM tab_ukm";
        $ukmResult = mysqli_query($conn, $ukmQuery);

        while ($ukmRow = mysqli_fetch_assoc($ukmResult)) {
            $id_ukm = $ukmRow['id_ukm'];
            $nama_ukm = $ukmRow['nama_ukm'];
            
            echo "<a class='dropdown-item' href='halaman_ukm.php?id_ukm=$id_ukm'>$nama_ukm</a>";
        }
        ?>
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
  <h1><?php echo $nama_ukm; ?></h1>
        <div class="ukm-info">
        <div class="ukm-logo">
            <img src="<?php echo $logo_src; ?>" alt="Logo UKM" class="ukm-logo">
          </div>
            <div>
                <h2>Informasi</h2>
                <p style="text-align: justify;"><?php echo $sejarah; ?></p>
            </div>
        </div>
     
        <div class="divider">
        <br>
        <div class="kegiatan-container">
          <h2 class="h2-kegiatan">Jadwal Kegiatan</h2>
          <?php if (empty($kegiatanData)) { ?>
              <p style="text-align: center; font-style: italic;">Belum ada jadwal kegiatan terdekat</p>
          <?php } else { ?>
              <!-- Display the kegiatan cards -->
              <div class="card-columns">
                  <?php foreach ($kegiatanData as $kegiatan) { ?>
                      <div class="card">
                          <div class="card-body">
                              <h5 class="card-title"><?php echo $kegiatan['nama_kegiatan']; ?></h5>
                              <p class="card-text"><?php echo formatDateIndonesia($kegiatan['tgl']); ?></p>
                          </div>
                          <!-- Add the image to the right side of the card -->
                          <img src="../assets/images/announcement.png" class="card-img-right" alt="Announcement">
                      </div>
                  <?php } ?>
              </div>
          <?php } ?>
      </div>

      <div class="divider">

      <div class="card mt-4">
      <div class="card-body d-flex flex-column align-items-center">
        <h5 class="card-title">Follow Us</h5>
    <p class="mt-2" style="font-size: 14px; color: #777;">Untuk bertanya lebih lanjut terkait UKM</p>
        <div class="card-text">
            <!-- Add Instagram and Facebook icons with links -->
            <a href="<?php echo $instagram; ?>" target="_blank" class="btn btn-primary mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                    <path d="M11 0H5C2.239 0 0 2.239 0 5v6c0 2.761 2.239 5 5 5h6c2.761 0 5-2.239 5-5V5C16 2.239 13.761 0 11 0zM8 12.5A4.502 4.502 0 0 1 3.5 8 4.502 4.502 0 0 1 8 3.5 4.502 4.502 0 0 1 12.5 8 4.502 4.502 0 0 1 8 12.5zM12 4.145a.855.855 0 1 1 0-1.71.855.855 0 0 1 0 1.71zM8 10a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5.22-8.008a1.472 1.472 0 0 1 1.477 1.477v10.062a1.472 1.472 0 0 1-1.477 1.477H2.778a1.472 1.472 0 0 1-1.477-1.477V2.47A1.472 1.472 0 0 1 2.778.993h10.442zM8 1.334a6.672 6.672 0 0 0-6.667 6.666A6.672 6.672 0 0 0 8 14.667a6.672 6.672 0 0 0 6.667-6.667A6.672 6.672 0 0 0 8 1.334z"/>
                </svg> <?php echo '@' . $row['instagram']; ?>
            </a>
            <a href="<?php echo $facebook; ?>" target="_blank" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                    <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                </svg> <?php echo  $row['facebook']; ?>
            </a>
             </div>
              </div>
</div>

        <div class="divider jumbotron">
     <div class="ukm-info">
           <!-- Visi -->
           <h3>Visi</h3>
        <p style="text-align: center; font-style: italic;"><?php echo $visi; ?></p>

        <!-- Misi -->
        <h3>Misi</h3>
        <p style="text-align: center; font-style: italic;"><?php echo $misi; ?></p>
        </div>

  </div>
		</div>
    <div class="card shadow mb-4">
    <div class="container">
    <h2 class="h2-struktur">Struktur Organisasi UKM</h2>
        <div class="ukm-button-grid">
        <button id="lihat-sk-button" class="sk-button" title="Tekan tombol untuk melihat SK UKM">
            <i class="fas fa-file-alt"></i>
        </button>
    <p><?php echo $nama_ukm; ?></p>
    <?php
    $id_ukm_target = '$id_ukm';

    echo "<table>";
    foreach ($jabatan as $id_jabatan => $nama_jabatan) {
        echo "<tr><th colspan='2' class='table-heading'>$nama_jabatan</th></tr>";
        foreach ($struktur[$id_jabatan] as $id_ukm => $anggota) {
            if ($id_ukm === $id_ukm_target) {
                foreach ($anggota as $data) {
                    $nim = $data['nim'];
                    $nama_lengkap = $data['nama_lengkap'];
                    echo "<tr><td>$nama_lengkap</td><td>$nim</td></tr>";
                }
            }
        }
    }
    echo "</table>";
    ?>
</div>



</div>
	</div>
	</div>
  </div>
    <footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
</svg> Website</a> 
| Connect with us on <a href="https://www.facebook.com/stmikkomputama"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
  <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
</svg> Facebook</a></footer>
</body>
</html>