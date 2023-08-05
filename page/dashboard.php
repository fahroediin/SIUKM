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

if ($result) {
    // Mengambil data pengguna
    $user = mysqli_fetch_assoc($result);

    // Menyimpan data pengguna ke dalam variabel
    $nama_lengkap = $user['nama_lengkap'];
    $email = $user['email'];
    $no_hp = $user['no_hp'];
    $prodi = $user['prodi']; // Assuming "prodi" is the field name in the database
    $semester = $user['semester']; // Assuming "semester" is the field name in the database

    // Check if the pasfoto field is not empty
    if (!empty($user['pasfoto'])) {
        // Assuming the "pasfoto" field contains only the filename (e.g., "sanji.jpg")
        $pasfotoFilename = $user['pasfoto'];
        // Assuming the path to the pasfoto directory is "../assets/images/pasfoto/"
        $pasfoto = "../assets/images/pasfoto/" . $pasfotoFilename;
    } else {
        // If pasfoto field is empty or not set, provide a default image path
        $pasfoto = "../assets/images/default_pasfoto.jpg"; // Change this to your desired default image path
    }
} else {
    // Jika query gagal, Anda dapat menambahkan penanganan kesalahan sesuai kebutuhan
    echo "Error: " . mysqli_error($conn);
}

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
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Pengguna</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">

    <style>


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
            display: flex;
            align-items: center;
            margin-top: 20px; /* Add some spacing between the buttons and card */
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
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Dashboard</h2>
        <a href="dashboard.php" class="btn btn-primary <?php if ($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
        <a href="beranda.php" class="btn btn-primary <?php if ($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
        <a href="?logout=true" class="btn btn-primary <?php if ($active_page == 'logout') echo 'active'; ?>">Logout</a>
    </div>

    <div class="content">
        <h1>Informasi Pengguna</h1>
       <!-- Tombol Ganti Password -->
<a href="ganti_password_pengguna.php" class="btn btn-primary"><i class="fas fa-key"></i> Ganti Password</a>

<!-- Tombol Update Data Diri -->
<a href="update_pengguna.php" class="btn btn-primary"><i class="fas fa-user-edit"></i> Update Data Diri</a>
        <hr class="divider">
        <div class="card shadow user-info"> <!-- Add 'card' and 'shadow' classes here -->
            <div class="row">
                <!-- Left column for profile picture -->
                <div class="col-md-4">
                    <div class="profile-container">
                        <img src="<?php echo $pasfoto; ?>" alt="Foto Profil" class="profil-picture">
                    </div>
                </div>
                <!-- Right column for user information -->
                <div class="col-md-8">
                    <div class="profile-details">
                        <p><span class="label">Nama Lengkap:</span> <span class="value"><?php echo $nama_lengkap; ?></span></p>
                        <p><span class="label">Email:</span> <span class="value"><?php echo $email; ?></span></p>
                        <p><span class="label">Nomor Telepon:</span> <span class="value"><?php echo $no_hp; ?></span></p>
                        <p><span class="label">Prodi:</span> <span class="value"><?php echo $prodi; ?></span></p>
                        <p><span class="label">Semester:</span> <span class="value"><?php echo $semester; ?></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
<script>
  // Ambil elemen toggle button dan content
  const toggleBtn = document.querySelector(".toggle-btn");
  const content = document.querySelector(".content");

  // Tambahkan event listener untuk toggle button
  toggleBtn.addEventListener("click", function () {
    // Toggle class 'collapsed' pada content dan sidebar
    content.classList.toggle("collapsed");
    document.querySelector(".sidebar").classList.toggle("collapsed");
  });
</script>
</body>

</html>
