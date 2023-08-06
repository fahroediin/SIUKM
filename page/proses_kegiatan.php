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
if ($_SESSION['level'] == "2" || $_SESSION['level'] == "3") {
    // Jika level adalah "2" atau "3", redirect ke halaman beranda.php
    header("Location: beranda.php");
    exit();
}

// Menandai halaman yang aktif
$active_page = 'kegiatan';

// Function to generate id_kegiatan based on tgl and random number
function generateIdKegiatan($tgl)
{
    // Convert the date to a DateTime object
    $date = new DateTime($tgl);
    
    // Extract the year, month, and day from the DateTime object
    $year = $date->format('Y');
    $month = $date->format('m');
    $day = $date->format('d');

    // Generate a unique random number
    $randomNumber = sprintf("%04d", mt_rand(1, 9999));

    // Concatenate the year, month, day, and random number without hyphens
    return $year . $month . $day . $randomNumber;
}


// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $id_ukm = mysqli_real_escape_string($conn, $_POST["id_ukm"]);
    $nama_ukm = mysqli_real_escape_string($conn, $_POST["nama_ukm"]);
    $nama_kegiatan = mysqli_real_escape_string($conn, $_POST["nama_kegiatan"]);
    $tgl = mysqli_real_escape_string($conn, $_POST["tgl"]);
            
    // Generate a unique id_kegiatan based on tgl and random number
    $id_kegiatan = generateIdKegiatan($tgl);

        // Prepare the SQL query to insert data into tab_galeri table
    $sql = "INSERT INTO tab_kegiatan (id_ukm, nama_ukm, id_kegiatan, nama_kegiatan, tgl) VALUES (?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters
    $stmt->bind_param("sssss", $id_ukm, $nama_ukm, $id_kegiatan, $nama_kegiatan, $tgl);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the same page with a success parameter
            header("Location: proses_kegiatan.php?success=1");
            exit();
        } else {
            // Handle the error condition, for example:
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }
// Mendapatkan data ID UKM dan nama UKM dari tabel tab_ukm
$query_ukm = "SELECT id_ukm, nama_ukm FROM tab_ukm";
$result_ukm = mysqli_query($conn, $query_ukm);

$query_kegiatan = "SELECT id_kegiatan, nama_kegiatan, id_ukm, nama_ukm, tgl FROM tab_kegiatan";
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

<div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Jadwal Kegiatan</i></h2>
            <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
            <a href="proses_struktur.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'struktur') echo 'active'; ?>">Pengurus</a>
    <a href="proses_dau.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_prestasi.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_ukm.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'ukm') echo 'active'; ?>">Data UKM</a>
    <a href="proses_galeri.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="proses_kegiatan.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
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
    <h2>Data Kegiatan</h2>
    <table class="table">
        <thead>
            <tr>
            <th>ID Kegiatan</th>
            <th>ID UKM</th>
            <th>Nama UKM</th>
            <th>Nama Kegiatan</th>
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
        echo "<td>" . date('d', strtotime($row_kegiatan['tgl'])) . " " . $indonesianMonths[intval(date('m', strtotime($row_kegiatan['tgl']))) - 1] . " " . date('Y', strtotime($row_kegiatan['tgl'])) . "</td>";
        echo "<td>
                <a href='edit_kegiatan.php?id_kegiatan=" . $row_kegiatan['id_kegiatan'] . "'>Edit</a>
                <a href='delete_kegiatan.php?id_kegiatan=" . $row_kegiatan['id_kegiatan'] . "'>Hapus</a>
            </td>";
        echo "</tr>";
    }
    ?>
</tbody>
</table>
<div class="container">
            <div class="row justify-content-center">
                <!-- Wrap the form with a card component -->
                <div class="card">
                    <div class="card-body">
                        <h2 style="text-align: center;">Tambah Kegiatan</h2>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                            enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="id_ukm">ID UKM:</label>
                                <select id="id_ukm" name="id_ukm" class="form-control" required>
                                    <option value="" selected disabled>Pilih ID UKM</option>
                                    <?php
                                        // Membuat opsi combobox dari hasil query
                                        while ($row_ukm = mysqli_fetch_assoc($result_ukm)) {
                                            echo "<option value='" . $row_ukm['id_ukm'] . "'>" . $row_ukm['id_ukm'] . "</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nama_ukm">Nama UKM:</label>
                                <input type="text" id="nama_ukm" name="nama_ukm" class="form-control" readonly>
                            </div>
                           
                                <input type="hidden" id="id_kegiatan" name="id_kegiatan" class="form-control" readonly>
                          
                                <div class="form-group">
                                <label for="nama_kegiatan">Nama Kegiatan:</label>
                                <input type="text" id="nama_kegiatan" placeholder="Nama Kegiatan Maksimal 15 karakter" name="nama_kegiatan" class="form-control" required maxlength="15" minlength="3">
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
    </div>

<!-- Add your JavaScript code here to populate the nama_ukm field -->
<script>
    const idUkmSelect = document.getElementById("id_ukm");
    const namaUkmField = document.getElementById("nama_ukm");

    idUkmSelect.addEventListener("change", function() {
        const selectedOption = idUkmSelect.options[idUkmSelect.selectedIndex];
        const idUkm = selectedOption.value;
        if (idUkm) {
            // Mengirim permintaan AJAX ke server
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    // Mengambil respons dari server dalam bentuk JSON
                    var data = JSON.parse(this.responseText);
                    if (data.error) {
                        // Handle error response from the server
                        console.error(data.error);
                        namaUkmField.value = ""; // Clear the field in case of an error
                    } else {
                        // Update nama_ukm field with the retrieved data
                        namaUkmField.value = data.nama_ukm;
                    }
                }
            };

            // Send the AJAX request to the server
            xhttp.open("GET", "get_data_ukm.php?id_ukm=" + idUkm, true);
            xhttp.send();
        } else {
            // If no id_ukm is selected, reset the nama_ukm field
            namaUkmField.value = "";
        }
    });
    // Fungsi untuk logout
    function logout() {
        // Redirect ke halaman logout
        window.location.href = "?logout=true";
    }
</script>
</body>
</html>