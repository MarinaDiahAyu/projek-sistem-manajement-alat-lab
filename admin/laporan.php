
<?php
session_start();
include "../config/koneksi.php";

// Cek hak akses Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data laporan peminjaman
$query = mysqli_query($connect, "
    SELECT
        p.kode_pinjam,
        pm.nama,
        pm.nim,
        a.nama_alat,
        dp.jumlah,
        p.tanggal_pinjam,
        p.tanggal_kembali_rencana,
        p.tanggal_kembali_aktual,
        p.status
    FROM peminjaman p
    JOIN peminjam pm ON p.peminjam_id = pm.id
    JOIN detail_peminjaman dp ON p.id = dp.peminjaman_id
    JOIN alat a ON dp.alat_id = a.id
    ORDER BY p.id DESC
");

if (!$query) {
    die("Query gagal: " . mysqli_error($connect));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - SIMALAB</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
            display: flex;
        }

        .sidebar {
            width: 230px;
            min-height: 100vh;
            background: #1e293b;
            padding: 25px 15px;
            color: white;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px;
            margin-bottom: 8px;
            border-radius: 6px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #334155;
        }

        .main {
            flex: 1;
            padding: 30px;
            min-width: 0;
        }

        .table-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 25px;
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #e2e8f0;
        }

        @media (max-width: 800px) {
            body {
                display: block;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h2>SIMALAB</h2>

    <a href="index.php">Dashboard</a>
    <a href="alat.php">Data Alat</a>
    <a href="peminjaman.php">Data Peminjaman</a>
    <a href="pengembalian.php">Data Pengembalian</a>
    <a href="laporan.php" class="active">Laporan</a>
    <a href="profil.php">Profil</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="main">

    <h1>Laporan Peminjaman</h1>
    <p>Laporan seluruh aktivitas peminjaman alat laboratorium.</p>

    <div class="table-box">

        <table>
            <tr>
                <th>No</th>
                <th>Kode Pinjam</th>
                <th>Nama Peminjam</th>
                <th>NIM</th>
                <th>Nama Alat</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Rencana Kembali</th>
                <th>Kembali Aktual</th>
                <th>Status</th>
            </tr>

            <?php
            $no = 1;

            while ($data = mysqli_fetch_assoc($query)) {
            ?>

            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($data['kode_pinjam']); ?></td>
                <td><?php echo htmlspecialchars($data['nama']); ?></td>
                <td><?php echo htmlspecialchars($data['nim']); ?></td>
                <td><?php echo htmlspecialchars($data['nama_alat']); ?></td>
                <td><?php echo $data['jumlah']; ?></td>
                <td><?php echo $data['tanggal_pinjam']; ?></td>
                <td><?php echo $data['tanggal_kembali_rencana']; ?></td>
                <td><?php echo $data['tanggal_kembali_aktual'] ?: '-'; ?></td>
                <td><?php echo htmlspecialchars($data['status']); ?></td>
            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>
