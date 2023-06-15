<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();
        
// Menonaktifkan pesan error
error_reporting(0);


// Mendapatkan nama depan dan level dari session
$nama_depan = $_SESSION["nama_depan"];
$nama_belakang = $_SESSION["nama_belakang"];
$level = $_SESSION["level"];


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
	<title>Halaman Tes Calon Anggota - SIUKM</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
	<link rel="stylesheet" href="../assets/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
	<style>
		.card {
			margin-top: 40px;
			width: 90%;
		}

		.welcome-container {
			background-color: #212A3E;
			height: 100%;
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 0 10px;
		}

		.welcome-text {
			font-size: 20px;
			color: #fff;
			height: 100%;
			display: flex;
			align-items: center;
		}

		.logout-container {
			display: flex;
			align-items: flex-end;
		}

		.logout-button {
			padding: 10px 20px;
			color: #ffffff;
		}

		.timer-container {
			display: flex;
			align-items: center;
			justify-content: flex-end;
		}

		.timer-label {
			margin-right: 10px;
		}

		h3 {
			margin: 0;
		}

		.question {
			display: none;
		}

		.question.active {
			display: block;
		}

		.card-body {
			border-top: 1px solid #ccc;
			padding-top: 10px;
		}

		.card-body h3 {
			margin-top: 0;
		}

		.card-body p {
			margin-bottom: 10px;
		}

		.question {
			display: none;
		}

		.question.active {
			display: block;
		}

		.card-body {
			border-top: 1px solid #ccc;
			padding-top: 10px;
		}

		.card-body h3 {
			margin-top: 0;
		}

		.card-body p {
			margin-bottom: 10px;
		}

		.divider {
			border-bottom: 1px solid #ccc;
			margin-bottom: 10px;
		}

			.button-container {
		display: flex;
		justify-content: space-between;
		margin-top: 20px;
		}

		.button-container .btn {
		margin-top: 10px;
		}
  </style>
   <script>
        // Fungsi untuk menghitung dan menampilkan timer
        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    // Timer selesai, tambahkan aksi yang diinginkan di sini
                }
            }, 1000);
        }

        // Panggil fungsi startTimer saat halaman dimuat
        window.onload = function () {
            var duration = 60 * 30; // 60 dikalikan dengan mau berapa menit
            var display = document.querySelector('.timer-container');
            startTimer(duration, display);
        };
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light custom-navbar">
<a class="navbar-brand"><span style="font-size: 25px; font-weight: bold;">TES POTENSI AKADEMIK</span></a>
		<div class="navbar-collapse justify-content-end">
			<ul class="navbar-nav">
				<li class="nav-item">
				<div class="welcome-container" style="display: flex; flex-direction: column;">
					<span class="welcome-text">Selamat datang,</span>
					<span class="welcome-text"> <?php echo $nama_depan . " " . $nama_belakang; ?></span>
					<div class="logout-container">
						<a class="logout-button" href="?logout">Logout</a>
					</div>
				</div>
				</li>
			</ul>
		</div>
	</nav>


<div class="container">
	<div class="card">
	<p>SOAL NOMOR:</p>
	<div class="timer-container">
		<span class="timer-label">Sisa Waktu:</span>
		<span id="timer"></span>
	</div>
		<div id="question1" class="question active">
			<div class="card-body">
				<div class="divider"></div>
				<p>Pilihlah pasangan kata yang paling tepat untuk mengisi titik-titik (...) pada setiap nomor soal, sehingga hubungan antara dua kata di bagian kiri tanda ≈ sepadan dengan hubungan antara dua kata di bagian kanan tanda ≈</p>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer1" id="answer1a" value="a">
					<label class="form-check-label" for="answer1a">
						A. Jawaban A
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer1" id="answer1b" value="b">
					<label class="form-check-label" for="answer1b">
						B. Jawaban B
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer1" id="answer1c" value="c">
					<label class="form-check-label" for="answer1c">
						C. Jawaban C
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer1" id="answer1d" value="d">
					<label class="form-check-label" for="answer1d">
						D. Jawaban D
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer1" id="answer1e" value="e">
					<label class="form-check-label" for="answer1e">
						E. Jawaban E
					</label>
				</div>
				<div class="button-container">
    <button onclick="previousQuestion()" class="btn btn-secondary">Sebelumnya</button>
    <button onclick="nextQuestion()" class="btn btn-primary">Selanjutnya</button>
