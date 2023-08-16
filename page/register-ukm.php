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

// Fungsi logout
function logout() {
    // Menghapus semua data session
    session_unset();
    // Menghancurkan session
    session_destroy();
    // Mengarahkan pengguna ke beranda.php setelah logout
    header("Location: beranda.php");
    exit();
}

// Memeriksa apakah tombol logout diklik
if (isset($_GET['logout'])) {
    // Memanggil fungsi logout
    logout();
}

// Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM tab_user WHERE id_user = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


$query = "SELECT id_ukm, nama_ukm FROM tab_ukm";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

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
  $id_user = $_POST['id_user'];
  $nama_lengkap = $_POST['nama_lengkap'];
  $semester = $_POST['semester'];
  $prodi = $_POST['prodi'];
  $id_ukm = $_POST['id_ukm'];
  $nama_ukm = $_POST['nama_ukm'];
  $email = $_POST['email'];
  $no_hp = $_POST['no_hp'];
  $alasan = $_POST['alasan'];


  // Generate 4 digit angka acak
  $randomDigits = rand(1000, 9999);

  $id_calabar = $id_user . $randomDigits;

   // Pastikan form di-submit dan kedua file berhasil di-upload sebelum melanjutkan
   if (isset($_FILES["pasfoto"]) && isset($_FILES["foto_ktm"])) {
    $pasfoto_dir = "../assets/images/pasfoto/";
    $ktm_dir = "../assets/images/ktm/";

    // Pastikan folder penyimpanan tersedia, jika belum maka buat folder tersebut
    if (!file_exists($pasfoto_dir)) {
        mkdir($pasfoto_dir, 0777, true);
    }
    if (!file_exists($ktm_dir)) {
        mkdir($ktm_dir, 0777, true);
    }

    $pasfoto_tmp_name = $_FILES['pasfoto']['tmp_name'];
    $pasfoto_extension = pathinfo($_FILES['pasfoto']['name'], PATHINFO_EXTENSION);
    $nama_pasfoto = $id_user . "_" . $nama_lengkap . "." . $pasfoto_extension;
    $foto_ktm_tmp_name = $_FILES['foto_ktm']['tmp_name'];
    $foto_ktm_extension = pathinfo($_FILES['foto_ktm']['name'], PATHINFO_EXTENSION);
    $nama_foto_ktm = $id_user . "_" . $nama_lengkap . "." . $foto_ktm_extension;

    // Pindahkan file yang di-upload ke folder tujuan
    if (move_uploaded_file($_FILES["pasfoto"]["tmp_name"], $pasfoto_dir . $nama_pasfoto) &&
        move_uploaded_file($_FILES["foto_ktm"]["tmp_name"], $ktm_dir . $nama_foto_ktm)) {

        // Menyimpan data pendaftaran ke tabel tab_pacab
        $query = "INSERT INTO tab_pacab (id_calabar, id_user, nama_lengkap, semester, prodi, id_ukm, nama_ukm, email, no_hp, pasfoto, foto_ktm, alasan) 
                  VALUES ('$id_calabar','$id_user', '$nama_lengkap', '$semester', '$prodi', '$id_ukm', '$nama_ukm', '$email', '$no_hp', '$nama_pasfoto', '$nama_foto_ktm', '$alasan')";

        // Menjalankan query
        if (mysqli_query($conn, $query)) {
            // Pendaftaran berhasil, simpan id_calabar ke dalam session
            $_SESSION['id_calabar'] = $id_calabar;
           
            
            header("Location: sukses_register_ukm.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // Tampilkan pesan error jika terjadi masalah saat meng-upload
        echo "Terjadi kesalahan saat meng-upload file.";
    }
}
}
// Mendapatkan data ID UKM dan nama UKM dari tabel tab_ukm
$query_ukm = "SELECT id_ukm, nama_ukm FROM tab_ukm";
$result_ukm = mysqli_query($conn, $query_ukm);
$query_bg = "SELECT bg_login FROM tab_beranda LIMIT 1"; // Retrieve the first row
$result_bg = mysqli_query($conn, $query_bg);
if ($result_bg && mysqli_num_rows($result_bg) > 0) {
    $row_bg = mysqli_fetch_assoc($result_bg);
    $background_image_filename = $row_bg["bg_login"];

    // Construct the background image URL
    $background_image_url = "../assets/images/bg/" . $background_image_filename;
} else {
    $background_image_url = ""; // Set a default image URL if not found
}
$query_logo = "SELECT logo_siukm FROM tab_profil LIMIT 1";
$result_logo = mysqli_query($conn, $query_logo);

if ($result_logo && mysqli_num_rows($result_logo) > 0) {
    $row_logo = mysqli_fetch_assoc($result_logo);
    $logo_image_path = $row_logo["logo_siukm"];
} else {
    $logo_image_path = ""; // Set a default logo path if not found
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Pendaftaran UKM - SIUKM STMIK KOMPUTAMA MAJENANG</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="../assets/js/script.js"></script>
  <script>
    // Function to show snackbar
    function showSnackbar() {
        var snackbar = document.getElementById("snackbar");
        snackbar.className = "show";
        setTimeout(function() {
            snackbar.className = snackbar.className.replace("show", "");
        }, 3000);
    }
</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>

/* Style untuk label */
label {
  font-size: 18px;
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
}
.button-container {
    display: flex;
    justify-content: center; /* Rata tengah secara horizontal */
    align-items: center; /* Rata tengah secara vertikal */
  }

  .button-container button {
    margin: 0 10px; /* Spasi antara tombol */
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
  opacity: 0.90;
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
    #nim {
            display: none;
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
  // Get the selected option element
  var selectedOption = select.options[select.selectedIndex];
      // Mengirim permintaan AJAX ke server
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          // Mengambil respons dari server
          var nama_ukm = this.responseText;

          // Mengatur nilai field nama_ukm dengan respons dari server
          nama_ukmField.value = selectedOption.text;
        }
      };
      xhttp.open("GET", "get_nama_ukm.php?id_ukm=" + id_ukm, true);
      xhttp.send();
    }
  </script>
