<?php
// Memasukkan file db_connect.php
require_once "db_connect.php";

// Fetch user data based on the provided ID
if (isset($_GET['id'])) {
    $id_user = $_GET['id'];
    $sql = "SELECT * FROM tab_user WHERE id_user = '$id_user'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            // If the user ID doesn't exist
            echo "Invalid user ID";
            exit();
        }
    } else {
        // If there was an error with the query
        echo "Error: " . $conn->error;
        exit();
    }
} else {
    // If the user ID is not provided
    echo "Invalid user ID";
    exit();
}


// Memeriksa apakah tombol update diklik
if (isset($_POST['update'])) {
    // Memeriksa apakah parameter id_user telah diberikan
    if (isset($_POST['id_user'])) {
        $id_user = $_POST['id_user'];
        $nama_depan = $_POST['nama_depan'];
        $nama_belakang = $_POST['nama_belakang'];
        $email = $_POST['email'];
        $no_hp = $_POST['no_hp'];
        $level = $_POST['level'];

        // Update user data in the database
        $sql = "UPDATE tab_user SET nama_depan = '$nama_depan', nama_belakang = '$nama_belakang', email = '$email', no_hp = '$no_hp', level = '$level' WHERE id_user = '$id_user'";
        $result = $conn->query($sql);

        if ($result) {
            // Redirect back to the user list after update
            header("Location: proses_user.php");
            exit();
        } else {
            // If an error occurs during the update
            echo "Error: " . $conn->error;
            exit();
        }
    } else {
        // If the user ID is not provided
        echo "Invalid user ID";
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Manager - SIUKM</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon-siukm.png">
</head>
<style>
    .card {
        width: 50%;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .btn {
        padding: 8px 12px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #0056b3;
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
</style>
<body>
<div class="sidebar">
    <h2>Manajemen Pengguna</h2>
    <a href="admin.php" class="btn btn-primary <?php if($active_page == 'dashboard') echo 'active'; ?>">Dashboard</a>
    <a href="beranda.php" class="btn btn-primary <?php if($active_page == 'beranda') echo 'active'; ?>">Beranda</a>
    <a href="proses_struktur.php" class="btn btn-primary <?php if($active_page == 'struktur') echo 'active'; ?>">Kepengurusan</a>
    <a href="proses_dau.php" class="btn btn-primary <?php if($active_page == 'data_anggota_ukm') echo 'active'; ?>">Data Anggota</a>
    <a href="proses_prestasi.php" class="btn btn-primary <?php if($active_page == 'prestasi') echo 'active'; ?>">Prestasi</a>
    <a href="proses_user.php" class="btn btn-primary <?php if($active_page == 'user_manager') echo 'active'; ?>">User Manager</a>
    <a href="proses_visimisi.php" class="btn btn-primary <?php if($active_page == 'visi_misi') echo 'active'; ?>">Data UKM</a>
    <a href="galeri.php" class="btn btn-primary <?php if($active_page == 'galeri') echo 'active'; ?>">Galeri</a>
    <a href="kegiatan.php" class="btn btn-primary <?php if($active_page == 'kegiatan') echo 'active'; ?>">Kegiatan</a>
    <a href="calon_anggota.php" class="btn btn-primary <?php if($active_page == 'calon_anggota') echo 'active'; ?>">Daftar Calon Anggota Baru</a>
</div>

<!-- Add a script to handle level dropdown updates -->
<script>
    // Function to handle level dropdown updates
    document.querySelectorAll('.level-dropdown').forEach(function (dropdown) {
        dropdown.addEventListener('change', function () {
            var userId = this.getAttribute('data-user-id');
            var newLevel = this.value;

            // Perform an AJAX request to update the user's level in the database
            // You can use fetch or jQuery.ajax to make the request

            // Example using fetch:
            fetch('update_user_level.php', {
                method: 'POST',
                body: JSON.stringify({ userId: userId, level: newLevel }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response if needed
                console.log(data);
            })
            .catch(error => {
                // Handle any errors if they occur
                console.error('Error:', error);
            });
        });
    });
</script>

<script>
    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus data ini?");
    }
</script>

<div class="content">
    <div class="card">
        <h2>Edit User</h2>
        <form method="POST" onsubmit="return validateForm();">
            <input type="hidden" name="id_user" value="<?php echo $row['id_user']; ?>">
            <div class="form-group">
                <label for="nama_depan">Nama Depan:</label>
                <input type="text" class="form-control" id="nama_depan" name="nama_depan" value="<?php echo $row['nama_depan']; ?>" required>
            </div>
            <div class="form-group">
                <label for="nama_belakang">Nama Belakang:</label>
                <input type="text" class="form-control" id="nama_belakang" name="nama_belakang" value="<?php echo $row['nama_belakang']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" required>
            </div>
            <div class="form-group">
            <label for="no_hp">No. HP:</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo $row['no_hp']; ?>" required pattern="[0-9]+" title="Please enter numeric characters only">
              </div>
            <div class="form-group">
                <label for="level">Level:</label>
                <select id="level" name="level" class="form-control">
                    <option value="3" <?php if ($row['level'] == '3') echo 'selected'; ?>>User</option>
                    <option value="2" <?php if ($row['level'] == '2') echo 'selected'; ?>>Kemahasiswaan</option>
                    <option value="1" <?php if ($row['level'] == '1') echo 'selected'; ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" name="update">Update</button>
            </div>
        </form>
    </div>
</div>
<script>
    // Function to allow only numeric input in the phone number field
    document.getElementById("no_hp").addEventListener("input", function (e) {
        var value = e.target.value;
        var numericValue = value.replace(/\D/g, ""); // Remove non-numeric characters
        e.target.value = numericValue;
    });
</script>

<script>
    // Function to handle level dropdown updates (move this block to the end of the file)
   // Function to handle level dropdown updates
   document.querySelectorAll('.level-dropdown').forEach(function (dropdown) {
        dropdown.addEventListener('change', function () {
            var userId = this.getAttribute('data-user-id');
            var newLevel = this.value;

            // Perform an AJAX request to update the user's level in the database
            // You can use fetch or jQuery.ajax to make the request

            // Example using fetch:
            fetch('update_user_level.php', {
                method: 'POST',
                body: JSON.stringify({ userId: userId, level: newLevel }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response if needed
                console.log(data);
            })
            .catch(error => {
                // Handle any errors if they occur
                console.error('Error:', error);
            });
        });
    });

    // Function to handle edit mode
    function enterEditMode(row) {
        // Get the data cells of the row
        const cells = row.getElementsByTagName('td');

        // Store the original values in data attributes
        for (let i = 0; i < cells.length - 1; i++) { // Ignore the last cell (actions cell)
            const originalValue = cells[i].textContent;
            cells[i].setAttribute('data-original-value', originalValue);
        }

        // Replace the content of the cells with input fields
        cells[0].innerHTML = '<input type="text" class="form-control" value="' + cells[0].textContent + '">';
        cells[1].innerHTML = '<input type="text" class="form-control" value="' + cells[1].textContent + '">';
        cells[2].innerHTML = '<input type="text" class="form-control" value="' + cells[2].textContent + '">';
        cells[3].innerHTML = '<input type="text" class="form-control" value="' + cells[3].textContent + '">';
        cells[4].innerHTML = '<input type="text" class="form-control" value="' + cells[4].textContent + '">';
        cells[5].innerHTML = '<select class="form-control">' +
                                '<option value="3" ' + (cells[5].textContent === 'User' ? 'selected' : '') + '>User</option>' +
                                '<option value="2" ' + (cells[5].textContent === 'Kemahasiswaan' ? 'selected' : '') + '>Kemahasiswaan</option>' +
                                '<option value="1" ' + (cells[5].textContent === 'Admin' ? 'selected' : '') + '>Admin</option>' +
                             '</select>';
        cells[6].innerHTML = '<button class="btn btn-primary btn-sm" onclick="updateUser(this)">Update</button> ' +
                             '<button class="btn btn-secondary btn-sm" onclick="cancelEdit(this)">Cancel</button>';
    }

    // Function to handle canceling edit mode
    function cancelEdit(row) {
        // Get the data cells of the row
        const cells = row.parentElement.getElementsByTagName('td');

        // Restore the original values from data attributes
        for (let i = 0; i < cells.length - 1; i++) { // Ignore the last cell (actions cell)
            const originalValue = cells[i].getAttribute('data-original-value');
            cells[i].textContent = originalValue;
        }

        // Remove the data attributes
        for (let i = 0; i < cells.length - 1; i++) { // Ignore the last cell (actions cell)
            cells[i].removeAttribute('data-original-value');
        }
    }

    // Function to handle updating the user data
    function updateUser(row) {
        // Get the data cells of the row
        const cells = row.parentElement.getElementsByTagName('td');

        // Get the updated values from the input fields
        const idUser = cells[0].querySelector('input').value;
        const namaDepan = cells[1].querySelector('input').value;
        const namaBelakang = cells[2].querySelector('input').value;
        const email = cells[3].querySelector('input').value;
        const noHp = cells[4].querySelector('input').value;
        const level = cells[5].querySelector('select').value;

        // Perform an AJAX request to update the user data in the database
        fetch('edit_user.php', {
            method: 'POST',
            body: JSON.stringify({ id_user: idUser, nama_depan: namaDepan, nama_belakang: namaBelakang, email: email, no_hp: noHp, level: level }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Handle the response if needed (e.g., display a success message)
            console.log(data);
            // Exit edit mode and update the row with the new data
            cancelEdit(row);
            cells[0].textContent = idUser;
            cells[1].textContent = namaDepan;
            cells[2].textContent = namaBelakang;
            cells[3].textContent = email;
            cells[4].textContent = noHp;
            cells[5].textContent = level === '3' ? 'User' : level === '2' ? 'Kemahasiswaan' : 'Admin';
        })
        .catch(error => {
            // Handle any errors if they occur
            console.error('Error:', error);
        });
    }
</script>

<script>
    function validateForm() {
        var password = document.getElementById("password").value;
        var konfirmasiPassword = document.getElementById("konfirmasi_password").value;

        if (password !== konfirmasiPassword) {
            alert("Password tidak cocok!");
            return false;
        }

        return true;
    }
</script>

<!-- Masukkan link JavaScript Anda di sini jika diperlukan -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>// Ambil elemen toggle button dan sidebar
const toggleBtn = document.querySelector('.toggle-btn');
const sidebar = document.querySelector('.sidebar');

// Tambahkan event listener untuk toggle button
toggleBtn.addEventListener('click', () => {
  // Toggle class 'collapsed' pada sidebar
  sidebar.classList.toggle('collapsed');
});

// Function to handle delete user
function deleteUser(userId) {
  // Prompt the user for confirmation
  var confirmDelete = confirm("Are you sure you want to delete this user?");

  // If the user confirms the deletion
  if (confirmDelete) {
    // Delete the user from the user list (assuming you have an array or object to store the user list)
    // Example code:
    // userList.splice(userId, 1);

    // Refresh the user list table
    populateUserList();
  }
}

// Add event listener to delete buttons
var deleteButtons = document.getElementsByClassName('deleteBtn');
for (var i = 0; i < deleteButtons.length; i++) {
  deleteButtons[i].addEventListener('click', function() {
    var userId = this.getAttribute('data-id');
    deleteUser(userId);
  });
}

</script>
</body>
</html>