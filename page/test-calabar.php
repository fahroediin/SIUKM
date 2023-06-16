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
// Mendapatkan nomor soal yang sedang aktif
$currentQuestion = isset($_GET['question']) ? intval($_GET['question']) : 1;

// Mendapatkan total jumlah soal
$totalQuestions = 50;

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
		body {
		width: 100%;
		}
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
        .card-body .options {
            margin-bottom: 10px;
        }

        .card-body .options label {
            display: block;
        }

        .card-body .options input[type="radio"] {
            margin-right: 5px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-footer .pagination {
            margin: 0;
        }

        .card-footer .pagination li {
            display: inline-block;
            margin-right: 5px;
        }

        .card-footer .pagination li:last-child {
            margin-right: 0;
        }

        .card-footer .pagination .page-link {
            padding: 6px 12px;
            background-color: #f8f9fa;
            color: #212529;
            border: 1px solid #dee2e6;
            transition: background-color 0.3s;
        }

        .card-footer .pagination .page-link:hover {
            background-color: #e9ecef;
        }

        .card-footer .pagination .page-link.active {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
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
			width: 80%;
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
		.button {
		width: 40px; /* Sesuaikan lebar sesuai dengan kebutuhan Anda */
		}

		.button-container {
		display: flex;
		justify-content: space-between;
		margin-top: 20px;
		}
		.button-container .btn {
		margin-top: 10px;
		/* Tambahkan gaya yang sama untuk kedua tombol */
		padding: 10px 20px;
		color: #ffffff;
		}
		.page-link {
		display: inline-block;
		padding: 8px 12px;
		margin: 0 5px;
		border: 1px solid #ccc;
		background-color: #f0f0f0;
		color: #333;
		text-decoration: none;
		border-radius: 4px;
		}

		.page-link:hover {
		background-color: #e0e0e0;
		}

		.page-link.active {
		background-color: #333;
		color: #fff;
		}
		/* Snackbar style */
		#snackbar {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
    }

    #snackbar.show {
        visibility: visible;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }

    @keyframes fadein {
        from {
            bottom: 0;
            opacity: 0;
        }
        to {
            bottom: 30px;
            opacity: 1;
        }
    }

    @keyframes fadeout {
        from {
            bottom: 30px;
            opacity: 1;
        }
        to {
            bottom: 0;
            opacity: 0;
        }
    }
  </style>
<script>
    var isTimerExpired = false; // Variabel untuk menandakan apakah timer telah selesai

    // Fungsi untuk menghitung dan menampilkan timer
    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        var timerInterval = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(timerInterval);
                isTimerExpired = true; // Timer telah selesai
                enableSubmitButton();
            }
        }, 1000);
    }

    // Panggil fungsi startTimer saat halaman dimuat
    window.onload = function () {
        var duration = 60 * 30; // 60 dikalikan dengan mau berapa menit
        var display = document.querySelector('.timer-container');
        startTimer(duration, display);
    };

    function enableSubmitButton() {
        var submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = false;
    }

    function submitAnswers() {
        if (isTimerExpired) {
            var selectedOptions = document.querySelectorAll('.question.active input[type="radio"]:checked');
            var answeredQuestions = selectedOptions.length;
            var totalQuestions = 50;

            if (answeredQuestions === totalQuestions) {
                // Semua pertanyaan telah dijawab, lakukan logika pengiriman jawaban atau tindakan lainnya
                // ...
                alert('Jawaban telah dikirim!');
            } else {
                // Belum semua pertanyaan dijawab, berikan pesan kepada pengguna
                alert('Mohon isi semua pertanyaan terlebih dahulu.');
            }
        } else {
            alert('Waktu belum habis!');
        }
    }
</script>
<script>

    var currentQuestion = 1; // Nomor soal saat ini

    function nextQuestion() {
        // Menampilkan nomor soal berikutnya
        if (currentQuestion < 4) {
            currentQuestion++;
            showQuestion(currentQuestion);
        }
    }

    function previousQuestion() {
        // Menampilkan nomor soal sebelumnya
        if (currentQuestion > 1) {
            currentQuestion--;
            showQuestion(currentQuestion);
        }
    }

    function showQuestion(questionNumber) {
        // Menampilkan pertanyaan sesuai nomor soal
        var questions = document.getElementsByClassName("question");
        var questionButtons = document.getElementsByClassName("question-button");
        var soalNomor = document.getElementById("soal-nomor");

        // Menyembunyikan semua pertanyaan
        for (var i = 0; i < questions.length; i++) {
            questions[i].style.display = "none";
        }

        // Mengubah kelas tombol aktif
        for (var j = 0; j < questionButtons.length; j++) {
            questionButtons[j].classList.remove("active");
        }
        
        // Menampilkan pertanyaan yang dipilih
        questions[questionNumber - 1].style.display = "block";
        questionButtons[questionNumber - 1].classList.add("active");

        // Mengubah label "Soal Nomor" dengan nomor soal saat ini
        soalNomor.textContent = "Soal Nomor: " + questionNumber;
    }
