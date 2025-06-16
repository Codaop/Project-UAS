<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/profile.css">
</head>

<body>
    <?php
    session_start();
    include './connect.php';
    if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
    $param_user = $_SESSION['nrp'];
    $user_id = $_SESSION['user_id'];
    $sql_mahasiswa = "SELECT nrp, nama_lengkap, program_studi, jenis_kelamin, email, alamat, nomor_telepon, created_at FROM mahasiswa WHERE nrp = '$param_user'";
    $result = mysqli_query($conn, $sql_mahasiswa);
    if (mysqli_num_rows($result) === 1) {
        $profile = mysqli_fetch_assoc($result);
    } else {
        // Jika tidak ditemukan, coba cari di tabel dosen
        $sql_dosen = "SELECT nidn, nama_lengkap, program_studi, jenis_kelamin, email, alamat, nomor_telepon, created_at FROM dosen WHERE nidn = '$param_user'";
        $result = mysqli_query($conn, $sql_dosen);
        if (mysqli_num_rows($result) === 1) {
            $profile = mysqli_fetch_assoc($result);
        } else {
            $errorMessage = 'NRP/NIDN not found!';
        }
    }

    if (isset($profile)) {
        $tanggal = date('l, d F Y', strtotime($profile['created_at']));
    }

    $sql_profile = "SELECT path FROM profile_pic WHERE user_id = '$user_id'";
    $result_profile_2 = mysqli_query($conn, $sql_profile);
    $dbfoto = mysqli_fetch_assoc($result_profile_2);

    if ($dbfoto && !empty($dbfoto['path'])) {
        $foto_profile = $dbfoto['path'];
    } else {
        $foto_profile = "Asset/person-circle.svg";
    }

    // Mengupload foto profile
    if (isset($_POST['upload_profile'])) {
        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] === UPLOAD_ERR_NO_FILE) {
            $foto_profile = "Asset/person-circle.svg";
            echo "<script>alert('Tidak ada file yang diupload.');</script>";
        } else {
            $fileName = mysqli_real_escape_string($conn, basename($_FILES['userfile']['name']));
            $filePath = __DIR__ . "/Upload/";
            $savePath = mysqli_real_escape_string($conn, "./Upload/" . $fileName);

            // Memindahkan foto yang di upload
            move_uploaded_file($_FILES['userfile']['tmp_name'], $filePath . $fileName);

            $sql_check = "SELECT * FROM profile_pic WHERE user_id = '$user_id'";
            $result_check = mysqli_query($conn, $sql_check);

            if (mysqli_num_rows($result_check) > 0) {
                $sql_upload = "UPDATE profile_pic SET file_name = '$fileName', path = '$savePath' WHERE user_id = '$user_id'";
            } else {
                $sql_upload = "INSERT INTO profile_pic (user_id, file_name, path) VALUES ('$user_id','$fileName','$savePath')";
            }

            if ($result_profile = mysqli_query($conn, $sql_upload)) {
                echo "<script>alert('Berhasil mengupload foto profil.');</script>";
                header('Location: profile.php');
                exit();
            } else {
                echo "<script>alert('Gagal mengupload foto profil.');</script>";
            }
        }
    }
    ?>
    <div class="wrapper">
        <?php include './sidebar.php'; ?>
        <div class="main-profile">
            <div class="profile-simple-100">
                <img id="profile-pic" src="<?php echo $foto_profile; ?>" alt="Profie photo">
                <h2><?php echo $profile['nama_lengkap']; ?></h2>
                <p class="subtext-profile"><?php echo $profile['program_studi']; ?></p>
                <div class="info-icon">
                    <img class="profile-icon" src="Asset/envelope.svg" alt="Mail icon">
                    <span><?php echo $profile['email']; ?></span>
                </div>
                <div class="info-icon">
                    <img class="profile-icon" src="Asset/telephone.svg" alt="Phone icon">
                    <span><?php echo $profile['nomor_telepon']; ?></span>
                </div>
                <div class="info-icon">
                    <img class="profile-icon" src="Asset/person.svg" alt="Person icon">
                    <span><?php echo $profile['jenis_kelamin']; ?></span>
                </div>
                <hr>
                <div class="info-extend">
                    <p style="color: gray;">NRP</p>
                    <p><?php
                    if ($_SESSION['role'] === '2') {
                        echo $profile['nidn'];
                    } else {
                        echo $profile['nrp'];
                    }
                    ?></p>
                </div>
                <div class="info-extend">
                    <p style="color: gray;">Alamat</p>
                    <p style="padding-left: 50px;"><?php echo $profile['alamat']; ?></p>
                </div>
                <div class="info-extend">
                    <p style="color: gray;">Registered Date</p>
                    <p><?php echo $tanggal; ?></p>
                </div>
                <form method='POST' action='' enctype="multipart/form-data" style='display:inline;'>
                    <input type='hidden' name='user_id' value='<?php $_SESSION['user_id'] ?>'>
                    <input name="userfile" type="file" class="box" id="userfile">
                    <input name="upload" type="hidden" class="uploadBtn" id="upload" value="Upload">
                    <button type='submit' name="upload_profile">Upload photo</button>
                </form>
            </div>

            <!-- Fitur coming soon -->
            <!-- <?php if ($_SESSION['role'] === '1' || $_SESSION['role'] === '0'): ?>
            <div class="profile-lengkap">
                <div class="header-profile">
                    <h2>IPS</h2>
                    <h2>SKS</h2>
                    <h2>Mata Kuliah</h2>
                </div>
            </div>
        <?php endif; ?> -->
            <!-- End fitur coming soon -->
        </div>
    </div>
</body>

</html>