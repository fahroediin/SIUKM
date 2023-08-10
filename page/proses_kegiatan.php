<?php
require_once "db_connect.php";
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['level'] == "2" || $_SESSION['level'] == "3") {
    header("Location: beranda.php");
    exit();
}

$active_page = 'kegiatan';

function generateIdKegiatan($tgl)
{
    $date = new DateTime($tgl);
    $year = $date->format('Y');
    $month = $date->format('m');
    $day = $date->format('d');
    $randomNumber = sprintf("%04d", mt_rand(1, 9999));
    return $year . $month . $day . $randomNumber;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs (add proper validation/sanitization)
    
    $id_ukm = mysqli_real_escape_string($conn, $_POST["id_ukm"]);
    $nama_ukm = mysqli_real_escape_string($conn, $_POST["nama_ukm"]);
    $nama_kegiatan = mysqli_real_escape_string($conn, $_POST["nama_kegiatan"]);
    $deskripsi = mysqli_real_escape_string($conn, $_POST["deskripsi"]);
    $jenis = mysqli_real_escape_string($conn, $_POST["jenis"]);
    $tgl = mysqli_real_escape_string($conn, $_POST["tgl"]);

    $id_kegiatan = generateIdKegiatan($tgl);

    $sql = "INSERT INTO tab_kegiatan (id_ukm, nama_ukm, id_kegiatan, nama_kegiatan, deskripsi, jenis, tgl) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $id_ukm, $nama_ukm, $id_kegiatan, $nama_kegiatan, $deskripsi, $jenis, $tgl);

    if ($stmt->execute()) {
        header("Location: proses_kegiatan.php?success=1");
        exit();
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit();
    }
}

$query_ukm = "SELECT id_ukm, nama_ukm FROM tab_ukm";
$result_ukm = mysqli_query($conn, $query_ukm);

$query_kegiatan = "SELECT id_kegiatan, nama_kegiatan, deskripsi, jenis, id_ukm, nama_ukm, tgl FROM tab_kegiatan";
$result_kegiatan = mysqli_query($conn, $query_kegiatan);
?>

<!DOCTYPE html>
<html>

<head>
<title>Kegiatan - SIUKM</title>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
       

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
<body>
    <!-- Modal -->
 <div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <div class="card-body">
                        <h2 style="text-align: center;">Tambah Kegiatan</h2>
                        <div class="form-group">
                            <label for="id_ukm">*Nama UKM:</label>
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

                           
                                <input type="hidden" id="id_kegiatan" name="id_kegiatan" class="form-control" readonly>
                          
                            <div class="form-group">
                                <label for="nama_kegiatan">*Nama Kegiatan:</label>
                                <input type="text" id="nama_kegiatan" placeholder="Nama Kegiatan Maksimal 15 karakter" name="nama_kegiatan" class="form-control" required maxlength="15" minlength="3">
                            </div>
                            <div class="form-group">
                                <label for="deskripsi">*Deskripsi:</label>
                                <input type="text" id="deskripsi" placeholder="Jelaskan Lebih Lanjut Kegiatan Tersebut" name="deskripsi" class="form-control" required maxlength="50" minlength="10">
                            </div>
                            <div class="form-group">
                                <label for="jenis">*Jenis Kegiatan:</label>
                                <select class="form-control" id="jenis" name="jenis" required>
                                    <option value="Rutin">Rutin</option>
                                    <option value="Tidak Rutin">Tidak Rutin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tgl">Tanggal:</label>
                                <input type="date" id="tgl" name="tgl" class="form-control" required>
                            </div>
                                    </div>
                            <div class="text-center"> <!-- Wrap the button in a div with the "text-center" class -->
                                        <button type="submit" class="btn btn-primary btn-sm btn-medium" name="submit">
                                <i class="fas fa-plus"></i> Tambah Kegiatan
                            </button>
                                </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
<div class="content">
    <h2>Data Kegiatan</h2>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahModal">
    Tambah Kegiatan
</button>
    <table class="table">
        <thead>
            <tr>
            <th>ID Kegiatan</th>
            <th>ID UKM</th>
            <th>Nama UKM</th>
            <th>Nama Kegiatan</th>
            <th>Deskripsi</th>
            <th>Jenis Kegiatan</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Define Indonesian month names
    $indonesianMonths = array(
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
        'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );

    while ($row_kegiatan = mysqli_fetch_assoc($result_kegiatan)) {
        // Output table rows
        echo "<tr>";
        echo "<td>" . $row_kegiatan['id_kegiatan'] . "</td>";
        echo "<td>" . $row_kegiatan['id_ukm'] . "</td>";
        echo "<td>" . $row_kegiatan['nama_ukm'] . "</td>";
        echo "<td>" . $row_kegiatan['nama_kegiatan'] . "</td>";
        echo "<td>" . $row_kegiatan['deskripsi'] . "</td>";
        echo "<td>" . $row_kegiatan['jenis'] . "</td>";
        echo "<td>" . date('d', strtotime($row_kegiatan['tgl'])) . " " . $indonesianMonths[intval(date('m', strtotime($row_kegiatan['tgl']))) - 1] . " " . date('Y', strtotime($row_kegiatan['tgl'])) . "</td>";
        echo "<td>
                <a href='edit_kegiatan.php?id_kegiatan=" . $row_kegiatan['id_kegiatan'] . "'>Edit</a>
                <a href='delete_kegiatan.php?id_kegiatan=" . $row_kegiatan['id_kegiatan'] . "' onclick='return confirmDelete(\"" . $row_kegiatan['nama_kegiatan'] . "\");'>Hapus</a>
            </td>";
        echo "</tr>";
    }
    ?>
</tbody>
</table>

 

<!-- Add your JavaScript code here to populate the nama_ukm field -->
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
<script>
    // Fungsi untuk mengaktifkan modal ketika tombol "Tambah Kegiatan" di dalam modal diklik
    document.getElementById("submitForm").addEventListener("click", function() {
        // Simulasikan klik pada tombol submit di form sebenarnya
        document.querySelector(".modal-body form").submit();
    });
</script>

</body>
</html>