
<?php
include '../config/koneksi.php';

$query = mysqli_query($connect, "
    SELECT 
        peminjaman.kode_pinjam,
        peminjam.nama,
        alat.nama_alat,
        detail_peminjaman.jumlah,
        peminjaman.tanggal_pinjam,
        peminjaman.tanggal_kembali_rencana,
        peminjaman.status
    FROM peminjaman
    JOIN peminjam ON peminjaman.peminjam_id = peminjam.id
    JOIN detail_peminjaman ON peminjaman.id = detail_peminjaman.peminjaman_id
    JOIN alat ON detail_peminjaman.alat_id = alat.id
    ORDER BY peminjaman.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">

    <h1>Riwayat Peminjaman</h1>
    <p>Daftar riwayat peminjaman alat laboratorium.</p>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>No</th>
            <th>Kode Pinjam</th>
            <th>Nama Peminjam</th>
            <th>Nama Alat</th>
            <th>Jumlah</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
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
            <td><?php echo $data['nama_alat']; ?></td>
            <td><?php echo $data['jumlah']; ?></td>
            <td><?php echo $data['tanggal_pinjam']; ?></td>
            <td><?php echo $data['tanggal_kembali_rencana']; ?></td>
            <td><?php echo $data['status']; ?></td>
        </tr>

        <?php } ?>

    </table>

</main>

</body>
</html>