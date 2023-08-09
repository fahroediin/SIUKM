<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menonaktifkan pesan error
error_reporting(0);

// Mendapatkan nama depan dari session
$nama_lengkap = $_SESSION["nama_lengkap"];
$level = $_SESSION["level"];

// Inisialisasi variabel pesan error
$error = '';

// Mengambil data prestasi dari tabel tab_prestasi
$query = "SELECT p.id_prestasi, p.nama_prestasi, p.penyelenggara, p.tingkat, p.tgl_prestasi, u.id_ukm, u.nama_ukm FROM tab_prestasi p INNER JOIN tab_ukm u ON p.id_ukm = u.id_ukm";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prestasi - SIUKM STMIK KOMPUTAMA MAJENANG</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-BaFh8/FDd3jxAl2OiD00pM5Y/r5mBRbBIrHwwUsBcnu8V6GwW1vFPTTy3MBLo+U/NWk1x4U+az1qKoHyyhxMQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
					<a class="nav-link active" href="prestasi.php">Prestasi</a>
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
<div class="container">
    <h1>Daftar Prestasi</h1>
    <div class="divider"></div>

   <!-- Filter form -->
   <form method="get">
        <label for="year">Filter by Year:</label>
        <select name="year" id="year">
            <option value="">All</option>
            <?php
            // Get unique years from the tgl_prestasi column
            $year_query = "SELECT DISTINCT YEAR(tgl_prestasi) AS year FROM tab_prestasi ORDER BY year DESC";
            $year_result = mysqli_query($conn, $year_query);
            while ($year_row = mysqli_fetch_assoc($year_result)) {
                echo '<option value="' . $year_row['year'] . '">' . $year_row['year'] . '</option>';
            }
            ?>
        </select>

        <label for="ukm">Filter by UKM:</label>
        <select name="ukm" id="ukm">
            <option value="">All</option>
            <?php
        // Get the filter values from the form submission
        $filter_year = $_GET['year'];
        $filter_ukm = $_GET['ukm'];

        // Add the filters to the SQL query
        $query = "SELECT p.id_prestasi, p.nama_prestasi, p.penyelenggara, p.tingkat, p.tgl_prestasi, p.sertifikat, u.nama_ukm FROM tab_prestasi p INNER JOIN tab_ukm u ON p.id_ukm = u.id_ukm";

        if (!empty($filter_year)) {
            $query .= " WHERE YEAR(p.tgl_prestasi) = '$filter_year'";
        }

        if (!empty($filter_ukm)) {
            $query .= !empty($filter_year) ? " AND u.nama_ukm = '$filter_ukm'" : " WHERE u.nama_ukm = '$filter_ukm'";
        }

        $result = mysqli_query($conn, $query);
            // Get unique UKM names from the tab_ukm table
            $ukm_query = "SELECT DISTINCT nama_ukm FROM tab_ukm ORDER BY nama_ukm";
            $ukm_result = mysqli_query($conn, $ukm_query);
            while ($ukm_row = mysqli_fetch_assoc($ukm_result)) {
                echo '<option value="' . $ukm_row['nama_ukm'] . '">' . $ukm_row['nama_ukm'] . '</option>';
            }
            ?>
        </select>

        <button type="submit" class="btn btn-primary filter-button">
        <i class="fas fa-search"></i> Filter
    </button>
    </form>
    <br>

    <table>
        <tr>
            <th>No.</th>
            <th>Nama Prestasi</th>
            <th>Penyelenggara</th>
            <th>Tingkat</th>
            <th>Tanggal Prestasi</th>
            <th>UKM</th>
            <th>Sertifikat</th>
        </tr>
        <?php
                    // Define Indonesian month names
            $indonesianMonths = array(
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                'Agustus', 'September', 'Oktober', 'November', 'Desember'
            );
            if (mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr data-certificate-url='../assets/images/sertifikat/" . $row['sertifikat'] . "'>";
                    echo "<td>" . $no . "</td>";
                    echo "<td>" . $row['nama_prestasi'] . "</td>";
                    echo "<td>" . $row['penyelenggara'] . "</td>";
                    echo "<td>" . $row['tingkat'] . "</td>";
                    echo "<td>" . date("d", strtotime($row['tgl_prestasi'])) . " " . $indonesianMonths[date("n", strtotime($row['tgl_prestasi'])) - 1] . " " . date("Y", strtotime($row['tgl_prestasi'])) . "</td>";
                    echo "<td>" . $row['nama_ukm'] . "</td>";
                    echo "<td><a class='certificate-link' href='#'><i class='fas fa-file-alt'></i> Lihat Sertifikat</a></td>";
                    echo "</tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='6'>Tidak ada data prestasi.</td></tr>";
            }
            ?>
    </table>
</div>
    </div>
 <!-- Modal for displaying certificate image -->
<div class="modal fade" id="certificateModal" tabindex="-1" role="dialog" aria-labelledby="certificateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="certificateModalLabel">Sertifikat Prestasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img id="certificateImage" src="" alt="Certificate" style="max-width: 720px;">
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Handle click event on table rows
  $("table").on("click", "tr", function() {
    var certificateUrl = $(this).find("td:last-child").text();
    $("#certificateImage").attr("src", "../assets/images/sertifikat/" + certificateUrl);
    $("#certificateModal").modal("show");
  });

    // Handle click event on the certificate link
  $("table").on("click", ".certificate-link", function(event) {
    event.preventDefault();
    var certificateUrl = $(this).closest("tr").data("certificate-url");
    $("#certificateImage").attr("src", certificateUrl);
    $("#certificateModal").modal("show");
  });
  // Handle modal close event
  $("#certificateModal").on("hidden.bs.modal", function() {
    $("#certificateImage").attr("src", "");
  });
});
</script>

</body>
<footer>SIUKM @2023 | Visit our <a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
</svg> Website</a> 
| Connect with us on <a href="https://www.facebook.com/stmikkomputama"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
  <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
</svg> Facebook</a></footer>
</html>