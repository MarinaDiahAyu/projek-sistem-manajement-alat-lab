<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['peminjam_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User SIMALAB</title>
   
    <link rel="stylesheet" href="user.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

<main class="main-content">

    <div class="welcome">
        <h1>Selamat Datang di SIMALAB</h1>
    </div>

    <h2>Aktivitas Sistem</h2>

    <div class="dashboard-cards">

        <div class="card">
            <h3>Data alat</h3>
            <p>Lihat berbagai alat laboratorium yang tersedia.</p>
            <a href="data_alat.php">Lihat Data alat</a>
        </div>

        <div class="card">
            <h3>Peminjaman</h3>
            <p>Ajukan peminjaman alat laboratorium.</p>
            <a href="peminjaman.php">Pinjam Alat</a>
        </div>

        <div class="card">
            <h3>Riwayat</h3>
            <p>Lihat riwayat peminjaman kamu.</p>
            <a href="riwayat.php">Lihat Riwayat</a>
        </div>
    </div>

</main>
</body>
</html>