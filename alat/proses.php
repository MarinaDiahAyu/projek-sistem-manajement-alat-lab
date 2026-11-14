
<?php
include '../config/koneksi.php';

$kode_alat = $_POST['kode_alat'];
$nama_alat = $_POST['nama_alat'];
$kategori_id = $_POST['kategori_id'];
$jumlah_total = $_POST['jumlah_total'];
$jumlah_tersedia = $_POST['jumlah_tersedia'];
$kondisi = $_POST['kondisi'];
$keterangan = $_POST['keterangan'];

$query = mysqli_query($connect, "INSERT INTO alat
(kode_alat, nama_alat, kategori_id, jumlah_total, jumlah_tersedia, kondisi, keterangan)
VALUES
('$kode_alat', '$nama_alat', '$kategori_id', '$jumlah_total', '$jumlah_tersedia', '$kondisi', '$keterangan')");

if ($query) {
    echo "Proses input berhasil!";
    echo "<a href='index.php'> Lihat Data Alat</a>";
} else {
    echo "Proses input gagal: " . mysqli_error($connect);
}
?>