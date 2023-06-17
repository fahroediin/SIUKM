<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Mendapatkan nama depan dan level dari session
$nama_depan = $_SESSION["nama_depan"];
$nama_belakang = $_SESSION["nama_belakang"];
$level = $_SESSION["level"];
$id_calabar = $_SESSION['id_calabar'];

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

// Mendapatkan nomor soal yang sedang aktif
$currentQuestion = isset($_GET['question']) ? intval($_GET['question']) : 1;

// Mendapatkan total jumlah soal
$totalQuestions = 50;

// Fungsi untuk menghitung nilai TPA berdasarkan jawaban
function calculateTPAScore($jawaban)
{
    $skorBenar = 1; // Skor untuk jawaban benar
    $skorSalah = 0; // Skor untuk jawaban salah
    $nilaiTPA = 0; // Nilai total TPA

    // Jawaban benar dan data-correct untuk setiap nomor soal
    $jawabanBenar = array(
        1 => 1, // Jawaban benar untuk soal 1
        2 => 0, // Jawaban benar untuk soal 2
        // Tambahkan jawaban benar untuk setiap nomor soal
    );

    // Menghitung nilai TPA berdasarkan jawaban
    foreach ($jawaban as $nomorSoal => $jawabanPengguna) {
        if (isset($jawabanBenar[$nomorSoal])) {
            if ($jawabanPengguna == $jawabanBenar[$nomorSoal]) {
                $nilaiTPA += $skorBenar;
            } else {
                $nilaiTPA += $skorSalah;
            }
        }
    }

    return $nilaiTPA;
}

// Fungsi untuk menentukan kategori berdasarkan nilai TPA
function determineCategory($nilaiTPA)
{
    $totalSoal = 50;
    $persentaseBenar = ($nilaiTPA / $totalSoal) * 100;

    if ($persentaseBenar == 100) {
        return "Sangat Baik";
    } elseif ($persentaseBenar >= 80) {
        return "Baik";
    } elseif ($persentaseBenar >= 50) {
        return "Cukup";
    } elseif ($persentaseBenar >= 30) {
        return "Kurang Baik";
    } else {
        return "Kategori Tidak Tersedia";
    }
}

