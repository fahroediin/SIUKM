<?php
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["token"])) {
    $token = $_GET["token"];

    // Validate token and retrieve user information
    $query = "SELECT id_user, email FROM password_reset_tokens WHERE token = ? AND expiry_timestamp > ?";
    $expiry_timestamp = time();
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $token, $expiry_timestamp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $id_user = $row["id_user"];
        $email = $row["email"];

        // Display reset password form
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reset Password - SIUKM STMIK Komputama Majenang</title>
            <!-- Include your CSS and JS files here -->
        </head>
        <body>
            <h2>Reset Password</h2>
            <form action="process_reset_password.php" method="POST">
                <input type="hidden" name="id_user" value="<?php echo $id_user; ?>">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <label for="password">New Password:</label>
                <input type="password" name="password" required>
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" required>
                <button type="submit">Reset Password</button>
            </form>
        </body>
        </html>
        <?php
    } else {
        echo "Invalid token or token has expired.";
    }
} else {
    echo "Invalid request.";
}
?>
