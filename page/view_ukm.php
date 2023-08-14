<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memeriksa apakah pengguna sudah login
session_start();
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}
// Menandai halaman yang aktif
$active_page = 'view_ukm';

// Fungsi logout
function logout()
{
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

// Mendapatkan data dari tabel tab_ukm
$query = "SELECT id_ukm, nama_ukm, logo_ukm, instagram, facebook, sejarah, visi, misi FROM tab_ukm";
$result = mysqli_query($conn, $query);

// Inisialisasi variabel untuk opsi combobox
$options = "";

// Buat array untuk menyimpan data UKM
$ukmData = array();
while ($row = mysqli_fetch_assoc($result)) {
    $ukmData[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data UKM - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Mendefinisikan fungsi JavaScript untuk memperbarui field nama_ukm, sejarah, nama_ketua, nim_ketua, visi, dan misi
    function updateFormData(select) {
      var id_ukm = select.value;
      var nama_ukmField = document.getElementById("nama_ukm");
      var sejarahField = document.getElementById("sejarah");
      var instagramField = document.getElementById("instagram");
      var facebookField = document.getElementById("facebook");
      var visiField = document.getElementById("visi");
      var misiField = document.getElementById("misi");
      var logo_ukmField = document.getElementById("logo_ukm_preview");

      // Mengirim permintaan AJAX ke server
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // Mengambil respons dari server dalam bentuk JSON
            var data = JSON.parse(this.responseText);

            // Mengatur nilai field-field yang sesuai dengan respons dari server
            nama_ukmField.value = data.nama_ukm;
            sejarahField.value = data.sejarah;
            instagramField.value = data.instagram;
            facebookField.value = data.facebook;
            visiField.value = data.visi;
            misiField.value = data.misi;

            // Update the logo preview image
            logo_ukmField.src = "../assets/images/logoukm/" + data.logo_ukm;
        }
    };
    xhttp.open("GET", "get_data_ukm.php?id_ukm=" + id_ukm, true);
    xhttp.send();
}
</script>
</head>
<style>
       .sidebar {
        text-align: center; /* Center the contents horizontally */
    }
    .card {
        width: 50%;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .btn {
        padding: 8px 12px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #0056b3;
    }
    .sidebar img {
        display: block;
        margin: 0 auto;
        margin-bottom: 20px;
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
<div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Data UKM</i></h2>
<a href="pengurus.php" class="btn btn-primary <?php if($active_page == 'kemahasiswaan') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
    <a href="proses_dau_pengurus.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_dau') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_struktur_pengurus.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'struktur') echo 'active'; ?>">Pengurus</a>
    <a href="view_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="view_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_ukm') echo 'active'; ?>">Data UKM</a>
    <a href="view_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_galeri') echo 'active'; ?>">Galeri</a>
    <a href="view_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="view_calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
    <a href="#" class="btn btn-primary" id="logout-btn" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<script>
    // Function to wrap buttons with a border, except for the Logout button
    function wrapButtonsWithBorder() {
        const buttons = document.querySelectorAll('.btn-manajemen');
        buttons.forEach((button) => {
            if (!button.getAttribute('id') || button.getAttribute('id') !== 'logout-btn') {
                button.style.border = '1px solid #ccc';
                button.style.borderRadius = '5px';
                button.style.padding = '8px';
                button.style.margin = '5px';
            }
        });
    }

    // Call the function to apply the border to the buttons
    wrapButtonsWithBorder();
</script>
    <!-- Main content -->
    <body>
    <div class="content">
  
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID UKM</th>
                        <th>Nama UKM</th>
                        <th>Logo UKM</th>
                        <th>Instagram</th>
                        <th>Facebook</th>
                        <th>Sejarah</th>
                        <th>Visi</th>
                        <th>Misi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($ukmData as $ukm) : ?>
                    <tr>
                        <td><?php echo $ukm['id_ukm']; ?></td>
                        <td><?php echo $ukm['nama_ukm']; ?></td>
                        <td>
                            <img src="../assets/images/logoukm/<?php echo $ukm['logo_ukm']; ?>" alt="Logo UKM" style="max-width: 50px; max-height: 50px;">
                        </td>
                        <td><?php echo $ukm['instagram']; ?></td>
                        <td><?php echo $ukm['facebook']; ?></td>
                        <td>
                            <?php
                            $sejarah = $ukm['sejarah'];
                            // Define the maximum character limit for sejarah
                            $maxChar = 100;
                            if (strlen($sejarah) > $maxChar) {
                                // Truncate the text if it exceeds the limit
                                $sejarah = substr($sejarah, 0, $maxChar) . '...';
                                echo $sejarah . ' <a href="#" class="read-more-link" data-content="' . htmlspecialchars($ukm['sejarah']) . '">read more</a>';
                            } else {
                                echo $sejarah;
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $visi = $ukm['visi'];
                            // Define the maximum character limit for visi
                            $maxChar = 100;
                            if (strlen($visi) > $maxChar) {
                                // Truncate the text if it exceeds the limit
                                $visi = substr($visi, 0, $maxChar) . '...';
                                echo $visi . ' <a href="#" class="read-more-link" data-content="' . htmlspecialchars($ukm['visi']) . '">read more</a>';
                            } else {
                                echo $visi;
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $misi = $ukm['misi'];
                            // Define the maximum character limit for misi
                            $maxChar = 100;
                            if (strlen($misi) > $maxChar) {
                                // Truncate the text if it exceeds the limit
                                $misi = substr($misi, 0, $maxChar) . '...';
                                echo $misi . ' <a href="#" class="read-more-link" data-content="' . htmlspecialchars($ukm['misi']) . '">read more</a>';
                            } else {
                                echo $misi;
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<script>
    // Function to handle the "read more" link click
    function handleReadMoreClick(event) {
        event.preventDefault();
        const content = event.target.dataset.content;
        alert(content); // You can replace this alert with a modal or other UI element to show the full content.
    }

    // Add event listeners to all "read more" links
    const readMoreLinks = document.querySelectorAll('.read-more-link');
    readMoreLinks.forEach((link) => {
        link.addEventListener('click', handleReadMoreClick);
    });
</script>
<script>
    // Fungsi untuk logout dengan konfirmasi
    function logout() {
        // Tampilkan dialog konfirmasi menggunakan SweetAlert
        Swal.fire({
            title: 'Apakah Anda yakin ingin keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengklik "Ya", maka lakukan proses logout
                window.location.href = "?logout=true";
            }
        });
    }
</script>
</body>
</html>
