
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

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #eaf2ff, #dce9fa);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 25px 15px;
        }

        .register-card {
            width: 100%;
            max-width: 950px;
            background: white;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(20, 50, 90, 0.13);
        }

        .left-panel {
            background: linear-gradient(145deg, #102b50, #1d5485);
            color: white;
            min-height: 650px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 35px;
        }

        .logo {
            font-size: 65px;
            margin-bottom: 15px;
        }

        .left-panel h1 {
            font-weight: bold;
            letter-spacing: 2px;
        }

        .left-panel p {
            color: #d8e8fa;
            line-height: 1.7;
        }

        .right-panel {
            padding: 40px 50px;
        }

        .right-panel h2 {
            color: #173b63;
            font-weight: bold;
        }

        .form-label {
            font-weight: 600;
            color: #344b63;
        }

        .form-control {
            padding: 11px 14px;
            border-radius: 9px;
        }

        .form-control:focus {
            border-color: #3979bd;
            box-shadow: 0 0 0 0.2rem rgba(57, 121, 189, 0.15);
        }

        .btn-register {
            background: #194c7d;
            color: white;
            padding: 12px;
            border-radius: 9px;
            font-weight: bold;
            border: none;
        }

        .btn-register:hover {
            background: #10375e;
            color: white;
        }

        .login-link {
            color: #2366a1;
            font-weight: bold;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 767px) {
            .left-panel {
                min-height: 220px;
                padding: 25px;
            }

            .logo {
                font-size: 42px;
            }

            .left-panel h1 {
                font-size: 27px;
            }

            .right-panel {
                padding: 30px 25px;
            }
        }
    </style>
</head>

<body>

<div class="register-card">
    <div class="row g-0">

        <!-- BAGIAN KIRI -->
        <div class="col-md-5">
            <div class="left-panel">

                <div class="logo">🔬</div>

                <h1>SIMALAB</h1>

                <p class="mt-3">
                    Sistem Manajemen Alat Laboratorium
                </p>

                <hr class="w-50 border-light">

                <p class="small">
                    Bergabunglah untuk mengakses informasi
                    alat laboratorium dan mengajukan
                    peminjaman alat dengan mudah.
                </p>

            </div>
        </div>

        <!-- BAGIAN KANAN -->
        <div class="col-md-7">
            <div class="right-panel">

                <h2>Buat Akun</h2>

                <p class="text-secondary mb-4">
                    Lengkapi data berikut untuk mendaftar sebagai User.
                </p>

                <?php if ($error != ""): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($berhasil != ""): ?>

                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($berhasil); ?>
                    </div>

                    <a href="login.php" class="btn btn-register w-100">
                        Login Sekarang
                    </a>

                <?php else: ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Masukkan nama lengkap"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIM</label>
                            <input
                                type="text"
                                name="nim"
                                class="form-control"
                                placeholder="Masukkan NIM"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <input
                                type="text"
                                name="kelas"
                                class="form-control"
                                placeholder="Masukkan kelas"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor HP</label>
                            <input
                                type="text"
                                name="no_hp"
                                class="form-control"
                                placeholder="Masukkan nomor HP"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Minimal 6 karakter"
                                minlength="6"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-register w-100">
                            Daftar Akun
                        </button>

                    </form>

                    <div class="text-center mt-4">
                        <span class="text-secondary">
                            Sudah punya akun?
                        </span>

                        <a href="login.php" class="login-link">
                            Login di sini
                        </a>
                    </div>

                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

</body>
</html>
