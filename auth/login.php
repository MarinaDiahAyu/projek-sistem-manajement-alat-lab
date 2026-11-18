<?php
session_start();
include '../config/koneksi.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_prepare($connect, "SELECT id, username, password, nama_lengkap FROM admin WHERE username = ?");

    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);
    mysqli_stmt_bind_result($query, $id, $db_username, $db_password, $nama);

    if (mysqli_stmt_fetch($query)){
        if (password_verify($password, $db_password) || $password === $db_password){
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['nama_lengkap'] = $nama;
            $_SESSION['role'] = 'admin';

            mysqli_stmt_close($query);
            header("Location: ../admin/index.php");
            exit();
        }
    }

    mysqli_stmt_close($query);

    $query = mysqli_prepare($connect, "SELECT id, nama, nim, password FROM peminjam WHERE nim = ?");

    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);
    mysqli_stmt_bind_result($query, $id, $nama, $nim, $db_password);

    if (mysqli_stmt_fetch($query)){
        if (password_verify($password, $db_password)){
            session_regenerate_id(true);

            $_SESSION['peminjam_id'] = $id;
            $_SESSION['nama'] = $nama;
            $_SESSION['nim'] = $nim;
            $_SESSION['role'] = 'user';

            mysqli_stmt_close($query);
            header("Location: ../user/index.php");
            exit();
        }
    }

    mysqli_stmt_close($query);
    $error = "Username atau password salah.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login SIMALAB</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #eaf2ff, #dce9fa);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .login-card {
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
            min-height: 550px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
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
            padding: 55px 50px;
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
            padding: 12px 14px;
            border-radius: 9px;
        }

        .form-control:focus {
            border-color: #3979bd;
            box-shadow: 0 0 0 0.2rem rgba(57, 121, 189, 0.15);
        }

        .btn-login {
            background: #194c7d;
            color: white;
            padding: 12px;
            border-radius: 9px;
            font-weight: bold;
            border: none;
        }

        .btn-login:hover {
            background: #10375e;
            color: white;
        }

        .register-link {
            color: #2366a1;
            font-weight: bold;
            text-decoration: none;
        }

        .register-link:hover {
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
                padding: 35px 25px;
            }
        }
    </style>
</head>

<body>

<div class="login-card">
    <div class="row g-0">

        <div class="col-md-5">
            <div class="left-panel">
                <div class="logo">🔬</div>
                <h1>SIMALAB</h1>
                <p class="mt-3">Sistem Manajemen Alat Laboratorium</p>
            </div>
        </div>

        <div class="col-md-7">
            <div class="right-panel">

                <h2>Selamat Datang</h2>
                <p class="text-secondary mb-4">Silakan masuk untuk melanjutkan.</p>

                <?php if ($error != ""): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Username / NIM</label>
                        <input
                            type="text" name="username" class="form-control" placeholder="Masukkan username atau NIM" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn btn-login w-100">
                        Masuk
                    </button>

                </form>

                <div class="text-center mt-4">
                    <span class="text-secondary">Belum punya akun?</span>

                    <a href="register.php" class="register-link">Daftar di sini</a>
                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>
