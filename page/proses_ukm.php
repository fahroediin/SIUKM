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

// Memeriksa level pengguna
if ($_SESSION['level'] == "3" || $_SESSION['level'] == "2") {
    // Jika level adalah "3" atau "2", redirect ke halaman beranda.php
    header("Location: beranda.php");
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

// Menandai halaman yang aktif
$active_page = 'ukm';

// Function to generate logo filename based on id_ukm and extension
function generateLogoFilename($id_ukm, $extension)
{
    // Concatenate the id_ukm with the "-logo" suffix and the extension
    $logoFilename = $id_ukm . "-logo." . $extension;
    return $logoFilename;
}

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai-nilai dari form
    $id_ukm_type = $_POST["id_ukm_type"];
    $nama_ukm = $_POST["nama_ukm"];
    $sejarah = $_POST["sejarah"];
    $instagram = $_POST["instagram"];
    $facebook = $_POST["facebook"];
    $visi = $_POST["visi"];
    $misi = $_POST["misi"];


    if ($id_ukm_type === "dropdown") {
        $id_ukm = $_POST["id_ukm"];
    } else {
        $id_ukm = $_POST["id_ukm_new"];
    }

    // Check if a logo file is uploaded
    if ($_FILES["logo_ukm"]["name"] != "") {
        // Define the target directory for the logo file
        $targetDir = "../assets/images/logoukm/";

        // Get the original filename and extension
        $logo_ukm_name = $_FILES["logo_ukm"]["name"];
        $logo_ukm_extension = strtolower(pathinfo($logo_ukm_name, PATHINFO_EXTENSION));

        // Check if the file format is allowed
        if (!in_array($logo_ukm_extension, ['jpeg', 'jpg', 'png'])) {
            echo "Sorry, only JPEG, JPG, and PNG files are allowed.";
            exit();
        }

        // Generate the logo filename based on id_ukm and the validated extension
        $logo_ukm_filename = generateLogoFilename($id_ukm, $logo_ukm_extension);

        // Move the uploaded logo file to the target directory
        if (!move_uploaded_file($_FILES["logo_ukm"]["tmp_name"], $targetDir . $logo_ukm_filename)) {
            // Handle the error condition, for example:
            echo "Sorry, there was an error uploading the logo file.";
            exit();
        }
    } else {
        // If no logo file is uploaded, use the existing logo filename
        $logo_ukm_filename = $_POST["existing_logo"];
    }

    // SQL query to update data in tab_ukm table
    $sql = "UPDATE tab_ukm SET nama_ukm=?, sejarah=?, logo_ukm=?, instagram=?, facebook=?, visi=?, misi=? WHERE id_ukm=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssi", $nama_ukm, $sejarah, $logo_ukm_filename, $instagram, $facebook, $visi, $misi, $id_ukm);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: proses_ukm.php?success=1");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
        exit();
    }
}

$query = "SELECT id_ukm, nama_ukm, logo_ukm, instagram, facebook, sejarah, visi, misi FROM tab_ukm";
$result = mysqli_query($conn, $query);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
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
    .content {
    /* Atur tata letak (layout) untuk kontainer utama */
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* Penyesuaian padding atau margin sesuai kebutuhan */
}

.header {
    /* Atur tata letak (layout) untuk header */
    display: flex;
    align-items: center;
}

.header h2 {
    /* Atur gaya untuk elemen H2 pada header */
    margin-right: 10px; /* Jarak antara H2 dan tombol tambah */
}
.carousel-control-prev-icon,
.carousel-control-next-icon {
    display: inline-block;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    width: 15px;
    height: 15px;
    margin-top: 7px;
}
</style>
  <!-- Sidebar -->

  <div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Data UKM</i></h2>
<a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <p style="text-align: center;">--Manajemen--</p>
    <a href="proses_beranda.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_beranda') echo 'active'; ?>">Beranda</a>
    <a href="proses_profil.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_profil') echo 'active'; ?>">Profil</a>
    <a href="proses_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="proses_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="proses_user.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_dau.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_struktur.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'struktur') echo 'active'; ?>">Pengurus</a>
    <a href="proses_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'ukm') echo 'active'; ?>">Data UKM</a>
    <a href="calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
    <a href="proses_lpj.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'lpj') echo 'active'; ?>">LPJ</a>
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
<body>
<!-- Main content -->
<div class="content">
        <div class="header">
            <h2>Data UKM</h2>
            <button type="button" class="btn btn-primary btn-sm btn-medium" onclick="openTambahUkmModal()">
                <i class="fas fa-plus"></i> Tambah UKM Baru
            </button>
        </div>
        <form class="form-inline mt-2 mt-md-0 float-right" method="get">
        <input class="form-control mr-sm-2" type="text" placeholder="Cari Nama UKM" name="search" aria-label="Search">
        <button type="submit" class="btn btn-outline-primary">Search</button>
    <a href="proses_ukm.php" class="btn btn-outline-secondary ml-2">
  <i class="fas fa-sync-alt"></i>
