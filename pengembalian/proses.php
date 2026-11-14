
<?php
include "../config/koneksi.php";

$id = (int) $_POST['id'];
$tanggal_kembali_aktual = $_POST['tanggal_kembali_aktual'];
$kondisi_kembali = $_POST['kondisi_kembali'];
$catatan = $_POST['catatan'];

if ($id < 1 || $tanggal_kembali_aktual == "" || $kondisi_kembali == ""){
    die("Semua data wajib diisi!");
}

if (
    $kondisi_kembali != 'Baik' &&
    $kondisi_kembali != 'Rusak Ringan' &&
    $kondisi_kembali != 'Rusak Berat'
) {
    die("Kondisi alat tidak valid!");
}

$queryPeminjaman = mysqli_query($connect, "SELECT * FROM peminjaman WHERE id = '$id'
");

$peminjaman = mysqli_fetch_array($queryPeminjaman);

if (!$peminjaman) {
    die("Data peminjaman tidak ditemukan!");
}

if (
    $peminjaman['status'] != 'Dipinjam' &&
    $peminjaman['status'] != 'Terlambat'
) {
    die("Peminjaman ini sudah dikembalikan!");
}

$queryDetail = mysqli_query($connect, "SELECT * FROM detail_peminjaman WHERE peminjaman_id = '$id'
");

if (!$queryDetail || mysqli_num_rows($queryDetail) == 0) {
    die("Detail peminjaman tidak ditemukan!");
}

$catatan = mysqli_real_escape_string($connect, $catatan);

$queryUpdate = mysqli_query($connect, "UPDATE peminjaman SET tanggal_kembali_aktual = '$tanggal_kembali_aktual', status = 'Dikembalikan', kondisi_kembali = '$kondisi_kembali', catatan = '$catatan' WHERE id = '$id' AND status IN ('Dipinjam', 'Terlambat')");

if ($queryUpdate && mysqli_affected_rows($connect) > 0) {

    if ($kondisi_kembali == 'Baik') {

        while ($detail = mysqli_fetch_array($queryDetail)) {

            $alat_id = $detail['alat_id'];
            $jumlah = $detail['jumlah'];

            mysqli_query($connect, "UPDATE alatSET jumlah_tersedia = jumlah_tersedia + $jumlahWHERE id = '$alat_id'");
        }
    }

    header("Location: index.php");
    exit;

} else {
    echo "Gagal menyimpan pengembalian: " . mysqli_error($connect);
}
?>
