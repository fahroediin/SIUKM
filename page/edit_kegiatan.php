<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();


// Menandai halaman yang aktif
$active_page = 'kegiatan';


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

// Memeriksa apakah parameter id_kegiatan telah diberikan
if (isset($_GET['id_kegiatan'])) {
    $id_kegiatan = $_GET['id_kegiatan'];

    // Fetch kegiatan data from the database
    $sql = "SELECT * FROM tab_kegiatan WHERE id_kegiatan = '$id_kegiatan'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            $kegiatan = $result->fetch_assoc();
        } else {
            // If the kegiatan ID doesn't exist
            echo "Invalid kegiatan ID";
            exit();
        }
    } else {
        // If there was an error with the query
        echo "Error: " . $conn->error;
        exit();
    }
} else {
    // If the kegiatan ID is not provided
    echo "Invalid kegiatan ID";
    exit();
}

// Function to get the list of UKM for dropdown
function getUkmList()
{
    global $conn;
    $ukmList = array();

    $sql = "SELECT id_ukm, nama_ukm FROM tab_ukm";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $ukmList[$row['id_ukm']] = $row['nama_ukm'];
        }
    }

    return $ukmList;
}

// Memeriksa apakah tombol update diklik
if (isset($_POST['update'])) {
    // Memeriksa apakah parameter id_kegiatan telah diberikan
    if (isset($_POST['id_kegiatan'])) {
        // Get the ID and other kegiatan data from the POST request
        $id_kegiatan = $_POST['id_kegiatan'];
        $nama_kegiatan = $_POST['nama_kegiatan'];
        $nama_ukm = $_POST['nama_ukm'];
        $id_ukm = $_POST['id_ukm'];
        $tgl = $_POST['tgl'];

        // Prepare the update query using prepared statements to avoid SQL injection
        $stmt = $conn->prepare("UPDATE tab_kegiatan SET nama_kegiatan = ?, id_ukm = ?, nama_ukm= ?, tgl = ? WHERE id_kegiatan = ?");
        $stmt->bind_param("ssssi", $nama_kegiatan, $id_ukm, $nama_ukm, $tgl, $id_kegiatan);

        // Execute the update query
        if ($stmt->execute()) {
            // Redirect back to the kegiatan list after update
            header("Location: proses_kegiatan.php");
            exit();
        } else {
            // If an error occurs during the update
            echo "Error: " . $stmt->error;
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Kegiatan - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
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
    .sidebar {
        text-align: center; /* Center the contents horizontally */
    }
    </style>
</head>

<body>
<div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Kegiatan</i></h2>
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
    <a href="lpj_upload.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'lpj') echo 'active'; ?>">LPJ</a>
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

<div class="content">
    <div class="card">
        <h2 style="text-align: center;">Edit Kegiatan</h2>
        <form method="POST">
            <input type="hidden" name="id_kegiatan" value="<?php echo $kegiatan['id_kegiatan']; ?>">
            <div class="form-group">
                <label for="nama_kegiatan">Nama Kegiatan:</label>
                <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan"
                    value="<?php echo $kegiatan['nama_kegiatan']; ?>" required>
            </div>
                    <div class="form-group">
            <label for="id_ukm">ID UKM:</label>
            <select id="id_ukm" name="id_ukm" class="form-control">
                <?php
                    $ukmList = getUkmList();
                    foreach ($ukmList as $id => $nama) {
                        echo '<option value="' . $id . '" ' . ($id == $kegiatan['id_ukm'] ? 'selected' : '') . '>' . $id . ' - ' . $nama . '</option>';
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="nama_ukm">Nama UKM:</label>
            <input type="text" class="form-control" id="nama_ukm" name="nama_ukm"
                value="<?php echo $kegiatan['nama_ukm']; ?>" required>
        </div>
            <div class="form-group">
                <label for="tgl">Tanggal:</label>
                <input type="date" class="form-control" id="tgl" name="tgl"
                    value="<?php echo $kegiatan['tgl']; ?>" required>
            </div>
            <div class="text-center"> <!-- Wrap the button in a div with the "text-center" class -->
                                        <button type="submit" class="btn btn-primary btn-sm btn-medium" name="submit">
                                <i class="fas fa-plus"></i> Tambah Kegiatan
                            </button>
                                </div>
        </form>
    </div>
</div>



    <!-- Add your scripts here -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    // Add event listener to the id_ukm dropdown
    document.getElementById('id_ukm').addEventListener('change', function() {
        // Get the selected option
        var selectedOption = this.options[this.selectedIndex];

        // Extract the nama_ukm from the selected option text
        var namaUkmText = selectedOption.text.split(' - ')[1];

        // Update the value of the nama_ukm text field
        document.getElementById('nama_ukm').value = namaUkmText;
    });
</script>
</body>

</html>
