
<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mencari username di database
    $query = mysqli_prepare($connect,
        "SELECT id, username, password, nama_lengkap
         FROM admin WHERE username = ?"
    );

    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);

    mysqli_stmt_bind_result(
        $query,
        $id,
        $db_username,
        $db_password,
        $nama_lengkap
    );

    if (mysqli_stmt_fetch($query)) {

        // Memeriksa password hash
        if (password_verify($password, $db_password)) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['nama_lengkap'] = $nama_lengkap;

            mysqli_stmt_close($query);

            header("Location: index.php");
            exit();

        }
    }

    mysqli_stmt_close($query);

    header("Location: login.php?error=1");
    exit();

} else {

    header("Location: login.php");
    exit();

}
?>