</head>
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
    <body style="background-image: url('<?php echo $background_image_url; ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="container" style="margin-top: 75px; margin-bottom: 75px;">
        <h2 style="text-align: center;">Form Pendaftaran Anggota</h2>
        <h3 style="text-align: center;">Unit Kegiatan Mahasiswa</h3>
        <form method="POST" action="" enctype="multipart/form-data">
            <div>
                <label for="id_user">ID User</label>
                <input type="text" name="id_user" value="<?php echo $_SESSION['id_user']; ?>" readonly>
            </div>
            <?php
            // Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
            $userId = $_SESSION['id_user'];
            $query = "SELECT * FROM tab_user WHERE id_user = '$userId'";

            // Mengeksekusi query
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            ?>
            <div>
                <label for="nama_lengkap">*Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="<?php echo $row['nama_lengkap']; ?>" required readonly>
            </div>
            <div>
            <div>
    <label for="semester">*Semester</label>
    <select id="semester" name="semester" required>
        <option value="" <?php echo ($row['semester'] === null) ? 'selected' : ''; ?>>--Pilih Semester--</option>
        <?php
        // Loop untuk menampilkan opsi angka 1 hingga 14
        for ($i = 1; $i <= 14; $i++) {
            // Periksa apakah nilai saat ini cocok dengan nilai dari database, jika cocok, tandai sebagai selected
            $selected = ($i == $row['semester']) ? 'selected' : '';
            echo "<option value='$i' $selected>$i</option>";
        }
        ?>
    </select>
</div>

<div>
    <label for="prodi">*Program Studi</label>
    <select id="prodi" name="prodi" required>
        <option value="" <?php echo ($row['prodi'] === null) ? 'selected' : ''; ?>>--Pilih Prodi--</option>
        <?php
        // Daftar program studi yang tersedia
        $prodi_options = array("Teknik Informatika", "Sistem Informasi");
        
        // Loop untuk menampilkan opsi program studi
        foreach ($prodi_options as $prodi) {
            // Periksa apakah nilai saat ini cocok dengan nilai dari database, jika cocok, tandai sebagai selected
            $selected = ($prodi == $row['prodi']) ? 'selected' : '';
            echo "<option value='$prodi' $selected>$prodi</option>";
        }
        ?>
    </select>
</div>


            <div class="form-group">
            <label for="email">*Email</label>
            <input type="text" id="email" name="email" value="<?php echo $row['email']; ?>" required>
        </div>
        <div class="form-group">
    <label for="no_hp">*Nomor HP</label>
    <input type="text" id="no_hp" name="no_hp" value="<?php echo $row['no_hp']; ?>" required>
</div>

<script>
    document.getElementById('no_hp').addEventListener('input', function () {
        // Hapus semua karakter selain angka
        this.value = this.value.replace(/\D/g, '');

        // Batasi panjang input menjadi maksimal 13 karakter
        if (this.value.length > 13) {
            this.value = this.value.slice(0, 13);
        }
    });