</a>
    </div>
</form>

<div class="container">
    <div id="ukmCarousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($ukmData as $index => $ukm) { ?>
                <div class="carousel-item <?php if ($index === 0) echo 'active'; ?>">
    
        <div class="col-md-12">
            <div class="card">
                <img src="../assets/images/logoukm/<?php echo $ukm['logo_ukm']; ?>" alt="Logo UKM" class="card-img-top mx-auto d-block" style="max-width: 100px; max-height: 100px;" data-toggle="modal" data-target="#ukmDetailsModal<?php echo $ukm['id_ukm']; ?>">
            </div>
    
    </div>
</div>

<!-- Modal for UKM Details -->
<div class="modal fade" id="ukmDetailsModal<?php echo $ukm['id_ukm']; ?>" tabindex="-1" role="dialog" aria-labelledby="ukmDetailsModalLabel<?php echo $ukm['id_ukm']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                                    <div class="card-body">
                                    <div class="modal-header">
                                        <h2 class="modal-title" id="ukmDetailsModalLabel<?php echo $ukm['id_ukm']; ?>"><?php echo $ukm['nama_ukm']; ?></h2>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="ukm-info">
                                    <p class="text-center"><strong>Sejarah</strong></p>
                                        <p><?php echo $ukm['sejarah']; ?></p>
                                    </div>
                                    <div class="ukm-info text-center">
                                    <p class="text-center"><strong>Visi</strong></p>
                                        <p><?php echo $ukm['visi']; ?></p>
                                    </div>
                                    <div class="ukm-info">
                                    <p class="text-center"><strong>Misi</strong></p>
                                        <p><?php echo $ukm['misi']; ?></p>
                                    </div>
                                    <div class="ukm-info">
                                        <p><strong>Instagram:</strong> @<?php echo $ukm['instagram']; ?></p>
                                    </div>
                                    <div class="ukm-info">
                                        <p><strong>Facebook:</strong> <?php echo $ukm['facebook']; ?></p>
                                
                                    </div>
                                    <a href="detail_ukm.php?id=<?php echo $ukm['id_ukm']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="detail_ukm.php?id=<?php echo $ukm['id_ukm']; ?>" class="btn btn-secondary">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
            </div>
        </div>
</div>                
        <a class="carousel-control-prev" href="#ukmCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#ukmCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>






    

    <!-- Snackbar -->
<div id="snackbar"></div>
<script>
    // Function to open the "Tambah UKM Baru" modal
function openTambahUkmModal() {
    // Reset form fields when opening the modal
    resetAllTextFields();
    
    // Show the modal
    $('#tambahUkmBaruModal').modal('show');
}

// Function to submit the form inside the modal
function submitForm() {
    // Show confirmation dialog
    var confirmation = confirm("Apakah Anda yakin ingin menyimpan data?");
    
    if (confirmation) {
        // Submit the form inside the modal
        document.getElementById("dataForm").submit();
    }
}

  function showConfirmation(event) {
    event.preventDefault(); // Menghentikan pengiriman form secara langsung

    // Menampilkan konfirmasi dengan fungsi confirm()
    var confirmation = confirm("Apakah Anda yakin ingin menyimpan data?");

    if (confirmation) {
      // Mengirim form jika pengguna menekan tombol "OK"
      document.getElementById("dataForm").submit();
    }
  }
</script>

<!-- Add the following script to show the alert after the page loads -->
<script>
    // Wait for the page to load
    window.addEventListener('DOMContentLoaded', (event) => {
        // Check if the URL contains a success query parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            // Show the success message
            showSnackbar('Data berhasil disimpan');
        }
    });

    // Function to show the snackbar with a message
    function showSnackbar(message) {
        const snackbar = document.getElementById('snackbar');
        snackbar.textContent = message;
        snackbar.className = 'show';

        // Hide the snackbar after 3 seconds
        setTimeout(() => {
            snackbar.className = snackbar.className.replace('show', '');
        }, 3000);
    }
</script>

<script>
    function resetAllTextFields() {
    document.getElementById("nama_ukm").value = "";
    document.getElementById("sejarah").value = "";
    document.getElementById("instagram").value = "";
    document.getElementById("facebook").value = "";
    document.getElementById("visi").value = "";
    document.getElementById("misi").value = "";
    document.getElementById("id_ukm_new").value = "";
}

