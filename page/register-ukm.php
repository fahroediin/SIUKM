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

// Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM tab_user WHERE id_user = '$userId'";

// Mengeksekusi query
$result = mysqli_query($conn, $query);

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $id_user = $_POST['id_user'];
}

// Menyimpan id_calabar ke dalam session
$_SESSION['id_calabar'] = $id_user;

// Mendapatkan data ID UKM dan nama UKM dari tabel tab_ukm
$query = "SELECT id_ukm, nama_ukm FROM tab_ukm";
$result = mysqli_query($conn, $query);

// Inisialisasi variabel untuk opsi combobox
$options = "";

// Buat array untuk menyimpan data nama_ukm berdasarkan id_ukm
$namaUKM = array();
while ($row = mysqli_fetch_assoc($result)) {
    $id_ukm = $row['id_ukm'];
    $nama_ukm = $row['nama_ukm'];
    $namaUKM[$id_ukm] = $nama_ukm;
}

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $id_calabar = $_POST['id_calabar'];
    $id_user = $_POST['id_user'];
    $nama_depan = $_POST['nama_depan'];
    $nama_belakang = $_POST['nama_belakang'];
    $nim = $_POST['nim'];
    $semester = $_POST['semester'];
    $prodi = $_POST['prodi'];
    $id_ukm = $_POST['id_ukm'];
    $nama_ukm = $_POST['nama_ukm'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $pasfoto = $_POST['pasfoto'];
    $foto_ktm = $_POST['foto_ktm'];
    $alasan = $_POST['alasan'];
    

    // Generate 4 digit angka acak
    $randomDigits = rand(1000, 9999);

    // Menggabungkan NIM dengan angka acak
    $id_calabar = $nim . $randomDigits;

    // Menyimpan data pendaftaran ke tabel tab_pacab
    $query = "INSERT INTO tab_pacab (id_calabar, id_user, nama_depan, nama_belakang, nim, semester, prodi, id_ukm, nama_ukm, email, no_hp, pasfoto, foto_ktm, alasan) 
             VALUES ('$id_calabar','$id_user', '$nama_depan', '$nama_belakang', '$nim', '$semester', '$prodi', '$id_ukm', '$nama_ukm', '$email', '$no_hp', '$pasfoto', '$foto_ktm', '$alasan')";

    // Menjalankan query
    if (mysqli_query($conn, $query)) {
        // Pendaftaran berhasil, redirect ke halaman test-calabar.php
        header("Location: test-calabar.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }

    // Menutup koneksi database
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>SISTEM INFORMASI UKM STMIK KOMPUTAMA MAJENANG</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
  <script src="../assets/js/script.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
<style>

/* Style untuk label */
label {
  font-size: 18px;
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
}

/* Style untuk input text */
input[type=text],  textarea {
  padding: 10px;
  border: none;
  border-radius: 3px;
  width: 100%;
  margin-bottom: 20px;
  box-shadow: 0 0 5px rgba(0,0,0,0.1);
}

/* Style untuk select */
select {
  padding: 10px;
  border: none;
  border-radius: 3px;
  width: 100%;
  margin-bottom: 20px;
  box-shadow: 0 0 5px rgba(0,0,0,0.1);
  appearance: none;
  -webkit-appearance: none;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px" fill="%23333"><path d="M7 10l5 5 5-5z"/></svg>');
  background-repeat: no-repeat;
  background-position-x: 100%;
  background-position-y: 50%;
}

/* Style untuk tombol */
button[type=submit] {
  padding: 10px 20px;
  border: none;
  border-radius: 3px;
  background-color: #007bff;
  color: #fff;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
}

button[type=submit]:hover {
  background-color: #0056b3;
}

/* Style untuk tombol "Batal" */
button[type=reset] {
  padding: 10px 20px;
  border: none;
  border-radius: 3px;
  background-color: #007bff;
  color: #fff;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-left: 10px;
}

button[type=reset]:hover {
  background-color: #0056b3;
}

/* Style untuk container */
.container {
  margin-top: 75px;
  max-width: 500px;
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  margin-left: auto;
  margin-right: auto;
}

.checkbox-container {
    display: flex;
    align-items: center;
  }

  /* Style untuk tombol Tampilkan Modal */
  button#modalButton {
    padding: 10px 20px;
    border: none;
    
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-left: 10px;
  }

  button#modalButton:hover {
    background-color: #0056b3;
  }

/* Style untuk caption modal */
.modal-caption {
  padding: 10px 5px;
  background-color: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
}

.modal-caption:hover {
  text-decoration: underline;
}

/* Style untuk modal */
.modal {
  display: none; /* Sembunyikan secara default */
  position: fixed; /* Tetapkan posisi modal */
  z-index: 1; /* Atur tumpukan z-index agar modal muncul di atas konten lainnya */
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto; /* Tampilkan scrollbar jika konten terlalu panjang */
  background-color: rgba(0, 0, 0, 0.5); /* Warna latar belakang transparan */
}

/* Style untuk konten modal */
.modal-content {
  background-color: #fefefe;
  margin: 10% auto; /* Posisikan di tengah vertikal dan horizontal */
  padding: 20px;
  border: 1px solid #888;
  width: 65%; /* Lebar konten modal */
  max-height: 80vh; /* Ketinggian maksimum konten modal */
  overflow-y: auto; /* Tampilkan scrollbar jika konten terlalu panjang */
}

/* Style untuk tombol penutup modal */
.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

  #alasan {
  height: 200px;
  width: 450px;
}

 /* Styles for snackbar */
 #snackbar {
      visibility: hidden;
      min-width: 250px;
      margin-left: -125px;
      background-color: #333;
      color: #fff;
      text-align: center;
      border-radius: 2px;
      padding: 16px;
      position: fixed;
      z-index: 1;
      left: 50%;
      bottom: 30px;
      font-size: 17px;
    }

    #snackbar.show {
      visibility: visible;
      -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
      animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }

    @-webkit-keyframes fadein {
      from {bottom: 0; opacity: 0;}
      to {bottom: 30px; opacity: 1;}
    }

    @keyframes fadein {
      from {bottom: 0; opacity: 0;}
      to {bottom: 30px; opacity: 1;}
    }

    @-webkit-keyframes fadeout {
      from {bottom: 30px; opacity: 1;}
      to {bottom: 0; opacity: 0;}
    }

    @keyframes fadeout {
      from {bottom: 30px; opacity: 1;}
      to {bottom: 0; opacity: 0;}
    }

