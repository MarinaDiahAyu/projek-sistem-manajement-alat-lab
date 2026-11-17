<?php
include '../config/koneksi.php';

$id = (int) $_GET['id'];

$query = mysqli_query($connect, "
    SELECT *
    FROM peminjaman
    WHERE id = '$id'
    AND status IN ('Dipinjam', 'Terlambat')
");

$data = mysqli_fetch_array($query);

if (!$data) {
    die("Data peminjaman tidak ditemukan!");
}

if (isset($_POST['submit'])) {
    echo "Pengajuan pengembalian berhasil dikirim!";
    echo "<br>Silakan tunggu Admin memproses pengembalian.";
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
    <h1>Ajukan Pengembalian</h1>

    <p>Kode Peminjaman:
        <?php echo $data['kode_pinjam']; ?>
    </p>

    <p>Tanggal Rencana Kembali:
        <?php echo $data['tanggal_kembali_rencana']; ?>
    </p>

    <form method="POST">
        <p>Apakah kamu yakin ingin mengajukan pengembalian alat ini?</p>

        <button type="submit" name="submit">
            Kirim Pengajuan
        </button>
    </form>

    <br>
    <a href="pengembalian.php">Kembali</a>
</main>

</body>
</html>