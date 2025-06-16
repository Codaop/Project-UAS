<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login MISpens</title>
    <link rel="stylesheet" href="CSS/login.css">
</head>

<?php session_start();
$errorMessage = '';
// Connect db
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'data_mhs';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)
    or die('Error connecting to mysql.' . mysqli_connect_error());
// Pengecekan submit form
if (isset($_POST['user_nrp_nidn']) && isset($_POST['user_pass'])) {
    $nrp_nidn = $_POST['user_nrp_nidn'];
    $pass = $_POST['user_pass'];
    $sql_mahasiswa = "SELECT mahasiswa_id, role, nrp, pass_user FROM mahasiswa WHERE nrp = '$nrp_nidn'";
    $result = mysqli_query($conn, $sql_mahasiswa);
    // Pengecekan apakah datanya ada dalam database
    if (mysqli_num_rows($result) === 1) {
        $user_info = mysqli_fetch_assoc($result);
    } else {
        // Jika tidak ditemukan, coba cari di tabel dosen
        $sql_dosen = "SELECT dosen_id, role, nidn, pass_user FROM dosen WHERE nidn = '$nrp_nidn'";
        $result = mysqli_query($conn, $sql_dosen);
        if (mysqli_num_rows($result) === 1) {
            $user_info = mysqli_fetch_assoc($result);
        } else {
            $errorMessage = 'NRP/NIDN not found!';
        }
    }

    if (isset($user_info)) {
        if (
            (($user_info['role'] === '0' && $pass == $user_info['pass_user']) || ($user_info['role'] === '1' &&
                password_verify($pass, $user_info['pass_user'])))
        ) {
            $_SESSION['user_id'] = $user_info['mahasiswa_id'];
            $_SESSION['role'] = $user_info['role'];
            $_SESSION['nrp'] = $user_info['nrp'];
            $_SESSION['user_is_logged_in'] = true;
            header('Location: main_page.php');
            exit;
        }
        if (
            ($user_info['role'] === '2') &&
            password_verify($pass, $user_info['pass_user'])
        ) {
            $_SESSION['user_id'] = $user_info['dosen_id'];
            $_SESSION['role'] = $user_info['role'];
            $_SESSION['nrp'] = $user_info['nidn'];
            $_SESSION['user_is_logged_in'] = true;
            header('Location: main_page.php');
            exit;
        } else {
            $errorMessage = 'Your password are wrong!';
        }
    } else {
        $errorMessage = 'Your NRP or password are wrong!';
    }
}
// Close connection db
mysqli_close($conn);
?>

<body>
    <div class="container">
        <?php if ($errorMessage != '') { ?>
            <p align="center"><strong>
                    <font color="#000000"><?php echo $errorMessage; ?></font>
                </strong></p> <?php } ?>
        <form action="" method="POST" id="form_utama">
            <h2>Login MISpens</h2>
            <p>
                <label for="userNrpNidn">NRP/NIDN</label><br>
                <input type="text" name="user_nrp_nidn" id="userNrpNidn" autocomplete="off" maxlength="50">
            </p>
            <p>
                <label for="userPass">Password</label><br>
                <input type="password" name="user_pass" id="userPass" autocomplete="new-password">
            </p>
            <p class="hide-pass">
                <input class="checkPass" type="checkbox" onclick="showHide()">Tampilkan Password
            </p>
            <p>
                <button type="submit">Submit</button>
            </p>
        </form>
        <a href="register.php">
            <button>Register</button>
        </a>
    </div>
    <script type="text/javascript">
        function showHide() {
            var inputan = document.getElementById("userPass");
            if (inputan.type === "password") {
                inputan.type = "text";
            } else {
                inputan.type = "password";
            }
        }
    </script>
</body>

</html>