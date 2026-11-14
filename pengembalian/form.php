<?php
include "../config/koneksi.php";

$id = (int) $_GET['id'];

$query = mysqli_query($connect, "SELECT peminjaman.*, peminjam.nama FROM peminjaman JOIN peminjam ON peminjaman.peminjam_id = peminjam.id WHERE peminjaman.id = '$id'");

$data = mysqli_fetch_array($query);

if(!$data){
    die("Data peminjaman tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengembalian</title>
</head>
<body>
    <h2>Form Pengembalian Alat</h2>

    <p>Kode Peminjaman: <?php echo $data['kode_pinjam']; ?></p>
    <p>Nama Peminjam: <?php echo $data['nama']; ?></p>

    <form method="POST" action="proses.php">

        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

        Tanggal Pengembalian:<br>
        <input type="date" name="tanggal_kembali_aktual">
        <br><br>

        Kondisi Alat:<br>
        <select name="kondisi_kembali">
            <option value="">Pilih Kondisi</option>
            <option value="Baik">Baik</option>
            <option value="Rusak Ringan">Rusak Ringan</option>
            <option value="Rusak Berat">Rusak Berat</option>
        </select>
        <br><br>

        Catatan:<br>
        <textarea name="catatan"></textarea>
        <br><br>

        <input type="submit" name="submit" value="Simpan Pengembalian">

    </form>

    <br>
    <a href="index.php">Kembali</a>

    
    
</body>
</html>