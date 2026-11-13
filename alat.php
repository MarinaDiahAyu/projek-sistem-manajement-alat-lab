<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Alat Laboratorium</title>
</head>
<body>
    <div class="container mt-5">
        <h2>Data Alat Laboratorium</h2>
        <a href="alat-tambah.php">Tambah Alat Baru</a>
        <br>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Kode Alat</th>
        <th>Nama Alat<th>
        <th>Kondisi</th>
        <th>Kategori ID</th>
        <th>Total</th>
        <th>Tersedian</th>
        <th>Aksi</th>
</tr>

<?php
//memanggil file koneksi 
include 'koneksi.php';

$no = 1;

$query= mysqli_query($connect, "SELECT * FROM alat ORDER BY id DESC");

while($data = mysqli_fetch_array($query)) {
    ?>
    <tr>
        <td><?php echo $no++;?></td>
        <td><?php echo $data['kode_alat']; ?></td>
        <td><?php echo $data['nama_alat']; ?></td>
        <td><?php echo $data['kondisi']; ?></td>
        <td><?php echo $data['kategori_id']; ?></td>
        <td><?php echo $data['jumlah_total']; ?></td>
        <td><?php echo $data['jumlah_tersedia']; ?></td>
        <td><?php echo $data['aksi']; ?></td>
        <td>
            <a href="alat-edit.php?id=<?php echo $data['id']; ?>">Edit</a>
            <a href="alat-hapus.php?id=<?php echo $data['id']; ?>" onclick="return confirm('yakin ingin menghapus?')">Hapus</a>
        </td>
    </tr>
    <?php
}
?>
</table>

</div>
</body>
</html>