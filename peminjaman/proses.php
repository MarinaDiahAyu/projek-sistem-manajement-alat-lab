
<?php
include "../config/koneksi.php";

$peminjam_id = $_POST['peminjam_id'];
$alat_id = $_POST['alat_id'];
$jumlah = $_POST['jumlah'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$tanggal_kembali_rencana = $_POST['tanggal_kembali_rencana'];

$jumlah = (int) $jumlah;

$queryAlat = mysqli_query($connect, "SELECT * FROM alat WHERE id = '$alat_id'");
$alat = mysqli_fetch_array($queryAlat);

if ($peminjam_id == "" || $alat_id == "" || $jumlah < 1 || $tanggal_pinjam == "" || $tanggal_kembali_rencana == "") {
    echo "Semua data harus diisi!";
} else if (!$alat) {
    echo "Data alat tidak ditemukan!";
} else if ($jumlah > $alat['jumlah_tersedia']) {
    echo "Stok alat tidak mencukupi!";
} else {

    $queryPeminjaman = mysqli_query($connect, "INSERT INTO peminjaman(kode_pinjam, peminjam_id, tanggal_pinjam, tanggal_kembali_rencana, status) VALUES('', '$peminjam_id', '$tanggal_pinjam', '$tanggal_kembali_rencana', 'Dipinjam')");

    if ($queryPeminjaman) {
        $peminjaman_id = mysqli_insert_id($connect);

        $tahun = date("Y");
        $kode_pinjam = "PJM-" . $tahun . "-" . str_pad($peminjaman_id, 3, "0", STR_PAD_LEFT);

        mysqli_query($connect, "UPDATE peminjaman SET kode_pinjam = '$kode_pinjam' WHERE id = '$peminjaman_id'");

        $queryDetail = mysqli_query($connect, "INSERT INTO detail_peminjaman (peminjaman_id, alat_id, jumlah) VALUES ('$peminjaman_id', '$alat_id', '$jumlah')");

        if ($queryDetail) {
            mysqli_query($connect, "UPDATE alat SET jumlah_tersedia = jumlah_tersedia - $jumlah WHERE id = '$alat_id'");

            echo "Peminjaman berhasil disimpan!";
            echo "<br><a href='index.php'>Lihat Data Peminjaman</a>";

        } else {
            echo "Gagal menyimpan detail peminjaman: " . mysqli_error($connect);
        }
    } else {
        echo "Gagal menyimpan peminjaman: " . mysqli_error($connect);
    }
}
?>
