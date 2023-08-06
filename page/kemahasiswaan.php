<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menandai halaman yang aktif
$active_page = 'dashboard';


// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}

// Mengambil data pengguna dari tabel tab_user berdasarkan ID yang ada di session
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM tab_user WHERE id_user = '$userId'";

// Mengeksekusi query
$result = mysqli_query($conn, $query);

// Memeriksa apakah query berhasil dieksekusi
if ($result) {
    // Mengambil data pengguna
    $user = mysqli_fetch_assoc($result);

    // Menyimpan data pengguna ke dalam variabel session
    $_SESSION['nama_depan'] = $user['nama_depan'];
    $_SESSION['nama_belakang'] = $user['nama_belakang'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['no_hp'] = $user['no_hp'];
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Kemahasiswaan</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
  .navbar .logout-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            color: #fff;
        }

        .navbar .logout-btn:hover {
            text-decoration: underline;
        }

 .card {
            background-color: #007bff;
            color: #fff;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            background-color: #4213;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .user-info {
             /* Add spacing between sidebar and user-info */
        }

                .profil-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: 4px solid #fff; /* Tambahkan border putih di sekitar foto */
        }

        .profile-details {
            flex: 1; /* Allow the details section to take up remaining space */
           
        }

                .label {
            font-weight: bold;
            color: #333;
        }

        .value {
            color: #555;
        }

        .divider {
            border: none;
            border-top: 2px solid #ccc;
            margin-bottom: 20px;
        }
        .btn {
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 10px 20px;
    cursor: pointer;
}

.btn:hover {
    background-color: #218838;
}
.wrapper {
            flex: 1 0 auto; /* Buat wrapper "sticky" dengan flex-grow: 1 dan flex-shrink: 0 */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-bottom: 100px; /* Atur padding-bottom agar ada ruang di antara konten dan footer */
        }
        .white-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    } .sidebar img {
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
    p {
        color: black;
    }
    .scrollable-content {
        height: 300px; /* Set the desired fixed height for the scrollable area */
        overflow-y: auto; /* Enable vertical scrolling when content overflows */
        /* Optional: Add padding to the scrollable area */
    }
    </style>
</head>
<div class="sidebar">
    <a href="beranda.php">
  <img src="../assets/images/siukm-logo.png" alt="Profile Picture" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
</a>
<h2><i>Dashboard</i></h2>
<a href="kemahasiswaan.php" class="btn btn-primary <?php if ($active_page == 'dashboard') echo 'active'; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <p style="text-align: center;">--Manajemen--</p>
            <a href="view_dau.php" class="btn btn-primary btn-manajemen <?php if($active_page == 'view_dau') echo 'active'; ?>">Data Anggota</a>
            <a href="?logout=1" class="btn btn-primary" id="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
    <h1>Dashboard Kemahasiswaan</h1>
    <hr class="divider">
    <div class="user-info">
    <div class="profile-container">
        <img src="../assets/images/sanji.jpg" alt="Foto Profil" class="profil-picture">
    </div>
    <div class="profile-details">
        <p><span class="label">Nama Depan:</span> <span class="value"><?php echo $_SESSION['nama_depan']; ?></span></p>
        <p><span class="label">Nama Belakang:</span> <span class="value"><?php echo $_SESSION['nama_belakang']; ?></span></p>
        <p><span class="label">Email:</span> <span class="value"><?php echo $_SESSION['email']; ?></span></p>
        <p><span class="label">Nomor Telepon:</span> <span class="value"><?php echo $_SESSION['no_hp']; ?></span></p>
    </div>
</div>
    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
