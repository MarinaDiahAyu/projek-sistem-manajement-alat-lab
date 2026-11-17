
<?php
session_start();
include '../config/koneksi.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // LOGIN ADMIN
    $query = mysqli_prepare($connect,
        "SELECT id, username, password, nama_lengkap
         FROM admin WHERE username = ?"
    );

    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);
    mysqli_stmt_bind_result($query, $id, $db_username, $db_password, $nama);

    if (mysqli_stmt_fetch($query)) {

        if (password_verify($password, $db_password) || $password === $db_password) {

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

    // LOGIN USER
    $query = mysqli_prepare($connect,
        "SELECT id, nama, nim, password
         FROM peminjam WHERE nim = ?"
    );

    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);
    mysqli_stmt_bind_result($query, $id, $nama, $nim, $db_password);

    if (mysqli_stmt_fetch($query)) {

        if (password_verify($password, $db_password)) {

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
    <title>SIMALAB - Login</title>
</head>
<body>

    <div>
        <h2>🔬 SIMALAB</h2>
        <p>Sistem Manajemen Alat Laboratorium</p>

        <?php if ($error != ""): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">

            <label>Username / NIM</label><br>
            <input type="text" name="username" required>

            <br><br>

            <label>Password</label><br>
            <input type="password" name="password" required>

            <br><br>

            <button type="submit">Masuk</button>

        </form>

        <p>Belum punya akun?
            <a href="register.php">Daftar di sini</a>
        </p>
    </div>

</body>
</html>
