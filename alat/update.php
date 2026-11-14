
<?php
include '../config/koneksi.php';

$id = $_POST['id'];
$kode_alat = $_POST['kode_alat'];
$nama_alat = $_POST['nama_alat'];
$kategori_id = $_POST['kategori_id'];
$jumlah_total = $_POST['jumlah_total'];
$jumlah_tersedia = $_POST['jumlah_tersedia'];
$kondisi = $_POST['kondisi'];
$keterangan = $_POST['keterangan'];

$query = mysqli_query($connect, "UPDATE alat SET
    kode_alat='$kode_alat',
    nama_alat='$nama_alat',
    kategori_id='$kategori_id',
    jumlah_total='$jumlah_total',
    jumlah_tersedia='$jumlah_tersedia',
    kondisi='$kondisi',
    keterangan='$keterangan'
    WHERE id='$id'");

if ($query) {
    echo "Proses update berhasil!";
    echo "<br>";
    echo "<a href='index.php'>Lihat Data Alat</a>";
} else {
    echo "Proses update gagal: " . mysqli_error($connect);
}
?>
