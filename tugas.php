<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/tugas.css">
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
    $role = $_SESSION['role'];

    // Input tugas baru ke dalam database
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $judul_tugas = $_POST['judul_tugas'];
        $desc_tugas = $_POST['desc_tugas'];
        $deadline = $_POST['deadline'];
        $mata_kuliah = $_POST['opsiMatkul'];
        $kelas = $_POST['opsiKelas'];

        if (isset($_POST['upload'])) {
            $fileName = basename($_FILES['userfile']['name']);
            $fileSizeStr = $_FILES['userfile']['size'];
            $fileType = $_FILES['userfile']['type'];
            $tmpName = $_FILES['userfile']['tmp_name'];

            //Pengecekan apakah ada lampiran file
            if (isset($_FILES['userfile']['name']) && $_FILES['userfile']['error'] == UPLOAD_ERR_OK && $_FILES['userfile']['size'] > 0) {

                $tmpName = $_FILES['userfile']['tmp_name'];
                $fileName = basename($_FILES['userfile']['name']);

                $uploadDir = 'C:/Users/MSA-DESKTOP/Documents/MISpens/';
                $filePath = $uploadDir . $fileName;

                $fileContent = mysqli_real_escape_string($conn, file_get_contents($tmpName));
                move_uploaded_file($tmpName, $filePath);

                //Memasukkan data ke dalam database
                $sql_matkul_id = "SELECT matkul_id FROM matkul WHERE nama_matkul = '$mata_kuliah'";
                $result_matkul = mysqli_query($conn, $sql_matkul_id);
                $hasil_matkul = mysqli_fetch_assoc($result_matkul);
                $matkul_id = $hasil_matkul['matkul_id'];

                $sql_file = "INSERT INTO tugas (matkul_id, judul_tugas, dosen_id, deskripsi, deadline, kelas, mata_kuliah, file_name, size, type, content, path) VALUES ('$matkul_id','$judul_tugas','$user_id','$desc_tugas','$deadline','$kelas','$mata_kuliah','$fileName','$fileSizeStr','$fileType','$fileContent','$filePath')";

                if (mysqli_query($conn, $sql_file)) {
                    echo "<script>alert('Tugas berhasil ditambahkan.');</script>";
                } else {
                    echo "<script>alert('Gagal menambahkan tugas.');</script>" . mysqli_error($conn);
                }
            } else {
                //Memasukkan data matkul_id ke dalam database
                $sql_matkul_id = "SELECT matkul_id FROM matkul WHERE nama_matkul = '$mata_kuliah'";
                $result_matkul = mysqli_query($conn, $sql_matkul_id);
                $hasil_matkul = mysqli_fetch_assoc($result_matkul);
                $matkul_id = $hasil_matkul['matkul_id'];

                //Memasukkan data ke dalam database
                $sql_file = "INSERT INTO tugas (matkul_id, judul_tugas, dosen_id, deskripsi, deadline, kelas, mata_kuliah) VALUES ('$matkul_id','$judul_tugas','$user_id','$desc_tugas','$deadline','$kelas','$mata_kuliah')";

                if (mysqli_query($conn, $sql_file)) {
                    echo "<script>alert('Tugas berhasil ditambahkan.');</script>";
                } else {
                    echo "<script>alert('Gagal menambahkan tugas.');</script>" . mysqli_error($conn);
                }
            }
        }
    }
    ?>
    <div class="wrapper">
        <?php include './sidebar.php'; ?>
        <div class="main-tugas">
            <h1>Tugas Mahasiswa</h1>
            <div class="section-tugas">
                <?php
                //Pengkondisian untuk card tugas
                if ($_SESSION['role'] == 1 || $_SESSION['role'] == 0) { //Untuk role admin dan mhs
                    $sql_mahasiswa = "SELECT kelas FROM mahasiswa WHERE nrp = '$param_user'";
                    $kelasMhs = mysqli_query($conn, $sql_mahasiswa);
                    $array_kelas = mysqli_fetch_assoc($kelasMhs);
                    $param_kelas = $array_kelas['kelas'];

                    $sql_tugas_mhs = "SELECT * FROM tugas WHERE kelas = '$param_kelas'";
                    $result = mysqli_query($conn, $sql_tugas_mhs);

                    if (mysqli_num_rows($result) > 0) {
                        while ($tugas = mysqli_fetch_assoc($result)) {
                            $id_tugas = $tugas['tugas_id'];

                            $sql_submit_mhs = "SELECT status_pengumpulan, tanggal_submit FROM submission_tugas WHERE mahasiswa_id = '$user_id' AND tugas_id = '$id_tugas'";
                            $result2 = mysqli_query($conn, $sql_submit_mhs);
                            $submit = mysqli_fetch_assoc($result2);

                            if (mysqli_num_rows($result2) > 0) {
                                if (strtotime($submit['tanggal_submit']) <= strtotime($tugas['deadline'])) {
                                    $submit['status_pengumpulan'] = 1;
                                } else {
                                    $submit['status_pengumpulan'] = 2;
                                }

                                if ($submit['status_pengumpulan'] == 1) {
                                    $status_tgs = "Sudah mengumpulkan";
                                } else if ($submit['status_pengumpulan'] == 2) {
                                    $status_tgs = "Mengumpulkan terlambat";
                                }
                            } else {
                                $status_tgs = "Belum mengumpulkan";
                            }

                            echo '<div class="card-tugas">
                                    <h4>' . $tugas['judul_tugas'] . '</h4>
                                    <p id="desc-tugas">' . $tugas['deskripsi'] . '</p>
                                    <p>Status: ' . $status_tgs . '</p>
                                    <form method="POST" action="detail_tugas.php" class="detail-tugas">
                                        <input type="hidden" name="id_tugas" value="' . $tugas["tugas_id"] . '">
                                        <button class="detailBtn" type="submit">Detail
                                            <img src="Asset/arrow-right.svg" alt="">
                                        </button>
                                    </form>
                                </div>';
                        }
                    }
                }
                if ($_SESSION['role'] == 2) { //Untuk role dosen
                    $sql_tugas_dosen = "SELECT * FROM tugas WHERE dosen_id = '$user_id'";
                    $result = mysqli_query($conn, $sql_tugas_dosen);
                    if (mysqli_num_rows($result) > 0) {
                        $tugas = mysqli_fetch_assoc($result);
                        if ($tugas['status_tugas'] == 1) {
                            $status_tgs = "Pengumpulan ditutup";
                        } else {
                            $status_tgs = "Pengumpulan dibuka";
                        }
                        while ($tugas) {
                            echo '<div class="card-tugas">
                                    <div class="">
                                        <h4>' . $tugas['judul_tugas'] . '</h4>
                                        <p id="desc-tugas">' . $tugas['deskripsi'] . '</p>
                                        <p>Status: ' . $status_tgs . '</p>
                                    </div>
                                    <form method="POST" action="detail_tugas.php" class="detail-tugas">
                                        <input type="hidden" name="id_tugas" value="' . $tugas["tugas_id"] . '">
                                        <button class="detailBtn" type="submit">Detail
                                            <img src="Asset/arrow-right.svg" alt="">
                                        </button>
                                    </form>
                                </div>';
                            $tugas = mysqli_fetch_assoc($result);
                        }
                    }
                }
                ?>
            </div>
            <?php
            if ($role == 2 || $role == 0) {
                echo '<div class="footer-tugas">
                <button class="addBtn" id="tambahTugas">
                    <img src="Asset/plus-square.svg" alt="Icon tambah tugas" style="width: 35px;">
                </button>
            </div>';
            }
            ?>
            <!--Start modal-->
            <div class="modal-outer" id="modal">
                <!-- Modal content -->
                <div class="modal-task" id="modalTask">
                    <button class="closeBtn" id="closeModal">
                        <img src="Asset/chevron-left.svg" alt="Icon tambah tugas">
                    </button>
                    <form action="" method="POST" id="form_input_tugas" enctype="multipart/form-data" name="uploadform">
                        <h2>Form Input Tugas</h2>
                        <p>
                            <label for="judul_tugas">Judul Tugas</label><br>
                            <input type="text" name="judul_tugas" id="judulTugas" required autocomplete="off">
                        </p>
                        <p>
                            <label for="deskripsi_tugas">Deskripsi</label><br>
                            <textarea type="text" name="desc_tugas" id="descTugas"></textarea>
                        </p>
                        <p>
                            <label for="deadline">Deadline</label><br>
                            <input type="datetime-local" name="deadline" id="deadlineTugas" required>
                        </p>
                        <p>
                            <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
                            <label for="userfile" style="padding-left: 75px; margin-right: 5px;">Upload File:</label>
                            <input name="userfile" type="file" class="box" id="userfile" style="">
                        </p>
                        <p>
                            <label for="matkul">Pilih Mata Kuliah</label><br>
                            <select name="opsiMatkul" id="matkul" required>
                                <?php
                                $sql_matkul = "SELECT * FROM matkul";
                                $result1 = mysqli_query($conn, $sql_matkul);
                                if (mysqli_num_rows($result1) > 0) {
                                    while ($matkul = mysqli_fetch_assoc($result1)) {
                                        echo '<option value="' . $matkul['nama_matkul'] . '">' . $matkul['nama_matkul'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </p>
                        <p>
                            <label for="kelas">Pilih Kelas</label><br>
                            <select name="opsiKelas" id="kelas" required>
                                <option value="A">Kelas A</option>
                                <option value="B">Kelas B</option>
                                <option value="C">Kelas C</option>
                                <option value="D">Kelas D</option>
                            </select>
                        </p>
                        <p>
                            <input name="upload" type="submit" class="uploadBtn" id="upload">
                        </p>
                    </form>
                </div>
            </div>
            <!--End modal-->
        </div>
    </div>
    <script>
        var modal = document.getElementById("modal");
        var btn = document.getElementById("tambahTugas");
        var close = document.getElementsByClassName("closeBtn")[0];

        btn.onclick = function () {
            modal.style.display = "block";
        }

        close.onclick = function () {
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>