function toggleIdUkmField() {
    var idUkmType = document.getElementById("id_ukm_type").value;
    var idUkmDropdown = document.getElementById("id_ukm_dropdown");
    var idUkmTextfield = document.getElementById("id_ukm_textfield");

    if (idUkmType === "dropdown") {
        idUkmDropdown.style.display = "block";
        idUkmTextfield.style.display = "none";
    } else {
        idUkmDropdown.style.display = "none";
        idUkmTextfield.style.display = "block";
        resetAllTextFields(); // Call the function to reset all text fields
    }
}

</script>


  <!-- Script untuk mengatur perubahan lebar sidebar -->
  <script>
        const sidebar = document.querySelector('.sidebar');
        document.addEventListener('DOMContentLoaded', function() {
            // Menambahkan event listener pada tombol collapse
            document.querySelector('#collapse-button').addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        });
        // Fungsi untuk logout
    function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
    </script>
    <!-- Modal for adding new UKM -->
<div class="modal fade" id="tambahUkmBaruModal" tabindex="-1" role="dialog" aria-labelledby="tambahUkmBaruModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <div class="card">
    <h2 style="text-align: center;">Data UKM</h2>
    <form id="dataForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
            <label for="id_ukm_type">Pilih Tipe ID UKM:</label>
            <select class="form-control" id="id_ukm_type" name="id_ukm_type" onchange="toggleIdUkmField()">
                <option value="dropdown">Dropdown</option>
                <option value="textfield">Text Field</option>
            </select>
        </div>

        <div class="form-group" id="id_ukm_dropdown">
            <label for="id_ukm_dropdown">ID UKM:</label>
            <select class="form-control" id="id_ukm" name="id_ukm" onchange="updateFormData(this)">
                <option value="" selected disabled>Pilih ID UKM</option>
                <?php
                // Membuat opsi combobox dari hasil query
                foreach ($namaUKM as $id_ukm => $nama_ukm) {
                    echo "<option value='$id_ukm'>$id_ukm</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group" id="id_ukm_textfield" style="display: none;">
            <label for="id_ukm_textfield">ID UKM Baru:</label>
            <input type="text" class="form-control" id="id_ukm_new" name="id_ukm_new">
        </div>

            <div class="form-group">
                <label for="nama_ukm">Nama UKM:</label>
                <input type="text" class="form-control" id="nama_ukm" name="nama_ukm" required>
            </div>
            <div class="form-group">
                <label for="sejarah">Sejarah:</label>
                <textarea class="form-control" placeholder="Isikan sejarah UKM" id="sejarah" name="sejarah" rows="5"></textarea>
            </div>
                    <div class="form-group">
                <label for="logo_ukm">Logo UKM:</label>
                <input type="file" class="form-control-file" id="logo_ukm" name="logo_ukm">
                <!-- Add this img tag to display the logo preview -->
                <img id="logo_ukm_preview" src="" alt="Logo UKM" style="max-width: 100px; max-height: 100px; margin-top: 10px;">

                <!-- Add the message for logo upload -->
                <p class="mt-2" style="font-size: 12px; color: #777;">Upload logo UKM dengan resolusi 512x512 pixel.</p>
            </div>
            <div class="form-group">
            <label for="instagram">Instagram:</label>
            <input type="text" placeholder="Masukkan akun instagram ukm tanpa @" class="form-control" id="instagram" name="instagram" pattern="[a-zA-Z]+">
        </div>
        <div class="form-group">
            <label for="facebook">Facebook:</label>
            <input type="text" placeholder="Masukkan akun facebook ukm tanpa @" class="form-control" id="facebook" name="facebook" pattern="[a-zA-Z]+">
        </div>

            <div class="form-group">
                <label for="visi">Visi:</label>
                <textarea class="form-control" placeholder="Sebaiknya buka dan tutup kalimat dengan tanda petik" id="visi" name="visi" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="misi">Misi:</label>
                <textarea class="form-control" placeholder="Sebaiknya buka dan tutup kalimat dengan tanda petik" id="misi" name="misi" rows="3"></textarea>
            </div>
            <div class="text-center"> <!-- Wrap the button in a div with the "text-center" class -->
            <button type="submit" class="btn btn-primary btn-sm btn-medium"  onclick="showConfirmation(event)" name="submit">
    <i class="fas fa-save"></i> Simpan
</button>
    </div>
        </form>
    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Simpan</button>
            </div>
        </div>
    </div>
</div>
        </body>
        </html>