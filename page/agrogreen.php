<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Inisialisasi variabel pesan error
$error = '';

// Mengambil data struktur organisasi dari tabel tab_strukm
$query = "SELECT * FROM tab_strukm";
$result = mysqli_query($conn, $query);

// Memeriksa apakah query berhasil dieksekusi
if (!$result) {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
    exit();
}

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
?>

<!DOCTYPE html>
<html>
<head>
	<title>AGRO - SIUKM STMIK KOMPUTAMA MAJENANG</title>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
    }

    th.table-heading {
        background-color: #f2f2f2;
        text-align: center;
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

	<div class="container">
        <h1>Pertanian</h1>
        <div class="ukm-info">
            <div class="ukm-logo">
                <img src="..\assets\images\logoukm\agro.jpg" alt="Logo UKM Pertanian" class="ukm-logo">
            </div>
            <div>
                <h2>Sejarah Agro Green</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae lectus sit amet risus porttitor venenatis sed id nunc. Nulla ut ipsum dapibus, eleifend lectus a, iaculis massa. Morbi malesuada nulla leo, ac interdum lacus facilisis id. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Integer consequat elit quis dapibus consequat. Vivamus in eros in nisi semper finibus id et enim. Nam facilisis, massa non suscipit facilisis, nisi diam scelerisque dolor, eu auctor enim nunc id mauris. Nullam euismod libero at tellus sagittis, vitae semper metus malesuada.</p>
            </div>
        </div>

        <div class="divider jumbotron">

        <div class="ukm-info">
            
        <div class="ukm-info">
            <h2>Visi Misi UKM</h2>
            <p>Visi:</p>
            <ul>
                <li>Visi UKM 1</li>
                <li>Visi UKM 2</li>
                <li>Visi UKM 3</li>
            </ul>
            <p>Misi:</p>
            <ul>
                <li>Misi UKM 1</li>
                <li>Misi UKM 2</li>
                <li>Misi UKM 3</li>
            </ul>
        </div>
        </div>
		</div>
		<div class="divider">
    <div class="container">
    <h2 class="h2-struktur">Struktur Organisasi UKM</h2>
    <?php
    $id_ukm_target = 'agrogreen';

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
    <footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
</svg> Website</a> 
| Connect with us on <a href="https://www.facebook.com/stmikkomputama"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
  <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
</svg> Facebook</a></footer>
</body>
</html>