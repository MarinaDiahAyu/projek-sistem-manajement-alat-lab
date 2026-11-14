
<?php
include "../config/koneksi.php";

$query = mysqli_query($connect, "
    SELECT
        peminjaman.*,
        peminjam.nama,
        alat.nama_alat,
        detail_peminjaman.jumlah
    FROM peminjaman
    JOIN peminjam
        ON peminjaman.peminjam_id = peminjam.id
    JOIN detail_peminjaman
        ON peminjaman.id = detail_peminjaman.peminjaman_id
    JOIN alat
        ON detail_peminjaman.alat_id = alat.id
    WHERE peminjaman.status = 'Dipinjam'
       OR peminjaman.status = 'Terlambat'
");

if (!$query) {
    die("Query gagal: " . mysqli_error($connect));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengembalian</title>
</head>
<body>

    <h2>Data Pengembalian Alat</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Kode Peminjaman</th>
            <th>Nama Peminjam</th>
            <th>Nama Alat</th>
            <th>Jumlah</th>
            <th>Rencana Pengembalian</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;
        while ($data = mysqli_fetch_array($query)) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $data['kode_pinjam']; ?></td>
            <td><?php echo $data['nama']; ?></td>
            <td><?php echo $data['nama_alat']; ?></td>
            <td><?php echo $data['jumlah']; ?></td>
            <td><?php echo $data['tanggal_kembali_rencana']; ?></td>
            <td>
                <a href="form.php?id=<?php echo $data['id']; ?>">
                    Proses Pengembalian
                </a>
            </td>
        </tr>

        <?php } ?>

    </table>

    <br>
    <a href="../peminjaman/index.php">Kembali ke Peminjaman</a>

</body>
</html>
