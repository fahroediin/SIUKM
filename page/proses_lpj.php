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
$active_page = 'lpj';

// Memeriksa level pengguna
if ($_SESSION['level'] == "3" || $_SESSION['level'] == "2") {
    // Jika level adalah "3" atau "2", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}

// Initialize variables
$id_laporan = $jenis = $id_ukm = $nama_ukm = $tgl_laporan = $file_name = "";
function generateUniqueId() {
    global $conn, $tgl_laporan, $id_ukm, $jenis;
    
    do {
        $random_digits = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT); // Generate 6 random digits
        $new_id_laporan = $tgl_laporan . $random_digits . $jenis;
        
        // Check if the generated id_laporan already exists in tab_lpj
        $check_query = "SELECT COUNT(*) as count FROM tab_lpj WHERE id_laporan = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $new_id_laporan);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $existing_count = $row['count'];
    } while ($existing_count > 0); // Keep generating until a unique id_laporan is found
    
    return $new_id_laporan;
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis = $_POST['jenis'];
    $id_ukm = $_POST['id_ukm'];
    $nama_ukm = $_POST['nama_ukm'];
    $tgl_laporan = $_POST['tgl_laporan'];

    // Generate a unique id_laporan
    $id_laporan = generateUniqueId();

    // Check if a file was uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

        // Generate a unique filename
        $unique_filename = $id_laporan . '_' . $jenis . '_' . $nama_ukm . '.' . $file_extension;

        // Destination directory for uploaded files
        $destination_directory = '../assets/images/lpj/';

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($file_tmp, $destination_directory . $unique_filename)) {
            $sql = "INSERT INTO tab_lpj (id_laporan, jenis, id_ukm, nama_ukm, tgl_laporan, file_lpj) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $id_laporan, $jenis, $id_ukm, $nama_ukm, $tgl_laporan, $unique_filename);

            if ($stmt->execute()) {
                header("Location: proses_lpj.php?success=1");
                exit();
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
<title>LPJ - SIUKM</title>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
           .sidebar {
        text-align: center; /* Center the contents horizontally */
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

       .card {
           border: 1px solid #ccc;
           border-radius: 8px;
           padding: 20px;
           max-width: 600px;
           box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
       }

       .card-body {
           display: flex;
           flex-direction: column;
           gap: 10px;
       }

       .card-body div {
           display: flex;
           flex-direction: column;
       }

       label {
           font-weight: bold;
           margin-bottom: 5px;
       }

       select,
       input[type="text"],
       input[type="date"],
       button {
           padding: 8px;
           border: 1px solid #ccc;
           border-radius: 5px;
           font-size: 16px;
       }

       select {
           width: 100%;
       }

       button {
           background-color: #007bff;
           color: #fff;
           cursor: pointer;
           transition: background-color 0.3s ease;
       }

       button:hover {
           background-color: #0056b3;
       }
       .preview-image {
    max-width: 100%;
    max-height: 200px;
    margin-top: 10px;
}
   </style>
 <!-- Sidebar -->
 <div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
    <h2><i>Profil</i></h2>
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
<div class="content">
        <div class="row justify-content-center">
            <div class="card">
                <h2 style="text-align: center;">UNGGAH LPJ</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                    enctype="multipart/form-data" class="form">
                    <input type="hidden" name="action" value="add">
                <div class="form-group">
                            <label for="jenis">*Jenis Laporan</label>
                             <!-- Input for id_laporan -->
                    <input type="hidden" id="id_laporan" name="id_laporan" value="<?php echo generateUniqueId(); ?>" readonly>
                            <select id="jenis" name="jenis" class="form-control">
                                <!-- Options for jenis values -->
                                <option value="Kegiatan">Kegiatan</option>
                                <option value="Semester">Semester</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jenis">*Nama UKM</label>
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
                    <label for="tgl_laporan">*Tanggal Laporan</label>
                    <input type="date" id="tgl_laporan" name="tgl_laporan" class="form-control" value="<?php echo date("Y-m-d"); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="file">*File</label>
                    <input type="file" id="file" name="file" accept=".pdf" class="form-control-file">
                    <!-- Add a link to the existing file for editing -->
                    <?php if(isset($row_lpj['file']) && !empty($row_lpj['file'])): ?>
                        <p>Existing File: <a href="../assets/lpj/<?php echo $row_lpj['file']; ?>" target="_blank"><?php echo $row_lpj['file']; ?></a></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>

<script>
    function updateNamaUKM(selectElement) {
    var selectedIdUkm = selectElement.value;
    var namaUkmField = document.getElementById("nama_ukm");
    var namaUkmHiddenField = document.getElementById("nama_ukm_hidden");

    // Set the value of the "nama_ukm" field based on the selected "id_ukm"
    if (selectedIdUkm in <?php echo json_encode($namaUKM); ?>) {
        namaUkmField.value = <?php echo json_encode($namaUKM); ?>[selectedIdUkm];
        namaUkmHiddenField.value = <?php echo json_encode($namaUKM); ?>[selectedIdUkm]; // Set the hidden field value
    } else {
        namaUkmField.value = '';
        namaUkmHiddenField.value = '';
    }
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

     // Function to confirm the delete action
     function confirmDelete() {
        return confirm("Apakah Anda yakin akan menghapus data kegiatan?");
    }
    // Fungsi untuk logout
    function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
</script>
</body>
</html>
