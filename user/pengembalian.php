<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['peminjam_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$query = mysqli_query($connect, "
    SELECT
        peminjaman.id,
        peminjaman.kode_pinjam,
        peminjam.nama,
        alat.nama_alat,
        detail_peminjaman.jumlah,
        peminjaman.tanggal_kembali_rencana,
        peminjaman.status
    FROM peminjaman
    JOIN peminjam
        ON peminjaman.peminjam_id = peminjam.id
    JOIN detail_peminjaman
        ON peminjaman.id = detail_peminjaman.peminjaman_id
    JOIN alat
        ON detail_peminjaman.alat_id = alat.id
    WHERE peminjaman.status IN ('Dipinjam', 'Terlambat')
");

if (!$query) {
    die("Query gagal: " . mysqli_error($connect));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengembalian Alat</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    <h1>Daftar Alat yang Perlu Dikembalikan</h1>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>No</th>
            <th>Kode Pinjam</th>
            <th>Nama Peminjam</th>
            <th>Nama Alat</th>
            <th>Jumlah</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_array($query)) {
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $data['kode_pinjam']; ?></td>
            <td><?php echo $data['nama']; ?></td>
            <td><?php echo $data['nama_alat']; ?></td>
            <td><?php echo $data['jumlah']; ?></td>
            <td><?php echo $data['tanggal_kembali_rencana']; ?></td>
            <td><?php echo $data['status']; ?></td>
            <td>
                <a href="ajukan_pengembalian.php?id=<?php echo $data['id']; ?>">
                    Ajukan Pengembalian
                </a>
            </td>
        </tr>
        <?php
            }
        } else {
        ?>
        <tr>
            <td colspan="8">Tidak ada alat yang sedang dipinjam.</td>
        </tr>
        <?php } ?>
    </table>
</main>

</body>
</html>