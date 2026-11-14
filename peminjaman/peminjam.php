<?php
include "../config/koneksi.php";

if(isset($_POST['submit'])){

    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $kelas = $_POST['kelas'];
    $no_hp = $_POST['no_hp'];

    $query = mysqli_query($connect, "INSERT INTO peminjam (nama, nim, kelas, no_hp) VALUES ('$nama', '$nim', '$kelas', '$no_hp')");

    if($query){
        echo "Data peminjam berhasil ditambahkan !";
        echo "<br><a href='tambah.php'>Kembali ke Halaman Peminjaman</a>";
    }else{
        echo "Gagal menambahkan peminjam: " . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjam</title>
</head>
<body>

    <h2>Tambah Data Peminjam</h2>

    <form method="POST">

        Nama:<br>
        <input type="text" name="nama">
        <br><br>

        NIM:<br>
        <input type="text" name="nim">
        <br><br>

        Kelas:<br>
        <input type="text" name="kelas">
        <br><br>

        Nomor HP:<br>
        <input type="text" name="no_hp">
        <br><br>

        <input type="submit" name="submit" value="Simpan">
    </form>
</body>
</html>



