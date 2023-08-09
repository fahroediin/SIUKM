<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Memulai session
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, redirect ke halaman login.php
    header("Location: login.php");
    exit();
}

// memanggil file question
require "questions.php";

// inisiasi sesi jawaban
if (!isset($_SESSION['jawaban'])) {
    $_SESSION['jawaban'] = array();
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

// Mendapatkan nama depan dan level dari session
$nama_lengkap = $_SESSION["nama_lengkap"];
$level = $_SESSION["level"];
$id_calabar = $_SESSION['id_calabar'];

// Mendapatkan nomor soal yang sedang aktif
$no_soal = isset($_GET['question']) ? intval($_GET['question']) <= 1 ? 1 : intval($_GET['question']) : 1;

// Mendapatkan total jumlah soal dari array soal;
$totalQuestions = sizeof($questions);
$totalDijawab = sizeof($_SESSION['jawaban']);

$prev_no = $no_soal <= 1 ? 1 : $no_soal -1;
$next_no = $no_soal >= $totalQuestions ? $totalQuestions : $no_soal +1;

// Ambil soal dari array berdasarkan nomor soal
$no_soal_key = $no_soal -1;
$soal = $questions[$no_soal_key];

$isAnswered = isset($_GET['a']);
$isQ = isset($_GET['q']);
if($isAnswered && $isQ && in_array($_GET['a'], ['a','b','c','d','e'])){
    $isInt = is_int($_GET['q']);
    if($isInt){
        $answeredSoal = intval($_GET['q']);
        if($answeredSoal >= 1 && $answeredSoal < $totalQuestions){
            $_SESSION['jawaban'][$answeredSoal] = $_GET['a'];
        }
    }
}


// Fungsi untuk menentukan kategori berdasarkan nilai TPA
function determineCategory($nilaiTPA, $total){
    $totalSoal = $total;
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

// Fungsi untuk menyimpan nilai TPA ke database
function saveTPAScore($id_calabar, $nilaiTPA)
{
    // Memasukkan file db_connect.php
    require_once "db_connect.php";

    // Menyimpan nilai TPA ke database berdasarkan id_calabar
    $sql = "UPDATE tab_pacab SET nilai_tpa = $nilaiTPA WHERE id_calabar = $id_calabar";

    if (mysqli_query($conn, $sql)) {
        echo "Nilai TPA berhasil disimpan.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Menutup koneksi database
    mysqli_close($conn);
}

// Fungsi untuk menghitung nilai TPA berdasarkan jawaban
function calculateTPAScore($jawaban, $jumlah_soal, $semua_soal){
    $skorBenar = 1; // Skor untuk jawaban benar
    $skorSalah = 0; // Skor untuk jawaban salah

    $totalBenar = 0;

    // Iterasi untuk setiap nomor soal
    for ($nomorSoal = 1; $nomorSoal <= $jumlah_soal; $nomorSoal++) {
        // Ambil nilai jawaban yang dipilih oleh pengguna
        $jawabanPengguna = $jawaban[$nomorSoal];
        $kunciJawaban = $semua_soal[$nomorSoal]['correct_answer']; 

        if($jawabanPengguna == $kunciJawaban){
            $totalBenar = $totalBenar + 1;
        }
    }

    $totalSalah = $jumlah_soal - $totalBenar;
    $nilaiTPA = ($totalBenar * $skorBenar) + ($totalSalah * $skorSalah);

    return $nilaiTPA;
}

// Memeriksa apakah pengguna sudah mengisi jawaban TPA
if (isset($_GET['submit'])) {
    if($totalQuestions !== $totalDijawab) return;

    // Mendapatkan jawaban pengguna dari form
    $jawaban = $_SESSION['jawaban'];

    // Menghitung nilai TPA
    $nilaiTPA = calculateTPAScore($jawaban, $totalQuestions, $questions);

    // Menyimpan nilai TPA ke database
    saveTPAScore($id_calabar, $nilaiTPA);

    // Menampilkan kategori berdasarkan nilai TPA
    $kategori = determineCategory($nilaiTPA, $totalQuestions);

    // Memperbarui nilai_tpa dan kategori_tpa di sesi dengan nilai terbaru
    $_SESSION['nilai_tpa'] = $nilaiTPA;
    $_SESSION['kategori_tpa'] = $kategori;

    // Reset sesi jawaban
    $_SESSION['jawaban'] = array();

    // INI KEMANA
    header("Location: selesai_test.php");
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
    <link rel="stylesheet" type="text/css" href="../assets/css/test-calabar.css">
	  <!-- Pustaka MathJax -->
	<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

      <!-- Pustaka KaTeX -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.13.11/katex.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.13.11/katex.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
	
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
            <h3>Selamat datang, <?php echo $nama_lengkap; ?></h3>
        </div>
		<div class="logout-container">
		<a href="?logout=true" class="logout-button">
			<span class="logout-icon"><i class="fas fa-sign-out-alt"></i></span>
			Logout
			</a>
		</div>

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
        <!-- Hidden input for indicator -->
        <input type="hidden" name="current" value="<?= $no_soal; ?>" />
        <input type="hidden" name="prev" value="<?= $prev_no; ?>" />
        <input type="hidden" name="next" value="<?= $next_no;?>" />
        <?php
            if($soal['type'] == 'a'){
        ?>

            <div class="question active">
                <h5>Soal <?= $no_soal; ?></h5>
                <p><?= $soal['prompt']; ?></p>
                <div class="options">
                    <?php
                        foreach(['a','b','c','d','e'] as $choice){
                    ?>
                    <label>
                        <input 
                            type="radio" 
                            name="answer" 
                            value="<?= $choice; ?>"
                            <?= $_SESSION['jawaban'][$no_soal] == $choice ? 'checked' : ''; ?>"
                        /> 
                        <?= $soal['options'][$choice]; ?>
                    </label>
                    <?php } ?>
                </div>
            </div>
        <?php };?>
  

        <?php
            if($soal['type'] == 'b'){
        ?>
            <div class="question active">
                <h5>Soal <?= $no_soal; ?></h5>
                <p><?= $soal['table_caption']; ?></p>
                <p>
                    <table>
						<tr>
                        <?php 
                            foreach ($soal['table_header'] as $item) {
                                echo '<th>' . $item . '</th>';
                            }
                        ?>
						</tr>

                        <?php
                            foreach($soal['table_content'] as $table_content_child){
                        ?>
                            <tr>
                            <?php 
                                foreach ($table_content_child as $item) {
                                    echo '<td>' . $item . '</td>';
                                }
                            ?>
                            </tr>
                        <?php } ?>
                    </table>
                </p>
                <p><?= $soal['prompt']; ?></p>
                <div class="options">
                <?php
                        foreach(['a','b','c','d','e'] as $choice){
                    ?>
                    <label>
                        <input 
                            type="radio" 
                            name="answer" 
                            value="<?= $choice; ?>"
                            <?= $_SESSION['jawaban'][$no_soal] == $choice ? 'checked' : ''; ?>/> 
                        <?= $soal['options'][$choice]; ?>
                    </label>
                    <?php } ?>
                </div>
            </div>
        <?php };?>
    </div>
	<div class="button-container">
		<button id="previousBtn" onclick="go('prev')" <?= $prev_no == $no_soal ? 'disabled': '';?>>Previous</button>
		<button id="nextBtn" onclick="go('next')" <?= $next_no == $no_soal ? 'disabled': '';?>>Next</button>
		<button
            id="submitBtn"
            onclick="finish(<?= $totalDijawab ?>, <?= $totalQuestions ?>)"
            <?= $totalDijawab !== $totalQuestions ? 'disabled': '';?>
        >Submit</button>
	</div>

<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js"></script>
<script>
    function go(act){
        const q = document.querySelector('input[name="current"]').value || "";
        const a = document.querySelector('input[name="answer"]:checked').value || "";
        const to = document.querySelector(`input[name="${act}"]`).value || 1;
        
        window.location.href = `test-calabar.php?q=${q}&a=${a}&question=${to}';`
    }

    function finish(answered,total){
        if(answered != total) return showSnackbar('Harap menyelesaikan seluruh pertanyaan!')

        showSnackbar('Terima kasih telah mengerjakan tes potensi akademik')
        setTimeout(function () {
            window.location.href = 'selesai_test.php?submit=1';
        }, 3500);
    }
    

    function showSnackbar(msg) {
        var snackbar = document.getElementById('snackbar');
        snackbar.textContent = msg;
        snackbar.classList.add('show');
        setTimeout(function () {
            snackbar.classList.remove('show');
        }, 3000);
    }

</script>

</body>
</html>