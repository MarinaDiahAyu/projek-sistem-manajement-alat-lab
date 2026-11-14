<?php
include "../config/koneksi.php";

$queryPeminjam = mysqli_query($connect, "SELECT * FROM peminjam");
$queryAlat = mysqli_query($connect, "SELECT * FROM alat");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjaman</title>
</head>
<body>

    <h2>Form Peminjaman Alat Laboratorium</h2>

    <form method="POST" action="proses.php">

        <a href="peminjam.php">Tambah Data Peminjam</a>
        <br><br>

        Nama Peminjam :<br>
        <select name="peminjam_id">
            <option value="">Pilih Peminjam</option>

            <?php
            while ($peminjam = mysqli_fetch_array($queryPeminjam)){
            ?>
                <option value="<?php echo $peminjam['id']; ?>">
                    <?php echo $peminjam['nama']; ?>
                </option>
            <?php } ?>

        </select>

        <br><br>

        Alat : <br>
        <select name="alat_id">
            <option value="">Pilih Alat</option>

            <?php
            while($alat = mysqli_fetch_array($queryAlat)){
            ?>
                <option value="<?php echo $alat['id']; ?>">
                    <?php echo $alat['nama_alat']; ?>
                    (Stok yang tersedia: <?php echo $alat['jumlah_tersedia']; ?>)
                </option>
            <?php } ?>

        </select>

        <br><br>

        Jumlah :<br>
        <input type="number" name="jumlah" min="1">

        <br><br>

        Tanggal Pinjam : <br>
        <input type="date" name="tanggal_pinjam">

        <br><br>

        Rencana Tanggal Pengembalian :<br>
        <input type="date" name="tanggal_kembali_rencana">

        <br><br>

        <input type="submit" name="submit" value="Simpan Peminjaman">

    </form> 

    <br>

    <a href="index.php">Kembali</a>
</body>
</html>