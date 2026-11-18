
<?php
session_start();
include "../config/koneksi.php";

// Pastikan hanya Admin yang bisa masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Menghitung jumlah alat
$queryAlat = mysqli_query($connect, "SELECT COUNT(*) AS total FROM alat");
$dataAlat = mysqli_fetch_assoc($queryAlat);

// Menghitung jumlah peminjam
$queryPeminjam = mysqli_query($connect, "SELECT COUNT(*) AS total FROM peminjam");
$dataPeminjam = mysqli_fetch_assoc($queryPeminjam);

// Menghitung jumlah peminjaman aktif
$queryPinjam = mysqli_query($connect, "
    SELECT COUNT(*) AS total FROM peminjaman
    WHERE status IN ('Dipinjam', 'Terlambat')
");
$dataPinjam = mysqli_fetch_assoc($queryPinjam);

// Menghitung jumlah pengembalian
$queryKembali = mysqli_query($connect, "
    SELECT COUNT(*) AS total FROM peminjaman
    WHERE status = 'Dikembalikan'
");
$dataKembali = mysqli_fetch_assoc($queryKembali);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIMALAB</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f6f9;
            display: flex;
        }

        .sidebar {
            width: 230px;
            min-height: 100vh;
            background: #1e293b;
            color: white;
            padding: 25px 15px;
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

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            margin-bottom: 8px;
            color: #1e293b;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .card h3 {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .card p {
            font-size: 30px;
            font-weight: bold;
            color: #1e293b;
        }

        @media (max-width: 900px) {
            .cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            body {
                display: block;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>SIMALAB</h2>

        <a href="index.php" class="active">Dashboard</a>
        <a href="alat.php">Data Alat</a>
        <a href="peminjaman.php">Data Peminjaman</a>
        <a href="pengembalian.php">Data Pengembalian</a>
        <a href="laporan.php">Laporan</a>
        <a href="profil.php">Profil</a>
        <a href="../auth/logout.php"
           onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </div>

    <div class="main">

        <div class="header">
            <h1>Dashboard Admin</h1>
            <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</p>
            <p>Selamat mengelola Sistem Manajemen Alat Laboratorium.</p>
        </div>

        <div class="cards">

            <div class="card">
                <h3>Total Alat</h3>
                <p><?php echo $dataAlat['total']; ?></p>
            </div>

            <div class="card">
                <h3>Total Peminjam</h3>
                <p><?php echo $dataPeminjam['total']; ?></p>
            </div>

            <div class="card">
                <h3>Peminjaman Aktif</h3>
                <p><?php echo $dataPinjam['total']; ?></p>
            </div>

            <div class="card">
                <h3>Sudah Dikembalikan</h3>
                <p><?php echo $dataKembali['total']; ?></p>
            </div>

        </div>

    </div>

</body>
</html>