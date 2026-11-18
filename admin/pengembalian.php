
<?php
session_start();
include "../config/koneksi.php";

// Cek hak akses Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pesan = "";

// Proses penyimpanan pengembalian
if (isset($_POST['simpan'])) {
    $id = (int) $_POST['id'];
    $tanggal = $_POST['tanggal_kembali_aktual'];
    $kondisi = $_POST['kondisi_kembali'];
    $catatan = $_POST['catatan'];

    if ($id < 1 || $tanggal == "" || $kondisi == "") {
        $pesan = "Semua data wajib diisi!";
    } elseif (!in_array($kondisi, ['Baik', 'Rusak Ringan', 'Rusak Berat'])) {
        $pesan = "Kondisi alat tidak valid!";
    } else {
        mysqli_begin_transaction($connect);

        try {
            // Cek data peminjaman
            $stmt = mysqli_prepare($connect, "
                SELECT status FROM peminjaman
                WHERE id = ? FOR UPDATE
            ");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $hasil = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_assoc($hasil);

            if (!$data) {
                throw new Exception("Data peminjaman tidak ditemukan!");
            }

            if (!in_array($data['status'], ['Dipinjam', 'Terlambat'])) {
                throw new Exception("Peminjaman ini sudah dikembalikan!");
            }

            // Ambil detail alat
            $stmt = mysqli_prepare($connect, "
                SELECT alat_id, jumlah
                FROM detail_peminjaman
                WHERE peminjaman_id = ?
            ");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $detail = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($detail) == 0) {
                throw new Exception("Detail peminjaman tidak ditemukan!");
            }

            // Simpan data pengembalian
            $stmt = mysqli_prepare($connect, "
                UPDATE peminjaman
                SET tanggal_kembali_aktual = ?,
                    status = 'Dikembalikan',
                    kondisi_kembali = ?,
                    catatan = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param($stmt, "sssi", $tanggal, $kondisi, $catatan, $id);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal menyimpan pengembalian!");
            }

            // Stok dikembalikan jika kondisi alat Baik
            if ($kondisi == 'Baik') {
                while ($item = mysqli_fetch_assoc($detail)) {
                    $stmtStok = mysqli_prepare($connect, "
                        UPDATE alat
                        SET jumlah_tersedia = jumlah_tersedia + ?
                        WHERE id = ?
                    ");

                    mysqli_stmt_bind_param($stmtStok, "ii", $item['jumlah'], $item['alat_id']);

                    if (!mysqli_stmt_execute($stmtStok)) {
                        throw new Exception("Gagal memperbarui stok alat!");
                    }
                }
            }

            mysqli_commit($connect);
            header("Location: pengembalian.php?pesan=berhasil");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($connect);
            $pesan = $e->getMessage();
        }
    }
}

// Ambil data untuk formulir pengembalian
$dataForm = null;

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = mysqli_prepare($connect, "
        SELECT peminjaman.*, peminjam.nama
        FROM peminjaman
        JOIN peminjam ON peminjaman.peminjam_id = peminjam.id
        WHERE peminjaman.id = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $dataForm = mysqli_fetch_assoc($hasil);
}

// Ambil daftar peminjaman yang belum dikembalikan
$query = mysqli_query($connect, "
    SELECT peminjaman.*, peminjam.nama, alat.nama_alat,
           detail_peminjaman.jumlah
    FROM peminjaman
    JOIN peminjam ON peminjaman.peminjam_id = peminjam.id
    JOIN detail_peminjaman ON peminjaman.id = detail_peminjaman.peminjaman_id
    JOIN alat ON detail_peminjaman.alat_id = alat.id
    WHERE peminjaman.status IN ('Dipinjam', 'Terlambat')
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
    <title>Pengembalian - SIMALAB</title>

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
            <a href="peminjaman.php" class="nav-link text-white">Data Peminjaman</a>
            <a href="pengembalian.php" class="nav-link text-white bg-secondary rounded">Data Pengembalian</a>
            <a href="laporan.php" class="nav-link text-white">Laporan</a>
            <a href="profil.php" class="nav-link text-white">Profil</a>
            <a href="../auth/logout.php" class="nav-link text-white">Logout</a>
        </nav>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-grow-1 p-4" style="min-width: 0;">

        <h2 class="fw-bold mb-1">Data Pengembalian Alat</h2>
        <p class="text-muted mb-4">Kelola pengembalian alat laboratorium.</p>

        <!-- Notifikasi -->
        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil') { ?>
            <div class="alert alert-success alert-dismissible fade show">
                Pengembalian berhasil disimpan!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <?php if ($pesan != "") { ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars($pesan); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <!-- Form Pengembalian -->
        <?php if (isset($_GET['id']) && $dataForm) { ?>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Form Pengembalian</h5>
                </div>

                <div class="card-body">

                    <p>
                        **Kode Peminjaman:**
                        <?php echo htmlspecialchars($dataForm['kode_pinjam']); ?>
                    </p>

                    <p>
                        **Nama Peminjam:**
                        <?php echo htmlspecialchars($dataForm['nama']); ?>
                    </p>

                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $dataForm['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pengembalian</label>
                            <input type="date" name="tanggal_kembali_aktual"
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kondisi Alat</label>
                            <select name="kondisi_kembali" class="form-select" required>
                                <option value="">Pilih Kondisi</option>
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-primary">
                            Simpan Pengembalian
                        </button>

                        <a href="pengembalian.php" class="btn btn-secondary">
                            Batal
                        </a>
                    </form>
                </div>
            </div>

        <?php } elseif (isset($_GET['id'])) { ?>

            <div class="alert alert-danger">
                Data peminjaman tidak ditemukan.
            </div>

        <?php } ?>

        <!-- Daftar Peminjaman -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Daftar Alat yang Belum Dikembalikan</h5>
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
                                    <td><?php echo $data['tanggal_kembali_rencana']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $data['status'] == 'Terlambat' ? 'danger' : 'warning text-dark'; ?>">
                                            <?php echo htmlspecialchars($data['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="pengembalian.php?id=<?php echo $data['id']; ?>"
                                           class="btn btn-success btn-sm">
                                            Proses Pengembalian
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
