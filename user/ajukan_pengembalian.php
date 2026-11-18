<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['peminjam_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = (int) $_GET['id'];
$berhasil = false;

$query = mysqli_query($connect, "SELECT * FROM peminjaman WHERE id = '$id' AND status IN ('Dipinjam', 'Terlambat')");

$data = mysqli_fetch_array($query);

if (!$data) {
    die("Data peminjaman tidak ditemukan!");
}

if (isset($_POST['submit'])) {
    $berhasil = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajukan Pengembalian</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">

    <?php if ($berhasil) { ?>

        <h1>Pengajuan Pengembalian</h1>
        <p>Pengajuan pengembalian berhasil dikirim!</p>
        <p>Silakan tunggu Admin memproses pengembalian.</p>
        <a href="pengembalian.php">Kembali ke Pengembalian</a>

    <?php } else { ?>

        <h1>Ajukan Pengembalian</h1>

        <p>Kode Peminjaman:
            <?php echo $data['kode_pinjam']; ?>
        </p>

        <p>Tanggal Rencana Kembali:
            <?php echo $data['tanggal_kembali_rencana']; ?>
        </p>

        <p>Apakah kamu yakin ingin mengajukan pengembalian alat ini?</p>

        <form method="POST">
            <button type="submit" name="submit">
                Kirim Pengajuan
            </button>
        </form>

        <br>
        <a href="pengembalian.php">Kembali</a>

    <?php } ?>

</main>

</body>
</html>