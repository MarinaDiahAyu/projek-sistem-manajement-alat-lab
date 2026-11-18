
<?php
session_start();
include "../config/koneksi.php";

// Pastikan hanya Admin yang bisa masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Menghitung jumlah alat
$queryAlat = mysqli_query($connect, "SELECT COUNT(*) AS total FROM alat");
$dataAlat = mysqli_fetch_assoc($queryAlat);

// Menghitung jumlah peminjam
$queryPeminjam = mysqli_query($connect, "SELECT COUNT(*) AS total FROM peminjam");
$dataPeminjam = mysqli_fetch_assoc($queryPeminjam);

// Menghitung jumlah peminjaman aktif
$queryPinjam = mysqli_query($connect, "
    SELECT COUNT(*) AS total FROM peminjaman
    WHERE status IN ('Dipinjam', 'Terlambat')
");
$dataPinjam = mysqli_fetch_assoc($queryPinjam);

// Menghitung jumlah pengembalian
$queryKembali = mysqli_query($connect, "
    SELECT COUNT(*) AS total FROM peminjaman
    WHERE status = 'Dikembalikan'
");
$dataKembali = mysqli_fetch_assoc($queryKembali);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIMALAB</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS utama -->
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

    <div class="d-flex min-vh-100">

        <!-- Sidebar -->
        <div class="bg-dark text-white p-3 flex-shrink-0" style="width: 230px;">

            <h3 class="text-center mb-4">SIMALAB</h3>

            <div class="nav flex-column gap-2">

                <a href="index.php" class="nav-link text-white bg-secondary rounded">
                    Dashboard
                </a>

                <a href="alat.php" class="nav-link text-white">
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

            </div>
        </div>

        <!-- Konten Utama -->
        <div class="flex-grow-1 p-4">

            <div class="mb-4">
                <h2 class="fw-bold">Dashboard Admin</h2>

                <p class="text-muted mb-1">
                    Selamat datang,
                    <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!
                </p>

                <p class="text-muted">
                    Selamat mengelola Sistem Manajemen Alat Laboratorium.
                </p>
            </div>

            <!-- Kartu Statistik -->
            <div class="row g-4">

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted">Total Alat</h6>
                            <h2 class="fw-bold mb-0">
                                <?php echo $dataAlat['total']; ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted">Total Peminjam</h6>
                            <h2 class="fw-bold mb-0">
                                <?php echo $dataPeminjam['total']; ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted">Peminjaman Aktif</h6>
                            <h2 class="fw-bold mb-0">
                                <?php echo $dataPinjam['total']; ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-muted">Sudah Dikembalikan</h6>
                            <h2 class="fw-bold mb-0">
                                <?php echo $dataKembali['total']; ?>
                            </h2>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
