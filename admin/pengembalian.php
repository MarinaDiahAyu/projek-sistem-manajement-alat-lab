
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

            mysqli_stmt_bind_param(
                $stmt, "sssi",
                $tanggal, $kondisi, $catatan, $id
            );

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

                    mysqli_stmt_bind_param(
                        $stmtStok, "ii",
                        $item['jumlah'], $item['alat_id']
                    );

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

// Ambil data untuk formulir jika Admin memilih proses pengembalian
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

        .box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #1e293b;
            color: white;
            border: none;
            padding: 11px 18px;
            border-radius: 5px;
            cursor: pointer;
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

            .main {
                overflow-x: auto;
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
    <a href="pengembalian.php" class="active">Data Pengembalian</a>
    <a href="laporan.php">Laporan</a>
    <a href="profil.php">Profil</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="main">

    <h1>Data Pengembalian Alat</h1>
    <p>Kelola pengembalian alat laboratorium.</p>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil') { ?>
        <div class="pesan">
            Pengembalian berhasil disimpan!
        </div>
    <?php } ?>

    <?php if ($pesan != "") { ?>
        <div class="pesan">
            <?php echo htmlspecialchars($pesan); ?>
        </div>
    <?php } ?>

    <?php if (isset($_GET['id']) && $dataForm) { ?>

        <div class="box">

            <h2>Form Pengembalian</h2>

            <p>Kode Peminjaman:
                <?php echo htmlspecialchars($dataForm['kode_pinjam']); ?>
            </p>

            <p>Nama Peminjam:
                <?php echo htmlspecialchars($dataForm['nama']); ?>
            </p>

            <form method="POST">

                <input type="hidden" name="id"
                    value="<?php echo $dataForm['id']; ?>">

                Tanggal Pengembalian:
                <input type="date" name="tanggal_kembali_aktual" required>

                Kondisi Alat:
                <select name="kondisi_kembali" required>
                    <option value="">Pilih Kondisi</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>

                Catatan:
                <textarea name="catatan" rows="3"></textarea>

                <button type="submit" name="simpan">
                    Simpan Pengembalian
                </button>

                <a href="pengembalian.php">Batal</a>

            </form>

        </div>

    <?php } elseif (isset($_GET['id'])) { ?>

        <div class="pesan">Data peminjaman tidak ditemukan.</div>

    <?php } ?>

    <div class="box">

        <h2>Daftar Alat yang Belum Dikembalikan</h2>

        <table>
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
                    <td><?php echo htmlspecialchars($data['status']); ?></td>
                    <td>
                        <a href="pengembalian.php?id=<?php echo $data['id']; ?>">
                            Proses Pengembalian
                        </a>
                    </td>
                </tr>
            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>
