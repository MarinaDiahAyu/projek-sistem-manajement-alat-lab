
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
        p.kode_pinjam, pm.nama, pm.nim, a.nama_alat,
        dp.jumlah, p.tanggal_pinjam, p.tanggal_kembali_rencana,
        p.tanggal_kembali_aktual, p.status
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="d-flex min-vh-100">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Konten -->
    <main class="flex-grow-1 p-4" style="min-width: 0;">

        <h2 class="fw-bold">Laporan Peminjaman</h2>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
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
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;

                            if (mysqli_num_rows($query) > 0) {
                                while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($data['kode_pinjam']); ?></td>
                                <td><?= htmlspecialchars($data['nama']); ?></td>
                                <td><?= htmlspecialchars($data['nim']); ?></td>
                                <td><?= htmlspecialchars($data['nama_alat']); ?></td>
                                <td><?= $data['jumlah']; ?></td>
                                <td><?= $data['tanggal_pinjam']; ?></td>
                                <td><?= $data['tanggal_kembali_rencana']; ?></td>
                                <td><?= $data['tanggal_kembali_aktual'] ?: '-'; ?></td>
                                <td>
                                    <?php
                                    $warna = 'secondary';

                                    if ($data['status'] == 'Dipinjam') {
                                        $warna = 'warning';
                                    } elseif ($data['status'] == 'Terlambat') {
                                        $warna = 'danger';
                                    } elseif ($data['status'] == 'Dikembalikan') {
                                        $warna = 'success';
                                    }
                                    ?>
                                    <span class="badge text-bg-<?= $warna; ?>">
                                        <?= htmlspecialchars($data['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    Belum ada data peminjaman.
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </main>
</div>

</body>
</html>
