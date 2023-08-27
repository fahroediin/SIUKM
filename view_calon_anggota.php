<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
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
// Menandai halaman yang aktif
$active_page = 'view_calon_anggota';

// Fungsi logout
function logout() {
    // Menghapus semua data session
    session_unset();
    // Menghancurkan session
    session_destroy();
    // Mengarahkan pengguna ke index.php setelah logout
    header("Location: index.php");
    exit();
}

// Memeriksa apakah tombol logout diklik
if (isset($_GET['logout'])) {
    // Memanggil fungsi logout
    logout();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Calon Anggota - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Perubahan pada tampilan tombol */
        .update-button {
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }

        .delete-button {
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
        }
        th {
        white-space: nowrap;
    }
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
    </style>
</head>
<body>
    

<div class="sidebar">
    <a href="index.php">
  <img src="./assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
    <h2><i>Calon Anggota</i></h2>
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
    wrapButtonsWithBorder();
</script>
                    <div class="content">
                    <h2>Daftar Calon Anggota</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Calabar</th>
                                <th>ID User</th>
                                <th>Nama Lengkap</th>
                                <th>Prodi</th>
                                <th>ID UKM</th>
                                <th>Nama UKM</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Pasfoto</th>
                                <th>Foto UKM</th>
                                <th>Alasan</th>
                                <th>Nilai TPA</th>
                                <th>Status</th>
                                <th>Diterima/Tidak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                                               <tbody>
                            <?php
                            // Mengambil data calabar dari database
                            $query = "SELECT * FROM tab_pacab";
                            $result = mysqli_query($conn, $query);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['id_calabar'] . "</td>";
                                echo "<td>" . $row['id_user'] . "</td>";
                                echo "<td>" . $row['nama_lengkap'] . "</td>";
                                echo "<td>" . $row['prodi'] . "</td>";
                                echo "<td>" . $row['id_ukm'] . "</td>";
                                echo "<td>" . $row['nama_ukm'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['no_hp'] . "</td>";
                                                            // Display pasfoto image
                                echo "<td><img src='./assets/images/pasfoto/" . $row['pasfoto'] . "' width='100'></td>";
                                // Display foto_ktm image
                                echo "<td><img src='./assets/images/ktm/" . $row['foto_ktm'] . "' width='100'></td>";
                                echo "<td>" . $row['alasan'] . "</td>";
                                echo "<td>" . $row['nilai_tpa'] . "</td>";
                                echo "<td>" . $row['status_cab'] . "</td>";
                                echo "<td>";
                                echo "<select id='status_calabar_" . $row['id_calabar'] . "'>";
                                echo "<option value='Diterima'>Diterima</option>";
                                echo "<option value='Tidak Diterima'>Tidak Diterima</option>";
                                echo "</select>";
                                echo "</td>";
                                echo "<td>";
                                echo "<button class='btn btn-primary' onclick='updateStatus(" . $row['id_calabar'] . ")'>Update</button>";
                                echo "</td>";               
                            }

                        } else {
                            // Jika tidak ada data, tampilkan pesan dalam baris tabel khusus
                            echo '<tr><td colspan="15" class="text-center">Tidak ada data pendaftar anggota baru.</td></tr>';
                        }
                
                        // Menutup koneksi
                        mysqli_close($conn);
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>

     // Function to update the status using AJAX
     function updateStatus(id_calabar) {
        const selectedStatus = document.getElementById('status_calabar_' + id_calabar).value;

        // Perform the AJAX request to update status
        $.ajax({
            url: 'update_status.php',
            type: 'POST',
            data: { id_calabar: id_calabar, status: selectedStatus },
            success: function(response) {
                alert('Status updated successfully.');
                location.reload(); // Refresh the page
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Fungsi untuk menghapus data calabar
    function deleteData(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            // Buat AJAX request untuk menghapus data calabar berdasarkan ID
            $.ajax({
                url: 'delete_calabar.php', // Ganti dengan file PHP yang menghapus data calabar berdasarkan ID
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    // Refresh halaman setelah data berhasil dihapus
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    }
</script>
<script src="script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
