<?php
require_once "db_connect.php";
session_start();

if (isset($_POST['change_password'])) {
    $id_user = $_SESSION['reset_id_user'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "Password dan konfirmasi password harus diisi.";
        header("Location: change_password.php");
        exit;
    } elseif ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok.";
        header("Location: change_password.php");
        exit;
    }

    // Enkripsi password sebelum menyimpan ke database (gunakan metode yang sesuai dengan aplikasi Anda)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update password baru ke dalam database
    $query = "UPDATE tab_user SET password = ? WHERE id_user = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $id_user);
    mysqli_stmt_execute($stmt);

    // Hapus token dari database setelah password berhasil diubah
    $query = "DELETE FROM reset_password_tokens WHERE id_user = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $id_user);
    mysqli_stmt_execute($stmt);

    $_SESSION['success_message'] = "Password Anda telah berhasil diubah.";
    header("Location: login.php"); // Ganti login.php dengan halaman login atau halaman beranda setelah password berhasil diubah
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ganti Password - SIUKM STMIK Komputama Majenang</title>
    <!-- Sisipkan link CSS dan script JS yang diperlukan -->
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Ganti Password</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="password">Password Baru:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="change_password">Ganti Password</button>
        </form>
    </div>
</div>
</body>
</html>
