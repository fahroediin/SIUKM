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

    // Mendapatkan data terakhir dari tabel tab_pacab
    $query = "SELECT * FROM tab_pacab ORDER BY id_calabar DESC LIMIT 1";
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
    $pasfoto_path = "../assets/images/pasfoto/" . $row['pasfoto'];
    $foto_ktm_path = "../assets/images/ktm/" . $row['foto_ktm'];

    $_SESSION['id_calabar'] = $id_calabar;
    ?>
<!DOCTYPE html>
<html>
<head>
	<title>Sukses Daftar - SIUKM STMIK KOMPUTAMA MAJENANG</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="../assets/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
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
        <img src="../assets/images/sukses.png" alt="Sukses" class="center-image">
        <h1 class="center-text">Registrasi UKM Berhasil</h1>
        <p  class="center-text">Berikut adalah data pendaftaran Anda</p>

        <div class="card">
            <div class="card-body">
            <div class="row mt-4 image-container">
            <div class="col-md-6 text-center">
            <p class="center-text">Pasfoto</p>
            <div class="frame rounded">
                <img src="<?php echo $pasfoto_path; ?>" alt="Pasfoto" width="150">
            </div>
        </div>
        <div class="col-md-6 text-center">
            <p class="center-text">Kartu Tanda Mahasiswa</p>
            <div class="frame">
                <img src="<?php echo $foto_ktm_path; ?>" alt="Foto KTM" width="150">
            </div>
        </div>
            </div>
    <table>
        <tr>
            <td>ID Calon Anggota</td>
            <td> <?php echo $id_calabar; ?></td>
        </tr>
        <tr>
            <td>ID User</td>
            <td> <?php echo $id_user; ?></td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td> <?php echo $nama_lengkap; ?></td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td> <?php echo $prodi; ?></td>
        </tr>
        <tr>
            <td>Semester</td>
            <td> <?php echo $semester; ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td> <?php echo $email; ?></td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td> <?php echo $no_hp; ?></td>
        </tr>
        <tr>
            <td>Nama UKM</td>
            <td> <?php echo $nama_ukm; ?></td>
        </tr>
        </table>
            </div>
            <div class="row">
    <div class="col-md-6">
        <a href="register-ukm.php" class="btn btn-primary btn-block">
            <i class="fas fa-chevron-left"></i> Kembali
        </a>
    </div>
    <div class="col-md-6">
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modalPendaftaran">
                    Lanjutkan Pendaftaran <i class="fas fa-chevron-right"></i>
                </button>
             </div>
           </div>
        </div>
        </div>
        </div>
    </div>
    <!-- Modal for "Lanjutkan Pendaftaran" button -->
<div class="modal fade" id="modalPendaftaran" tabindex="-1" role="dialog" aria-labelledby="modalPendaftaranLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPendaftaranLabel">Satu langkah lagi untuk menyelesaikan pendaftaran Anda bergabung dengan UKM yang Anda minati.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Selanjutnya Anda akan mengerjakan 50 butir soal Tes Potensi Akademik. Kerjakan sebaik mungkin dalam waktu 30 menit.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a href="test-calabar.php" class="btn btn-primary">Kerjakan Soal</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
