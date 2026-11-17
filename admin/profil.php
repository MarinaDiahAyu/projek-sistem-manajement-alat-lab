
<?php
session_start();
include "../config/koneksi.php";

// Cek login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil data Admin
$id = $_SESSION['admin_id'];

$query = mysqli_prepare($connect, "
    SELECT username, nama_lengkap
    FROM admin
    WHERE id = ?
");

mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);

mysqli_stmt_bind_result($query, $username, $nama_lengkap);

if (!mysqli_stmt_fetch($query)) {
    die("Data profil Admin tidak ditemukan.");
}

mysqli_stmt_close($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - SIMALAB</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
            display: flex;
        }

        .sidebar {
            width: 230px;
            min-height: 100vh;
            background: #1e293b;
            padding: 25px 15px;
            color: white;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px;
            margin-bottom: 8px;
            border-radius: 6px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #334155;
        }

        .main {
            flex: 1;
            padding: 30px;
        }

        .profile-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            max-width: 600px;
            margin-top: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .profile-box p {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .profile-box strong {
            display: inline-block;
            width: 150px;
        }

        @media (max-width: 800px) {
            body {
                display: block;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h2>SIMALAB</h2>

    <a href="index.php">Dashboard</a>
    <a href="alat.php">Data Alat</a>
    <a href="peminjaman.php">Data Peminjaman</a>
    <a href="pengembalian.php">Data Pengembalian</a>
    <a href="laporan.php">Laporan</a>
    <a href="profil.php" class="active">Profil</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="main">

    <h1>Profil Admin</h1>
    <p>Informasi akun Administrator SIMALAB.</p>

    <div class="profile-box">

        <h2>Data Admin</h2>

        <p>
            <strong>Nama Lengkap</strong>
            <?php echo htmlspecialchars($nama_lengkap); ?>
        </p>

        <p>
            <strong>Username</strong>
            <?php echo htmlspecialchars($username); ?>
        </p>

        <p>
            <strong>Role</strong>
            Administrator
        </p>

    </div>

</div>

</body>
</html>
