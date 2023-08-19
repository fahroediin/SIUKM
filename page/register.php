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
    $query_bg = "SELECT bg_login FROM tab_beranda LIMIT 1"; // Retrieve the first row
$result_bg = mysqli_query($conn, $query_bg);
if ($result_bg && mysqli_num_rows($result_bg) > 0) {
    $row_bg = mysqli_fetch_assoc($result_bg);
    $background_image_filename = $row_bg["bg_login"];

    // Construct the background image URL
    $background_image_url = "../assets/images/bg/" . $background_image_filename;
} else {
    $background_image_url = ""; // Set a default image URL if not found
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Pengguna</title>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="../assets/js/script.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
        .container {
			z-index: 1;
			background-color: #fff;
			border-radius: 5px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.2);
			margin: 80px auto;
			max-width: 400px;
			padding: 20px;
			display: flex;
			flex-direction: column;
			align-items: center; /* Tengahkan horizontal */
			opacity: 0.90;
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
    .is-invalid {
    border-color: red;
}
    </style>
</head>
<body style="background-image: url('<?php echo $background_image_url; ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
<div class="container">   
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <h1>Register</h1>
    <div>
    <label for="id_user">*ID User (NIM)</label>
    <input type="text" class="form-control" id="id_user" placeholder="Masukan NIM anda" maxlength="10" name="id_user" required>
    <div class="invalid-feedback" id="id-user-error" style="color: red;"></div>
</div>
<script>
document.getElementById("id_user").addEventListener("input", function(event) {
    let input = event.target.value;
    input = input.replace(/\D/g, ''); // Menghapus karakter non-angka
    input = input.slice(0, 10); // Membatasi panjang maksimal menjadi 10 karakter
    event.target.value = input;

    let errorElement = document.getElementById("id-user-error");
    if (input.length < 9 || input.length > 10 || !/^[0-9]+$/.test(input)) {
        errorElement.textContent = "ID User harus NIM dengan panjang minimal 9 dan maksimal 10 digit angka!";
        event.target.classList.add("is-invalid"); // Tambahkan class is-invalid untuk merahkan input
    } else {
        errorElement.textContent = "";
        event.target.classList.remove("is-invalid"); // Hapus class is-invalid jika valid
    }
});
</script>
<div class="form-group">
    <label for="password">*Password:</label>
    <div class="password-input">
        <input type="password" class="form-control" placeholder="Password kombinasi huruf angka" id="password" name="password" required>
        <i class="fas fa-eye" id="passwordToggle1"></i>
    </div>
</div>
<div class="form-group">
    <label for="konfirmasi_password">*Konfirmasi Password</label>
    <div class="password-input">
        <input type="password" class="form-control" placeholder="Ulangi password" id="konfirmasi_password" name="confirmPassword" required>
        <i class="fas fa-eye" id="passwordToggle2"></i>
        <div class="invalid-feedback" id="passwordMismatchFeedback">
            Password tidak sesuai.
        </div>
    </div>
</div>
<script>
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('konfirmasi_password');
    const mismatchFeedback = document.getElementById('passwordMismatchFeedback');

    confirmPasswordInput.addEventListener('input', () => {
        if (passwordInput.value !== confirmPasswordInput.value) {
            mismatchFeedback.style.display = 'block';
            confirmPasswordInput.setCustomValidity('Password tidak sesuai');
        } else {
            mismatchFeedback.style.display = 'none';
            confirmPasswordInput.setCustomValidity('');
        }
    });
</script>
    <div>
    <label for="nama_lengkap">*Nama Lengkap</label>
    <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Masukkan Nama Lengkap" oninput="validasiHuruf(event)">
    <div id="invalid-error" class="invalid-feedback" style="color: red;"></div>
</div>

<script>
document.getElementById("nama_lengkap").addEventListener("input", function(event) {
    let input = event.target.value;
    input = input.replace(/[^A-Za-z\s]/g, ''); // Menghapus karakter non-huruf
    input = input.slice(0, 70); // Membatasi panjang maksimal menjadi 70 karakter
    event.target.value = input;

    let errorElement = document.getElementById("invalid-error");
    if (input.length < 1 || input.length > 70) {
        errorElement.textContent = "Nama lengkap harus terdiri dari huruf saja dan memiliki panjang 1-70 karakter!";
        event.target.classList.add("is-invalid"); // Tambahkan class is-invalid untuk merahkan input
    } else {
        errorElement.textContent = "";
        event.target.classList.remove("is-invalid"); // Hapus class is-invalid jika valid
    }
});
</script>
        <script>
        function validasiHuruf(event) {
            const input = event.target;
            const filteredValue = input.value.replace(/[^A-Za-z\s]/g, '');
            input.value = filteredValue;
        }
        </script>


       <div>
    <label for="email">*Email</label>
    <input type="email" id="email" name="email" required placeholder="Masukkan Email">
    <div id="email-error" class="invalid-feedback" style="color: red;"></div>
</div>

<script>
document.getElementById("email").addEventListener("input", function(event) {
    let input = event.target.value;

    let errorElement = document.getElementById("email-error");
    if (!validateEmail(input)) {
        errorElement.textContent = "Email harus memiliki format yang valid (contoh: nama@example.com)";
        event.target.classList.add("is-invalid"); // Tambahkan class is-invalid untuk merahkan input
    } else {
        errorElement.textContent = "";
        event.target.classList.remove("is-invalid"); // Hapus class is-invalid jika valid
    }
});

function validateEmail(email) {
    // Menggunakan regular expression untuk memeriksa format email
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|co\.id|net|org|edu|gov|mil|biz|info|name|museum|us|ca|uk|au|eu|jp|ae|in|cn|br|ch|es|fr|it|kr|ru|sa|se|za)$/i;
    return emailPattern.test(email);
}
</script>

