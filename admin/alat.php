
<?php
session_start();
include "../config/koneksi.php";

// memastikan yang membuka halaman adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pesan = "";

// menambahkan alat baru 
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

// mengubah data alat 
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

// mengahapus data alat
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

        .form-box, .table-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
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

        .cancel {
            color: #555;
            margin-left: 10px;
            text-decoration: none;
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

        .aksi a {
            margin-right: 8px;
        }

        .pesan {
            background: #e0f2fe;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
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
    <a href="alat.php" class="active">Data Alat</a>
    <a href="peminjaman.php">Data Peminjaman</a>
    <a href="pengembalian.php">Data Pengembalian</a>
    <a href="laporan.php">Laporan</a>
    <a href="profil.php">Profil</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="main">

    <h1>Data Alat Laboratorium</h1>

    <?php if (isset($_GET['pesan'])) { ?>
        <div class="pesan">
            <?php
            if ($_GET['pesan'] == 'tambah') echo "Alat berhasil ditambahkan!";
            if ($_GET['pesan'] == 'edit') echo "Data alat berhasil diperbarui!";
            if ($_GET['pesan'] == 'hapus') echo "Alat berhasil dihapus!";
            ?>
        </div>
    <?php } ?>

    <?php if ($pesan != "") { ?>
        <div class="pesan">
            <?php echo htmlspecialchars($pesan); ?>
        </div>
    <?php } ?>

    <div class="form-box">

        <h2><?php echo $dataEdit ? "Edit Data Alat" : "Tambah Data Alat"; ?></h2>

        <form method="POST">

            <?php if ($dataEdit) { ?>
                <input type="hidden" name="id"
                    value="<?php echo $dataEdit['id']; ?>">
            <?php } ?>

            Kode Alat:
            <input type="text" name="kode_alat" required
                value="<?php echo htmlspecialchars($dataEdit['kode_alat'] ?? ''); ?>">

            Nama Alat:
            <input type="text" name="nama_alat" required
                value="<?php echo htmlspecialchars($dataEdit['nama_alat'] ?? ''); ?>">

            Kategori:
            <select name="kategori_id" required>
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

            Jumlah Total:
            <input type="number" name="jumlah_total" min="0" required
                value="<?php echo htmlspecialchars($dataEdit['jumlah_total'] ?? ''); ?>">

            <?php if ($dataEdit) { ?>
                Jumlah Tersedia:
                <input type="number" name="jumlah_tersedia" min="0" required
                    value="<?php echo $dataEdit['jumlah_tersedia']; ?>">
            <?php } ?>

            Kondisi:
            <select name="kondisi" required>
                <?php
                $kondisiPilihan = ['Baik', 'Rusak Ringan', 'Rusak Berat'];

                foreach ($kondisiPilihan as $kondisi) {
                    $selected = ($dataEdit && $dataEdit['kondisi'] == $kondisi)
                        ? 'selected' : '';
                    echo "<option value='$kondisi' $selected>$kondisi</option>";
                }
                ?>
            </select>

            Keterangan:
            <textarea name="keterangan" rows="3"><?php
                echo htmlspecialchars($dataEdit['keterangan'] ?? '');
            ?></textarea>

            <?php if ($dataEdit) { ?>
                <button type="submit" name="edit">Simpan Perubahan</button>
                <a href="alat.php" class="cancel">Batal</a>
            <?php } else { ?>
                <button type="submit" name="tambah">Tambah Alat</button>
            <?php } ?>

        </form>

    </div>

    <div class="table-box">

        <h2>Daftar Alat</h2>

        <table>
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

            <?php
            $no = 1;

            while ($data = mysqli_fetch_assoc($queryAlat)) {
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($data['kode_alat']); ?></td>
                    <td><?php echo htmlspecialchars($data['nama_alat']); ?></td>
                    <td><?php echo htmlspecialchars($data['nama_kategori'] ?? '-'); ?></td>
                    <td><?php echo $data['jumlah_total']; ?></td>
                    <td><?php echo $data['jumlah_tersedia']; ?></td>
                    <td><?php echo htmlspecialchars($data['kondisi']); ?></td>

                    <td class="aksi">
                        <a href="alat.php?edit=<?php echo $data['id']; ?>">
                            Edit
                        </a>

                        <a href="alat.php?hapus=<?php echo $data['id']; ?>"
                           onclick="return confirm('Yakin ingin menghapus alat ini?')">
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
