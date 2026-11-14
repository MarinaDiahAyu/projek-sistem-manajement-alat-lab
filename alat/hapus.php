<?php
include '../config/koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($connect, "DELETE FROM alat WHERE id='$id'");

if ($query) {
    echo "Proses hapus berhasil!";
    echo "<a href='index.php'> Lihat Data Alat</a>";
} else {
    echo "Proses hapus gagal";
}
?>