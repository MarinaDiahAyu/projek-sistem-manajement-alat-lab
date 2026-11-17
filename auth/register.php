
<?php
session_start();
include '../config/koneksi.php';

$error = "";
$berhasil = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = trim($_POST['nama']);
    $nim = trim($_POST['nim']);
    $kelas = trim($_POST['kelas']);
    $no_hp = trim($_POST['no_hp']);
    $password = $_POST['password'];

    if ($nama == "" || $nim == "" || $kelas == "" || $no_hp == "" || $password == "") {

        $error = "Semua kolom wajib diisi.";

    } elseif (strlen($password) < 6) {

        $error = "Password minimal 6 karakter.";

    } else {

        // Cek NIM
        $cek = mysqli_prepare($connect,
            "SELECT id FROM peminjam WHERE nim = ?"
        );

        mysqli_stmt_bind_param($cek, "s", $nim);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $error = "NIM sudah terdaftar.";

        } else {

            // Enkripsi password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Simpan data User
            $query = mysqli_prepare($connect,
                "INSERT INTO peminjam (nama, nim, kelas, no_hp, password)
                 VALUES (?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $query,
                "sssss",
                $nama,
                $nim,
                $kelas,
                $no_hp,
                $password_hash
            );

            if (mysqli_stmt_execute($query)) {
                $berhasil = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Registrasi gagal. Silakan coba lagi.";
            }

            mysqli_stmt_close($query);
        }

        mysqli_stmt_close($cek);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMALAB - Registrasi</title>
</head>
<body>

    <div>
        <h2>🔬 SIMALAB</h2>
        <p>Daftar Akun User</p>

        <?php if ($error != ""): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($berhasil != ""): ?>

            <p><?php echo htmlspecialchars($berhasil); ?></p>
            <a href="login.php">Login sekarang</a>

        <?php else: ?>

            <form method="POST">

                <label>Nama Lengkap</label><br>
                <input type="text" name="nama" required>

                <br><br>

                <label>NIM</label><br>
                <input type="text" name="nim" required>

                <br><br>

                <label>Kelas</label><br>
                <input type="text" name="kelas" required>

                <br><br>

                <label>Nomor HP</label><br>
                <input type="text" name="no_hp" required>

                <br><br>

                <label>Password</label><br>
                <input type="password" name="password" minlength="6" required>

                <br><br>

                <button type="submit">Daftar</button>

            </form>

            <p>Sudah punya akun? <a href="login.php">Login</a></p>

        <?php endif; ?>

    </div>

</body>
</html>
