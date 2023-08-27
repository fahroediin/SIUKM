<?php
require_once "db_connect.php";
session_start();

// Check if id_admin is sent through the form submission
if (isset($_POST['id_admin'])) {
    $id_admin = $_POST['id_admin'];
}
// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_admin'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}
$id_ukm = $_SESSION['id_ukm'];

function logout() {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_GET['logout'])) {
    logout();
}

$active_page = 'view_ukm';

function generateLogoFilename($id_ukm, $extension) {
    return $id_ukm . "-logo." . $extension;
}
function sendError($message) {
    echo '<script>showSnackbar("' . $message . '");</script>';
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_ukm = str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
    $nama_ukm = $_POST["nama_ukm"];
    $sejarah = $_POST["sejarah"];
    $instagram = $_POST["instagram"];
    $facebook = $_POST["facebook"];
    $visi = $_POST["visi"];
    $misi = $_POST["misi"];
    $sk = $_FILES["sk"]["name"];

    $targetDir = "./assets/images/logoukm/";

    // Handle logo file upload
    $logo_ukm_filename = "";
    if ($_FILES["logo_ukm"]["error"] === UPLOAD_ERR_OK) {
        $logo_ukm_name = $_FILES["logo_ukm"]["name"];
        $logo_ukm_extension = strtolower(pathinfo($logo_ukm_name, PATHINFO_EXTENSION));
        
        if (!in_array($logo_ukm_extension, ['jpeg', 'jpg', 'png'])) {
            sendError("Sorry, only JPEG, JPG, and PNG files are allowed.");
            exit();
        }

        $logo_ukm_filename = generateLogoFilename($id_ukm, $logo_ukm_extension);

        if (!move_uploaded_file($_FILES["logo_ukm"]["tmp_name"], $targetDir . $logo_ukm_filename)) {
            sendError("Sorry, there was an error uploading the logo file.");
            exit();
        }
    }

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    function generateSKFilename($id_ukm, $extension) {
        return $id_ukm . "-sk." . $extension;
    }
    
    // Handle SK file upload
    $targetDirSK = "./assets/images/sk/";
    $sk_filename = "";
    if ($_FILES["sk"]["error"] === UPLOAD_ERR_OK) {
    $sk_name = $_FILES["sk"]["name"];
    $sk_extension = strtolower(pathinfo($sk_name, PATHINFO_EXTENSION));

    if ($sk_extension !== 'pdf') {
        sendError("Sorry, only PDF files are allowed for SK.");
        exit();
    }

    $sk_filename = generateSKFilename($id_ukm, $sk_extension);

    if (!move_uploaded_file($_FILES["sk"]["tmp_name"], $targetDirSK . $sk_filename)) {
        echo "Sorry, there was an error uploading the SK file.";
        exit();
    }
}

    $sql = "INSERT INTO tab_ukm (id_ukm, nama_ukm, sejarah, logo_ukm, instagram, facebook, visi, misi, sk) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssss", $id_ukm, $nama_ukm, $sejarah, $logo_ukm_filename, $instagram, $facebook, $visi, $misi, $sk_filename);

        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: proses_ukm.php?success=1&showSnackbar=true"); // Add &showSnackbar=true
            exit();
        
        } else {
            echo "Error saving data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $query = "SELECT id_ukm, nama_ukm, logo_ukm, instagram, facebook, sejarah, visi, misi, sk FROM tab_ukm WHERE id_ukm = '$id_ukm'";
    
    if (!empty($searchTerm)) {
        $searchTerm = mysqli_real_escape_string($conn, $searchTerm);
        $query .= " WHERE nama_ukm LIKE '%$searchTerm%'";
    }
    
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
   <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/favicon-siukm.png">
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
            logo_ukmField.src = "./assets/images/logoukm/" + data.logo_ukm;
        }
    };
    xhttp.open("GET", "get_data_ukm.php?id_ukm=" + id_ukm, true);
    xhttp.send();
}
</script>
</head>
<style>
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
    animation: fadein 0.5s, fadeout 0.5s 2.5s;
}
@keyframes fadein {
    from {bottom: 0; opacity: 0;}
    to {bottom: 30px; opacity: 1;}
}
@keyframes fadeout {
    from {bottom: 30px; opacity: 1;}
    to {bottom: 0; opacity: 0;}
}
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
.ukm-card {
    margin-top: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    width: 500px; /* Set the desired width */
    height: 850px; /* Set the same value as the width for a square shape */
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* To align content vertically */
}


