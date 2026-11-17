<?php
include '../config/koneksi.php';

if (isset($_POST['submit'])) {

    $nama = mysqli_real_escape_string($connect, $_POST['nama']);
    $nim = mysqli_real_escape_string($connect, $_POST['nim']);
    $kelas = mysqli_real_escape_string($connect, $_POST['kelas']);
    $no_hp = mysqli_real_escape_string($connect, $_POST['no_hp']);

    $alat_id = (int) $_POST['alat_id'];
    $jumlah = (int) $_POST['jumlah'];

    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    if ($jumlah < 1 || $alat_id < 1) {
        echo "Pilihan alat atau jumlah tidak valid.";
    } elseif ($tanggal_kembali < $tanggal_pinjam) {
        echo "Tanggal kembali tidak boleh sebelum tanggal pinjam.";
    } else {

        mysqli_begin_transaction($connect);

        // Mengurangi stok jika stok masih mencukupi
        $update = mysqli_query($connect, "
            UPDATE alat
            SET jumlah_tersedia = jumlah_tersedia - $jumlah
            WHERE id = $alat_id
            AND jumlah_tersedia >= $jumlah
        ");

        if ($update && mysqli_affected_rows($connect) > 0) {

            // Menyimpan data peminjam
            $query1 = mysqli_query($connect, "
                INSERT INTO peminjam (nama, nim, kelas, no_hp)
                VALUES ('$nama', '$nim', '$kelas', '$no_hp')
            ");

            if ($query1) {

                $peminjam_id = mysqli_insert_id($connect);
                $kode_pinjam = "PJM" . date("ymdHis");

                // Menyimpan transaksi peminjaman
                $query2 = mysqli_query($connect, "
                    INSERT INTO peminjaman
                    (kode_pinjam, peminjam_id, tanggal_pinjam, tanggal_kembali_rencana)
                    VALUES
                    ('$kode_pinjam', '$peminjam_id', '$tanggal_pinjam', '$tanggal_kembali')
                ");

                if ($query2) {

                    $peminjaman_id = mysqli_insert_id($connect);

                    // Menyimpan detail alat
                    $query3 = mysqli_query($connect, "
                        INSERT INTO detail_peminjaman
                        (peminjaman_id, alat_id, jumlah)
                        VALUES
                        ('$peminjaman_id', '$alat_id', '$jumlah')
                    ");

                    if ($query3) {
                        mysqli_commit($connect);
                        echo "Pengajuan peminjaman berhasil!";
                    } else {
                        mysqli_rollback($connect);
                        echo "Gagal menyimpan detail alat.";
                    }

                } else {
                    mysqli_rollback($connect);
                    echo "Gagal menyimpan peminjaman.";
                }

            } else {
                mysqli_rollback($connect);
                echo "Gagal menyimpan data peminjam.";
            }

        } else {
            mysqli_rollback($connect);
            echo "Stok alat tidak mencukupi atau alat tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajukan Peminjaman</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">

    <h1>Ajukan Peminjaman Alat</h1>

    <form method="POST">

        <h3>Data Peminjam</h3>

        Nama:<br>
        <input type="text" name="nama" required>
        <br><br>

        NIM:<br>
        <input type="text" name="nim" required>
        <br><br>

        Kelas:<br>
        <input type="text" name="kelas" required>
        <br><br>

        Nomor HP:<br>
        <input type="text" name="no_hp" required>
        <br><br>

        <h3>Data Peminjaman</h3>

        Pilih Alat:<br>
        <select name="alat_id" required>
            <option value="">-- Pilih Alat --</option>

            <?php
            $alat = mysqli_query($connect, "SELECT * FROM alat WHERE jumlah_tersedia > 0");

            while ($data = mysqli_fetch_array($alat)) {
            ?>
                <option value="<?php echo $data['id']; ?>">
                    <?php echo $data['nama_alat']; ?>
                </option>
            <?php } ?>

        </select>
        <br><br>

        Jumlah:<br>
        <input type="number" name="jumlah" min="1" required>
        <br><br>

        Tanggal Pinjam:<br>
        <input type="date" name="tanggal_pinjam" required>
        <br><br>

        Tanggal Rencana Kembali:<br>
        <input type="date" name="tanggal_kembali" required>
        <br><br>

        <button type="submit" name="submit">Ajukan Peminjaman</button>

    </form>
</main>
</body>
</html>