<div>
    <label for="no_hp">*Nomor HP</label>
    <input type="text" id="no_hp" name="no_hp" required placeholder="Masukkan Nomor HP" oninput="validasiNomorHP(event)">
    <div id="no-hp-error" class="invalid-feedback" style="color: red;"></div>
</div>

<script>
document.getElementById("no_hp").addEventListener("input", function(event) {
    validasiNomorHP(event);
});

function validasiNomorHP(event) {
    const input = event.target;
    let filteredValue = input.value.replace(/\D/g, ''); // Menghapus karakter non-angka
    filteredValue = filteredValue.slice(0, 13); // Membatasi panjang maksimal menjadi 13 karakter

    let errorElement = document.getElementById("no-hp-error");
    const minLength = 10;
    const maxLength = 13;

    if (filteredValue.length < minLength || filteredValue.length > maxLength) {
        errorElement.textContent = `Nomor HP harus terdiri dari ${minLength} - ${maxLength} digit angka.`;
        input.classList.add("is-invalid"); // Tambahkan class is-invalid untuk merahkan input
    } else if (!filteredValue.startsWith("08")) {
        errorElement.textContent = "Nomor HP harus diawali dengan '08'.";
        input.classList.add("is-invalid"); // Tambahkan class is-invalid untuk merahkan input
    } else {
        errorElement.textContent = "";
        input.classList.remove("is-invalid"); // Hapus class is-invalid jika valid
    }

    input.value = filteredValue; // Update nilai input yang telah difilter
}
</script>
<div>
    <button type="submit">Daftar</button>
</form>
<br>
<p>Sudah memiliki akun? <a href="login.php" style="color: black;"><i class="fas fa-sign-in-alt"></i> Login</a></p>
</div>

    <!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");

    form.addEventListener("submit", function(event) {
        let isValid = true;

        // Cek setiap input field
        const inputFields = form.querySelectorAll("input");
        inputFields.forEach(function(input) {
            if (input.classList.contains("is-invalid")) {
                isValid = false;
                event.preventDefault(); // Hentikan pengiriman data
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains("invalid-feedback")) {
                    errorElement.style.display = "block"; // Tampilkan pesan error
                }
            }
        });

        if (!isValid) {
            // Tampilkan pesan kesalahan di atas form (opsional)
            const errorMessage = document.getElementById("global-error-message");
            errorMessage.textContent = "Terdapat kesalahan pada data yang dimasukkan.";
            errorMessage.style.display = "block";
        }
    });
});
</script>
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