</style>
<script>
    // Mendefinisikan fungsi JavaScript untuk memperbarui field nama_ukm
    function updateNamaUKM(select) {
      var id_ukm = select.value;
      var nama_ukmField = document.getElementById("nama_ukm");

      // Mengirim permintaan AJAX ke server
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          // Mengambil respons dari server
          var nama_ukm = this.responseText;

          // Mengatur nilai field nama_ukm dengan respons dari server
          nama_ukmField.value = nama_ukm;
        }
      };
      xhttp.open("GET", "get_nama_ukm.php?id_ukm=" + id_ukm, true);
      xhttp.send();
    }

       // Function to show snackbar
       function showSnackbar() {
      var snackbar = document.getElementById("snackbar");
      snackbar.className = "show";
      setTimeout(function(){ snackbar.className = snackbar.className.replace("show", ""); }, 3000);
    }
  </script>
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

  <div class="container" style="margin-top: 75px;">
  <h1>Form Pendaftaran Anggota UKM Baru</h1>
  
  <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div>
        <label for="id_user">ID User</label>
          <input type="text" name="id_user" value="<?php echo $_SESSION['id_user']; ?>" readonly>
                </div>
                <div>
                    <label for="nama_depan">Nama Depan:</label>
                    <input type="text" id="nama_depan" name="nama_depan" required placeholder="Masukkan nama depan">
                </div>
                <div>
                    <label for="nama_belakang">Nama Belakang:</label>
                    <input type="text" id="nama_belakang" name="nama_belakang" placeholder="Masukkan nama belakang">
                </div>
                <div>
                    <label for="nim">Masukkan NIM:</label>
                    <input type="text" id="nim" name="nim" required placeholder="Masukkan NIM">
                </div>
                <div>
          <label for="semester">Masukkan semester:</label>
          <select id="semester" name="semester" required>
          <option value="" selected disabled>Pilih semester</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
          </select>
        </div>

        <div>
        <label for="prodi">Program studi:</label>
        <select id="prodi" name="prodi" required>
        <option value="" selected disabled>Pilih program studi</option>
          <option value="sistem_informasi">Sistem Informasi</option>
          <option value="teknik_informatika">Teknik Informatika</option>
        </select>

        </div>
        <!-- Tambahkan div untuk combobox ID UKM -->
        <div>
    <label for="id_ukm">ID UKM:</label>
    <select id="id_ukm" name="id_ukm" required onchange="updateNamaUKM(this)">
      <option value="" selected disabled>Pilih ID UKM</option>
      <?php
      // Membuat opsi combobox dari hasil query
      foreach ($namaUKM as $id_ukm => $nama_ukm) {
        echo "<option value='$id_ukm'>$id_ukm</option>";
      }
      ?>
    </select>
  </div>

  <div class="form-group">
    <label for="nama_ukm">Nama UKM:</label>
    <input type="text" id="nama_ukm" name="nama_ukm" readonly>
  </div>
  <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required placeholder="Masukkan email">
        </div>
        <div class="form-group">
            <label for="no_hp">Nomor HP:</label>
            <input type="text" id="no_hp" name="no_hp" required placeholder="Masukkan nomor handphone anda">
        </div>
        <div class="form-group">
            <label for="pasfoto">Upload pas foto:</label>
            <input type="file" id="pasfoto" name="pasfoto" accept="image/*">
        </div>
        <div class="form-group">
            <label for="foto_ktm">Upload foto KTM:</label>
            <input type="file" id="foto_ktm" name="foto_ktm" accept="image/*">
        </div>
        <div class="form-group">
        <label for="alasan">Alasan Bergabung:</label>
    <textarea id="alasan" name="alasan" placeholder="Masukkan alasan Anda bergabung" required></textarea>
