<?php
require_once "db_connect.php";
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}
require "questions.php";
if (!isset($_SESSION['jawaban'])) {
    $_SESSION['jawaban'] = array();
}
function logout() {
    session_unset();
    session_destroy();
    header("Location: beranda.php");
    exit();
}
if (isset($_GET['logout'])) {
    logout();
}
$nama_lengkap = $_SESSION["nama_lengkap"];
$level = $_SESSION["level"];
$id_calabar = $_SESSION['id_calabar'];
$no_soal = isset($_GET['question']) ? max(1, intval($_GET['question'])) : 1; // Ensure positive question number
$totalQuestions = sizeof($questions);
$totalDijawab = sizeof($_SESSION['jawaban']);
$prev_no = max(1, $no_soal - 1);
$next_no = min($totalQuestions, $no_soal + 1);
if(!isset($_SESSION['jawaban'][$no_soal])){
    $_SESSION['jawaban'][$no_soal] = '';
}
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
function saveTPAScore($id_calabar, $nilaiTPA) {
    global $conn; // Use the global $conn variable
    $sql = "UPDATE tab_pacab SET nilai_tpa = ? WHERE id_calabar = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $nilaiTPA, $id_calabar);
        if (mysqli_stmt_execute($stmt)) {
            echo "Nilai TPA berhasil disimpan.";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
function calculateTPAScore($jawaban, $jumlah_soal, $semua_soal){
    $skorBenar = 1; // Skor untuk jawaban benar
    $skorSalah = 0; // Skor untuk jawaban salah
    $totalBenar = 0;
    for ($nomorSoal = 1; $nomorSoal <= $jumlah_soal; $nomorSoal++) {
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
if (isset($_GET['submit'])) {
    if($totalQuestions !== $totalDijawab) return;
    $jawaban = $_SESSION['jawaban'];
    $nilaiTPA = calculateTPAScore($jawaban, $totalQuestions, $questions);
    saveTPAScore($id_calabar, $nilaiTPA);
    $kategori = determineCategory($nilaiTPA, $totalQuestions);
    $_SESSION['nilai_tpa'] = $nilaiTPA;
    $_SESSION['kategori_tpa'] = $kategori;
    $_SESSION['jawaban'] = array();
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
	<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.13.11/katex.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.13.11/katex.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function updateRefreshCount() {
      if (typeof(Storage) !== "undefined") {
        if (sessionStorage.refreshCount) {
          sessionStorage.refreshCount = Number(sessionStorage.refreshCount) + 1;
        } else {
          sessionStorage.refreshCount = 0;
        }
      } else {
        console.log("Session storage is not supported.");
      }
    }
    function displayRefreshCount() {
      // Cek apakah session storage tersedia
      if (typeof(Storage) !== "undefined") {
        if (sessionStorage.refreshCount) {
          var refreshCount = sessionStorage.refreshCount;
          document.getElementById("refreshCount").textContent = refreshCount;
        } else {
          document.getElementById("refreshCount").textContent = "0";
        }
      } else {
        console.log("Session storage is not supported.");
      }
    }
    window.onload = function() {
      updateRefreshCount();
      displayRefreshCount();
    }
  </script>
<script>
       var isTimerExpired = false;

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
            isTimerExpired = true;
            enableSubmitButton();
        }
    }, 1000);
}
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
    var currentQuestion = 1; 
    function nextQuestion() {
        if (currentQuestion < 4) {
            currentQuestion++;
            showQuestion(currentQuestion);
        }
    }
    function previousQuestion() {
        if (currentQuestion > 1) {
            currentQuestion--;
            showQuestion(currentQuestion);
        }
    }
    function showQuestion(questionNumber) {
        var questions = document.getElementsByClassName("question");
        var questionButtons = document.getElementsByClassName("question-button");
        var soalNomor = document.getElementById("soal-nomor");
        for (var i = 0; i < questions.length; i++) {
            questions[i].style.display = "none";
        }
        for (var j = 0; j < questionButtons.length; j++) {
            questionButtons[j].classList.remove("active");
        }
        questions[questionNumber - 1].style.display = "block";
        questionButtons[questionNumber - 1].classList.add("active");
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
		<a href="#" class="logout-button" id="logout-btn" onclick="logout()">
			<span class="logout-icon"><i class="fas fa-sign-out-alt"></i></span>
			Logout
			</a>
		</div>
</div>
</div>
</div>
<div id="question-content">
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
                           $choiced = $_SESSION['jawaban'][$no_soal] == $choice ? true : false; 
                    ?>
                            <label>
                        <input 
                            type="radio" 
                            name="answer" 
                            value="<?= $choice; ?>"
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
				$choiced = $_SESSION['jawaban'][$no_soal] == $choice ? true : false; 
                    ?>
                    <label>
                        <input 
                            type="radio" 
                            name="answer" 
                            value="<?= $choice; ?>"
                        /> 
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
            window.location.href = 'test-calabar.php?submit=1';
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
<script>
    // Fungsi untuk logout dengan konfirmasi
    function logout() {
        // Tampilkan dialog konfirmasi menggunakan SweetAlert
        Swal.fire({
            title: 'Tes Potensi Akademik belum selesai dikerjakan, yakin ingin keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengklik "Ya", maka lakukan proses logout
                window.location.href = "?logout=true";
            }
        });
    }
</script>
</body>
</html>         