function saveTPAScore($userId, $nilaiTPA)
{
    // Memasukkan file db_connect.php
    require_once "db_connect.php";

    // Menyimpan nilai TPA ke database berdasarkan id_calabar
    $sql = "UPDATE tab_pacab SET nilai_tpa = $nilaiTPA WHERE id_calabar = $userId";

    if ($conn->query($sql) === true) {
        echo "Nilai TPA berhasil disimpan.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Menutup koneksi database
    $conn->close();
}

// Memeriksa apakah pengguna sudah mengisi jawaban TPA
if (isset($_POST['submit_jawaban'])) {
    // Mendapatkan jawaban pengguna dari form
    $jawaban = $_POST['jawaban'];

    // Menghitung nilai TPA
    $nilaiTPA = calculateTPAScore($jawaban);

    // Mendapatkan id_calabar dari session
    $userId = $_SESSION["id_calabar"];

    // Menyimpan nilai TPA ke database
    saveTPAScore($userId, $nilaiTPA);

    // Menampilkan kategori berdasarkan nilai TPA
    $kategori = determineCategory($nilaiTPA);
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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="../assets/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
	<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  	<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>
	<style>
body {
  width: 100%;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.card {
  margin-top: 40px;
  width: 90%;
  margin-left: auto;
  margin-right: auto;
}

.welcome-container {
  background-color: #F6F1F1;
  border-top-right-radius: 5px;
  border-bottom-right-radius: 5px;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 10px;
}

.welcome-text {
  font-size: 20px;
  color: #212A3E;
  height: 100%;
  display: flex;
  align-items: center;
}

.logout-container {
  display: flex;
  align-items: center;
}

.logout-button {
  margin-left: 30px;
  align-items: center;
  justify-content: center;
  color: #212A3E;
  font-size: 20px;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
  transition: background-color 0.3s ease;
}

.logout-button:hover {
  background-color: #6DA9E4;
}

.logout-icon {
  margin-right: 5px;
}

.timer-container {
  display: flex;
  align-items: flex-end;
  font-size: 24px;
  font-weight: bold;
}

.timer-label {
	margin-left: 540px;
  font-weight: bold;
  font-size: 24px;
  margin-top: 5px;
}

h3 {
  margin: 0;
}

h4 {
  font-size: 24px;
}

h5 {
  color: #333;
  font-size: 20px;
  text-align: start;
  background-color: #AFD3E2;
  padding: 10px;
  border-radius: 3px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
  display: inline-block;
}

.question {
  display: none;
}

.question.active {
  display: block;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #146C94;
  color: #fff;
  padding: 10px;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}
	.card-body {
		border-top: 1px solid #ccc;
		padding-top: 10px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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


.divider {
  border-bottom: 1px solid #ccc;
  margin-bottom: 10px;
}

.button-container {
  display: flex;
  justify-content: space-between;
  padding-top: 10px;
}

.button-container button {
  width: 100px;
  margin-right: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

#previousBtn {
  background-color: #F39C12;
}

#submitBtn {
  background-color: #27AE60;
  margin-left: auto;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
}

#nextBtn {
  background-color: #3498DB;
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
    // Fungsi untuk mengupdate dan menyimpan jumlah refresh halaman dalam session
    function updateRefreshCount() {
      // Cek apakah session storage tersedia
      if (typeof(Storage) !== "undefined") {
        // Cek apakah sudah ada data refreshCount dalam session
        if (sessionStorage.refreshCount) {
          // Jika sudah ada, tambahkan 1 ke jumlah refresh
          sessionStorage.refreshCount = Number(sessionStorage.refreshCount) + 1;
        } else {
          // Jika belum ada, set jumlah refresh menjadi 0
          sessionStorage.refreshCount = 0;
        }
      } else {
        // Session storage tidak tersedia
        console.log("Session storage is not supported.");
      }
    }

    // Fungsi untuk mendapatkan dan menampilkan jumlah refresh halaman dari session
    function displayRefreshCount() {
      // Cek apakah session storage tersedia
      if (typeof(Storage) !== "undefined") {
        // Cek apakah data refreshCount ada dalam session
        if (sessionStorage.refreshCount) {
          // Dapatkan nilai refreshCount dari session
          var refreshCount = sessionStorage.refreshCount;

          // Tampilkan nilai refreshCount di elemen HTML
          document.getElementById("refreshCount").textContent = refreshCount;
        } else {
          // Jika data refreshCount tidak ada dalam session, tampilkan nilai default
          document.getElementById("refreshCount").textContent = "0";
        }
      } else {
        // Session storage tidak tersedia
        console.log("Session storage is not supported.");
      }
    }

    // Panggil fungsi updateRefreshCount saat halaman selesai dimuat
    window.onload = function() {
      updateRefreshCount();
      displayRefreshCount();
    }
  </script>
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
  <button class="logout-button">
    <span class="logout-icon"><i class="fas fa-sign-out-alt"></i></span>
    Logout
  </button>
</div>
</div>
</div>
   
<div class="card">
    <div class="card-header">
      <h4>Tes Potensi Akademik - SIUKM</h4>
	  <label class="timer-label">Sisa Waktu:</label>
      <div class="timer-container">
        <span class="timer">30:00</span>
      </div>
    </div>
    <div id="snackbar"></div>
    <div class="card-body">
                <div class="question active" id="question1">
                    <h5>Soal 1</h5>
                    <p>gitar : ... ≈ ... : pukul</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer1"  value="A"> bernyanyi tukang
                        </label>
                        <label>
                            <input type="radio" name="answer1" value="B"> kayu besi
                        </label>
                        <label>
                            <input type="radio" name="answer1" value="C" data-correct> petik jimbe
                        </label>
						<label>
                            <input type="radio" name="answer1" value="D"> musik paku
                        </label>
						<label>
                            <input type="radio" name="answer1" value="E"> senar gendang
                        </label>
                    </div>
                </div>
                <div class="question" id="question2">
                    <h5>Soal 2</h5>
                    <p>hard disk : ... ≈ ... : uang</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer2" value="A"> piringan logam
                        </label>
                        <label>
                            <input type="radio" name="answer2" value="B" data-correct> data dompet
                        </label>
                        <label>
                            <input type="radio" name="answer2" value="C"> piringan kertas
                        </label>
						<label>
                            <input type="radio" name="answer2" value="D"> disket barter
                        </label>
						<label>
                            <input type="radio" name="answer2" value="E"> komputer penghasilan
                        </label>
                    </div>
                </div>
                <div class="question" id="question3">
                    <h5>Soal 3</h5>
                    <p>ikan : ... ≈ ... : kulit</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer3" value="A"> laut sel
                        </label>
                        <label>
                            <input type="radio" name="answer3" value="B"> asin gelap
                        </label>
                        <label>
                            <input type="radio" name="answer3" value="C" data-correct> sisik manusia
                        </label>
						<label>
                            <input type="radio" name="answer3" value="D"> insang pori-pori
                        </label>
						<label>
                            <input type="radio" name="answer3" value="E"> budidaya perawatan
                        </label>
                    </div>
                </div>
				<div class="question" id="question4">
                    <h5>Soal 4</h5>
                    <p>lombok : ... ≈ ... : manis</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer4" value="A" data-correct> pedas gula
                        </label>
                        <label>
                            <input type="radio" name="answer4" value="B"> cabe tebu
                        </label>
                        <label>
                            <input type="radio" name="answer4" value="C"> kecap sirup
                        </label>
						<label>
                            <input type="radio" name="answer4" value="D"> saos sakarin
                        </label>
						<label>
                            <input type="radio" name="answer4" value="E"> petani perempuan
                        </label>
                    </div>
                </div>
				<div class="question" id="question5">
                    <h5>Soal 5</h5>
                    <p>catur : ... ≈ ... : knock down</p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answer5" value="A" data-correct> skakmat tinju
                        </label>
                        <label>
                            <input type="radio" name="answer5" value="B"> bidak mebel
                        </label>
                        <label>
                            <input type="radio" name="answer5" value="C"> empat jatuh
                        </label>
						<label>
                            <input type="radio" name="answer5" value="D"> papan ring
                        </label>
						<label>
                            <input type="radio" name="answer5" value="E"> hitam putih sepuluh
                        </label>
                    </div>
                </div>
				<div class="question" id="question6">
					<h5>Soal 6</h5>
					<p>polisi : .... ≈ TNI : ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer6" value="A"> komisaris polisi letnan
						</label>
						<label>
							<input type="radio" name="answer6" value="B"> ajun komisaris polisi kolonel
						</label>
						<label>
							<input type="radio" name="answer6" value="C"> komisaris besar polisi kapten
						</label>
						<label>
							<input type="radio" name="answer6" value="D" data-correct> ajun komisaris besar polisi letnan kolonel
						</label>
						<label>
							<input type="radio" name="answer6" value="E"> komisaris jenderal polisi mayor jenderal
						</label>
					</div>
				</div>
				<div class="question" id="question7">
					<h5>Soal 7</h5>
					<p>wisuda : ... ≈ pertunangan : ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer7" value="A" data-correct> toga cincin
						</label>
						<label>
							<input type="radio" name="answer7" value="B"> gelar pelaminan
						</label>
						<label>
							<input type="radio" name="answer7" value="C"> berhasil cinta
						</label>
						<label>
							<input type="radio" name="answer7" value="D"> sarjana mempelai
						</label>
						<label>
							<input type="radio" name="answer7" value="E"> kuliah pernikahan
						</label>
					</div>
				</div>
				<div class="question" id="question8">
					<h5>Soal 8</h5>
					<p>karet : ... ≈ aren : …</p>
					<div class="options">
						<label>
							<input type="radio" name="answer8" value="A" data-correct> getah nira
						</label>
						<label>
							<input type="radio" name="answer8" value="B"> ban manis
						</label>
						<label>
							<input type="radio" name="answer8" value="C"> sadap gula
						</label>
						<label>
							<input type="radio" name="answer8" value="D"> pohon buah
						</label>
						<label>
							<input type="radio" name="answer8" value="E"> hutan ladang
						</label>
					</div>
				</div>
				<div class="question" id="question9">
					<h5>Soal 9</h5>
					<p>sayap : ... ≈ kaki : ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer9" value="A"> terbang pijak
						</label>
						<label>
							<input type="radio" name="answer9" value="B"> samping dua
						</label>
						<label>
							<input type="radio" name="answer9" value="C"> burung ikan
						</label>
						<label>
							<input type="radio" name="answer9" value="D" data-correct> kepak hentak
						</label>
						<label>
							<input type="radio" name="answer9" value="E"> udara air
						</label>
					</div>
				</div>
				<div class="question" id="question10">
					<h5>Soal 10</h5>
					<p>analgesik : ... ≈ pelumas : ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer10" value="A"> obat oli
						</label>
						<label>
							<input type="radio" name="answer10" value="B"> apotek montir
						</label>
						<label>
							<input type="radio" name="answer10" value="C"> sakit rusak
						</label>
						<label>
							<input type="radio" name="answer10" value="D"> tubuh obat
						</label>
						<label>
							<input type="radio" name="answer10" value="E" data-correct> nyeri gesekan
						</label>
					</div>
				</div>
				<div class="question" id="question11">
					<h5>Soal 11</h5>
					<p> ... : malam ≈ matahari : ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer11" value="A"> gelap panas
						</label>
						<label>
							<input type="radio" name="answer11" value="B" data-correct> bulan siang
						</label>
						<label>
							<input type="radio" name="answer11" value="C"> tidur bekerja
						</label>
						<label>
							<input type="radio" name="answer11" value="D"> langit atmosfer
						</label>
						<label>
							<input type="radio" name="answer11" value="E"> kelelawar ultraviolet
						</label>
					</div>
				</div>
				<div class="question" id="question12">
					<h5>Soal 12</h5>
					<p>... : konglomerat ≈ pandai : ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer12" value="A"> prestis profesor
						</label>
						<label>
							<input type="radio" name="answer12" value="B"> aset pandai
						</label>
						<label>
							<input type="radio" name="answer12" value="C"> uang debil
						</label>
						<label>
							<input type="radio" name="answer12" value="D"> harta buku
						</label>
						<label>
							<input type="radio" name="answer12" value="E" data-correct> kaya jenius
						</label>
					</div>
				</div>
				<div class="question" id="question13">
					<h5>Soal 13</h5>
					<p>... : katak ≈ ulat : ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer13" value="A"> kolam bulu
						</label>
						<label>
							<input type="radio" name="answer13" value="B" data-correct> nyamuk burung
						</label>
						<label>
							<input type="radio" name="answer13" value="C"> serangga kupu-kupu
						</label>
						<label>
							<input type="radio" name="answer13" value="D"> sawah kepompong
						</label>
						<label>
							<input type="radio" name="answer13" value="E"> berlendir gatal
						</label>
					</div>
				</div>
				<div class="question" id="question14">
					<h5>Soal 14</h5>
					<p>... : astronom ≈ buku : …</p>
					<div class="options">
						<label>
							<input type="radio" name="answer14" value="A"> bintang penerbit
						</label>
						<label>
							<input type="radio" name="answer14" value="B"> boscha perpustakaan
						</label>
						<label>
							<input type="radio" name="answer14" value="C" data-correct> teleskop pelajar
						</label>
						<label>
							<input type="radio" name="answer14" value="D"> rasi penulis
						</label>
						<label>
							<input type="radio" name="answer14" value="E"> peneliti penyunting
						</label>
					</div>
				</div>
				<div class="question" id="question15">
					<h5>Soal 15</h5>
					<p>... : penjahit ≈ kuas : ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer15" value="A" data-correct> mesin jahit tukang cat
						</label>
						<label>
							<input type="radio" name="answer15" value="B"> jarum cat
						</label>
						<label>
							<input type="radio" name="answer15" value="C"> pakaian pelukis
						</label>
						<label>
							<input type="radio" name="answer15" value="D"> kain kanvas
						</label>
						<label>
							<input type="radio" name="answer15" value="E"> gunting tembok
						</label>
					</div>
				</div>
				<div class="question" id="question16">
					<h5>Soal 16</h5>
					<p>Setelah lulus S1, jika mahasiswa melanjutkan studi S2 maka ia tidak menikah.
						Resita menikah setelah lulus S1.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer16" value="A"> Resita melanjutkan studi S2
						</label>
						<label>
							<input type="radio" name="answer16" value="B"> Resita tidak melanjutkan studi S2 dan tidak menikah
						</label>
						<label>
							<input type="radio" name="answer16" value="C"> Resita menikah kemudian melanjutkan studi S2
						</label>
						<label>
							<input type="radio" name="answer16" value="D"> Resita menikah setelah melanjutkan studi S2
						</label>
						<label>
							<input type="radio" name="answer16" value="E" data-correct> Resita tidak melanjutkan studi S2
						</label>
					</div>
				</div>
				<div class="question" id="question17">
					<h5>Soal 17</h5>
					<p>Sebelum jam istirahat, siswa mengikuti kegiatan di aula. Firman makan di kantin sekolah pada jam istirahat.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer17" value="A"> Firman tidak mengikuti kegiatan setelah makan di kantin sekolah
						</label>
						<label>
							<input type="radio" name="answer17" value="B"> Firman mengikuti kegiatan dan makan di kantin sekolah
						</label>
						<label>
							<input type="radio" name="answer17" value="C"> Firman tidak mengikuti kegiatan karena makan di kantin sekolah
						</label>
						<label>
							<input type="radio" name="answer17" value="D" data-correct> Firman mengikuti kegiatan sebelum makan di kantin sekolah
						</label>
						<label>
							<input type="radio" name="answer17" value="E"> Firman mengikuti kegiatan setelah makan di kantin sekolah
						</label>
					</div>
				</div>
				<div class="question" id="question18">
					<h5>Soal 18</h5>
					<p>Semua bunga berwarna cerah penyerbukannya dibantu serangga. Sebagian bunga di taman tidak berwarna cerah.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer18" value="A"> Semua bunga di taman penyerbukannya dibantu serangga
						</label>
						<label>
							<input type="radio" name="answer18" value="B"> Semua bunga di taman penyerbukannya tidak dibantu serangga
						</label>
						<label>
							<input type="radio" name="answer18" value="C"> Sebagian bunga di taman penyerbukannya dibantu serangga
						</label>
						<label>
							<input type="radio" name="answer18" value="D" data-correct> Sebagian bunga di taman penyerbukannya tidak dibantu serangga
						</label>
						<label>
							<input type="radio" name="answer18" value="E"> Sebagian bunga di taman penyerbukannya dibantu serangga, sebagiannya lagi tidak
						</label>
					</div>
				</div>
				<div class="question" id="question19">
					<h5>Soal 19</h5>
					<p>Jika musim kemarau, maka tumbuhtumbuhan meranggas. Saat tumbuh-tumbuhan meranggas, sampah berserakan.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer19" value="A"> Saat kemarau sampah tidak berserakan
						</label>
						<label>
							<input type="radio" name="answer19" value="B"> Sampah berserakan terjadi pada bukan musim kemarau
						</label>
						<label>
							<input type="radio" name="answer19" value="C"> Sampah berserakan bukan karena tumbuh- tumbuhan yang meranggas
						</label>
						<label>
							<input type="radio" name="answer19" value="D" data-correct> Saat musim kemarau sampah berserakan
						</label>
						<label>
							<input type="radio" name="answer19" value="E"> Saat musim bukan kemarau sampah berserakan
						</label>
					</div>
				</div>
				<div class="question" id="question20">
					<h5>Soal 20</h5>
					<p>Siswa kursus level 1 baru naik ke level 2 jika sudah lulus ujian geometri. Ardi dan Nolang adalah siswa kursus level 2.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer20" value="A"> Ardi lulus ujian geometri dengan nilai bagus sedangkan Nolang hampir lulus ujian geometri
						</label>
						<label>
							<input type="radio" name="answer20" value="B" data-correct> Ardi dan Nolang keduanya lulus ujian geometri
						</label>
						<label>
							<input type="radio" name="answer20" value="C"> Ardi lulus ujian geometri tetapi Ardi tidak lulus ujian geometri
						</label>
						<label>
							<input type="radio" name="answer20" value="D"> Ardi dan Nolang keduanya tidak lulus ujian geometri
						</label>
						<label>
							<input type="radio" name="answer20" value="E"> Ardi tidak lulus ujian geometri tetapi Ardi lulus ujian geometri
						</label>
					</div>
				</div>
				<div class="question" id="question21">
					<h5>Soal 21</h5>
					<p>Semua atlet berada di pusat pelatihan atau libur di rumah masing-masing. Ruang pusat pelatihan atlet sedang digunakan.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer21" value="A" data-correct> Semua atlet tidak berada di rumah masing-masing
						</label>
						<label>
							<input type="radio" name="answer21" value="B"> Semua atlet sedang berlatih di rumah masing-masing
						</label>
						<label>
							<input type="radio" name="answer21" value="C"> Semua atlet sedang berada di rumah masing-masing
						</label>
						<label>
							<input type="radio" name="answer21" value="D"> Tidak ada atlet yang sedang latihan di rumah masing-masing
						</label>
						<label>
							<input type="radio" name="answer21" value="E"> Semua atlet, sedang berada di ruang pusat latihan di rumah masing-masing
						</label>
					</div>
				</div>
				<div class="question" id="question22">
					<h5>Soal 22</h5>
					<p> Semua pegawai diberikan THR. Sebagian pegawai diberikan cuti.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer22" value="A" data-correct> Sebagian pegawai tidak diberikan cuti tetapi diberikan THR
						</label>
						<label>
							<input type="radio" name="answer22" value="B"> Sebagian pegawai diberikan cuti tetapi tidak diberikan THR
						</label>
						<label>
							<input type="radio" name="answer22" value="C"> Sebagian pegawai tidak diberikan cuti dan tidak diberikan THR
						</label>
						<label>
							<input type="radio" name="answer22" value="D"> Semua pegawai diberikan cuti dan diberikan THR
						</label>
						<label>
							<input type="radio" name="answer22" value="E"> Semua pegawai tidak diberikan cuti tetapi diberikan THR
						</label>
					</div>
				</div>
				<div class="question" id="question23">
					<h5>Soal 23</h5>
					<p>Penonton dapat memperoleh informasi pembelian karcis melalui poster atau internet.
						Hari ini layanan internet tidak dapat diakses. Hari ini poster belum ditempel.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer23" value="A"> Penonton dapat memperoleh informasi pembelian karcis
						</label>
						<label>
							<input type="radio" name="answer23" value="B" data-correct> Penonton tidak dapat memperoleh informasi pembelian karcis
						</label>
						<label>
							<input type="radio" name="answer23" value="C"> Penonton tidak dapat membeli karcis
						</label>
						<label>
							<input type="radio" name="answer23" value="D"> Penonton tidak memerlukan informasi pembelian karcis
						</label>
						<label>
							<input type="radio" name="answer23" value="E"> Penonton dapat memperoleh informasi selain pembelian karcis
						</label>
					</div>
				</div>
				<div class="question" id="question24">
					<h5>Soal 24</h5>
					<p>Memancing adalah aktivitas yang selalu Eko lakukan pada hari Minggu. Minggu ini pekerjaan Eko menumpuk.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer24" value="A" data-correct> Hari Minggu ini Eko memancing
						</label>
						<label>
							<input type="radio" name="answer24" value="B"> Hari Minggu ini Eko tidak memancing
						</label>
						<label>
							<input type="radio" name="answer24" value="C"> Hari Minggu ini Eko menyelesaikan pekerjaan
						</label>
						<label>
							<input type="radio" name="answer24" value="D"> JHari Minggu ini Eko memancing setelah menyelesaikan pekerjaan
						</label>
						<label>
							<input type="radio" name="answer24" value="E"> Hari Minggu ini Eko tidak memancing tetapi dia menyelesaikan pekerjaan
						</label>
					</div>
				</div>
				<div class="question" id="question25">
					<h5>Soal 25</h5>
					<p>Semua handphone dapat digunakan untuk mengirim SMS.
					Sebagian handphone dapat digunakan untuk mengakses internet.
					</p>
					<div class="options">
						<label>
							<input type="radio" name="answer25" value="A" data-correct> Sebagian handphone dapat digunakan untuk mengakses internet dan untuk mengirim SMS
						</label>
						<label>
							<input type="radio" name="answer25" value="B"> Sebagian handphone dapat digunakan untuk mengakses internet tetapi tidak bisa untuk mengirim SMS
						</label>
						<label>
							<input type="radio" name="answer25" value="C"> Sebagian handphone tidak dapat digunakan untuk mengakses internet tetapi bisa untuk mengirim SMS
						</label>
						<label>
							<input type="radio" name="answer25" value="D"> Semua handphone dapat digunakan untuk mengakses internet tetapi tidak bisa untuk mengirim SMS
						</label>
						<label>
							<input type="radio" name="answer25" value="E">  Semua handphone tidak dapat digunakan untuk mengakses internet dan tidak bisa untuk mengirim SMS

						</label>
					</div>
				</div>
				<div class="question" id="question26">
					<h5>Soal 26</h5>
					<p>Semua peserta SBMPTN harus mengerjakan soal TPA dan TKD Umum. Peserta SBMPTN kelompok Soshum harus mengerjakan soal TKD Soshum. Vita adalah peserta SBMPTN kelompok Soshum.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer26" value="A"> Vita hanya harus mengerjakan soal TPA dan TKD Umum
						</label>
						<label>
							<input type="radio" name="answer26" value="B" data-correct> Vita harus mengerjakan soal TPA, TKD Umum, dan TKD Soshum
						</label>
						<label>
							<input type="radio" name="answer26" value="C"> Vita harus mengerjakan soal TPA, TKD Umum, atau TKD Soshum
						</label>
						<label>
							<input type="radio" name="answer26" value="D"> Vita tidak harus mengerjakan soal TPA, TKD Umum, dan TKD Soshum
						</label>
						<label>
							<input type="radio" name="answer26" value="E"> Vita tidak harus mengerjakan soal TPA, TKD Umum, atau TKD Soshum
						</label>
					</div>
				</div>
				<div class="question" id="question27">
					<h5>Soal 27</h5>
					<p>Jika pemasukan pajak berkurang, maka anggaran belanja negara turun. Penurunan anggaran belanja negara menyebabkan pembangunan terhambat.</p>
					<div class="options">
						<label>
							<input type="radio" name="answer27" value="A"> Penurunan anggaran belanja negara tidak menghambat pembangunan
						</label>
						<label>
							<input type="radio" name="answer27" value="B"> Pembangunan terhambat selalu disebabkan oleh turunnya pemasukan pajak
						</label>
						<label>
							<input type="radio" name="answer27" value="C" data-correct> Pemasukan pajak yang berkurang menyebabkan terhambatnya pembangunan
						</label>
						<label>
							<input type="radio" name="answer27" value="D"> Pemasukan pajak yang berkurang tidak mempengaruhi pembangunan
						</label>
						<label>
							<input type="radio" name="answer27" value="E"> Pemasukan pajak tidak berkurang maka terjadi hambatan dalam pembangunan
						</label>
					</div>
				</div>
				<div class="question" id="question28">
					<h5>Soal 28</h5>
					<p>Pilihlah jawaban yang paling tepat
						berdasarkan fakta atau informasi yang
						disajikan dalam setiap teks!
						<p>TEKS 1</p>
						(untuk menjawab soal nomor 28 sampai
						dengan nomor 31).</p>
						<p>Di suatu pertemuan ada 4 orang pria dewasa,
						4 wanita dewasa, dan 4 anak-anak. Keempat
						pria dewasa itu bernama Santo, Markam,
						Gunawan, dan Saiful. Keempat wanita dewasa
						itu bernama Ria, Gina, Dewi, dan Hesti.
						Keempat anak itu bernama Hadi, Putra, Bobby
						dan Soleh. Sebenarnya mereka berasal dari 4
						keluarga yang setiap keluarga terdiri dari
						seorang ayah, seorang ibu dan satu orang
						anak, namun tidak diketahui yang mana yang
						menjadi ayah, dan mana yang menjadi ibu,
						dan mana yang menjadi anak dari masing-masing keluarga itu, kecuali beberapa hal
						sebagai berikut:
						(1) Ibu Ria adalah ibu dari Soleh
						(2) Pak Santo adalah ayah dari Hadi
						(3) Pak Saiful adalah suami dari Ibu Dewi,
						tetapi bukan ayah dari Bobby
						(4) Pak Gunawan adalah suami Ibu Hesti.</p>
						<p>Putra adalah ....</p>
					<div class="options">
						<label>
							<input type="radio" name="answer28" value="A"> Anak dari Pak Markam
						</label>
						<label>
							<input type="radio" name="answer28" value="B" data-correct> Anak dari Pak Saiful
						</label>
						<label>
							<input type="radio" name="answer28" value="C"> Anak dari Pak Santo
						</label>
						<label>
							<input type="radio" name="answer28" value="D"> Anak dari Pak Gunawan
						</label>
						<label>
							<input type="radio" name="answer28" value="E"> Anak dari Ibu Ria
						</label>
					</div>
				</div>
				<div class="question" id="question29">
					<h5>Soal 29</h5>
					<p>Ibu Gina adalah ....</p>
					<div class="options">
						<label>
							<input type="radio" name="answer29" value="A"> Isteri Pak Saiful dengan anak bernama Bobby
						</label>
						<label>
							<input type="radio" name="answer29" value="B"> Isteri Pak Gunawan dengan anak bernama Bobby
						</label>
						<label>
							<input type="radio" name="answer29" value="C"> Isteri Pak Markam dengan anak bernama Hadi
						</label>
						<label>
							<input type="radio" name="answer29" value="D"> Isteri Pak Gunawan dengan anak bernama Putra
						</label>
						<label>
							<input type="radio" name="answer29" value="E" data-correct> Isteri Pak Santo dengan anak bernama Hadi
						</label>
					</div>
				</div>
				<div class="question" id="question30">
					<h5>Soal 30</h5>
					<p>Ibu Hesti dan Ibu Dewi dan masing-masing keluarganya tinggal di kota Bandung, sementara kedua keluarga lainnya tinggal di kota Jakarta. Siapakah yang tinggal di kota Jakarta? </p>
					<div class="options">
						<label>
							<input type="radio" name="answer30" value="A" data-correct> Pak Markam
						</label>
						<label>
							<input type="radio" name="answer30" value="B"> Putra
						</label>
						<label>
							<input type="radio" name="answer30" value="C"> Pak Saiful
						</label>
						<label>
							<input type="radio" name="answer30" value="D"> Bobby
						</label>
						<label>
							<input type="radio" name="answer30" value="E"> Pak Gunawan
						</label>
					</div>
				</div>
				<div class="question" id="question31">
					<h5>Soal 31</h5>
					<p>Jika pernyataan (1) di atas dihilangkan, periksalah apakah masih bisa disimpulkan bahwa ....</p>
					<p>I. Ibu Ria kemungkinannya bersuamikan Pak Markam atau Pak Santo</p>
					<p>II. Soleh kemungkinannya anak dari Pak Markam atau Pak Santo</p>
					<p>III. Ibu Dewi kemungkinannya adalah ibu dari Soleh atau Putra</p>
					<div class="options">
						<label>
							<input type="radio" name="answer31" value="A"> Hanya I yang benar
						</label>
						<label>
							<input type="radio" name="answer31" value="B"> Hanya II yang benar
						</label>
						<label>
							<input type="radio" name="answer31" value="C"> Hanya III yang benar
						</label>
						<label>
							<input type="radio" name="answer31" value="D" data-correct> Hanya I dan III yang benar
						</label>
						<label>
							<input type="radio" name="answer31" value="E"> Ketiganya benar
						</label>
					</div>
				</div>
				<div class="question" id="question32">
					<h5>Soal 32</h5>
					<p>TEKS 2</p>
						(untuk menjawab soal nomor 32 sampai dengan nomor 34)</p>
						<p>Rista adalah siswa dari sekolah Pribadi. Ia sekolah dari Senin sampai Jumat, masuk dari jam 8 pagi hingga jam 3 sore, kecuali hari Kamis, sudah pulang sejak jam 12.00. Kebetulan lokasi sekolah cukup dekat dari
						rumahnya sehingga dapat ditempuh hanya
						dalam beberapa menit saja. Selain
						bersekolah, ia juga mengikuti les piano, latihan
						taekwondo, dan les melukis.</p>
						<p>• Les piano diperoleh dari seorang guru
						privat yang datang ke rumahnya setiap
						hari Senin jam 3.30 s.d 4.30 sore.</p>
						<p>• Latihan taekwondo ia lakukan bersama
						teman-temannya di lapangan kompleks
						perumahannya setiap hari Selasa dan
						Kamis jam 4.00 s.d 6.00 sore.</p>
						<p>• Les melukisnya dijadwalkan setiap hari
						Rabu jam 4.30 s.d 6.00 sore.</p>
						<p>• Setiap hari Jumat usai sekolah biasanya
						ia tetap tinggal di sekolah mengikuti
						kegiatan ekstrakurikuler selama 90 menit.</p>
						<p>Guru piano menawarkan Rista untuk
						mengganti hari les pianonya ke hari lain
						tapi masih dengan jam yang sama. Hari
						lain yang dapat diambil oleh Rista adalah?</p>
					<div class="options">
						<label>
							<input type="radio" name="answer32" value="A"> Senin
						</label>
						<label>
							<input type="radio" name="answer32" value="B"> Selasa
						</label>
						<label>
							<input type="radio" name="answer32" value="C" data-correct> Rabu
						</label>
						<label>
							<input type="radio" name="answer32" value="D"> Kamis
						</label>
						<label>
							<input type="radio" name="answer32" value="E"> Jumat
						</label>
					</div>
				</div>
				<div class="question" id="question33">
					<h5>Soal 33</h5>
					<p>Karena bakat yang baik yang dimiliki Rista
					dalam melukis, ia diberi kesempatan oleh
					guru seninya untuk naik ke kelas
					lanjutannya. Ia bisa ambil kelas lanjutannya
					di salah satu hari pada jam yang masih
					sama dengan yang sekarang. Hari-hari yang dapat ia gunakan tanpa mengganggu
					kegiatan lainnya adalah ....
					</p>
					<div class="options">
						<label>
							<input type="radio" name="answer33" value="A"> Selasa dan Kamis
						</label>
						<label>
							<input type="radio" name="answer33" value="B"> Selasa dan Jumat
						</label>
						<label>
							<input type="radio" name="answer33" value="C" data-correct> Senin dan Jumat
						</label>
						<label>
							<input type="radio" name="answer33" value="D"> Kamis dan Jumat
						</label>
						<label>
							<input type="radio" name="answer33" value="E"> Senin dan Kamis
						</label>
					</div>
				</div>
				<div class="question" id="question34">
					<h5>Soal 34</h5>
					<p>Rista terpilih untuk mewakili sekolahnya
					dalam pertandingan bola basket antar
					sekolah di kotanya. Untuk itu sekolah
					menjadwalkan latihan setiap hari mulai dari
					jam 5.00 s.d 6.30 sore. Untuk itu kegiatan
					yang harus batalkan karena bentrok dengan
					latihan bola basket adalah ....</p>
					<div class="options">
						<label>
							<input type="radio" name="answer34" value="A"> Les piano dan latihan taekwondo
						</label>
						<label>
							<input type="radio" name="answer34" value="B"> Latihan taekwondo dan kegiatan ekstrakurikuler sekolah
						</label>
						<label>
							<input type="radio" name="answer34" value="C"> Les melukis dan kegiatan ekstrakurikuler sekolah
						</label>
						<label>
							<input type="radio" name="answer34" value="D"> Les piano dan les melukis
						</label>
						<label>
							<input type="radio" name="answer34" value="E" data-correct> Les melukis dan latihan taekwondo
						</label>
					</div>
				</div>
				<div class="question" id="question35">
					<h5>Soal 35</h5>
					<p>TEKS 3</p>
					(untuk menjawab soal nomor 35 sampai
					dengan nomor 38)</p>
					<p>Tiga orang dewasa Roni, Susi, dan Vina
					bersama dengan lima anak-anak Nuri, Heru,
					Jono, Lisa dan Marta akan pergi berwisata
					dengan menggunakan sebuah kendaraan
					minibus. Minibus tersebut memiliki satu tempat
					di sebelah pengemudi, dan dua buah bangku
					panjang di belakang yang masing-masing
					terdiri dari 3 tempat duduk, sehingga total
					terdapat delapan tempat duduk di dalam
					minibus tersebut, termasuk pengemudi. Setiap
					peserta wisata harus duduk sendiri, masingmasing di sebuah kursi yang ada. Susunan
					tempat duduk harus disesuaikan dengan
					beberapa ketentuan sebagai berikut:</p>
					<p>• Pada masing-masing bangku harus
					terdapat satu orang dewasa yang duduk</p>
					<p>• Salah satu di antara Roni dan Susi harus
					duduk sebagai pengemudi
					</p>
					<p>• Jono harus duduk bersebelahan dengan Marta</p>
					<p>Peserta wisata yang dapat duduk di
					sebelah pengemudi adalah ....</p>
					<div class="options">
						<label>
							<input type="radio" name="answer35" value="A" data-correct> Lisa 
						</label>
						<label>
							<input type="radio" name="answer35" value="B"> Jono
						</label>
						<label>
							<input type="radio" name="answer35" value="C"> Roni
						</label>
						<label>
							<input type="radio" name="answer35" value="D"> Susi
						</label>
						<label>
							<input type="radio" name="answer35" value="E"> Vina
						</label>
					</div>
				</div>
				<div class="question" id="question36">
					<h5>Soal 36</h5>
					<p> Jika Nuri duduk bersebelahan dengan
					Vina, maka pernyataan berikut ini yang
					tidak benar adalah...
					</p>
					<div class="options">
						<label>
							<input type="radio" name="answer36" value="A"> Jono duduk berdampingan di sebelah Susi
						</label>
						<label>
							<input type="radio" name="answer36" value="B"> Lisa duduk berdampingan di sebelah Vina
						</label>
						<label>
							<input type="radio" name="answer36" value="C"> Heru duduk di bangku paling depan
						</label>
						<label>
							<input type="radio" name="answer36" value="D"> Nuri duduk di bangku yang sama dengan Heru
						</label>
						<label>
							<input type="radio" name="answer36" value="E" data-correct> Heru duduk di bangku yang sama dengan Roni
						</label>
					</div>
				</div>
				<div class="question" id="question37">
					<h5>Soal 37</h5>
					<p> Jika Susi duduk di bangku yang berada di belakang bangku Jono, pernyataan yang pasti benar adalah ....</p>
					<div class="options">
						<label>
							<input type="radio" name="answer37" value="A"> Heru duduk di bangku di depan bangku tempat Marta duduk
						</label>
						<label>
							<input type="radio" name="answer37" value="B"> Lisa duduk di bangku di depan bangku tempat Nuri duduk
						</label>
						<label>
							<input type="radio" name="answer37" value="C"> Nuri duduk di bangku yang sama dengan Heru
						</label>
						<label>
							<input type="radio" name="answer37" value="D"> Lisa duduk di bangku yang sama dengan Sarah
						</label>
						<label>
							<input type="radio" name="answer37" value="E" data-correct> Marta duduk di bangku yang sama dengan Vina
						</label>
					</div>
				</div>
				<div class="question" id="question38">
					<h5>Soal 38</h5>
					<p>Susunan tempat duduk yang mungkin dalam sebuah bangku adalah ....</p>
					<div class="options">
						<label>
							<input type="radio" name="answer38" value="A"> Nuri, Jono, dan Marta
						</label>
						<label>
							<input type="radio" name="answer38" value="B"> Nuri, Jono, dan Vina
						</label>
						<label>
							<input type="radio" name="answer38" value="C"> Nuri, Susi, dan Vina
						</label>
						<label>
							<input type="radio" name="answer38" value="D" data-correct> Heru, Lisa, dan Susi
						</label>
						<label>
							<input type="radio" name="answer38" value="E"> Lisa, Marta, dan Roni
						</label>
					</div>
				</div>
				<div class="question" id="question39">
					<h5>Soal 39</h5>
					<p>Pecahan yang nilainya terletak antara $\frac{3}{5}$ dan $\frac{9}{10}$ adalah ....</p>
					<div class="options">
						<label>
							<input type="radio" name="answer39" value="A"> $\frac{3}{8}$
						</label>
						<label>
							<input type="radio" name="answer39" value="B"> $\frac{1}{2}$
						</label>
						<label>
							<input type="radio" name="answer39" value="C"> $\frac{4}{7}$
						</label>
						<label>
							<input type="radio" name="answer39" value="D" data-correct> $\frac{3}{4}$
						</label>
						<label>
							<input type="radio" name="answer39" value="E"> $\frac{5}{11}$
						</label>
					</div>
				</div>
				<div class="question" id="question40">
					<h5>Soal 40</h5>
					<p>$\frac{5}{8} - 1 + \frac{2}{3} =$</p>
					<div class="options">
						<label>
							<input type="radio" name="answer40" value="A"> 7/48
						</label>
						<label>
							<input type="radio" name="answer40" value="B"> 23/48
						</label>
						<label>
							<input type="radio" name="answer40" value="C" data-correct> 7/24
						</label>
						<label>
							<input type="radio" name="answer40" value="D"> 23/24
						</label>
						<label>
							<input type="radio" name="answer40" value="E"> 7/12
						</label>
					</div>
				</div>
				<div class="question" id="question41">
					<h5>Soal 41</h5>
					<p>Jika 4 adalah x% dari 160 maka nilai x adalah ....</p>
					<div class="options">
						<label>
							<input type="radio" name="answer41" value="A"> 0,025
						</label>
						<label>
							<input type="radio" name="answer41" value="B"> 0,25 
						</label>
						<label>
							<input type="radio" name="answer41" value="C" data-correct> 2,5
						</label>
						<label>
							<input type="radio" name="answer41" value="D"> 25
						</label>
						<label>
							<input type="radio" name="answer41" value="E"> 250
						</label>
					</div>
				</div>
				<div class="question" id="question42">
					<h5>Soal 42</h5>
					<p>Jika $15 \times \frac{2}{8} = 8 \times \frac{2}{5} + \frac{2}{5} + \frac{2}{5} + \frac{2}{5} \times a$, maka nilai $a$ adalah ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer42" value="A"> 3
						</label>
						<label>
							<input type="radio" name="answer42" value="B"> 4
						</label>
						<label>
							<input type="radio" name="answer42" value="C" data-correct> 5
						</label>
						<label>
							<input type="radio" name="answer42" value="D"> 6
						</label>
						<label>
							<input type="radio" name="answer42" value="E"> 7
						</label>
					</div>
				</div>
				<div class="question" id="question43">
					<h5>Soal 43</h5>
					<p>Jika $\frac{5}{9}$ dari $\frac{27}{35}$ sama dengan $\frac{x}{1/14}$, maka nilai $x$ adalah ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer43" value="A"> 2
						</label>
						<label>
							<input type="radio" name="answer43" value="B"> 4
						</label>
						<label>
							<input type="radio" name="answer43" value="C" data-correct> 6
						</label>
						<label>
							<input type="radio" name="answer43" value="D"> 8
						</label>
						<label>
							<input type="radio" name="answer43" value="E"> 10
						</label>
					</div>
				</div>
				<div class="question" id="question44">
					<h5>Soal 44</h5>
					<p>Ibu membeli 4 $\frac{3}{5}$ kg jeruk di sebuah supermarket. Jika harga satu kg jeruk adalah Rp15.000,00 dan ibu menyerahkan 2 lembar uang Rp50.000,00 ke kasir, maka uang kembalian yang diterima ibu adalah ...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer44" value="A"> Rp 19.000,00
						</label>
						<label>
							<input type="radio" name="answer44" value="B" data-correct> Rp 31.000,00
						</label>
						<label>
							<input type="radio" name="answer44" value="C"> Rp 36.000,00
						</label>
						<label>
							<input type="radio" name="answer44" value="D"> Rp 63.000,00

						</label>
						<label>
							<input type="radio" name="answer44" value="E"> Rp 69.000,00
						</label>
					</div>
				</div>
				<div class="question" id="question45">
					<h5>Soal 45</h5>
					<p>Tabel berikut menunjukan hasil dua kali tes matematika.</p>
					<p><table>
						<tr>
						<th>Nama</th>
						<th>Tes1</th>
						<th>Tes2</th>
						</tr>
						<tr>
						<td>Ahmad</td>
						<td>80</td>
						<td>75</td>
						</tr>
						<tr>
						<td>Beny</td>
						<td>80</td>
						<td>96</td>
						</tr>
						<tr>
						<td>Citra</td>
						<td>80</td>
						<td>84</td>
						</tr>
						<tr>
						<td>Dinda</td>
						<td>80</td>
						<td>100</td>
						</tr>
						<tr>
						<td>Eka</td>
						<td>80</td>
						<td>90</td>
						</tr>
					</table>
					</p>
					<p>Peserta yang nilainya meningkat 20%
					pada tes kedua jika dibandingkan tes
					pertama adalah ....</p>
					<div class="options">
						<label>
							<input type="radio" name="answer45" value="A"> Ahmad
						</label>
						<label>
							<input type="radio" name="answer45" value="B" data-correct> Beny
						</label>
						<label>
							<input type="radio" name="answer45" value="C"> Citra
						</label>
						<label>
							<input type="radio" name="answer45" value="D"> Dinda
						</label>
						<label>
							<input type="radio" name="answer45" value="E"> Eka
						</label>
					</div>
				</div>
				<div class="question" id="question46">
					<h5>Soal 46</h5>
					<p>Agar Jika kita sudah melakukan copy dan ingin
					paste, kita bisa melakukan paste dengan
					menekan tombol pada keyboad...Perintah copy
					atau salin dapat dilakukan dengan kombinasi
					tombol pada keyboard dengan menekan...
					</p>
					<div class="options">
						<label>
							<input type="radio" name="answer46" value="A"> Ctrl + O
						</label>
						<label>
							<input type="radio" name="answer46" value="B"> Ctrl + X
						</label>
						<label>
							<input type="radio" name="answer46" value="C" data-correct> Ctrl + V
						</label>
						<label>
							<input type="radio" name="answer46" value="D"> Ctrl + C
						</label>
						<label>
							<input type="radio" name="answer46" value="E"> Ctrl + P
						</label>
					</div>
				</div>
				<div class="question" id="question47">
					<h5>Soal 47</h5>
					<p>Perintah copy atau salin dapat dilakukan
					dengan kombinasi tombol pada keyboard
					dengan menekan...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer47" value="A"> Ctrl + O
						</label>
						<label>
							<input type="radio" name="answer47" value="B"> Ctrl + X
						</label>
						<label>
							<input type="radio" name="answer47" value="C"> Ctrl + V
						</label>
						<label>
							<input type="radio" name="answer47" value="D" data-correct> Ctrl + C
						</label>
						<label>
							<input type="radio" name="answer47" value="E"> Ctrl + P
						</label>
					</div>
				</div>
				<div class="question" id="question48">
					<h5>Soal 48</h5>
					<p>Jika ingin memberi cetak tebal pada sebuah text kita perlu menekan icon...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer48" value="A"> Bold
						</label>
						<label>
							<input type="radio" name="answer48" value="B"> Italic
						</label>
						<label>
							<input type="radio" name="answer48" value="C" data-correct> Garis
						</label>
						<label>
							<input type="radio" name="answer48" value="D"> Underline
						</label>
						<label>
							<input type="radio" name="answer48" value="E"> Justify
						</label>
					</div>
				</div>
				<div class="question" id="question49">
					<h5>Soal 49</h5>
					<p>Berikut ini yang tidak terdapat icon pada Office Button adalah... </p>
					<div class="options">
						<label>
							<input type="radio" name="answer49" value="A"> New
						</label>
						<label>
							<input type="radio" name="answer49" value="B"> Save
						</label>
						<label>
							<input type="radio" name="answer49" value="C"> Print
						</label>
						<label>
							<input type="radio" name="answer49" value="D" data-correct> Copy
						</label>
						<label>
							<input type="radio" name="answer49" value="E"> Semua benar
						</label>
					</div>
				</div>
				<div class="question" id="question50">
					<h5>Soal 50</h5>
					<p>Program yang digunakan untuk
					pengelolah kata pada Microsft Windows
					bernama...</p>
					<div class="options">
						<label>
							<input type="radio" name="answer50" value="A"> Microsoft Excel
						</label>
						<label>
							<input type="radio" name="answer50" value="B" data-correct> Microsoft Word
						</label>
						<label>
							<input type="radio" name="answer50" value="C"> Microsoft Powerpoint
						</label>
						<label>
							<input type="radio" name="answer50" value="D"> One Drive
						</label>
						<label>
							<input type="radio" name="answer50" value="E"> Microsoft Office 365
						</label>
					</div>
				</div>
            </div>

			<div class="button-container">
			<button id="previousBtn" onclick="previousQuestion()" disabled>Previous</button>
			<button id="nextBtn" onclick="nextQuestion()">Next</button>
			<button id="submitBtn" onclick="submitAnswers()">Submit</button>
			</div>
			
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
            calculateTPA();
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

    function calculateTPA() {
		var answers = {};
    var questions = document.getElementsByClassName('question');
    for (var i = 0; i < questions.length; i++) {
        var questionNumber = i + 1;
        var answer = document.querySelector('input[name="answer' + questionNumber + '"]:checked');
        if (answer) {
            var isCorrect = answer.getAttribute('data-correct') === '1' ? 1 : 0;
            answers[questionNumber] = isCorrect;
        }
    }
	
		var xhr = new XMLHttpRequest();
	xhr.open('POST', 'save_tpa_score.php');
	xhr.setRequestHeader('Content-Type', 'application/json');
	xhr.onload = function () {
		if (xhr.status === 200) {
			// Process the response from the server
			var response = JSON.parse(xhr.responseText);
			// Do something with the response
			var nilaiTPA = response.nilaiTPA;
			var kategori = determineCategory(nilaiTPA);
			saveTPAScore(id_calabar, nilaiTPA); // Simpan nilai TPA ke database
			// Lakukan tindakan yang sesuai berdasarkan respons dari server
		}
	};
	xhr.send(JSON.stringify(answers));


    function determineCategory(nilaiTPA) {
        var totalSoal = 50;
        var persentaseBenar = (nilaiTPA / totalSoal) * 100;

        if (persentaseBenar === 100) {
            return "Sangat Baik";
        } else if (persentaseBenar >= 80) {
            return "Baik";
        } else if (persentaseBenar >= 50) {
            return "Cukup";
        } else if (persentaseBenar >= 30) {
            return "Kurang Baik";
        } else {
            return "Kategori Tidak Tersedia";
        }
    }

    function saveTPAScore(id_calabar, nilaiTPA) {
        // Send the data to the server to save the TPA score
        // You can use AJAX to send the data to a PHP script
        // and process it on the server side.

        // For example:
        // var xhr = new XMLHttpRequest();
        // xhr.open('POST', 'save_tpa_score.php');
        // xhr.setRequestHeader('Content-Type', 'application/json');
        // xhr.onload = function () {
        //     if (xhr.status === 200) {
        //         // Process the response from the server
        //         var response = JSON.parse(xhr.responseText);
        //         // Do something with the response
        //     }
        // };
        // xhr.send(JSON.stringify({ id_calabar: id_calabar, nilaiTPA: nilaiTPA }));
    }
}
</script>

</body>
</html>