</div>
			</div>
		</div>

		<div id="question2" class="question">
			<div class="card-body">
				<h3>No.2</h3>
				<div class="divider"></div>
				<p>Tekst soal 2.</p>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer2" id="answer2a" value="a">
					<label class="form-check-label" for="answer2a">
						A. Jawaban A
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer2" id="answer2b" value="b">
					<label class="form-check-label" for="answer2b">
						B. Jawaban B
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer2" id="answer2c" value="c">
					<label class="form-check-label" for="answer2c">
						C. Jawaban C
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer2" id="answer2d" value="d">
					<label class="form-check-label" for="answer2d">
						D. Jawaban D
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer2" id="answer2e" value="e">
					<label class="form-check-label" for="answer2e">
						E. Jawaban E
					</label>
				</div>
				<div class="button-container">
					<button onclick="previousQuestion()">Sebelumnya</button>
					<button onclick="nextQuestion()">Selanjutnya</button>
				</div>
			</div>
			</div>
			<div id="question3" class="question">
			<div class="card-body">
				<h3>No.3</h3>
				<div class="divider"></div>
				<p>Tekst soal 3.</p>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer3" id="answer3a" value="a">
					<label class="form-check-label" for="answer3a">
						A. Jawaban A
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer3" id="answer3b" value="b">
					<label class="form-check-label" for="answer3b">
						B. Jawaban B
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer3" id="answer3c" value="c">
					<label class="form-check-label" for="answer3c">
						C. Jawaban C
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer3" id="answer3d" value="d">
					<label class="form-check-label" for="answer3d">
						D. Jawaban D
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer3" id="answer3e" value="e">
					<label class="form-check-label" for="answer3e">
						E. Jawaban E
					</label>
				</div>
				<div class="button-container">
					<button onclick="previousQuestion()">Sebelumnya</button>
					<button onclick="nextQuestion()">Selanjutnya</button>
				</div>
			</div>
			</div>
			<div id="question4" class="question">
			<div class="card-body">
				<h3>No.4</h3>
				<div class="divider"></div>
				<p>Tekst soal 4.</p>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer4" id="answer4a" value="a">
					<label class="form-check-label" for="answer4a">
						A. Jawaban A
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer4" id="answer4b" value="b">
					<label class="form-check-label" for="answer4b">
						B. Jawaban B
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer4" id="answer4c" value="c">
					<label class="form-check-label" for="answer4c">
						C. Jawaban C
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer4" id="answer4d" value="d">
					<label class="form-check-label" for="answer4d">
						D. Jawaban D
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="answer4" id="answer4e" value="e">
					<label class="form-check-label" for="answer4e">
						E. Jawaban E
					</label>
				</div>
				<div class="button-container">
					<button onclick="previousQuestion()">Sebelumnya</button>
					<button onclick="nextQuestion()">Selanjutnya</button>
				</div>
			</div>
			</div>
		</div>
	</div>

	<script>
		// Fungsi untuk pindah ke soal sebelumnya
		function previousQuestion() {
			var currentQuestion = $(".question.active");
			var previousQuestion = currentQuestion.prev(".question");

			currentQuestion.removeClass("active");
			previousQuestion.addClass("active");
		}

		// Fungsi untuk pindah ke soal selanjutnya
		function nextQuestion() {
			var currentQuestion = $(".question.active");
			var nextQuestion = currentQuestion.next(".question");

			currentQuestion.removeClass("active");
			nextQuestion.addClass("active");
		}
	</script>
</body>
</html>

<script>window.addEventListener('beforeunload', function (event) {
  // Tuliskan pesan konfirmasi di sini
  event.preventDefault();
  // Jika pengguna memilih untuk tetap tinggal, fungsi preventDefault() akan mencegah pengguna untuk meninggalkan halaman
  // Jika pengguna memilih untuk meninggalkan halaman, maka tidak perlu melakukan apapun karena browser akan menangani penggunaannya secara otomatis.
});</script>