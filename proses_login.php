
<?php
session_start();
include 'connect.php';

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($connect, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

    if (mysqli_num_rows($query) > 0) {

        $_SESSION['username'] = $username;

        header("Location: index.php");
        exit();

    } else {

        echo "<script>
            alert('Username atau password salah!');
            window.location='login.php';
        </script>";

    }

}
?>
