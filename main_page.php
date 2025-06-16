<?php session_start();
include './connect.php';
if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISpens</title>
    <link rel="stylesheet" href="CSS/main_page.css">

<body>
    <div class="wrapper">
        <?php include './sidebar.php' ?>
        <div class="main-content">
            <div class="container">
                <h1>Dashboard Data Mahasiswa PENS</h1>
                <table>
                    <tr>
                        <td>No</td>
                        <td>NRP</td>
                        <td>Nama Mahasiswa</td>
                        <td>Jenis Kelamin</td>
                        <td>Program Studi</td>
                        <td>Email Student</td>
                        <td>Alamat</td>
                        <td>Nomor Telepon</td>
                        <td>Asal Sekolah</td>
                        <td>Kelas</td>
                    </tr>
                    <?php
                    $showBtn_updtdel = $_SESSION['role'];
                    $showInput = $_SESSION['role'];
                    $number = 1;
                    $sql = "SELECT * FROM mahasiswa WHERE role = 1";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $number . "</td>";
                            echo "<td>" . $row["nrp"] . "</td>";
                            echo "<td>" . $row["nama_lengkap"] . "</td>";
                            echo "<td>" . $row["jenis_kelamin"] . "</td>";
                            echo "<td>" . $row["program_studi"] . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>" . $row["alamat"] . "</td>";
                            echo "<td>" . $row["nomor_telepon"] . "</td>";
                            echo "<td>" . $row["asal_sekolah"] . "</td>";
                            echo "<td>" . $row["kelas"] . "</td>";
                            if ($showBtn_updtdel == 0) {
                                echo "<td>";
                                echo "<form method='POST' action='update_form.php' style='display:inline;'>
                                <input type='hidden' name='mahasiswa_id' value='" . $row['mahasiswa_id'] . "'>
                                <button type='submit'>Update</button>
                                </form>";
                                echo "<form method='POST' action='delete_data.php' style='display:inline;'>
                                <input type='hidden' name='mahasiswa_id' value='" . $row['mahasiswa_id'] . "'>
                                <button type='submit'>Delete</button>
                                </form>";
                                echo "</td>";
                            }
                            $number++;
                        }
                    }
                    if ($showInput == 0) {
                        echo "<a href='input_form.php'>
                        <button>Tambah Data</button>
                    </a>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>