</script>

        <div class="form-group">
                            <label for="id_ukm">*Nama UKM</label>
                            <select class="form-control" name="id_ukm" id="id_ukm_dropdown" required onchange="updateNamaUKM(this)">
                        <option value="">Pilih Nama UKM</option>
                        <?php
                        // Fetch data from the tab_ukm table and populate the dropdown options
                        $ukmQuery = "SELECT id_ukm, nama_ukm FROM tab_ukm";
                        $ukmResult = mysqli_query($conn, $ukmQuery);

                        while ($ukmRow = mysqli_fetch_assoc($ukmResult)) {
                            echo '<option value="' . $ukmRow['id_ukm'] . '">' . $ukmRow['nama_ukm'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" id="nama_ukm" name="nama_ukm" class="form-control">
                <div class="form-group">
          <label for="pasfoto">*Pasfoto</label>
          <input type="file" id="pasfoto" name="pasfoto" accept="image/*" onchange="showPreview(this, 'pasfotoPreview');" <?php if ($row['pasfoto']) echo "required"; ?>>
          <?php if ($row['pasfoto']): ?>
              <img id="pasfotoPreview" src="<?php echo $row['pasfoto']; ?>" alt="Pasfoto Preview" style="max-width: 100px; max-height: 100px;">
          <?php else: ?>
              <img id="pasfotoPreview" src="#" alt="Pasfoto Preview" style="max-width: 100px; max-height: 100px; display: none;">
          <?php endif; ?>
      </div>
      <div class="form-group">
          <label for="foto_ktm">*Foto KTM</label>
          <input type="file" id="foto_ktm" name="foto_ktm" accept="image/*" onchange="showPreview(this, 'fotoKtmPreview');" <?php if ($row['foto_ktm']) echo "required"; ?>>
          <?php if ($row['foto_ktm']): ?>
              <img id="fotoKtmPreview" src="<?php echo $row['foto_ktm']; ?>" alt="Foto KTM Preview" style="max-width: 100px; max-height: 100px;">
          <?php else: ?>
              <img id="fotoKtmPreview" src="#" alt="Foto KTM Preview" style="max-width: 100px; max-height: 100px; display: none;">
          <?php endif; ?>
      </div>
        <div class="form-group">
      <label for="alasan">*Alasan Bergabung</label>
      <textarea id="alasan" name="alasan" placeholder="Masukkan alasan Anda bergabung minimal 50 karakter dan maksimal 250 karakter" required minlength="50" maxlength="250"></textarea>
      <div class="invalid-feedback">
        Alasan harus memiliki panjang antara 50 dan 250 karakter.
      </div>
    </div>
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
</div>
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p><b>Syarat dan Ketentuan Anggota UKM di STMIK Komputama Majenang:</b><br>
    <br>
      1. Kualifikasi Anggota:<br>
          a. Mahasiswa aktif di STMIK Komputama Majenang.<br>
          b. Tidak ada batasan jurusan atau program studi tertentu.<br>
          c. Maksimal mahasiswa semester 14.<br>
      2. Pendaftaran:<br>
          a. Calon anggota wajib mengisi formulir pendaftaran yang disediakan oleh SIUKM.<br>
          b. Pendaftaran wajib mengerjakan soal TPA yang diberikan oleh UKM.<br>
          c. Calon anggota wajib melampirkan foto kartu tanda mahasiswa sebagai bukti keaktifan.<br>

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
        <p><b>Kebeneran Data</b></p>
        <p>Persetujuan untuk mengisi form pendaftaran dengan data yang sebenarnya dan mengakui sebagai pemilik data tersebut adalah langkah yang penting dalam proses pendaftaran. Dengan memberikan persetujuan ini, calon anggota UKM di STMIK Komputama Majenang menyatakan bahwa informasi yang mereka berikan adalah akurat dan valid. Mereka juga menegaskan tanggung jawab mereka sebagai pemilik data yang disampaikan.

Hal ini penting untuk menjaga kepercayaan dan integritas dalam pengelolaan data. Dalam hal terbukti bahwa calon anggota telah menyalahi persetujuan tersebut dan menggunakan data orang lain secara tidak sah, konsekuensi sanksi akan diberlakukan. Sanksi ini bertujuan sebagai tindakan disipliner yang mendorong calon anggota untuk bertanggung jawab atas tindakan mereka dan mencegah penyalahgunaan data.

Sanksi yang dikenakan dapat bervariasi tergantung pada kebijakan dan peraturan yang berlaku di STMIK Komputama Majenang. Beberapa kemungkinan sanksi dapat mencakup teguran tertulis, larangan partisipasi dalam kegiatan UKM, atau bahkan pembatalan pendaftaran calon anggota tersebut. Dengan menerapkan sanksi ini, diharapkan calon anggota UKM menjadi lebih sadar akan pentingnya menjaga keamanan dan privasi data, serta mendorong mereka untuk bertindak dengan integritas dan kejujuran.

Dengan demikian, persetujuan untuk mengisi form pendaftaran dengan data yang sebenarnya dan pengakuan sebagai pemilik data yang disampaikan adalah langkah yang penting untuk memastikan transparansi dan keamanan dalam pengelolaan data. Sanksi yang dikenakan atas penyalahgunaan data orang lain bertujuan untuk mencegah pelanggaran dan mempromosikan sikap bertanggung jawab dalam penggunaan informasi pribadi.</p>
        <p><b>Keamanan Data</b></p>
        <p>Kami ingin menekankan bahwa keamanan data menjadi prioritas utama dalam proses pendaftaran di STMIK Komputama Majenang. Kami menjamin bahwa data yang diisi dalam form pendaftaran akan disimpan dengan baik dan tidak akan disebarluaskan dengan cara yang melanggar aturan yang berlaku. Kami menjunjung tinggi prinsip privasi dan keamanan data, serta berkomitmen untuk mematuhi UU ITE (Undang-Undang Informasi dan Transaksi Elektronik) yang berlaku di Indonesia.

Menurut UU ITE, setiap individu memiliki hak privasi terhadap data pribadinya. Oleh karena itu, data yang diungkapkan dalam form pendaftaran akan dikelola secara rahasia dan hanya digunakan untuk tujuan yang relevan dengan proses pendaftaran anggota UKM. Data tersebut tidak akan disebarkan kepada pihak ketiga tanpa izin atau melanggar aturan yang berlaku.

Kami juga melaksanakan langkah-langkah keamanan yang tepat untuk melindungi data yang disimpan. Penggunaan sistem keamanan yang mutakhir dan pengaturan akses yang ketat merupakan bagian dari upaya kami untuk menjaga kerahasiaan dan integritas data yang dikumpulkan. Kami berkomitmen untuk mengambil tindakan yang diperlukan guna mencegah akses tidak sah, penggunaan yang tidak diizinkan, atau perubahan data yang melanggar privasi calon anggota UKM.

Dalam hal terjadi pelanggaran atau penyalahgunaan data yang melanggar UU ITE, kami akan mengikuti prosedur hukum yang berlaku dan melibatkan pihak berwenang yang berkompeten untuk menindaklanjuti. Kami mengimbau kepada calon anggota UKM untuk bertanggung jawab atas informasi yang mereka berikan dan menghormati hak privasi serta keamanan data diri sendiri dan orang lain.

Dengan demikian, kami memastikan bahwa keamanan data menjadi prioritas utama, dan segala langkah yang kami ambil bertujuan untuk mematuhi aturan dan regulasi, termasuk UU ITE, serta melindungi kepentingan dan privasi calon anggota UKM di STMIK Komputama Majenang.</p>
<p><b>Mengerjakan Soal Tes Potensi Akademik</b></p>
        <p>Ketersediaan calon anggota UKM di STMIK Komputama Majenang harus memenuhi persyaratan penting, yaitu mengisi dan mengerjakan soal tes potensi akademik. Tes potensi akademik ini dijadikan sebagai salah satu langkah dalam proses pendaftaran agar dapat menjadi calon anggota UKM. Hal ini dilakukan sebagai upaya untuk mengidentifikasi kemampuan dan potensi calon anggota dalam bidang akademik, sehingga dapat memastikan bahwa mereka memiliki dasar pengetahuan yang cukup untuk aktif berpartisipasi dalam kegiatan UKM yang berfokus pada bidang komputer dan teknologi informasi.

Melalui tes potensi akademik, calon anggota diharapkan dapat menunjukkan kemampuan mereka dalam menganalisis, berpikir logis, serta memecahkan masalah secara efektif. Soal-soal yang diajukan dalam tes ini mencakup berbagai aspek seperti logika, matematika, pemahaman teks, dan pengetahuan umum. Dengan mengisi dan mengerjakan soal tes potensi akademik, pihak UKM dapat mengidentifikasi calon anggota yang memiliki potensi dan kemampuan yang sesuai dengan kebutuhan UKM tersebut.</p>
  </div>
</div>

<div id="snackbar">NIM harus terdiri dari minimal 9 digit angka</div>

 <script>
  function showSnackbar() {
    var snackbar = document.getElementById("snackbar");
    snackbar.className = "show";
    setTimeout(function() {
      snackbar.className = snackbar.className.replace("show", "");
    }, 3000);
  }
</script>
</body>
<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
</svg> Website</a> 
| Connect with us on <a href="https://www.facebook.com/stmikkomputama"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
  <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
</svg> Facebook</a></footer>
<script>
function showPreview(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = "none";
    }
}
</script>


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

    // Fungsi untuk logout
    function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
</script>
<script>
const idUkmSelect = document.getElementById("id_ukm_dropdown");
const namaUkmField = document.getElementById("nama_ukm");

idUkmSelect.addEventListener("change", function() {
    const selectedOption = idUkmSelect.options[idUkmSelect.selectedIndex];
    const namaUkm = selectedOption.text; // Get the text of the selected option
    namaUkmField.value = namaUkm; // Set the value of the hidden input field
});
</script>
</html>
