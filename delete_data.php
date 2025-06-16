<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISpens</title>
    <link rel="stylesheet" href="CSS/delete_data.css">
</head>

<body>
    <div class="container">
        <?php
        session_start();
        include "./connect.php";
        if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
            header('Location: login.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $param_delete = $_POST['mahasiswa_id'];
            $check_isi = "SELECT * FROM mahasiswa WHERE mahasiswa_id = '$param_delete'";
            $result = mysqli_query($conn, $check_isi);
            if (mysqli_num_rows($result) > 0) {
                $sql = "DELETE FROM mahasiswa WHERE mahasiswa_id = '$param_delete'";
                if (mysqli_query($conn, $sql)) {
                    echo "<script>alert('Data terhapus.')
                    window.location.href='main_page.php';</script>";
                } else {
                    echo "<script>alert('Gagal menghapus data.')
                    window.location.href='main_page.php';</script>" . mysqli_error($conn);
                }
            } else {
                echo "<script>alert('Data tidak ditemukan! Tidak ada yang dihapus.')
                window.location.href='main_page.php';</script>";
            }
        }
        mysqli_close($conn);
        ?>
    </div>
</body>

</html>