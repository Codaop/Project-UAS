<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Data Baru Mahasiswa - MISpens</title>
    <link rel="stylesheet" href="CSS/register.css">
</head>

<body>
    <?php
    session_start();
    include "connect.php";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $role = trim($_POST['role']);
        $nama_lengkap = trim($_POST['nama_lengkap']); //Menambahkan trim untuk menghapus whitespace diawal dan diakhir kalimat
        $nrp = trim($_POST['nrp']);
        $gender = trim($_POST['gender']);
        $prodi = trim($_POST['prodi_student']);
        $emailMhs = trim($_POST['email_student']);
        $alamat = trim($_POST['alamat']);
        $nomor_telepon = trim($_POST['nomor_telepon']);
        $asal_sekolah = trim($_POST['asal_sekolah']);
        $kelas = trim($_POST['kelas_mhs']);
        $u_pass = trim($_POST['user_pass']);
        $hash_pass = password_hash($u_pass, PASSWORD_DEFAULT);

        if ($role == 1) {
            $check_nrp = "SELECT * FROM mahasiswa WHERE nrp = '$nrp'";
            $result = mysqli_query($conn, $check_nrp);
            if (mysqli_num_rows($result) > 0) {
                echo "<script>alert('Gagal mendaftarkan akun, NRP sudah terdaftar.');
                window.location.href='register.php';</script>";
            } else {
                $sql = "INSERT INTO mahasiswa (role, nama_lengkap, nrp, jenis_kelamin, program_studi, email, alamat, nomor_telepon, asal_sekolah, kelas, pass_user)
            VALUES ('$role','$nama_lengkap','$nrp','$gender','$prodi','$emailMhs','$alamat','$nomor_telepon','$asal_sekolah','$kelas','$hash_pass')";
                if (mysqli_query($conn, $sql)) {
                    echo "<script>alert('Akun berhasil ter-register.');
                window.location.href='login.php';</script>";
                } else {
                    echo "<script>alert('Gagal mendaftarkan akun.');
                window.location.href='login.php';</script>" . mysqli_error($conn);
                }
            }
        }
        if ($role == 2) {
            $check_nrp = "SELECT * FROM dosen WHERE nidn = '$nrp'";
            $result = mysqli_query($conn, $check_nrp);
            if (mysqli_num_rows($result) > 0) {
                echo "<script>alert('Gagal mendaftarkan akun, NRP sudah terdaftar.');
                window.location.href='register.php';</script>";
            } else {
                $sql = "INSERT INTO dosen (role, nama_lengkap, nidn, jenis_kelamin, program_studi, email, alamat, nomor_telepon, pass_user)
            VALUES ('$role','$nama_lengkap','$nrp','$gender','$prodi','$emailMhs','$alamat','$nomor_telepon','$hash_pass')";
            }
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Akun berhasil ter-register.');
                window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Gagal mendaftarkan akun.');
                window.location.href='login.php';</script>" . mysqli_error($conn);
            }
        }
    }
    mysqli_close($conn);
    ?>
    <div class="container">
        <form action="" method="POST" id="form_utama">
            <h2>Register Akun Mahasiswa</h2>
            <p>
                <label for="namaMahasiswa">Nama Lengkap</label><br>
                <input type="text" name="nama_lengkap" id="namaMahasiswa" placeholder="ex: Syauqy Arrayyan" required
                    maxlength="50">
            </p>
            <p>
                <label for="role">Saya adalah seorang...</label><br>
                <select name="role" id="role" required>
                    <option value="1">Mahasiswa</option>
                    <option value="2">Dosen</option>
                </select>
            </p>
            <p>
                <label for="nrpMhs">NRP/NIDN</label><br>
                <input type="number" name="nrp" id="nrpMhs" placeholder="ex: 312460xxxxx" required>
            </p>
            <p>
                <label for="genderMhs">Jenis Kelamin</label><br>
                <input type="text" name="gender" id="genderMhs" placeholder="ex: Laki-laki" required>
            </p>
            <p>
                <label for="prodiMhs">Program Studi</label><br>
                <input type="text" name="prodi_student" id="prodiMhs" placeholder="ex: D4 Teknik Informatika" required>
            </p>
            <p>
                <label for="kelasMhs">Kelas</label><br>
                <select name="kelas_mhs" id="kelasMhs" required>
                    <option value="A">Kelas A</option>
                    <option value="B">Kelas B</option>
                    <option value="C">Kelas C</option>
                    <option value="D">Kelas D</option>
                </select>
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
            <p id="asalSekolahGroup">
                <label for="asalSekolahMhs">Asal Sekolah</label><br>
                <input type="text" name="asal_sekolah" id="asalSekolahMhs" placeholder="ex: SMA Negeri" required>
            </p>
            <p>
                <label for="userPass">Password</label><br>
                <input type="password" name="user_pass" id="userPass" autocomplete="off" required>
            </p>
            <p class="hide-pass">
                <input class="checkPass" type="checkbox" onclick="showHide()">Tampilkan Password
            </p>
            <p>
                <button type="submit">Submit</button>
            </p>
        </form>
        <a href="login.php">
            <button>Back to login page</button>
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
        document.getElementById("role").addEventListener("change", function () {
            var asalSekolahGroup = document.getElementById("asalSekolahGroup");
            var asalSekolahInput = document.getElementById("asalSekolahMhs");

            if (this.value === "2") {
                asalSekolahGroup.style.display = "none";
                asalSekolahInput.required = false;
            } else {
                asalSekolahGroup.style.display = "block";
                asalSekolahInput.required = true;
            }
        });
    </script>
</body>

</html>