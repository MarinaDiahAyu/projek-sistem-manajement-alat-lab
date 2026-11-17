
<?php
session_start();
include "../config/koneksi.php";

// Cek hak akses Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pesan = "";

// Proses hapus peminjaman
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    mysqli_begin_transaction($connect);

    try {
        // Ambil data peminjaman
        $stmt = mysqli_prepare($connect, "
            SELECT status FROM peminjaman WHERE id = ?
        ");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($hasil);

        if (!$data) {
            throw new Exception("Data peminjaman tidak ditemukan!");
        }

        // Jika masih dipinjam, stok dikembalikan
        if ($data['status'] == 'Dipinjam' || $data['status'] == 'Terlambat') {

            $stmt = mysqli_prepare($connect, "
                SELECT alat_id, jumlah
                FROM detail_peminjaman
                WHERE peminjaman_id = ?
            ");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $detail = mysqli_stmt_get_result($stmt);

            while ($item = mysqli_fetch_assoc($detail)) {
                $stmtStok = mysqli_prepare($connect, "
                    UPDATE alat
                    SET jumlah_tersedia = jumlah_tersedia + ?
                    WHERE id = ?
                ");

                mysqli_stmt_bind_param(
                    $stmtStok, "ii",
                    $item['jumlah'], $item['alat_id']
                );

                mysqli_stmt_execute($stmtStok);
            }
        }

        // Hapus detail peminjaman
        $stmt = mysqli_prepare($connect, "
            DELETE FROM detail_peminjaman WHERE peminjaman_id = ?
        ");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        // Hapus data peminjaman
        $stmt = mysqli_prepare($connect, "
            DELETE FROM peminjaman WHERE id = ?
        ");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        mysqli_commit($connect);

        header("Location: peminjaman.php?pesan=hapus");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($connect);
        $pesan = $e->getMessage();
    }
}

// Ambil seluruh data peminjaman
$query = mysqli_query($connect, "
    SELECT
        peminjaman.*,
        peminjam.nama,
        alat.nama_alat,
        detail_peminjaman.jumlah
    FROM peminjaman
    JOIN peminjam
        ON peminjaman.peminjam_id = peminjam.id
    JOIN detail_peminjaman
        ON peminjaman.id = detail_peminjaman.peminjaman_id
    JOIN alat
        ON detail_peminjaman.alat_id = alat.id
    ORDER BY peminjaman.id DESC
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
    <title>Data Peminjaman - SIMALAB</title>

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

        h1 {
            color: #1e293b;
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
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #e2e8f0;
        }

        .hapus {
            color: #dc2626;
        }

        .pesan {
            background: #e0f2fe;
            padding: 12px;
            border-radius: 5px;
            margin-top: 15px;
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
    <a href="peminjaman.php" class="active">Data Peminjaman</a>
    <a href="pengembalian.php">Data Pengembalian</a>
    <a href="laporan.php">Laporan</a>
    <a href="profil.php">Profil</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="main">

    <h1>Data Peminjaman</h1>
    <p>Daftar seluruh peminjaman alat laboratorium.</p>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus') { ?>
        <div class="pesan">
            Data peminjaman berhasil dihapus!
        </div>
    <?php } ?>

    <?php if ($pesan != "") { ?>
        <div class="pesan">
            <?php echo htmlspecialchars($pesan); ?>
        </div>
    <?php } ?>

    <div class="table-box">

        <table>
            <tr>
                <th>No</th>
                <th>Kode Peminjaman</th>
                <th>Nama Peminjam</th>
                <th>Nama Alat</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Rencana Pengembalian</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php
            $no = 1;

            while ($data = mysqli_fetch_assoc($query)) {
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($data['kode_pinjam']); ?></td>
                    <td><?php echo htmlspecialchars($data['nama']); ?></td>
                    <td><?php echo htmlspecialchars($data['nama_alat']); ?></td>
                    <td><?php echo $data['jumlah']; ?></td>
                    <td><?php echo $data['tanggal_pinjam']; ?></td>
                    <td><?php echo $data['tanggal_kembali_rencana']; ?></td>
                    <td><?php echo htmlspecialchars($data['status']); ?></td>

                    <td>
                        <a class="hapus"
                           href="peminjaman.php?hapus=<?php echo $data['id']; ?>"
                           onclick="return confirm('Yakin ingin menghapus data peminjaman ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>
