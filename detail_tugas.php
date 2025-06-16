<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/detail_tugas.css">
</head>

<body>
    <?php
    session_start();
    include './connect.php';
    if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }

    // Menyimpan id_tugas yang dipilih dari halaman tugas
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_tugas'])) {
        $_SESSION['id_tugas'] = $_POST['id_tugas'];
        header("Location: detail_tugas.php");
        exit();
    }

    if (isset($_SESSION['id_tugas'])) {
        // Query tugas
        $id_tugas = $_SESSION['id_tugas'];
        $sql_tugas = "SELECT * FROM tugas WHERE tugas_id = '$id_tugas'";
        $result = mysqli_query($conn, $sql_tugas);
        $detail_tugas = mysqli_fetch_assoc($result);
        if (isset($detail_tugas['deadline'])) {
            $deadline = date('l, d F Y. H:i', strtotime($detail_tugas['deadline']));
        }

        // Query submission tugas
        $param_mhs = $_SESSION['user_id'];
        $sql_submission = "SELECT tanggal_submit, nilai, status_pengumpulan FROM submission_tugas WHERE mahasiswa_id = '$param_mhs' AND tugas_id = '$id_tugas'";
        $result2 = mysqli_query($conn, $sql_submission);

        if (mysqli_num_rows($result2) > 0) {
            $detail_submit = mysqli_fetch_assoc($result2);
            $tgl_submit = date('l, d F Y. H:i', strtotime($detail_submit['tanggal_submit']));
            $nilai = $detail_submit['nilai'];

            // Konversi status tugas ke string
            if (strtotime($detail_submit['tanggal_submit']) <= strtotime($detail_tugas['deadline'])) {
                $detail_submit['status_pengumpulan'] = 1;
            } else {
                $detail_submit['status_pengumpulan'] = 2;
            }

            if ($detail_submit['status_pengumpulan'] == 1) {
                $status_tgs = "Sudah mengumpulkan";
            } else if ($detail_submit['status_pengumpulan'] == 2) {
                $status_tgs = "Mengumpulkan terlambat";
            }
        } else {
            $status_tgs = "Belum mengumpulkan";
            $tgl_submit = "";
            $nilai = "";
        }

        if ($_SESSION['role'] == 2) {
            if ($detail_tugas['status_tugas'] == 1) {
                $status_tgs = "Pengumpulan ditutup";
            } else {
                $status_tgs = "Pengumpulan dibuka";
            }
        }

        // Ketika mahasiswa melakukan upload file
        if (isset($_POST['upload'])) {
            $mhs_id = $_SESSION['user_id'];
            // Cek apakah tugas sudah dinilai
            $cek_penilaian = "SELECT * FROM submission_tugas WHERE mahasiswa_id = '$mhs_id' AND tugas_id = '$id_tugas' AND nilai IS NOT NULL";
            $result_cek = mysqli_query($conn, $cek_penilaian);

            if (mysqli_num_rows($result_cek) > 0) {
                echo "<script>
                alert('Tugas Anda sudah dinilai dan tidak dapat melakukan edit tugas.');
                window.location.href = window.location.href;
                </script>";
                exit();
            }
            // Pengecekan submission sudah dilakukan atau belum (belum dinilai)
            $cek_upload = "SELECT * FROM submission_tugas WHERE mahasiswa_id = '$mhs_id' AND tugas_id = '$id_tugas'";
            $result_upload = mysqli_query($conn, $cek_upload);
            $is_edit = mysqli_num_rows($result_upload) > 0; // Jika di tabel sudah ada maka dinyatakan update baru
    
            $judul_tugas = $detail_tugas['judul_tugas'];
            $catatan = isset($_POST['desc_file']) ? $_POST['desc_file'] : null;

            if (isset($_FILES['userfile']) && $_FILES['userfile']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK) {
                    $fileName = basename($_FILES['userfile']['name']);
                    $fileSizeStr = $_FILES['userfile']['size'];
                    $fileType = $_FILES['userfile']['type'];
                    $tmpName = $_FILES['userfile']['tmp_name'];

                    $tmpName = $_FILES['userfile']['tmp_name'];
                    $fileName = basename($_FILES['userfile']['name']);

                    $uploadDir = 'C:/Users/MSA-DESKTOP/Documents/MISpens/';
                    $filePath = $uploadDir . $fileName;

                    $fileContent = mysqli_real_escape_string($conn, file_get_contents($tmpName));
                    move_uploaded_file($tmpName, $filePath);

                    //Memasukkan data ke dalam database
                    if ($is_edit) {
                        // UPDATE
                        if ($catatan !== null) {
                            $sql_file = "UPDATE submission_tugas 
                                SET catatan = '$catatan', status_pengumpulan = 1,
                                    file_name = '$uniqueName', size = '$fileSizeStr',
                                    type = '$fileType', content = '$fileContent', path = '$filePath', tanggal_submit = NOW()
                                WHERE mahasiswa_id = '$mhs_id' AND tugas_id = '$id_tugas'";
                        } else {
                            $sql_file = "UPDATE submission_tugas 
                                SET status_pengumpulan = 1,
                                    file_name = '$uniqueName', size = '$fileSizeStr',
                                    type = '$fileType', content = '$fileContent', path = '$filePath', tanggal_submit = NOW()
                                WHERE mahasiswa_id = '$mhs_id' AND tugas_id = '$id_tugas'";
                        }
                    } else {
                        // INSERT
                        if ($catatan !== null) {
                            $sql_file = "INSERT INTO submission_tugas 
                                (judul_tugas, mahasiswa_id, tugas_id, catatan, status_pengumpulan, 
                                file_name, size, type, content, path) 
                                VALUES ('$judul_tugas','$mhs_id','$id_tugas','$catatan', 1,
                                        '$uniqueName','$fileSizeStr','$fileType','$fileContent','$filePath')";
                        } else {
                            $sql_file = "INSERT INTO submission_tugas 
                                (judul_tugas, mahasiswa_id, tugas_id, status_pengumpulan, 
                                file_name, size, type, content, path) 
                                VALUES ('$judul_tugas','$mhs_id','$id_tugas', 1,
                                        '$uniqueName','$fileSizeStr','$fileType','$fileContent','$filePath')";
                        }
                    }

                    if (mysqli_query($conn, $sql_file)) {
                        echo "<script>
                            alert('Berhasil mengupload tugas.');
                            window.location.href = window.location.href;
                            </script>";
                        exit();
                    } else {
                        echo "<script>alert('Gagal mengupload tugas.');</script>" . mysqli_error($conn);
                    }
                }
            } else {
                echo "<script>alert('Wajib mengunggah dokumen.');</script>";
            }
        }

        // Trigger membuka modal edit tugas
        if (isset($_POST['buka_modal'])) {
            $_SESSION['show_modal'] = true;
        }

        if (isset($_POST['close_modal'])) {
            unset($_SESSION['show_modal']);
        }

        if (isset($_POST['editTugas'])) {
            $edit_judul = $_POST['edit_judul'];
            $edit_desc = $_POST['edit_desc'];
            $edit_deadline = $_POST['edit_deadline'];

            if (isset($_FILES['userfile']) && $_FILES['userfile']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK) {
                    $fileName = basename($_FILES['userfile']['name']);
                    $fileSizeStr = $_FILES['userfile']['size'];
                    $fileType = $_FILES['userfile']['type'];
                    $tmpName = $_FILES['userfile']['tmp_name'];

                    $tmpName = $_FILES['userfile']['tmp_name'];
                    $fileName = basename($_FILES['userfile']['name']);

                    $uploadDir = 'C:/Users/MSA-DESKTOP/Documents/MISpens/';
                    $filePath = $uploadDir . $fileName;

                    $fileContent = mysqli_real_escape_string($conn, file_get_contents($tmpName));
                    move_uploaded_file($tmpName, $filePath);

                    // Mengupdate data ke dalam tabel submission
                    $edit_sql = "UPDATE tugas SET judul_tugas = COALESCE(NULLIF('$edit_judul', ''), judul_tugas), deskripsi = COALESCE(NULLIF('$edit_desc', ''), deskripsi), deadline = COALESCE(NULLIF('$edit_deadline', ''), deadline), file_name = COALESCE(NULLIF('$fileName', ''), file_name), size = COALESCE(NULLIF('$fileSizeStr', ''), size), type = COALESCE(NULLIF('$fileType', ''), type), content = COALESCE(NULLIF('$fileContent', ''), content), path = COALESCE(NULLIF('$filePath', ''), path) WHERE tugas_id = '$id_tugas'";

                    if (mysqli_query($conn, $edit_sql)) {
                        echo "<script>alert('Tugas berhasil di edit.');</script>";
                        unset($_SESSION['show_modal']);
                    } else {
                        echo "<script>alert('Gagal mengedit tugas.');</script>" . mysqli_error($conn);
                    }
                }
            } else {
                // Mengupdate data ke dalam tabel submission
                $edit_sql = "UPDATE tugas SET judul_tugas = COALESCE(NULLIF('$edit_judul', ''), judul_tugas), deskripsi = COALESCE(NULLIF('$edit_desc', ''), deskripsi), deadline = COALESCE(NULLIF('$edit_deadline', ''), deadline) WHERE tugas_id = '$id_tugas'";

                if (mysqli_query($conn, $edit_sql)) {
                    echo "<script>alert('Tugas berhasil di edit.');</script>";
                } else {
                    echo "<script>alert('Gagal mengedit tugas.');</script>" . mysqli_error($conn);
                }
            }
        }
        
        // Menghapus tugas dari sisi dosen
        if (isset($_POST['hapus_tugas'])) {
            $id_tugas = $_POST['detail_tugas'];

            // Hapus submission yang ada dahulu (dari mhs)
            $hapus_submission = mysqli_query($conn, "DELETE FROM submission_tugas WHERE tugas_id = '$id_tugas'");

            if ($hapus_submission) {
                // Baru hapus tugas utamanya
                $hapus_tugas = mysqli_query($conn, "DELETE FROM tugas WHERE tugas_id = '$id_tugas'");
                if ($hapus_tugas) {
                    echo "<script>alert('Tugas berhasil dihapus.'); window.location.href='tugas.php';</script>";
                } else {
                    echo "<script>alert('Gagal menghapus tugas.');</script>";
                }
            } else {
                echo "<script>alert('Gagal menghapus tugas.');</script>";
            }
        }
    }
    ?>

    <div class="header-tugas">
        <a href="tugas.php">
            <img src="Asset/chevron-left.svg" alt="Back button">
        </a>
        <p>Detail Tugas</p>
    </div>

    <div class="wrapper">
        <div class="section-tugas">
            <h1><?php echo $detail_tugas['judul_tugas'] ?></h1>
            <p style="font-size: medium; color: grey; margin-bottom: 25px;"><?php echo $detail_tugas['mata_kuliah'] ?>
            </p>
            <p>Deskripsi:</p>
            <p class="desc-tugas"><?php echo $detail_tugas['deskripsi'] ?></p>
            <p>Batas Waktu:</p>
            <p class="desc-tugas"><?php echo $deadline ?></p>
            <?php if (!empty($detail_tugas['file_name'])): ?>
                <p>Lampiran:</p>
                <div class="container-lampiran">
                    <a href="<?php echo 'data:' . $detail_tugas['type'] . ';base64,' . base64_encode($detail_tugas['content']); ?>"
                        <?php echo "download='" . $detail_tugas['file_name'] . "'" ?>
                        style="text-decoration: none; color: #333;" class="download-lampiran">
                        <img src="Asset/file-earmark-fill.svg" alt="file-icon" style="width: 25px;">
                        <p><?php echo $detail_tugas['file_name'] ?></p>
                        <img src="Asset/download.svg" alt="Download" style="width: 24px;">
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-upload">
            <?php
            // Tampilan detail tugas mahasiswa
            if ($_SESSION['role'] == 1 || $_SESSION['role'] == 0) {
                echo '<div class="main-upload">
                        <h2>Detail Tugas</h2>
                        <p>Status: ' . $status_tgs . '</p>
                        <p>Tanggal Pengumpulan: ' . $tgl_submit . '</p>
                        <p style="margin-bottom: 30px;">Nilai: ' . $nilai . '</p>
                    </div>
                    <div class="upload-button">
                        <p>Upload File:</p>
                        <form action="" method="POST" id="form_upload" enctype="multipart/form-data" name="uploadform">
                            <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
                            <input name="userfile" type="file" class="box" id="userfile">
                            <p>Catatan</p>
                            <textarea type="text" name="desc_file" id="descFile"></textarea>
                            <input name="upload" type="submit" class="uploadBtn" id="uploadTugas" value="Upload">
                        </form>
                    </div>';
            }
            // Tampilan detail tugas dosen
            if ($_SESSION['role'] == 2) {
                // Menghitung jumlah pengumpul tugas
                $sql_jumlah = "SELECT COUNT(*) AS total_pengumpulan FROM submission_tugas WHERE tugas_id = '$id_tugas'";
                $result_jumlah = mysqli_query($conn, $sql_jumlah);
                $data_jumlah = mysqli_fetch_assoc($result_jumlah);
                $pengumpulan = $data_jumlah['total_pengumpulan'];
                // Menghitung jumlah penilaian yang dilakukan dosen
                $sql_jml_nilai = "SELECT COUNT(*) AS total_penilaian FROM submission_tugas WHERE tugas_id = '$id_tugas' AND nilai IS NOT NULL";
                $result_nilai = mysqli_query($conn, $sql_jml_nilai);
                $data_penilaian = mysqli_fetch_assoc($result_nilai);
                $penilaian = $data_penilaian['total_penilaian'];

                echo '<div class="main-upload">
                <h2>Detail Tugas</h2>
                <p>Status: ' . $status_tgs . '</p>
                <p>Jumlah Pengumpulan: ' . $pengumpulan . '</p>
                <p>Jumlah Penilaian: ' . $penilaian . '</p>
                </div>
                <div class="detailBtn-1">
                    <form method="POST" action="submission_mhs.php">
                        <input type="hidden" name="detail_tugas" value="' . $id_tugas . '">
                        <button type="submit" style="background-color: #e3ddc5;">Beri Nilai</button>
                    </form>
                    <div class="detailBtn-2">
                        <form method="POST" action="">
                            <input type="hidden" name="detail_tugas" value="' . $id_tugas . '">
                            <button type="submit" name="buka_modal" id="detailBtn" style="background-color: #e3ddc5;">Edit tugas</button>
                        </form>
                        <form method="POST" action="">
                            <input type="hidden" name="detail_tugas" value="' . $id_tugas . '">
                            <button type="submit" name="hapus_tugas" id="detailBtn" style="background-color: #D5451B;">Tutup tugas</button>
                        </form>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>

    <!--Start modal edit tugas-->
    <?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']): ?>
        <div class="modal-outer" id="modal">
            <!-- Modal content -->
            <div class="modal-edit" id="modalEdit">
                <form method="POST" action="">
                    <button id="closeModal" name="close_modal" type="submit" style="background-color: whitesmoke;">
                        <img src="Asset/chevron-left.svg" alt="Icon tambah tugas">
                    </button>
                </form>
                <form action="" method="POST" id="form_edit_tugas">
                    <h2>Form Edit Tugas</h2>
                    <p>
                        <label for="edit_judul">Judul Tugas</label><br>
                        <input type="text" name="edit_judul" id="editJudul" autocomplete="off">
                    </p>
                    <p>
                        <label for="edit_deskripsi">Deskripsi</label><br>
                        <textarea type="text" name="edit_desc" id="editDesc"></textarea>
                    </p>
                    <p>
                        <label for="edit_deadline">Deadline</label><br>
                        <input type="datetime-local" name="edit_deadline" id="editDeadline">
                    </p>
                    <p>
                        <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
                        <label for="userfile" style="padding-left: 75px; margin-right: 5px;">Upload File:</label>
                        <input name="userfile" type="file" class="box" id="userfile" style="">
                    </p>
                    <input type="hidden" name="tugas_id" value="<?php echo $_SESSION['id_tugas']; ?>">
                    <p>
                        <input name="editTugas" type="submit" class="uploadBtn" id="uploadEdit">
                    </p>
                </form>
            </div>
        </div>
    <?php endif; ?>
    <!--End modal edit tugas-->
</body>

</html>