.ukm-card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.ukm-logo {
    max-width: 100px;
    max-height: 100px;
    margin: 0 auto;
    display: block;
    border-radius: 50%;
}

</style>
  <!-- Sidebar -->

  <div class="sidebar">
    <a href="index.php">
  <img src="./assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Data UKM</i></h2>
<a href="pengurus.php" class="btn btn-primary <?php if($active_page == 'pengurus') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
    <a href="proses_dau_pengurus.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'proses_dau_pengurus') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_struktur_pengurus.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'struktur') echo 'active'; ?>">Pengurus</a>
    <a href="view_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="view_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_ukm') echo 'active'; ?>">Data UKM</a>
    <a href="view_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_galeri') echo 'active'; ?>">Galeri</a>
    <a href="view_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="view_calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
    <a href="view_lpj.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_lpj') echo 'active'; ?>">Unggah LPJ</a>
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
        <body>
        <div class="content">
        <div class="header">
    <h2>Data UKM</h2>
    </div>
    </div>
<div class="content">
<div class="row">
<?php if (empty($ukmData)) { ?>
    <?php } else { ?>
    <?php foreach ($ukmData as $index => $ukm) { ?>
        <div class="col-md-6">
            <div class="card ukm-card">
                <img src="./assets/images/logoukm/<?php echo $ukm['logo_ukm']; ?>" alt="Logo UKM" class="card-img-top mx-auto d-block ukm-logo">
                <div class="card-body">
                    <h3 class="card-title text-center"><?php echo $ukm['nama_ukm']; ?></h3>
                    <p class="text-center"><strong>Sejarah</strong></p>
                    <?php
                    $sejarah = $ukm['sejarah'];
                    if (strlen($sejarah) > 50) {
                        $sejarah = substr($sejarah, 0, 50) . ".";
                        echo "<p>" . $sejarah . "</p>";
                        echo '<p><a href="#" class="read-more-link" data-toggle="modal" data-target="#sejarahModal' . $index . '">Read More</a></p>';
                    } else {
                        echo "<p>" . $sejarah . "</p>";
                    }
                    ?>
                     <p class="text-center"><strong>Visi</strong></p>
                    <?php
                    $visi = $ukm['visi'];
                    if (strlen($visi) > 50) {
                        $visi = substr($visi, 0, 50) . ".";
                        echo "<p>" . $visi . "</p>";
                        echo '<p><a href="#" class="read-more-link" data-toggle="modal" data-target="#visiModal' . $index . '">Read More</a></p>';
                    } else {
                        echo "<p>" . $visi . "</p>";
                    }
                    ?>
                      <p class="text-center"><strong>Misi</strong></p>
                     <?php
                    $misi = $ukm['misi'];
                    if (strlen($misi) > 50) {
                        $misi = substr($misi, 0, 50) . ".";
                        echo "<p>" . $misi . "</p>";
                        echo '<p><a href="#" class="read-more-link" data-toggle="modal" data-target="#misiModal' . $index . '">Read More</a></p>';
                    } else {
                        echo "<p>" . $misi . "</p>";
                    }
                    ?>
                     <p class="text-center"><strong>Social Media</strong></p>
                    <p>Instagram: <?php echo $ukm['instagram']; ?></p>
                    <p>Facebook: <?php echo $ukm['facebook']; ?></p>
                    <p class="text-center">
                   
                    <a href="#" class="btn btn-primary btn-edit-ukm" data-ukm-id="<?php echo $ukm['id_ukm']; ?>" data-toggle="modal" data-target="#editUkmModal" onclick="openEditModal(this)">Edit</a>
                    <a href="halaman_ukm.php?id_ukm=<?php echo $ukm['id_ukm']; ?>" class="btn btn-secondary" target="_blank">Lihat Halaman</a>
                    <a href="#" class="btn btn-info" data-toggle="modal" data-target="#skModal<?php echo $index; ?>">Lihat SK</a> <!-- Tombol untuk melihat SK -->
                </p>
                </div>
            </div>
        </div>
                </div>
        </div>
         <!-- Delete Confirmation Modal -->
         <div class="modal fade" id="deleteModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $index; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel<?php echo $index; ?>">Hapus UKM</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus UKM <?php echo $ukm['nama_ukm']; ?>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <a href="delete_ukm.php?id_ukm=<?php echo $ukm['id_ukm']; ?>" class="btn btn-danger">Hapus</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="sejarahModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="sejarahModalLabel<?php echo $index; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sejarahModalLabel<?php echo $index; ?>">Sejarah - <?php echo $ukm['nama_ukm']; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo $ukm['sejarah']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="visiModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="visiModalLabel<?php echo $index; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="visiModalLabel<?php echo $index; ?>">Visi - <?php echo $ukm['nama_ukm']; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo $ukm['visi']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="misiModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="misiModalLabel<?php echo $index; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="misiModalLabel<?php echo $index; ?>">Misi - <?php echo $ukm['nama_ukm']; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo $ukm['misi']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="skModal<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="skModalLabel<?php echo $index; ?>" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="skModalLabel<?php echo $index; ?>">SK - <?php echo $ukm['nama_ukm']; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php $sk_filename = $ukm['sk']; ?>
                    <?php if (!empty($sk_filename)) { ?>
                        <a href="./assets/images/sk/<?php echo $sk_filename; ?>" target="_blank">Lihat SK</a>
                    <?php } else { ?>
                        <p>Belum ada SK yang diunggah untuk UKM ini.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
        <?php if (($index + 1) % 2 === 0) { ?>
            </div><div class="row">
            <?php } ?>
        <?php } ?>
    <?php } ?>
</div>
<!-- Modal for Adding UKM Data -->
<div class="modal fade" id="tambahUkmBaruModal" tabindex="-1" role="dialog" aria-labelledby="tambahUkmBaruModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahUkmBaruModalLabel">Tambah Data UKM Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="tambahUkmForm" method="post" enctype="multipart/form-data" action="proses_ukm.php">
                        <input type="hidden" class="form-control" id="id_ukm" name="id_ukm" required>
                    <div class="form-group">
                        <label for="nama_ukm">Nama UKM:</label>
                        <input type="text" class="form-control" id="nama_ukm" name="nama_ukm" required>
                    </div>
                    <div class="form-group">
                        <label for="sejarah">Sejarah:</label>
                        <textarea class="form-control" id="sejarah" name="sejarah" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="visi">Visi:</label>
                        <textarea class="form-control" id="visi" name="visi" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="misi">Misi:</label>
                        <textarea class="form-control" id="misi" name="misi" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="facebook">Facebook:</label>
                        <input type="text" class="form-control" id="facebook" name="facebook" required>
                    </div>
                    <div class="form-group">
                        <label for="instagram">Instagram:</label>
                        <input type="text" class="form-control" id="instagram" name="instagram" required>
                    </div>
                    <div class="form-group">
                        <label for="logo_ukm">Logo:</label>
                        <input type="file" class="form-control-file" id="logo_ukm" name="logo_ukm" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="sk">SK:</label>
                        <input type="file" class="form-control-file" id="sk" name="sk" accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Editing UKM Data -->
<div class="modal fade" id="editUkmModal" tabindex="-1" role="dialog" aria-labelledby="editUkmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUkmModalLabel">Edit Data UKM</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUkmForm" method="post" enctype="multipart/form-data" action="proses_edit_ukm.php">
                    <input type="hidden" class="form-control" id="id_ukm_edit" name="id_ukm_edit" required>
                    <div class="form-group">
                        <label for="nama_ukm_edit">Nama UKM:</label>
                        <input type="text" class="form-control" id="nama_ukm_edit" name="nama_ukm_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="sejarah_edit">Sejarah:</label>
                        <textarea class="form-control" id="sejarah_edit" name="sejarah_edit" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="visi_edit">Visi:</label>
                        <textarea class="form-control" id="visi_edit" name="visi_edit" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="misi_edit">Misi:</label>
                        <textarea class="form-control" id="misi_edit" name="misi_edit" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="instagram_edit">Instagram:</label>
                        <input type="text" class="form-control" id="instagram_edit" name="instagram_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="facebook_edit">Facebook:</label>
                        <input type="text" class="form-control" id="facebook_edit" name="facebook_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="logo_edit">Logo:</label>
                        <img id="logo_edit_preview" src="" alt="Logo Preview" style="max-width: 100px; max-height: 100px;">
                        <input type="file" class="form-control-file" id="logo_edit" name="logo_edit">
                    </div>
                    <div class="form-group">
                        <label for="sk_edit">SK:</label>
                        <input type="file" class="form-control-file" id="sk_edit" name="sk_edit" accept=".pdf">
                        <div id="sk_preview_container"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="snackbar"></div>
<script>
    // Wait for the page to load
    window.addEventListener('DOMContentLoaded', (event) => {
        // Add an event listener to the SK input field
        const logoInput = document.getElementById('logo_edit');
        const logoPreview = document.getElementById('logo_edit_preview');
        logoInput.addEventListener('change', function() {
    // Clear the previous logo preview
    logoPreview.src = '';
        // Check if a logo file was selected
        if (logoInput.files && logoInput.files[0]) {
        const logoFile = logoInput.files[0];
        logoPreview.src = URL.createObjectURL(logoFile);
    }
});
        const skInput = document.getElementById('sk_edit');
        const skPreviewContainer = document.getElementById('sk_preview_container');
        skInput.addEventListener('change', function() {
            // Clear the previous preview
            skPreviewContainer.innerHTML = '';

            // Check if a file was selected
            if (skInput.files && skInput.files[0]) {
                const skFile = skInput.files[0];

                // Create a PDF viewer object
                const pdfViewer = document.createElement('iframe');
                pdfViewer.setAttribute('src', URL.createObjectURL(skFile));
                pdfViewer.setAttribute('width', '100%');
                pdfViewer.setAttribute('height', '400px');

                // Append the PDF viewer to the preview container
                skPreviewContainer.appendChild(pdfViewer);
            }
        });
    });
</script>

<script>
        // Wait for the page to load
        window.addEventListener('DOMContentLoaded', (event) => {
        // Check if the URL contains a deleteSuccess query parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('deleteSuccess')) {
            // Show the success message for data deletion
            showSnackbar('Data UKM berhasil dihapus');
        }
        if (urlParams.has('editSuccess') && urlParams.get('editSuccess') === 'true') {
                // Check if the showSnackbar parameter is present and set to "true"
                if (urlParams.has('showSnackbar') && urlParams.get('showSnackbar') === 'true') {
                    // Show the success message
                    showSnackbar('Data UKM berhasil diedit');
                }
            }
        });
    // Wait for the page to load
    window.addEventListener('DOMContentLoaded', (event) => {
        // Check if the URL contains a success query parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            // Check if the showSnackbar parameter is present and set to "true"
            if (urlParams.has('showSnackbar') && urlParams.get('showSnackbar') === 'true') {
                // Show the success message
                showSnackbar('UKM baru berhasil ditambahkan');
            }
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
function openEditModal(button) {
    const id_ukm = button.getAttribute("data-ukm-id");
    const ukmData = <?php echo json_encode($ukmData); ?>;
    
    const ukm = ukmData.find(item => item.id_ukm === id_ukm);
    
    if (ukm) {
        document.getElementById("id_ukm_edit").value = ukm.id_ukm;
        document.getElementById("nama_ukm_edit").value = ukm.nama_ukm;
        document.getElementById("sejarah_edit").value = ukm.sejarah;
        document.getElementById("visi_edit").value = ukm.visi;
        document.getElementById("misi_edit").value = ukm.misi;
        document.getElementById("logo_edit_preview").src = "./assets/images/logoukm/" + ukm.logo_ukm;
        document.getElementById("instagram_edit").value = ukm.instagram;
        document.getElementById("facebook_edit").value = ukm.facebook;
        document.getElementById("sk_edit").value = ukm.sk;
    }
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
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit-ukm');
    const editForm = document.getElementById('editUkmForm');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            openEditModal(button);

            if (ukm) {
                editForm.querySelector('#id_ukm_edit').value = ukm.id_ukm;
                editForm.querySelector('#nama_ukm_edit').value = ukm.nama_ukm;
                editForm.querySelector('#sejarah_edit').value = ukm.sejarah;
                editForm.querySelector('#visi_edit').value = ukm.visi;
                editForm.querySelector('#misi_edit').value = ukm.misi;
                editForm.querySelector('#logo_edit').value = ukm.logo;
                editForm.querySelector('#instagram_edit').value = ukm.instagram;
                editForm.querySelector('#facebook_edit').value = ukm.facebook;
                editForm.querySelector('#sk_edit').value = ukm.sk;
            }
        });
    });
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