<div class="checkbox-container">
  <input type="checkbox" id="persetujuan" name="persetujuan" required>
  <span class="modal-caption" id="modalButton">Syarat dan Persetujuan</span>

</div>
<div class="button-container">
  <button type="submit">DAFTAR</button>
  <button type="reset">CLEAR</button>
</div>

</div>
</div>
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p><b>Syarat dan Ketentuan Anggota UKM di STMIK Komputama Majenang:</b><br>
    <br>
      1. Kualifikasi Anggota:<br>
          a. Mahasiswa aktif di STMIK Komputama Majenang.<br>
          b. Tidak ada batasan jurusan atau program studi tertentu.<br>
          c. Tidak ada batasan tingkat semester tertentu.<br>
      2. Pendaftaran:<br>
          a. Calon anggota wajib mengisi formulir pendaftaran yang disediakan oleh UKM.<br>
          b. Pendaftaran wajib mengerjakan soal TPA yang diberikan oleh UKM.<br>
          c. Calon anggota wajib melampirkan fotokopi kartu mahasiswa sebagai bukti keaktifan.<br>

      3. Kepatuhan Hukum:<br>
          a. Seluruh anggota UKM wajib mematuhi undang-undang yang berlaku di negara Indonesia.<br>
          b. Dilarang melakukan tindakan yang melanggar hukum, termasuk penyebaran konten ilegal atau melanggar hak kekayaan intelektual.<br>

      4. Kehadiran dan Partisipasi:<br>
          a. Anggota wajib hadir dalam rapat-rapat rutin yang diadakan oleh UKM.<br>
          b. Anggota wajib berpartisipasi dalam kegiatan-kegiatan yang diorganisir oleh UKM, sesuai dengan kemampuan dan minat masing-masing.<br>

      5. Kedisiplinan:<br>
          a. Anggota wajib menjaga sikap disiplin selama menjadi anggota UKM.<br>
          b. Anggota diharapkan tiba tepat waktu dalam setiap kegiatan yang diadakan oleh UKM.<br>
          c. Dalam hal anggota tidak dapat menghadiri kegiatan, diharapkan memberikan pemberitahuan kepada pengurus UKM.<br>

      6. Etika dan Sikap:<br>
          a. Anggota diharapkan menjunjung tinggi etika dan sikap yang baik dalam berinteraksi dengan anggota UKM lainnya, dosen, dan pihak lain yang terkait.<br>
          b. Dilarang melakukan tindakan diskriminasi, pelecehan, atau perilaku yang tidak pantas.<br>

      7. Keanggotaan:<br>
          a. Keanggotaan UKM bersifat sukarela dan dapat ditarik kembali oleh anggota atau pengurus UKM.<br>
          b. Anggota yang melanggar ketentuan-ketentuan ini dapat dikenai sanksi, termasuk penangguhan atau pemecatan dari keanggotaan UKM.<br>

      8. Perubahan Ketentuan:<br>
          a. Ketentuan dan syarat keanggotaan UKM dapat berubah sewaktu-waktu dengan pemberitahuan sebelumnya kepada anggota.<br>
          b. Perubahan akan diberlakukan setelah disetujui oleh pengurus UKM dan pihak yang berwenang.<br>
             <br>
          Ketentuan di atas disusun untuk memastikan keberlangsungan UKM sesuai dengan undang-undang yang berlaku dan memberikan
          lingkungan yang kondusif untuk pengembangan anggotanya. Anggota diharapkan mematuhi ketentuan-ketentuan ini agar UKM dapat 
          berjalan dengan baik dan memberikan manfaat yang optimal bagi anggotanya dan masyarakat.
        </p>
  </div>
</div>
 <div id="snackbar">Pendaftaran berhasil!</div>
</body>
<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
</svg> Website</a> 
| Connect with us on <a href="https://www.facebook.com/stmikkomputama"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
  <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
</svg> Facebook</a></footer>
<script>
  // Mendapatkan elemen modal, tombol, dan span penutup modal
  var modal = document.getElementById("myModal");
  var btn = document.getElementById("modalButton");
  var span = document.getElementsByClassName("close")[0];

  // Menampilkan modal saat tombol diklik
  btn.onclick = function() {
    modal.style.display = "block";
  }

  // Menyembunyikan modal saat span penutup diklik
  span.onclick = function() {
    modal.style.display = "none";
  }

  // Menyembunyikan modal saat area di luar modal diklik
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }

</script>
</html>
