
<?php
include "../config/koneksi.php";

$id = (int) $_GET['id'];

$query = mysqli_query($connect, "SELECT * FROM peminjaman WHERE id = '$id'");
$data = mysqli_fetch_array($query);

if (!$data) {
    die("Data peminjaman tidak ditemukan!");
}

$queryDetail = mysqli_query($connect, "SELECT * FROM detail_peminjaman WHERE peminjaman_id = '$id'");

if ($data['status'] == 'Dipinjam' || $data['status'] == 'Terlambat') {

    while ($detail = mysqli_fetch_array($queryDetail)) {
        $alat_id = $detail['alat_id'];
        $jumlah = $detail['jumlah'];

        mysqli_query($connect, "UPDATE alat SET jumlah_tersedia = jumlah_tersedia + $jumlah WHERE id = '$alat_id'");
    }
}

$queryHapusDetail = mysqli_query($connect, "DELETE FROM detail_peminjaman WHERE peminjaman_id = '$id'");

if ($queryHapusDetail) {

    $queryHapus = mysqli_query($connect, "DELETE FROM peminjaman WHERE id = '$id'");

    if ($queryHapus) {
       header("Location: index.php");
       exit;
    } else {
        echo "Gagal menghapus peminjaman: " . mysqli_error($connect);
    }

} else {
    echo "Gagal menghapus detail peminjaman: " . mysqli_error($connect);
}
?>
