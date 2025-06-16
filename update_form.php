<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Update Data Mahasiswa - MISpens</title>
    <link rel="stylesheet" href="CSS/update_form.css">
</head>

<html>
<?php
include "connect.php";
session_start();
if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['mahasiswa'] = $_POST['mahasiswa_id'];
}
$param_update = $_SESSION['mahasiswa'];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $nama_lengkap = trim($_GET['nama_lengkap']); //menambahkan trim untuk menghapus whitespace diawal dan diakhir kalimat
    $gender = trim($_GET['jenis_kelamin']);
    $prodi = trim($_GET['program_studi']);
    $nrp = trim($_GET['nrp']);
    $emailMhs = trim($_GET['email']);
    $alamat = trim($_GET['alamat']);
    $nomor_telepon = trim($_GET['nomor_telepon']);
    $asal_sekolah = trim($_GET['asal_sekolah']);
    $matkul = trim($_GET['mata_kuliah_favorit']);

    $check_isi = "SELECT * FROM mahasiswa WHERE mahasiswa_id = '$param_update'"; //memilih tabel yang akan diupdate
    $result = mysqli_query($conn, $check_isi);
    if (mysqli_num_rows($result) === 1) {  //menerima hasil inputan form pada satu halaman
        $sql = "UPDATE mahasiswa SET
                nrp = COALESCE(NULLIF('$nrp', ''), nrp),
                nama_lengkap = COALESCE(NULLIF('$nama_lengkap', ''), nama_lengkap),
                jenis_kelamin = COALESCE(NULLIF('$gender', ''), jenis_kelamin),
                program_studi = COALESCE(NULLIF('$prodi', ''), program_studi),
                email = COALESCE(NULLIF('$emailMhs', ''), email),
                alamat = COALESCE(NULLIF('$alamat', ''), alamat),
                nomor_telepon = COALESCE(NULLIF('$nomor_telepon', ''), nomor_telepon),
                asal_sekolah = COALESCE(NULLIF('$asal_sekolah', ''), asal_sekolah),
                mata_kuliah_favorit = COALESCE(NULLIF('$matkul', ''), mata_kuliah_favorit)
                WHERE mahasiswa_id LIKE '$param_update'"; //menambahkan COALESCE untuk tidak mengganti valuenya jika input yang baru adalah null
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Data berhasil terupdate.');
            window.location.href='main_page.php';</script>";
        } else {
            echo "<script>alert('Tidak dapat melakukan update.');
            window.location.href='main_page.php';</script>" . mysqli_error($conn);
        }
    }
}
mysqli_close($conn);
?>
<div class="container">
    <form action="" method="GET" id="form_utama">
        <h2>Form Update Data Mahasiswa</h2>
        <p>
            <label for="namaMahasiswa">Nama Mahasiswa</label><br>
            <input type="text" name="nama_lengkap" id="namaMahasiswa" placeholder="ex: Syauqy Arrayyan" maxlength="50">
        </p>
        <p>
            <label for="jenisKelaminMhs">Jenis Kelamin</label><br>
            <input type="text" name="jenis_kelamin" id="jenisKelaminMhs" placeholder="ex: Laki-laki">
        </p>
        <p>
            <label for="prodi">Program Studi</label><br>
            <input type="text" name="program_studi" id="prodi" placeholder="ex: D4 Teknik Informatika" maxlength="50">
        </p>
        <p>
            <label for="nrpMhs">NRP</label><br>
            <input type="number" name="nrp" id="nrpMhs" placeholder="ex: 312460xxxxx" maxlength="10">
        </p>
        <p>
            <label for="emailMhs">Email Student</label><br>
            <input type="text" name="email" id="emailMhs" placeholder="ex: syauqya@it.student.pens.ac.id">
        </p>
        <p>
            <label for="alamatMhs">Alamat</label><br>
            <input type="text" name="alamat" id="alamatMhs" placeholder="ex: Jl. Kalimantan No.1, Gresik">
        </p>
        <p>
            <label for="nomorTelpMhs">Nomor Telepon</label><br>
            <input type="number" name="nomor_telepon" id="nomorTelpMhs" placeholder="ex: 62878xxxxxx">
        </p>
        <p>
            <label for="asalSekolahMhs">Asal Sekolah</label><br>
            <input type="text" name="asal_sekolah" id="asalSekolahMhs" placeholder="ex: SMA Negeri">
        </p>
        <p>
            <label for="matkulFavMhs">Mata Kuliah Favorit</label><br>
            <input type="text" name="mata_kuliah_favorit" id="matkulFavMhs" placeholder="ex: Praktikum Pemrograman Web">
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