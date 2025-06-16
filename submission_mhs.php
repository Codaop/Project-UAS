<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MISpens</title>
    <link rel="stylesheet" href="CSS/submission.css">

<body>
    <?php
    session_start();
    include './connect.php';
    if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
    $id_tugas = $_SESSION['id_tugas'];

    // Trigger membuka modal isi nilai tugas
    if (isset($_POST['buka_modal'])) {
        $_SESSION['submit_id'] = $_POST['submit_id'];
        $_SESSION['show_modal'] = true;
    }

    if (isset($_POST['close_modal'])) {
        unset($_SESSION['submit_id']);
        unset($_SESSION['show_modal']);
    }

    if (isset($_POST['upload'])) {
        $nilai = $_POST['nilai_tugas'];
        $submit_id = $_POST['submit_id'];

        // Memasukkan nilai ke dalam tabel submission
        $sql_submission = "UPDATE submission_tugas SET nilai = '$nilai' WHERE submit_id = '$submit_id'";

        if (mysqli_query($conn, $sql_submission)) {
            echo "<script>alert('Nilai berhasil ditambahkan.');</script>";
            unset($_SESSION['submit_id']);
            unset($_SESSION['show_modal']);

        } else {
            echo "<script>alert('Gagal menambahkan nilai.')";
            echo mysqli_error($conn);
        }
    }
    ?>

    <div class="header-tugas">
        <form method="POST" action="detail_tugas.php">
            <input type="hidden" name="id_tugas" value="<?php echo $id_tugas; ?>">
            <button type="submit">
                <img src="Asset/chevron-left.svg" alt="Back button">
            </button>
        </form>
        <p>Detail Tugas</p>
    </div>

    <div class="main-content">
        <div class="container">
            <table>
                <tr>
                    <td>No</td>
                    <td>NRP</td>
                    <td>Nama Mahasiswa</td>
                    <td>Catatan</td>
                    <td>Tanggal Pengumpulan</td>
                    <td>Lampiran</td>
                    <td>Nilai</td>
                    <td>Status Pengumpulan</td>
                </tr>
                <?php
                $number = 1;
                $sql_submission = "SELECT st.*, m.nama_lengkap, m.nrp, t.deadline, st.tanggal_submit FROM submission_tugas st
                JOIN mahasiswa m ON st.mahasiswa_id = m.mahasiswa_id
                JOIN tugas t ON st.tugas_id = t.tugas_id
                WHERE st.tugas_id = '$id_tugas'";
                $result = mysqli_query($conn, $sql_submission);

                if ($submit = mysqli_fetch_assoc($result)) {
                    if (mysqli_num_rows($result) > 0) {

                        if (strtotime($submit['tanggal_submit']) <= strtotime($submit['deadline'])) {
                            $submit['status_pengumpulan'] = 1;
                        } else {
                            $submit['status_pengumpulan'] = 2;
                        }

                        if ($submit['status_pengumpulan'] == 1) {
                            $status_tgs = "Mengumpulkan tepat waktu";
                        } else if ($submit['status_pengumpulan'] == 2) {
                            $status_tgs = "Mengumpulkan terlambat";
                        } else {
                            $status_tgs = "Belum mengumpulkan";
                        }

                        while ($submit) {
                            echo "<tr>";
                            echo "<td>" . $number . "</td>";
                            echo "<td>" . $submit["nrp"] . "</td>";
                            echo "<td>" . $submit["nama_lengkap"] . "</td>";
                            echo "<td>" . $submit["catatan"] . "</td>";
                            echo "<td>" . $submit["tanggal_submit"] . "</td>";
                            echo "<td>";
                            echo "<a href='data:" . $submit['type'] . ";base64," . base64_encode($submit['content']) . "' download='" . $submit['file_name'] . "'>Download</a>";
                            echo "</td>";
                            echo "<td>" . $submit["nilai"] . "</td>";
                            echo "<td>" . $status_tgs . "</td>";
                            echo "<td>";
                            echo "<form method='POST' action=''>
                                    <input type='hidden' name='submit_id' value='" . $submit['submit_id'] . "'>
                                    <input type='submit' name='buka_modal' value='Nilai' class='nilaiBtn' style='width: 100%; padding-bottom: 20px;'>
                                </form>";
                            echo "</td>";
                            $submit = mysqli_fetch_assoc($result);
                            $number++;
                        }
                    }
                }
                ?>
            </table>
        </div>
        <!--Start modal nilai tugas-->
        <?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']): ?>
            <div class="modal-outer" id="modal">
                <!-- Modal content -->
                <div class="modal-nilai" id="modalNilai">
                    <form method="POST" action="">
                        <button id="closeModal" name="close_modal" type="submit">
                            <img src="Asset/chevron-left.svg" alt="Icon tambah tugas">
                        </button>
                    </form>
                    <form action="" method="POST" id="form_input_nilai">
                        <h2>Input Nilai</h2>
                        <p>
                            <label for="nilai_tugas">Nilai</label><br>
                            <input type="number" name="nilai_tugas" id="nilaiTugas" step="0.01" min="0" max="100" required
                                autocomplete="off">
                        </p>
                        <input type="hidden" name="submit_id" value="<?php echo $_SESSION['submit_id']; ?>">
                        <p>
                            <input name="upload" type="submit" class="uploadBtn" id="upload">
                        </p>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <!--End modal nilai tugas-->
    </div>
</body>

</html>