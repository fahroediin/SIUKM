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
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];

    // Validate email
    if (empty($email)) {
        $error = "Email harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak benar. Masukkan email yang valid.";
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
        .password-input {
    position: relative;
    }

    .password-input input {
    padding-right: 30px; /* To make space for the icon */
    }

    .password-input i {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    }
    </style>
</head>
<body>
    <h1>Registrasi Pengguna</h1>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div>
        <label for="id_user">ID User (NIM):</label>
        <input type="text" id="id_user" name="id_user" required placeholder="Masukkan ID User (NIM)" oninput="validasiIdUser(event, 10)">
        </div>
    <!-- Add this part to display the error message -->
    <?php if ($error) : ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>
</div>
        <script>
        function validasiIdUser(event, maxLength) {
            const input = event.target;
            const filteredValue = input.value.replace(/[^0-9]/g, '').slice(0, maxLength);
            input.value = filteredValue;
        }
        </script>

<div class="form-group">
    <label for="password">Password:</label>
    <div class="password-input">
        <input type="password" class="form-control" id="password" name="password" required>
        <i class="fas fa-eye" id="passwordToggle1"></i>
    </div>
</div>
<div class="form-group">
    <label for="konfirmasi_password">Konfirmasi Password:</label>
    <div class="password-input">
        <input type="password" class="form-control" id="konfirmasi_password" name="confirmPassword" required>
        <i class="fas fa-eye" id="passwordToggle2"></i>
    </div>


        <div>
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Masukkan Nama Lengkap" oninput="validasiHuruf(event)">
        </div>

        <script>
        function validasiHuruf(event) {
            const input = event.target;
            const filteredValue = input.value.replace(/[^A-Za-z\s]/g, '');
            input.value = filteredValue;
        }
        </script>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Masukkan Email">
        </div>
        <div>
            <label for="no_hp">Nomor HP:</label>
            <input type="text" id="no_hp" name="no_hp" required placeholder="Masukkan Nomor HP" oninput="validasiAngka(event, 15)">
        </div>
        <script>
        function validasiAngka(event, maxLength) {
            const input = event.target;
            const filteredValue = input.value.replace(/[^0-9]/g, '').slice(0, maxLength);
            input.value = filteredValue;
        }
        </script>
        <div>
            <button type="submit">Daftar</button>
        </div>
    </form>

    <div id="snackbar"></div>

    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
    function validatePassword() {
        var passwordInput = document.getElementById("password");
        var confirmPasswordInput = document.getElementById("konfirmasi_password");
        var password = passwordInput.value;
        var confirmPassword = confirmPasswordInput.value;

        if (password !== confirmPassword) {
            showSnackbar("Password tidak sesuai");
            confirmPasswordInput.focus();
            return false;
        }

        return true;
    }

    function validateForm() {
        return validateIDUser() && validatePassword();
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
    <script>
    const passwordInput1 = document.getElementById("password");
    const passwordToggle1 = document.getElementById("passwordToggle1");
    const passwordInput2 = document.getElementById("konfirmasi_password");
    const passwordToggle2 = document.getElementById("passwordToggle2");

    passwordToggle1.addEventListener("click", function () {
        if (passwordInput1.type === "password") {
            passwordInput1.type = "text";
            passwordToggle1.classList.remove("fa-eye");
            passwordToggle1.classList.add("fa-eye-slash");
        } else {
            passwordInput1.type = "password";
            passwordToggle1.classList.remove("fa-eye-slash");
            passwordToggle1.classList.add("fa-eye");
        }
    });

    passwordToggle2.addEventListener("click", function () {
        if (passwordInput2.type === "password") {
            passwordInput2.type = "text";
            passwordToggle2.classList.remove("fa-eye");
            passwordToggle2.classList.add("fa-eye-slash");
        } else {
            passwordInput2.type = "password";
            passwordToggle2.classList.remove("fa-eye-slash");
            passwordToggle2.classList.add("fa-eye");
        }
    });
    </script>
</body>
</html>
