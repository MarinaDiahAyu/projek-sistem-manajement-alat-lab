
<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['peminjam_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Alat SIMALAB</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">

    <h1>Data Alat Laboratorium yang Tersedia</h1>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>No</th>
            <th>Kode Alat</th>
            <th>Nama Alat</th>
            <th>Jumlah Total</th>
            <th>Jumlah Tersedia</th>
            <th>Kondisi</th>
        </tr>

        <?php
        $no = 1;
        $query = mysqli_query($connect, "SELECT * FROM alat");

        while ($data = mysqli_fetch_array($query)) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $data['kode_alat']; ?></td>
            <td><?php echo $data['nama_alat']; ?></td>
            <td><?php echo $data['jumlah_total']; ?></td>
            <td><?php echo $data['jumlah_tersedia']; ?></td>
            <td><?php echo $data['kondisi']; ?></td>
        </tr>

        <?php } ?>

    </table>
</main>

</body>
</html>