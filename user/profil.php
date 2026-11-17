
<?php
session_start();
include '../config/koneksi.php';

// Cek login User
if (!isset($_SESSION['peminjam_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil data User
$id = $_SESSION['peminjam_id'];

$query = mysqli_prepare($connect,
    "SELECT nama, nim, kelas, no_hp
     FROM peminjam WHERE id = ?"
);

mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);
mysqli_stmt_bind_result($query, $nama, $nim, $kelas, $no_hp);

if (!mysqli_stmt_fetch($query)) {
    die("Data profil tidak ditemukan.");
}

mysqli_stmt_close($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User - SIMALAB</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f5f9;
        }

        .sidebar {
            width: 270px;
            height: 100vh;
            background: #08084f;
            color: white;
            position: fixed;
            padding: 24px;
            box-sizing: border-box;
        }

        .sidebar h2 {
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px 0;
        }

        .sidebar a:hover {
            color: #aaaaff;
        }

        .content {
            margin-left: 270px;
            padding: 35px;
        }

        .profile-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
        }

        .profile-box p {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .profile-box strong {
            display: inline-block;
            width: 150px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>🔬 SIMALAB</h2>
        <p>Lab Management</p>
        <hr>

        <a href="index.php">Dashboard</a>
        <a href="data_alat.php">Data Alat</a>
        <a href="peminjaman.php">Ajukan Peminjaman</a>
        <a href="riwayat.php">Riwayat Peminjaman</a>
        <a href="pengembalian.php">Pengembalian</a>
        <a href="profil.php">Profil</a>

        <br><br><br>

        <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="content">

        <h1>Profil Saya</h1>
        <p>Informasi akun pengguna SIMALAB.</p>

        <div class="profile-box">

            <h2>Data Pengguna</h2>

            <p>
                <strong>Nama Lengkap</strong>
                <?php echo htmlspecialchars($nama); ?>
            </p>

            <p>
                <strong>NIM</strong>
                <?php echo htmlspecialchars($nim); ?>
            </p>

            <p>
                <strong>Kelas</strong>
                <?php echo htmlspecialchars($kelas); ?>
            </p>

            <p>
                <strong>Nomor HP</strong>
                <?php echo htmlspecialchars($no_hp); ?>
            </p>

        </div>

    </div>

</body>
</html>
