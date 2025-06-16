<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Data Baru Mahasiswa - MISpens</title>
    <link rel="stylesheet" href="CSS/input_form.css">
</head>

<body>
    <?php
    session_start();
    include "connect.php";
    if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama_lengkap = trim($_POST['nama_lengkap']); //Menambahkan trim untuk menghapus whitespace diawal dan diakhir kalimat
        $gender = trim($_POST['jenis_kelamin']);
        $prodi = trim($_POST['program_studi']);
        $nrp = trim($_POST['nrp']);
        $emailMhs = trim($_POST['email_student']);
        $alamat = trim($_POST['alamat']);
        $nomor_telepon = trim($_POST['nomor_telepon']);
        $asal_sekolah = trim($_POST['asal_sekolah']);
        $matkul = trim($_POST['mata_kuliah_favorit']);

        $sql = "INSERT INTO mahasiswa (nrp, nama_lengkap, jenis_kelamin, program_studi, email_student, alamat, nomor_telepon, asal_sekolah, mata_kuliah_favorit) 
        VALUES ('$nrp','$nama_lengkap','$gender','$prodi','$emailMhs','$alamat','$nomor_telepon','$asal_sekolah','$matkul')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Data berhasil ditambahkan.');
            window.location.href='main_page.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data.');
            window.location.href='main_page.php';</script>" . mysqli_error($conn);
        }
    }
    mysqli_close($conn);
    ?>
    <div class="container">
        <form action="" method="POST" id="form_utama">
            <h2>Form Input Data Mahasiswa</h2>
            <p>
                <label for="namaMahasiswa">Nama Mahasiswa</label><br>
                <input type="text" name="nama_lengkap" id="namaMahasiswa" placeholder="ex: Syauqy Arrayyan" required
                    maxlength="50">
            </p>
            <p>
                <label for="jenisKelaminMhs">Jenis Kelamin</label><br>
                <input type="text" name="jenis_kelamin" id="jenisKelaminMhs" placeholder="ex: Laki-laki" required>
            </p>
            <p>
                <label for="prodi">Program Studi</label><br>
                <input type="text" name="program_studi" id="prodi" placeholder="ex: D4 Teknik Informatika" required>
            </p>
            <p>
                <label for="nrpMhs">NRP</label><br>
                <input type="number" name="nrp" id="nrpMhs" placeholder="ex: 312460xxxxx" required>
            </p>
            <p>
                <label for="emailMhs">Email Student</label><br>
                <input type="text" name="email_student" id="emailMhs" placeholder="ex: syauqya@it.student.pens.ac.id"
                    required>
            </p>
            <p>
                <label for="alamatMhs">Alamat</label><br>
                <input type="text" name="alamat" id="alamatMhs" placeholder="ex: Jl. Kalimantan No.1, Gresik" required>
            </p>
            <p>
                <label for="nomorTelpMhs">Nomor Telepon</label><br>
                <input type="number" name="nomor_telepon" id="nomorTelpMhs" placeholder="ex: 62878xxxxxx" required>
            </p>
            <p>
                <label for="asalSekolahMhs">Asal Sekolah</label><br>
                <input type="text" name="asal_sekolah" id="asalSekolahMhs" placeholder="ex: SMA Negeri" required>
            </p>
            <p>
                <label for="matkulFavMhs">Mata Kuliah Favorit</label><br>
                <input type="text" name="mata_kuliah_favorit" id="matkulFavMhs"
                    placeholder="ex: Praktikum Pemrograman Web" required>
            </p>
            <p>
                <button type="submit">Submit</button>
            </p>
        </form>
        <a href="main_page.php">
            <button>Back to main page</button>
        </a>
    </div>
</body>

</html>