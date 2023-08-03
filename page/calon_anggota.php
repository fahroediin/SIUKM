<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menandai halaman yang aktif
$active_page = 'calon_anggota';


// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Calon Anggota - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
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
    </style>
</head>
<body>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2">
                <div class="sidebar">
                    <h2>Daftar Calon Anggota</h2>
                    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
                    <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
                    <a href="proses_struktur.php" class="btn btn-primary <?php if($active_page == 'struktur') echo 'active'; ?>">Kepengurusan</a>
                    <a href="proses_dau.php" class="btn btn-primary <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
                    <a href="proses_prestasi.php" class="btn btn-primary <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
                    <a href="proses_user.php" class="btn btn-primary <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
                    <a href="proses_ukm.php" class="btn btn-primary <?php if($active_page == 'visi_misi') echo 'active'; ?>">Data UKM</a>
                    <a href="proses_galeri.php" class="btn btn-primary <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
                    <a href="proses_kegiatan.php" class="btn btn-primary <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
                    <a href="calon_anggota.php" class="btn btn-primary <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
                </div>
            </div>
                    <div class="content">
                    <div class="container">
                    <h2>Daftar Calon Anggota</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Calabar</th>
                                <th>ID User</th>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Prodi</th>
                                <th>ID UKM</th>
                                <th>Nama UKM</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Pasfoto</th>
                                <th>Foto UKM</th>
                                <th>Alasan</th>
                                <th>Nilai TPA</th>
                                <th>Aksi</th> <!-- Kolom "Aksi" untuk tombol "Update" dan "Delete" -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Mengambil data calabar dari database
                            $query = "SELECT * FROM tab_pacab";
                            $result = mysqli_query($conn, $query);

                            // Menampilkan data dalam tabel
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['id_calabar'] . "</td>";
                                echo "<td>" . $row['id_user'] . "</td>";
                                echo "<td>" . $row['nama_lengkap'] . "</td>";
                                echo "<td>" . $row['nim'] . "</td>";
                                echo "<td>" . $row['prodi'] . "</td>";
                                echo "<td>" . $row['id_ukm'] . "</td>";
                                echo "<td>" . $row['nama_ukm'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['no_hp'] . "</td>";
                                echo "<td><img src='../assets/images/" . $row['pasfoto'] . "' width='100'></td>";
                                echo "<td><img src='../assets/images/" . $row['foto_ktm'] . "' width='100'></td>";
                                echo "<td>" . $row['alasan'] . "</td>";
                                echo "<td>" . $row['nilai_tpa'] . "</td>";
                                echo "<td>";
                                // Tombol "Update" untuk menampilkan pilihan "Lolos" dan "Tidak Lolos"
                                echo "<button class='btn btn-primary' onclick='showUpdateOptions(" . $row['id_calabar'] . ")'>Update</button>";
                                // Kotak pilihan untuk "Lolos" dan "Tidak Lolos" (sembunyikan awalnya)
                                echo "<div id='update_options_" . $row['id_calabar'] . "' style='display:none;'>";
                                echo "<button class='btn btn-success' onclick='updateNilaiTPA(" . $row['id_calabar'] . ", \"Lolos\")'>Lolos</button>";
                                echo "<button class='btn btn-danger' onclick='updateNilaiTPA(" . $row['id_calabar'] . ", \"Tidak Lolos\")'>Tidak Lolos</button>";
                                echo "</div>";
                                echo "</td>";
                                // Tombol "Delete"
                                echo "<td>";
                                echo "<button class='btn btn-danger' onclick='deleteData(" . $row['id_calabar'] . ")'>Delete</button>";
                                echo "</td>";
                                echo "</tr>";
                            }

                            // Menutup koneksi
                            mysqli_close($conn);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>

     // Function to show the alert and update nilai_tpa
     function updateNilaiTPA(id, status) {
        var updateOptions = document.getElementById('update_options_' + id);
        updateOptions.style.display = 'none'; // Hide the update options after choosing

        // Show the alert with the status
        alert('Calon Anggota dengan ID ' + id + ' dinyatakan ' + status);

        // Perform the AJAX request to update nilai_tpa using update_nilai_tpa.php
        $.ajax({
            url: 'update_nilai_tpa.php', // Update this with the correct file path
            type: 'POST',
            data: { id: id, status: status },
            success: function(response) {
                // Refresh the page after successful update
                location.reload();
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

</body>
</html>
