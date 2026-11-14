
<?php
include '../config/koneksi.php';

$query = mysqli_query($connect, "SELECT * FROM alat");

if (!$query) {
    die("Query gagal: " . mysqli_error($connect));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Alat Laboratorium</title>
</head>
<body>

    <h2>Data Alat Laboratorium</h2>

    <a href="tambah.php">Tambah Alat</a>

    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Kode Alat</th>
            <th>Nama Alat</th>
            <th>Jumlah Total</th>
            <th>Jumlah Tersedia</th>
            <th>Kondisi</th>
            <th>Keterangan</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        while ($data = mysqli_fetch_array($query)) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $data['kode_alat']; ?></td>
            <td><?php echo $data['nama_alat']; ?></td>
            <td><?php echo $data['jumlah_total']; ?></td>
            <td><?php echo $data['jumlah_tersedia']; ?></td>
            <td><?php echo $data['kondisi']; ?></td>
            <td><?php echo $data['keterangan']; ?></td>
            <td>
                <a href="edit.php?id=<?php echo $data['id']; ?>">Edit</a> 
                <a href="hapus.php?id=<?php echo $data['id']; ?>">Hapus</a>
            </td>
        </tr>

        <?php
        }
        ?>

    </table>

</body>
</html>