<?php
require_once "db_connect.php";

// Function to generate a random token
function generateToken($length = 32)
{
    return bin2hex(random_bytes($length));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = $_POST["id_user"];
    $email = $_POST["email"];

    // Check if the user exists in the database
    $query = "SELECT * FROM tab_user WHERE id_user = ? AND email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $id_user, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // User exists, generate a unique token
        $token = generateToken();

        // Save the token, id_user, email, and expiry timestamp to the password_reset_tokens table
        $expiry_timestamp = time() + (24 * 60 * 60); // Token valid for 24 hours

        // Insert or update token in password_reset_tokens table
        $query = "INSERT INTO password_reset_tokens (token, id_user, email, expiry_timestamp) VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE token = VALUES(token), expiry_timestamp = VALUES(expiry_timestamp)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $token, $id_user, $email, $expiry_timestamp);
        $stmt->execute();
        
        // Clean up expired tokens
        $cleanup_query = "DELETE FROM password_reset_tokens WHERE expiry_timestamp < ?";
        $cleanup_stmt = $conn->prepare($cleanup_query);
        $cleanup_stmt->bind_param("i", time());
        $cleanup_stmt->execute();
        

        // Send email to the user with the password reset link
        $reset_link = "http://localhost/SIUKMSTMIK/page/forgot_password.php?token=" . $token;
        $to = 'fahroediinj@gmail.com';
        $subject = "Password Reset Link - SIUKM STMIK Komputama Majenang";
        $message = "Hello,\n\nYou have requested to reset your password. Click the link below to reset your password:\n\n{$reset_link}\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\nThe SIUKM Team";
        $headers = "From: fahroediinj@gmail.com"; // Replace with your email address

        // Use the mail() function to send the email
        if (mail($email, $subject, $message, $headers)) {
            // Email sent successfully
            // Redirect to a success page or show a success message
            header("Location: forgot_password_success.php");
            exit();
        } else {
            // Email sending failed
            echo "Failed to send email. Check your mail configuration.";
        }
    } else {
        // User not found or incorrect email, show an error message with JavaScript
        echo '<script type="text/javascript">';
        echo 'alert("Username tidak terdaftar atau email tidak benar, ulangi.");';
        echo '</script>';
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
    <title>Lupa Password - SIUKM STMIK Komputama Majenang</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/js/script.js">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
</head>
<style>
        .container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.card {
    opacity: 0.75;
  width: 400px;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 15px;
}

.btn-primary {
  background-color: #007bff;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}

.btn-primary:hover {
  background-color: #0069d9;
}
body {
            background-image: url('<?php echo $background_image_url; ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* Add any other styles you want for the body */
        }
    </style>
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
                    <a class="dropdown-item" href="wanacetta.php">Wanacetta</a>
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
    <div class="card">
        <h2>Lupa Password</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="id_user">Username:</label>
                <input type="text" class="form-control" id="id_user" name="id_user" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>
</div>
</body>
<?php if (isset($error_message)) : ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>
<footer>SIUKM @2023 | Visit our<a href="https://stmikkomputama.ac.id/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
  <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/>
</svg> Website</a> 
| Connect with us on <a href="https://www.facebook.com/stmikkomputama"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
  <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
</svg> Facebook</a></footer>
</html>