
<?php
session_start();
include "../config/koneksi.php";

// Memastikan yang membuka halaman adalah Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pesan = "";

// Menambahkan alat baru
if (isset($_POST['tambah'])) {
    $kode = $_POST['kode_alat'];
    $nama = $_POST['nama_alat'];
    $kategori = $_POST['kategori_id'];
    $total = (int) $_POST['jumlah_total'];
    $tersedia = $total;
    $kondisi = $_POST['kondisi'];
    $keterangan = $_POST['keterangan'];

    $stmt = mysqli_prepare($connect, "
        INSERT INTO alat
        (kode_alat, nama_alat, kategori_id, jumlah_total,
         jumlah_tersedia, kondisi, keterangan)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param(
        $stmt, "ssiiiss",
        $kode, $nama, $kategori, $total,
        $tersedia, $kondisi, $keterangan
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: alat.php?pesan=tambah");
        exit;
    } else {
        $pesan = "Gagal menambahkan alat. Pastikan kode alat belum digunakan.";
    }
}

// Mengubah data alat
if (isset($_POST['edit'])) {
    $id = (int) $_POST['id'];
    $kode = $_POST['kode_alat'];
    $nama = $_POST['nama_alat'];
    $kategori = $_POST['kategori_id'];
    $total = (int) $_POST['jumlah_total'];
    $tersedia = (int) $_POST['jumlah_tersedia'];
    $kondisi = $_POST['kondisi'];
    $keterangan = $_POST['keterangan'];

    if ($tersedia > $total || $tersedia < 0 || $total < 0) {
        $pesan = "Jumlah stok tidak valid.";
    } else {
        $stmt = mysqli_prepare($connect, "
            UPDATE alat SET
                kode_alat = ?,
                nama_alat = ?,
                kategori_id = ?,
                jumlah_total = ?,
                jumlah_tersedia = ?,
                kondisi = ?,
                keterangan = ?
            WHERE id = ?
        ");

        mysqli_stmt_bind_param(
            $stmt, "ssiiissi",
            $kode, $nama, $kategori, $total,
            $tersedia, $kondisi, $keterangan, $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: alat.php?pesan=edit");
            exit;
        } else {
            $pesan = "Gagal mengubah data alat.";
        }
    }
}

// Menghapus data alat
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    // Cek apakah alat pernah digunakan dalam peminjaman
    $stmt = mysqli_prepare($connect, "
        SELECT COUNT(*) AS total
        FROM detail_peminjaman
        WHERE alat_id = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $cek = mysqli_fetch_assoc($hasil);

    if ($cek['total'] > 0) {
        $pesan = "Alat tidak bisa dihapus karena memiliki riwayat peminjaman.";
    } else {
        $stmt = mysqli_prepare($connect, "DELETE FROM alat WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: alat.php?pesan=hapus");
            exit;
        } else {
            $pesan = "Gagal menghapus alat.";
        }
    }
}

// Ambil data kategori
$queryKategori = mysqli_query($connect, "SELECT * FROM kategori");

// Ambil data alat
$queryAlat = mysqli_query($connect, "
    SELECT alat.*, kategori.nama_kategori
    FROM alat
    LEFT JOIN kategori ON alat.kategori_id = kategori.id
    ORDER BY alat.id DESC
");

// Ambil data untuk form edit
$dataEdit = null;

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];

    $stmt = mysqli_prepare($connect, "SELECT * FROM alat WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $hasilEdit = mysqli_stmt_get_result($stmt);
    $dataEdit = mysqli_fetch_assoc($hasilEdit);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Alat - SIMALAB</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS utama -->
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="d-flex min-vh-100">

    <!-- Sidebar -->
    <aside class="bg-dark text-white p-3 flex-shrink-0" style="width: 230px;">

        <h3 class="text-center mb-4">SIMALAB</h3>

        <nav class="nav flex-column gap-2">

            <a href="index.php" class="nav-link text-white">
                Dashboard
            </a>

            <a href="alat.php" class="nav-link text-white bg-secondary rounded">
                Data Alat
            </a>

            <a href="peminjaman.php" class="nav-link text-white">
                Data Peminjaman
            </a>

            <a href="pengembalian.php" class="nav-link text-white">
                Data Pengembalian
            </a>

            <a href="laporan.php" class="nav-link text-white">
                Laporan
            </a>

            <a href="profil.php" class="nav-link text-white">
                Profil
            </a>

            <a href="../auth/logout.php"
               class="nav-link text-white"
               onclick="return confirm('Yakin ingin keluar?')">
                Logout
            </a>

        </nav>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-grow-1 p-4" style="min-width: 0;">

        <h2 class="fw-bold mb-4">Data Alat Laboratorium</h2>

        <!-- Pesan Berhasil -->
        <?php if (isset($_GET['pesan'])) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                if ($_GET['pesan'] == 'tambah') echo "Alat berhasil ditambahkan!";
                if ($_GET['pesan'] == 'edit') echo "Data alat berhasil diperbarui!";
                if ($_GET['pesan'] == 'hapus') echo "Alat berhasil dihapus!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <!-- Pesan Kesalahan -->
        <?php if ($pesan != "") { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($pesan); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <!-- Form Tambah dan Edit -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">
                    <?php echo $dataEdit ? "Edit Data Alat" : "Tambah Data Alat"; ?>
                </h5>
            </div>

            <div class="card-body">

                <form method="POST">

                    <?php if ($dataEdit) { ?>
                        <input type="hidden" name="id"
                            value="<?php echo $dataEdit['id']; ?>">
                    <?php } ?>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Kode Alat</label>
                            <input type="text" name="kode_alat"
                                class="form-control" required
                                value="<?php echo htmlspecialchars($dataEdit['kode_alat'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Alat</label>
                            <input type="text" name="nama_alat"
                                class="form-control" required
                                value="<?php echo htmlspecialchars($dataEdit['nama_alat'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">Pilih Kategori</option>

                                <?php
                                mysqli_data_seek($queryKategori, 0);
                                while ($kategori = mysqli_fetch_assoc($queryKategori)) {
                                ?>
                                    <option value="<?php echo $kategori['id']; ?>"
                                        <?php
                                        if ($dataEdit && $dataEdit['kategori_id'] == $kategori['id']) {
                                            echo "selected";
                                        }
                                        ?>>
                                        <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Total</label>
                            <input type="number" name="jumlah_total"
                                class="form-control" min="0" required
                                value="<?php echo htmlspecialchars($dataEdit['jumlah_total'] ?? ''); ?>">
                        </div>

                        <?php if ($dataEdit) { ?>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah Tersedia</label>
                                <input type="number" name="jumlah_tersedia"
                                    class="form-control" min="0" required
                                    value="<?php echo $dataEdit['jumlah_tersedia']; ?>">
                            </div>
                        <?php } ?>

                        <div class="col-md-6">
                            <label class="form-label">Kondisi</label>
                            <select name="kondisi" class="form-select" required>

                                <?php
                                $kondisiPilihan = ['Baik', 'Rusak Ringan', 'Rusak Berat'];

                                foreach ($kondisiPilihan as $kondisi) {
                                    $selected = ($dataEdit && $dataEdit['kondisi'] == $kondisi)
                                        ? 'selected' : '';
                                    echo "<option value='$kondisi' $selected>$kondisi</option>";
                                }
                                ?>

                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?php
                                echo htmlspecialchars($dataEdit['keterangan'] ?? '');
                            ?></textarea>
                        </div>

                        <div class="col-12">

                            <?php if ($dataEdit) { ?>

                                <button type="submit" name="edit" class="btn btn-primary">
                                    Simpan Perubahan
                                </button>

                                <a href="alat.php" class="btn btn-secondary">
                                    Batal
                                </a>

                            <?php } else { ?>

                                <button type="submit" name="tambah" class="btn btn-primary">
                                    Tambah Alat
                                </button>

                            <?php } ?>

                        </div>

                    </div>

                </form>

            </div>
        </div>

        <!-- Tabel Daftar Alat -->
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Daftar Alat</h5>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Alat</th>
                                <th>Kategori</th>
                                <th>Stok Total</th>
                                <th>Stok Tersedia</th>
                                <th>Kondisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            $no = 1;

                            while ($data = mysqli_fetch_assoc($queryAlat)) {
                            ?>

                                <tr>
                                    <td><?php echo $no++; ?></td>

                                    <td>
                                        <?php echo htmlspecialchars($data['kode_alat']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($data['nama_alat']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($data['nama_kategori'] ?? '-'); ?>
                                    </td>

                                    <td><?php echo $data['jumlah_total']; ?></td>

                                    <td><?php echo $data['jumlah_tersedia']; ?></td>

                                    <td>
                                        <?php echo htmlspecialchars($data['kondisi']); ?>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">

                                            <a href="alat.php?edit=<?php echo $data['id']; ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <a href="alat.php?hapus=<?php echo $data['id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus alat ini?')">
                                                Hapus
                                            </a>

                                        </div>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
