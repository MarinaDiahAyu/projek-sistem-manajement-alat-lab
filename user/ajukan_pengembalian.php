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

        <div class="success-icon">✓</div>
            <h1 class="form-title center">Pengajuan Terkirim</h1>
            <p class="form-text center">
                Pengajuan pengembalian berhasil dikirim.<br>
                Silakan tunggu Admin memproses pengembalian.
            </p>
            <div class="form-actions">
                <a href="pengembalian.php" class="btn-primary">Kembali ke Pengembalian</a>
            </div>
    <?php } else { ?>

          <h1 class="form-title">Ajukan Pengembalian</h1>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Kode Peminjaman</span>
                    <span class="info-value"><?php echo htmlspecialchars($data['kode_pinjam']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Rencana Kembali</span>
                    <span class="info-value"><?php echo htmlspecialchars($data['tanggal_kembali_rencana']); ?></span>
                </div>
            </div>

            <p class="form-text">Apakah kamu yakin ingin mengajukan pengembalian alat ini?</p>

            <form method="POST">
                <div class="form-actions">
                    <a href="pengembalian.php" class="btn-secondary">Kembali</a>
                    <button type="submit" name="submit" class="btn-primary">Kirim Pengajuan</button>
                </div>
            </form>


    <?php } ?>

</main>

</body>
</html>