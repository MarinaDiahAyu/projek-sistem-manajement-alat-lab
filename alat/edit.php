
<?php
include '../config/koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($connect, "SELECT * FROM alat WHERE id='$id'");
if (!$query){
    die("query gagal: " . mysqli_error($connect));
}

$data = mysqli_fetch_array($query);

if(!$data) {
    die("Data alat tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Alat</title>
</head>
<body>

    <h2>Edit Data Alat Laboratorium</h2>

    <form method="POST" action="update.php">

        <input type="hidden" name="id"
        value="<?php echo $data['id']; ?>">

        Kode Alat:<br>
        <input type="text" name="kode_alat"
        value="<?php echo $data['kode_alat']; ?>"><br><br>

        Nama Alat:<br>
        <input type="text" name="nama_alat"
        value="<?php echo $data['nama_alat']; ?>"><br><br>

        Kategori ID:<br>
        <input type="number" name="kategori_id"
        value="<?php echo $data['kategori_id']; ?>"><br><br>

        Jumlah Total:<br>
        <input type="number" name="jumlah_total"
        value="<?php echo $data['jumlah_total']; ?>"><br><br>

        Jumlah Tersedia:<br>
        <input type="number" name="jumlah_tersedia"
        value="<?php echo $data['jumlah_tersedia']; ?>"><br><br>

        Kondisi:<br>
        <input type="text" name="kondisi"
        value="<?php echo $data['kondisi']; ?>"><br><br>

        Keterangan:<br>
        <textarea name="keterangan"><?php echo $data['keterangan']; ?></textarea><br><br>

        <input type="submit" name="submit" value="Simpan Perubahan">

    </form>

    <a href="index.php">Kembali</a>

</body>
</html>