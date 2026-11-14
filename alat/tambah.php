
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Alat</title>
</head>
<body>

    <h2>FORM TAMBAH ALAT LABORATORIUM</h2>

    <form action="proses.php" method="post">

        <label>Kode Alat</label><br>
        <input type="text" name="kode_alat">
        <br><br>

        <label>Nama Alat</label><br>
        <input type="text" name="nama_alat">
        <br><br>

        <label>Kategori ID</label><br>
        <input type="number" name="kategori_id">
        <br><br>

        <label>Jumlah Total</label><br>
        <input type="number" name="jumlah_total">
        <br><br>

        <label>Jumlah Tersedia</label><br>
        <input type="number" name="jumlah_tersedia">
        <br><br>

        <label>Kondisi</label><br>
        <select name="kondisi">
            <option value="">-- Pilih Kondisi --</option>
            <option value="Baik">Baik</option>
            <option value="Rusak Ringan">Rusak Ringan</option>
            <option value="Rusak Berat">Rusak Berat</option>
        </select>
        <br><br>

        <label>Keterangan</label><br>
        <textarea name="keterangan"></textarea>
        <br><br>

        <button type="submit" name="simpan">Simpan</button>
        <a href="index.php">Kembali</a>

    </form>
<a href="index.php">kembali</a>
</body>
</html>