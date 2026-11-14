
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peminjaman</title>
</head>
<body>

    <h2>Data Peminjaman Alat Laboratorium</h2>
    <a href="tambah.php">Tambah Peminjaman</a>

    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Kode Peminjaman</th>
            <th>Nama Peminjam</th>
            <th>Tanggal Pinjam</th>
            <th>Rencana Pengembalian</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        include "../config/koneksi.php";

        $query = mysqli_query($connect, "SELECT peminjaman.*, peminjam.nama FROM peminjaman JOIN peminjam ON peminjaman.peminjam_id = peminjam.id");

        $no = 1;

        while ($data = mysqli_fetch_array($query)) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $data['kode_pinjam']; ?></td>
            <td><?php echo $data['nama']; ?></td>
            <td><?php echo $data['tanggal_pinjam']; ?></td>
            <td><?php echo $data['tanggal_kembali_rencana']; ?></td>
            <td><?php echo $data['status']; ?></td>
            <td>
                <a href="hapus.php?id=<?php echo $data['id']; ?>"
                    onclick="return confirm('Yakin ingin menghapus peminjaman ini?')">
                    Hapus
                </a>
            </td>
        </tr>

        <?php
        }
        ?>
    </table>

</body>
</html>