</script>

</head>
<body>
<div class="navbar">
    <div class="welcome-container">
        <div class="welcome-text">
            <h3>Selamat datang, <?php echo $nama_depan . ' ' . $nama_belakang; ?></h3>
        </div>
        <div class="logout-container">
            <a href="?logout=true" class="logout-button">Logout</a>
        </div>
    </div>
</div>

		<div class="timer-container">
        <span class="timer-label">Time Left:</span>
        <span class="timer">30:00</span>
    </div>
        <div class="card">
            <div class="card-header">
                <h4>Halaman Tes Calon Anggota - SIUKM</h4>
            </div>
			<div id="snackbar"></div>
            <div class="card-body">
                <div class="question active" id="question1">
                    <h5>Soal 1</h5>
                    <p>Pertanyaan 1</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer1"  value="A" data-correct> Jawaban A
                        </label>
                        <label>
                            <input type="radio" name="answer1" value="B"> Jawaban B
                        </label>
                        <label>
                            <input type="radio" name="answer1" value="C"> Jawaban C
                        </label>
						<label>
                            <input type="radio" name="answer1" value="D"> Jawaban D
                        </label>
						<label>
                            <input type="radio" name="answer1" value="E"> Jawaban E
                        </label>
                    </div>
                </div>
                <div class="question" id="question2">
                    <h5>Soal 2</h5>
                    <p>Pertanyaan 2</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer2" value="A"> Jawaban A
                        </label>
                        <label>
                            <input type="radio" name="answer2" value="B"> Jawaban B
                        </label>
                        <label>
                            <input type="radio" name="answer2" value="C"> Jawaban C
                        </label>
						<label>
                            <input type="radio" name="answer2" value="D"> Jawaban D
                        </label>
						<label>
                            <input type="radio" name="answer2" value="E"> Jawaban E
                        </label>
                    </div>
                </div>
                <div class="question" id="question3">
                    <h5>Soal 3</h5>
                    <p>Pertanyaan 3</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer3" value="A"> Jawaban A
                        </label>
                        <label>
                            <input type="radio" name="answer3" value="B"> Jawaban B
                        </label>
                        <label>
                            <input type="radio" name="answer3" value="C"> Jawaban C
                        </label>
						<label>
                            <input type="radio" name="answer3" value="D"> Jawaban D
                        </label>
						<label>
                            <input type="radio" name="answer3" value="E"> Jawaban E
                        </label>
                    </div>
                </div>
				<div class="question" id="question4">
                    <h5>Soal 4</h5>
                    <p>Pertanyaan 4</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer4" value="A"> Jawaban A
                        </label>
                        <label>
                            <input type="radio" name="answer4" value="B"> Jawaban B
                        </label>
                        <label>
                            <input type="radio" name="answer4" value="C"> Jawaban C
                        </label>
						<label>
                            <input type="radio" name="answer4" value="D"> Jawaban D
                        </label>
						<label>
                            <input type="radio" name="answer4" value="E"> Jawaban E
                        </label>
                    </div>
                </div>
				<div class="question" id="question5">
                    <h5>Soal 5</h5>
                    <p>Pertanyaan 5</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer5" value="A"> Jawaban A
                        </label>
                        <label>
                            <input type="radio" name="answer5" value="B"> Jawaban B
                        </label>
                        <label>
                            <input type="radio" name="answer5" value="C"> Jawaban C
                        </label>
						<label>
                            <input type="radio" name="answer5" value="D"> Jawaban D
                        </label>
						<label>
                            <input type="radio" name="answer5" value="E"> Jawaban E
                        </label>
                    </div>
                </div>
				<div class="question" id="question6">
					<h5>Soal 6</h5>
					<p>Pertanyaan 6</p>
					<div class="options">
						<label>
							<input type="radio" name="answer6" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer6" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer6" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer6" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer6" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question7">
					<h5>Soal 7</h5>
					<p>Pertanyaan 7</p>
					<div class="options">
						<label>
							<input type="radio" name="answer7" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer7" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer7" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer7" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer7" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question8">
					<h5>Soal 8</h5>
					<p>Pertanyaan 8</p>
					<div class="options">
						<label>
							<input type="radio" name="answer8" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer8" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer8" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer8" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer8" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question9">
					<h5>Soal 9</h5>
					<p>Pertanyaan 9</p>
					<div class="options">
						<label>
							<input type="radio" name="answer9" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer9" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer9" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer9" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer9" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question10">
					<h5>Soal 10</h5>
					<p>Pertanyaan 10</p>
					<div class="options">
						<label>
							<input type="radio" name="answer10" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer10" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer10" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer10" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer10" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question11">
					<h5>Soal 11</h5>
					<p>Pertanyaan 11</p>
					<div class="options">
						<label>
							<input type="radio" name="answer11" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer11" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer11" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer11" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer11" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question12">
					<h5>Soal 12</h5>
					<p>Pertanyaan 12</p>
					<div class="options">
						<label>
							<input type="radio" name="answer12" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer12" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer12" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer12" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer12" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question13">
					<h5>Soal 13</h5>
					<p>Pertanyaan 13</p>
					<div class="options">
						<label>
							<input type="radio" name="answer13" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer13" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer13" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer13" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer13" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question14">
					<h5>Soal 14</h5>
					<p>Pertanyaan 14</p>
					<div class="options">
						<label>
							<input type="radio" name="answer14" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer14" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer14" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer14" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer14" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question15">
					<h5>Soal 15</h5>
					<p>Pertanyaan 15</p>
					<div class="options">
						<label>
							<input type="radio" name="answer15" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer15" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer15" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer15" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer15" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question16">
					<h5>Soal 16</h5>
					<p>Pertanyaan 16</p>
					<div class="options">
						<label>
							<input type="radio" name="answer16" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer16" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer16" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer16" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer16" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question17">
					<h5>Soal 17</h5>
					<p>Pertanyaan 17</p>
					<div class="options">
						<label>
							<input type="radio" name="answer17" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer17" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer17" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer17" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer17" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question18">
					<h5>Soal 18</h5>
					<p>Pertanyaan 18</p>
					<div class="options">
						<label>
							<input type="radio" name="answer18" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer18" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer18" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer18" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer18" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question19">
					<h5>Soal 19</h5>
					<p>Pertanyaan 19</p>
					<div class="options">
						<label>
							<input type="radio" name="answer19" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer19" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer19" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer19" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer19" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question20">
					<h5>Soal 20</h5>
					<p>Pertanyaan 20</p>
					<div class="options">
						<label>
							<input type="radio" name="answer20" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer20" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer20" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer20" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer20" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question21">
					<h5>Soal 21</h5>
					<p>Pertanyaan 21</p>
					<div class="options">
						<label>
							<input type="radio" name="answer21" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer21" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer21" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer21" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer21" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question22">
					<h5>Soal 22</h5>
					<p>Pertanyaan 22</p>
					<div class="options">
						<label>
							<input type="radio" name="answer22" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer22" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer22" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer22" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer22" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question23">
					<h5>Soal 23</h5>
					<p>Pertanyaan 23</p>
					<div class="options">
						<label>
							<input type="radio" name="answer23" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer23" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer23" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer23" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer23" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question24">
					<h5>Soal 24</h5>
					<p>Pertanyaan 24</p>
					<div class="options">
						<label>
							<input type="radio" name="answer24" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer24" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer24" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer24" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer24" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question25">
					<h5>Soal 25</h5>
					<p>Pertanyaan 25</p>
					<div class="options">
						<label>
							<input type="radio" name="answer25" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer25" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer25" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer25" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer25" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question26">
					<h5>Soal 26</h5>
					<p>Pertanyaan 26</p>
					<div class="options">
						<label>
							<input type="radio" name="answer26" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer26" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer26" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer26" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer26" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question27">
					<h5>Soal 27</h5>
					<p>Pertanyaan 27</p>
					<div class="options">
						<label>
							<input type="radio" name="answer27" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer27" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer27" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer27" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer27" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question28">
					<h5>Soal 28</h5>
					<p>Pertanyaan 28</p>
					<div class="options">
						<label>
							<input type="radio" name="answer28" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer28" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer28" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer28" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer28" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question29">
					<h5>Soal 29</h5>
					<p>Pertanyaan 29</p>
					<div class="options">
						<label>
							<input type="radio" name="answer29" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer29" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer29" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer29" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer29" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question30">
					<h5>Soal 30</h5>
					<p>Pertanyaan 30</p>
					<div class="options">
						<label>
							<input type="radio" name="answer30" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer30" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer30" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer30" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer30" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question31">
					<h5>Soal 31</h5>
					<p>Pertanyaan 31</p>
					<div class="options">
						<label>
							<input type="radio" name="answer31" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer31" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer31" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer31" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer31" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question32">
					<h5>Soal 32</h5>
					<p>Pertanyaan 32</p>
					<div class="options">
						<label>
							<input type="radio" name="answer32" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer32" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer32" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer32" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer32" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question33">
					<h5>Soal 33</h5>
					<p>Pertanyaan 33</p>
					<div class="options">
						<label>
							<input type="radio" name="answer33" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer33" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer33" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer33" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer33" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question34">
					<h5>Soal 34</h5>
					<p>Pertanyaan 34</p>
					<div class="options">
						<label>
							<input type="radio" name="answer34" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer34" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer34" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer34" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer34" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question35">
					<h5>Soal 35</h5>
					<p>Pertanyaan 35</p>
					<div class="options">
						<label>
							<input type="radio" name="answer35" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer35" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer35" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer35" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer35" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question36">
					<h5>Soal 36</h5>
					<p>Pertanyaan 36</p>
					<div class="options">
						<label>
							<input type="radio" name="answer36" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer36" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer36" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer36" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer36" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question37">
					<h5>Soal 37</h5>
					<p>Pertanyaan 37</p>
					<div class="options">
						<label>
							<input type="radio" name="answer37" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer37" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer37" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer37" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer37" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question38">
					<h5>Soal 38</h5>
					<p>Pertanyaan 38</p>
					<div class="options">
						<label>
							<input type="radio" name="answer38" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer38" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer38" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer38" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer38" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question39">
					<h5>Soal 39</h5>
					<p>Pertanyaan 39</p>
					<div class="options">
						<label>
							<input type="radio" name="answer39" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer39" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer39" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer39" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer39" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question40">
					<h5>Soal 40</h5>
					<p>Pertanyaan 40</p>
					<div class="options">
						<label>
							<input type="radio" name="answer40" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer40" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer40" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer40" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer40" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question41">
					<h5>Soal 41</h5>
					<p>Pertanyaan 41</p>
					<div class="options">
						<label>
							<input type="radio" name="answer41" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer41" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer41" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer41" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer41" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question42">
					<h5>Soal 42</h5>
					<p>Pertanyaan 42</p>
					<div class="options">
						<label>
							<input type="radio" name="answer42" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer42" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer42" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer42" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer42" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question43">
					<h5>Soal 43</h5>
					<p>Pertanyaan 43</p>
					<div class="options">
						<label>
							<input type="radio" name="answer43" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer43" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer43" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer43" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer43" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question44">
					<h5>Soal 44</h5>
					<p>Pertanyaan 44</p>
					<div class="options">
						<label>
							<input type="radio" name="answer44" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer44" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer44" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer44" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer44" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question45">
					<h5>Soal 45</h5>
					<p>Pertanyaan 45</p>
					<div class="options">
						<label>
							<input type="radio" name="answer45" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer45" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer45" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer45" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer45" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question46">
					<h5>Soal 46</h5>
					<p>Pertanyaan 46</p>
					<div class="options">
						<label>
							<input type="radio" name="answer46" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer46" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer46" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer46" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer46" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question47">
					<h5>Soal 47</h5>
					<p>Pertanyaan 47</p>
					<div class="options">
						<label>
							<input type="radio" name="answer47" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer47" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer47" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer47" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer47" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question48">
					<h5>Soal 48</h5>
					<p>Pertanyaan 48</p>
					<div class="options">
						<label>
							<input type="radio" name="answer48" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer48" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer48" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer48" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer48" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question49">
					<h5>Soal 49</h5>
					<p>Pertanyaan 49</p>
					<div class="options">
						<label>
							<input type="radio" name="answer49" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer49" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer49" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer49" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer49" value="E"> Jawaban E
						</label>
					</div>
				</div>
				<div class="question" id="question50">
					<h5>Soal 50</h5>
					<p>Pertanyaan 50</p>
					<div class="options">
						<label>
							<input type="radio" name="answer50" value="A"> Jawaban A
						</label>
						<label>
							<input type="radio" name="answer50" value="B"> Jawaban B
						</label>
						<label>
							<input type="radio" name="answer50" value="C"> Jawaban C
						</label>
						<label>
							<input type="radio" name="answer50" value="D"> Jawaban D
						</label>
						<label>
							<input type="radio" name="answer50" value="E"> Jawaban E
						</label>
					</div>
				</div>
            </div>
			<button id="previousBtn" onclick="previousQuestion()" disabled>Previous</button>
			<button id="nextBtn" onclick="nextQuestion()">Next</button>
			<button id="submitBtn" onclick="submitAnswers()">Submit</button>
			<script>
    var currentQuestion = 1;
    var totalQuestions = 50;

    function previousQuestion() {
        var currentQuestionElement = document.getElementById('question' + currentQuestion);
        currentQuestionElement.classList.remove('active');
        currentQuestion--;

        var previousQuestionElement = document.getElementById('question' + currentQuestion);
        previousQuestionElement.classList.add('active');

        var isAllOptionsSelected = checkAllOptionsSelected();
        document.getElementById('submitBtn').disabled = !isAllOptionsSelected;

        document.getElementById('nextBtn').disabled = false;

        if (currentQuestion === 1) {
            document.getElementById('previousBtn').disabled = true;
        }
    }

    function nextQuestion() {
        var currentQuestionElement = document.getElementById('question' + currentQuestion);
        currentQuestionElement.classList.remove('active');
        currentQuestion++;

        var nextQuestionElement = document.getElementById('question' + currentQuestion);
        nextQuestionElement.classList.add('active');

        var isAllOptionsSelected = checkAllOptionsSelected();
        document.getElementById('submitBtn').disabled = !isAllOptionsSelected;

        document.getElementById('previousBtn').disabled = false;

        if (currentQuestion === totalQuestions) {
            document.getElementById('nextBtn').disabled = true;
        }
    }

    function submitAnswers() {
        var isAllOptionsSelected = checkAllOptionsSelected();
        if (isAllOptionsSelected) {
            showSnackbar();
            redirectToDashboard();
        } else {
            alert('Harap kerjakan semua soal.');
        }
    }

    function checkAllOptionsSelected() {
        var questions = document.querySelectorAll('.question');
        for (var i = 0; i < questions.length; i++) {
            var question = questions[i];
            var selectedOption = question.querySelector('input[type="radio"]:checked');
            if (!selectedOption) {
                return false; // Ada pertanyaan yang belum terisi
            }
        }
        return true; // Semua pertanyaan terisi
    }

    function showSnackbar() {
        var snackbar = document.getElementById('snackbar');
        snackbar.textContent = 'Terima kasih telah mengerjakan tes potensi akademik';
        snackbar.classList.add('show');
        setTimeout(function () {
            snackbar.classList.remove('show');
        }, 3000);
    }

    function redirectToDashboard() {
        // Redirect to dashboard.php
        window.location.href = 'dashboard.php';
    }
