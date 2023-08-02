<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Inisialisasi variabel pesan error
$error = '';

// Memeriksa apakah form pendaftaran telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $id_user = $_POST['id_user'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword']; // Add this line to get the value of "Konfirmasi Password"
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];

    // Validate email
    if (empty($email)) {
        $error = "Email harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak benar. Masukkan email yang valid.";
    } else {
        // Other validation and database operations here
    }

    // Memeriksa apakah password dan konfirmasi password cocok
    if ($password !== $confirmPassword) {
        $error = "Password dan konfirmasi password tidak cocok";
    } else {
        // Memeriksa apakah ID User (NIM) sudah digunakan
        $query = "SELECT * FROM tab_user WHERE id_user = '$id_user'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $error = "ID User (NIM) sudah terdaftar. Silakan gunakan ID User (NIM) lain.";
        } else {
            // Memasukkan data pengguna ke dalam tabel tab_user
            $query = "INSERT INTO tab_user (id_user, password, nama_lengkap, email, no_hp, level) VALUES ('$id_user', '$password', '$nama_lengkap', '$email', '$no_hp', '3')";
            if (mysqli_query($conn, $query)) {
                // Pendaftaran berhasil, simpan session dan redirect ke beranda
                $_SESSION['id_user'] = $id_user;
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['level'] = '3';
                header("Location: beranda.php");
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Pengguna</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        form div {
            margin-bottom: 10px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0069d9;
        }

        p.error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>Registrasi Pengguna</h1>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div>
            <label for="id_user">ID User (NIM):</label>
            <input type="text" id="id_user" name="id_user" required placeholder="Masukkan ID User (NIM)" oninput="validateIDUser()">
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="text" id="password" name="password" required placeholder="Masukkan password">
        </div>
        <div>
            <label for="confirmPassword">Konfirmasi Password:</label>
            <input type="text" id="confirmPassword" name="confirmPassword" required placeholder="Ulangi password">
            <?php if ($error && $password !== $confirmPassword) : ?>
                <p class="error-message">Password dan Konfirmasi Password tidak sesuai.</p>
            <?php endif; ?>
        </div>
        <div>
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Masukkan Nama Lengkap">
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Masukkan Email">
        </div>
        <div>
            <label for="no_hp">Nomor HP:</label>
            <input type="text" id="no_hp" name="no_hp" required placeholder="Masukkan Nomor HP" pattern="[0-9]{1,13}" title="Hanya angka dengan maksimal 13 digit diperbolehkan">
        </div>
        <?php if ($error) : ?>
            <p class="error-message"><?php echo $error; ?></p>
            <script>
                alert("<?php echo $error; ?>"); // Add this line to display the error in an alert
            </script>
        <?php endif; ?>
        <div>
            <button type="submit">Daftar</button>
        </div>
    </form>

    <div id="snackbar"></div>

    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function showSnackbar(message) {
            var snackbar = document.getElementById("snackbar");
            snackbar.innerHTML = message;
            snackbar.className = "show";
            setTimeout(function() {
                snackbar.className = snackbar.className.replace("show", "");
            }, 3000);
        }
        function validateIDUser() {
        var idUserInput = document.getElementById('id_user');
        var idUserValue = idUserInput.value.trim();
        var numericRegex = /^[0-9]+$/;

        if (idUserValue === '') {
            idUserInput.setCustomValidity('ID User (NIM) harus diisi.');
        } else if (!numericRegex.test(idUserValue)) {
            idUserInput.setCustomValidity('ID User (NIM) hanya dapat diisi dengan angka.');
        } else if (idUserValue.length > 11) {
            idUserInput.setCustomValidity('ID User (NIM) maksimal 11 digit.');
        } else {
            idUserInput.setCustomValidity('');
        }
    }
      // Set focus to the appropriate field on page load
      window.onload = function() {
            <?php
            if (isset($_POST['confirmPassword']) && $password !== $confirmPassword) {
                echo 'document.getElementById("confirmPassword").focus();';
            } elseif (isset($_POST['email']) && (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
                echo 'document.getElementById("email").focus();';
            }
            ?>
        };

    // Check if there is any form data to populate
    <?php if (isset($_SESSION['form_data'])) : ?>
        var formData = <?php echo json_encode($_SESSION['form_data']); ?>;
        document.getElementById("id_user").value = formData.id_user;
        document.getElementById("nama_lengkap").value = formData.nama_lengkap;
        document.getElementById("email").value = formData.email;
        document.getElementById("no_hp").value = formData.no_hp;
        <?php unset($_SESSION['form_data']); ?>
    <?php endif; ?>
    </script>

    <?php if (isset($_SESSION['registration_success'])) : ?>
        <script>
            $(document).ready(function() {
                var message = "<?php echo $_SESSION['registration_success']; ?>";
                showSnackbar(message);
            });
        </script>
    <?php endif; ?>
</body>
</html>
