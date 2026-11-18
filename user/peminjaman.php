<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['peminjam_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$pesan = "";
$tipe  = "error";

if (isset($_POST['submit'])) {

    $nama   = mysqli_real_escape_string($connect, $_POST['nama']);
    $nim    = mysqli_real_escape_string($connect, $_POST['nim']);
    $kelas  = mysqli_real_escape_string($connect, $_POST['kelas']);
    $no_hp  = mysqli_real_escape_string($connect, $_POST['no_hp']);

    $alat_id = (int) $_POST['alat_id'];
    $jumlah  = (int) $_POST['jumlah'];

    $tanggal_pinjam  = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    if ($jumlah < 1 || $alat_id < 1) {
        $pesan = "Pilihan alat atau jumlah tidak valid.";
    } elseif ($tanggal_kembali < $tanggal_pinjam) {
        $pesan = "Tanggal kembali tidak boleh sebelum tanggal pinjam.";
    } else {

        mysqli_begin_transaction($connect);

        $update = mysqli_query($connect, " UPDATE alat SET jumlah_tersedia = jumlah_tersedia - $jumlah WHERE id = $alat_id AND jumlah_tersedia >= $jumlah");

        if ($update && mysqli_affected_rows($connect) > 0) {

            $query1 = mysqli_query($connect, "
                INSERT INTO peminjam (nama, nim, kelas, no_hp)
                VALUES ('$nama', '$nim', '$kelas', '$no_hp')");

            if ($query1) {

                $peminjam_id = mysqli_insert_id($connect);
                $kode_pinjam = "PJM" . date("ymdHis");

                $query2 = mysqli_query($connect, "INSERT INTO peminjaman (kode_pinjam, peminjam_id, tanggal_pinjam, tanggal_kembali_rencana) VALUES ('$kode_pinjam', '$peminjam_id', '$tanggal_pinjam', '$tanggal_kembali')");

                if ($query2) {

                    $peminjaman_id = mysqli_insert_id($connect);

                    $query3 = mysqli_query($connect, "INSERT INTO detail_peminjaman (peminjaman_id, alat_id, jumlah) VALUES
                        ('$peminjaman_id', '$alat_id', '$jumlah')");

                    if ($query3) {
                        mysqli_commit($connect);
                        $pesan = "Pengajuan peminjaman berhasil!";
                        $tipe  = "sukses";
                    } else {
                        mysqli_rollback($connect);
                        $pesan = "Gagal menyimpan detail alat.";
                    }

                } else {
                    mysqli_rollback($connect);
                    $pesan = "Gagal menyimpan peminjaman.";
                }

            } else {
                mysqli_rollback($connect);
                $pesan = "Gagal menyimpan data peminjam.";
            }

        } else {
            mysqli_rollback($connect);
            $pesan = "Stok alat tidak mencukupi atau alat tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Peminjaman</title>
    <link rel="stylesheet" href="user.css?v=2">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">

    <h1>Ajukan Peminjaman Alat</h1>

    <?php if ($pesan != "") { ?>
        <div class="pesan <?php echo $tipe; ?>">
            <?php echo htmlspecialchars($pesan); ?>
        </div>
    <?php } ?>

    <form method="POST" class="form-card">

        <h3>Data Peminjam</h3>

        <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" required>
        </div>

        <div class="form-group">
            <label for="nim">NIM</label>
            <input type="text" id="nim" name="nim" required>
        </div>

        <div class="form-group">
            <label for="kelas">Kelas</label>
            <input type="text" id="kelas" name="kelas" required>
        </div>

        <div class="form-group">
            <label for="no_hp">Nomor HP</label>
            <input type="text" id="no_hp" name="no_hp" required>
        </div>

        <h3>Data Peminjaman</h3>

        <div class="form-group">
            <label for="alat_id">Pilih Alat</label>
            <select id="alat_id" name="alat_id" required>
                <option value="">-- Pilih Alat --</option>
                <?php
                $alat = mysqli_query($connect, "SELECT * FROM alat WHERE jumlah_tersedia > 0");
                while ($data = mysqli_fetch_array($alat)) {
                ?>
                    <option value="<?php echo $data['id']; ?>">
                        <?php echo htmlspecialchars($data['nama_alat']); ?>
                        (stok: <?php echo $data['jumlah_tersedia']; ?>)
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="jumlah">Jumlah</label>
            <input type="number" id="jumlah" name="jumlah" min="1" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="tanggal_pinjam">Tanggal Pinjam</label>
                <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" required>
            </div>
            <div class="form-group">
                <label for="tanggal_kembali">Tanggal Rencana Kembali</label>
                <input type="date" id="tanggal_kembali" name="tanggal_kembali" required>
            </div>
        </div>

        <button type="submit" name="submit" class="btn-submit">Ajukan Peminjaman</button>

    </form>
</main>
</body>
</html>