</script>
        <script>
        // Timer functionality
        var timerElement = document.querySelector('.timer');
        var duration = 60 * 60; // 60 minutes in seconds

        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            var timerInterval = setInterval(function() {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(timerInterval);
                    // Perform action when timer reaches 0
                    alert('Time is up! Submit your answers.');
                    submitForm();
                }
            }, 1000);
        }

        // Call the timer function
        startTimer(duration, timerElement);

        function changeQuestion(questionNumber) {
            var questions = document.getElementsByClassName('question');
            for (var i = 0; i < questions.length; i++) {
                questions[i].classList.remove('active');
            }
            document.getElementById('question' + questionNumber).classList.add('active');
        }

        function submitForm() {
            var answers = [];
            var questions = document.getElementsByClassName('question');
            for (var i = 0; i < questions.length; i++) {
                var questionNumber = i + 1;
                var answer = document.querySelector('input[name="answer' + questionNumber + '"]:checked');
                if (answer) {
                    answers.push(answer.value);
                } else {
                    answers.push('');
                }
            }

            // Send the answers to the server for processing
            // You can use AJAX to send the data to a PHP script
            // and process it on the server side.

            // For example:
            // var xhr = new XMLHttpRequest();
            // xhr.open('POST', 'process_answers.php');
            // xhr.setRequestHeader('Content-Type', 'application/json');
            // xhr.onload = function () {
            //     if (xhr.status === 200) {
            //         // Process the response from the server
            //         var response = JSON.parse(xhr.responseText);
            //         // Do something with the response
            //     }
            // };
            // xhr.send(JSON.stringify(answers));
        }
    </script>
</body>
</html>