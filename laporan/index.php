
<?php
include '../config/koneksi.php';

$query = mysqli_query($connect, "SELECT
    p.kode_pinjam,
    pm.nama,
    pm.nim,
    a.nama_alat,
    dp.jumlah,
    p.tanggal_pinjam,
    p.tanggal_kembali_rencana,
    p.tanggal_kembali_aktual,
    p.status
    FROM peminjaman p
    JOIN peminjam pm ON p.peminjam_id = pm.id
    JOIN detail_peminjaman dp ON p.id = dp.peminjaman_id
    JOIN alat a ON dp.alat_id = a.id
");

if (!$query) {
    die("Query gagal: " . mysqli_error($connect));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>
</head>
<body>

    <h2>Laporan Peminjaman Alat Laboratorium</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>No</th>
            <th>Kode Pinjam</th>
            <th>Nama Peminjam</th>
            <th>NIM</th>
            <th>Nama Alat</th>
            <th>Jumlah</th>
            <th>Tanggal Pinjam</th>
            <th>Rencana Kembali</th>
            <th>Kembali Aktual</th>
            <th>Status</th>
        </tr>

        <?php
        $no = 1;

        while ($data = mysqli_fetch_array($query)) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $data['kode_pinjam']; ?></td>
            <td><?php echo $data['nama']; ?></td>
            <td><?php echo $data['nim']; ?></td>
            <td><?php echo $data['nama_alat']; ?></td>
            <td><?php echo $data['jumlah']; ?></td>
            <td><?php echo $data['tanggal_pinjam']; ?></td>
            <td><?php echo $data['tanggal_kembali_rencana']; ?></td>
            <td><?php echo $data['tanggal_kembali_aktual']; ?></td>
            <td><?php echo $data['status']; ?></td>
        </tr>

        <?php
        }
        ?>

    </table>

</body>
</html>