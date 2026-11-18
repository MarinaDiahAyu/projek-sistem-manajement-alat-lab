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
        $stmt = mysqli_prepare($connect, "SELECT status FROM peminjaman WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($hasil);

        if (!$data) {
            throw new Exception("Data peminjaman tidak ditemukan!");
        }

        // Kembalikan stok jika masih dipinjam
        if ($data['status'] == 'Dipinjam' || $data['status'] == 'Terlambat') {
            $stmt = mysqli_prepare($connect, "
                SELECT alat_id, jumlah FROM detail_peminjaman
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
                mysqli_stmt_bind_param($stmtStok, "ii", $item['jumlah'], $item['alat_id']);
                mysqli_stmt_execute($stmtStok);
            }
        }

        // Hapus detail dan data peminjaman
        $stmt = mysqli_prepare($connect, "DELETE FROM detail_peminjaman WHERE peminjaman_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $stmt = mysqli_prepare($connect, "DELETE FROM peminjaman WHERE id = ?");
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

// Ambil data peminjaman
$query = mysqli_query($connect, "
    SELECT peminjaman.*, peminjam.nama, alat.nama_alat, detail_peminjaman.jumlah
    FROM peminjaman
    JOIN peminjam ON peminjaman.peminjam_id = peminjam.id
    JOIN detail_peminjaman ON peminjaman.id = detail_peminjaman.peminjaman_id
    JOIN alat ON detail_peminjaman.alat_id = alat.id
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="d-flex min-vh-100">

    <!-- Sidebar -->
    <aside class="bg-dark text-white p-3 flex-shrink-0" style="width: 230px;">
        <h3 class="text-center mb-4">SIMALAB</h3>

        <nav class="nav flex-column gap-2">
            <a href="index.php" class="nav-link text-white">Dashboard</a>
            <a href="alat.php" class="nav-link text-white">Data Alat</a>
            <a href="peminjaman.php" class="nav-link text-white bg-secondary rounded">Data Peminjaman</a>
            <a href="pengembalian.php" class="nav-link text-white">Data Pengembalian</a>
            <a href="laporan.php" class="nav-link text-white">Laporan</a>
            <a href="profil.php" class="nav-link text-white">Profil</a>
            <a href="../auth/logout.php" class="nav-link text-white">Logout</a>
        </nav>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-grow-1 p-4" style="min-width: 0;">

        <h2 class="fw-bold mb-1">Data Peminjaman</h2>
        

        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus') { ?>
            <div class="alert alert-success alert-dismissible fade show">
                Data peminjaman berhasil dihapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <?php if ($pesan != "") { ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars($pesan); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Daftar Peminjaman</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
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
                        </thead>

                        <tbody>
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
                                    <td>
                                        <span class="badge bg-<?php
                                            echo $data['status'] == 'Dikembalikan' ? 'success' :
                                                ($data['status'] == 'Terlambat' ? 'danger' : 'warning text-dark');
                                        ?>">
                                            <?php echo htmlspecialchars($data['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="peminjaman.php?hapus=<?php echo $data['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus data peminjaman ini?')">
                                            Hapus
                                        </a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
