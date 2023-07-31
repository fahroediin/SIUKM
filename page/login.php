<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Menonaktifkan pesan error
error_reporting(0);

// Menyimpan nilai id_user, nama_user, dan level ke dalam session
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan nilai input dari form
    $id_user = $_POST["id_user"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Mempersiapkan pernyataan SQL untuk memeriksa username dan password
    $query = "SELECT * FROM tab_user WHERE id_user = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    // Memasukkan nilai parameter ke pernyataan SQL
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);

    // Menjalankan pernyataan SQL
    mysqli_stmt_execute($stmt);

    // Mengambil hasil query
    $result = mysqli_stmt_get_result($stmt);

   // Memeriksa apakah username dan password sesuai
if (mysqli_num_rows($result) > 0) {
	// Jika sesuai, arahkan ke halaman beranda atau halaman lain yang diinginkan
	$row = mysqli_fetch_assoc($result);
	$_SESSION["id_user"] = $row["id_user"];
	$_SESSION["nama_lengkap"] = $row["nama_lengkap"];
	$_SESSION["level"] = $row["level"];
	$lowercaseLevel = strtolower($row["level"]);
    
    if ($lowercaseLevel == "1" || $lowercaseLevel == "admin") {
        header("Location: admin.php");
    } elseif ($lowercaseLevel == "2" || $lowercaseLevel == "kemahasiswaan") {
        header("Location: kemahasiswaan.php");
    } elseif ($lowercaseLevel == "3") {
        header("Location: beranda.php");
    }
    exit();
} else {
    // Jika tidak sesuai, tampilkan pesan kesalahan
    $error_message = "Username atau password salah. Silakan coba lagi.";
}

    // Menutup pernyataan dan koneksi database
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>


<!-- Kode HTML untuk halaman login -->
<!DOCTYPE html>
<html>
<head>
    <title>Halaman Login - SIUKM STMIK Komputama Majenang</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/js/script.js">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <style>
     body {
			background-color: #f5f5f5;
			font-family: Arial, sans-serif;
			font-size: 16px;
			line-height: 1.5;
			margin: 0;
			padding: 0;
		}
		.container {
			background-color: #fff;
			border-radius: 5px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.2);
			margin: 80px auto;
			max-width: 400px;
			padding: 20px;
		}
		.container-form {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			}
			.button-group {
			display: flex;
			justify-content: space-between;
			}

			.button-group .button-login,
			.button-group .button-batal {
			background-color: #3F72AF;
			border: none;
			border-radius: 3px;
			color: #fff;
			cursor: pointer;
			font-size: 16px;
			padding: 10px 20px;
			margin: 20px
			}

			.button-group .button-login:hover,
			.button-group .button-batal:hover {
			background-color: #112D4E;
			}

		h1 {
			font-size: 28px;
			margin: 0 0 20px;
			text-align: center;
		}
		input[type="password"] {
			border: 1px solid #ccc;
			border-radius: 3px;
			box-sizing: border-box;
			display: block;
			font-size: 16px;
			padding: 10px;
			width: 100%;
		}
		input[type="submit"],
		input[type="button"] {
			background-color: #3F72AF;
			border: none;
			border-radius: 4px;
			color: #fff;
			cursor: pointer;
			font-size: 16px;
			margin-right: 10px;
			padding: 10px;
			width: 100px;
		}
		input[type="submit"]:hover,
		input[type="button"]:hover {
			background-color: #112D4E;
		}
		.signup {
			margin-top: 10px;
			text-align: center;
		}
		.signup a {
			color: #112D4E;
			text-decoration: none;
		}
		.signup a:hover {
			text-decoration: underline;
		}
		
		.form-control {
		display: block;
		width: 100%;
		padding: 10px;
		font-size: 16px;
		line-height: 1.5;
		color: #555;
		background-color: #fff;
		background-image: none;
		border: 1px solid #ccc;
		border-radius: 4px;
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
		transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
		}

		.form-control:focus {
		border-color: #66afe9;
		outline: 0;
		box-shadow: 0 0 0 2px rgba(102, 175, 233, 0.6);
		}

		.form-control::placeholder {
		color: #ccc;
		}
		 .button-group {
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.button-group .button-login {
			width: 150px;
			background-color: #007bff;
			color: white;
		}

		.button-group .button-batal {
			width: 150px;
			background-color: #f44336;
			color: white;
		}

		.forgot-password-link {
			font-size: 15px;
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
	.form-group {
        width: 100%;
        max-width: 250px;
        margin: auto;
    }
	.forgot-password-link {
        margin-top: 10px; /* Add margin to create space between the link and the password field */
    }
    </style>
	<script>
  function resetForm() {
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
  }
</script>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark fixed-top">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
			<span class="navbar-toggler-icon"></span>
		</button>
	        	<div class="collapse navbar-collapse" id="collapsibleNavbar">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" href="beranda.php">Beranda</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="profil.php">Profil</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="prestasi.php">Prestasi</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="galeri.php">Galeri</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
						Pilih UKM
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						<a class="dropdown-item" href="racana.php">Pramuka</a>
						<a class="dropdown-item" href="wanacetta.php">Wanaceta</a>
						<a class="dropdown-item" href="agrogreen.php">Agro Green</a>
						<a class="dropdown-item" href="ecc.php">ECC</a>
						<a class="dropdown-item" href="riset.php">Riset</a>
						<a class="dropdown-item" href="kwu.php">Kewirausahaan</a>
						<a class="dropdown-item" href="hsr.php">HSR</a>
					</div>
				</li>
			</ul>
		</div>
	</nav>
    <div class="container">
        <h1>SIGN IN</h1>
        <?php
        // Menampilkan pesan kesalahan jika ada
        if (isset($error_message)) {
            echo '<div class="alert alert-danger">' . $error_message . '</div>';
        }
        ?>
		<form action="" method="POST">
		<div class="container-form">
		<div class="form-group">
			<label for="username">Username:</label>
			<input type="text" class="form-control" id="username" name="username" required>
		</div>
		<div class="form-group">
			<label for="password">Password:</label>
			<div class="password-input">
				<input type="password" class="form-control" id="password" name="password" required>
				<i class="fas fa-eye" id="passwordToggle"></i>
			</div>
		</div>

		<p class="forgot-password-link">Lupa Password? <a href="forgot_password.php">Reset Password</a></p>
			<div class="button-group">
			<div>
				<button class="button-login" type="submit">LOGIN</button>
			</div>
			<div>
			<button class="button-batal" type="button" onclick="goToBeranda()">BATAL</button>
			</div>
			</div>
		</div>
		</form>

<div class="signup">
  <p>Tidak punya akun? <a href="register.php">Daftar</a></p>
</div>
	</div>
	
    <script>
		  function goToBeranda() {
        window.location.href = "beranda.php";
    }
    const passwordInput = document.getElementById("password");
    const passwordToggle = document.getElementById("passwordToggle");
   
    passwordToggle.addEventListener("click", function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordToggle.classList.remove("fa-eye");
            passwordToggle.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            passwordToggle.classList.remove("fa-eye-slash");
            passwordToggle.classList.add("fa-eye");
        }
    });
</script>

</body>
<footer>SIUKM @2023 | Visit our<a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
</svg> Website</a> 
| Connect with us on <a href="https://www.facebook.com/stmikkomputama"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
  <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
</svg> Facebook</a></footer>
</html>