
<?php
session_start();
include "../config/koneksi.php";

// Cek login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil data Admin
$id = $_SESSION['admin_id'];

$query = mysqli_prepare($connect, "
    SELECT username, nama_lengkap
    FROM admin
    WHERE id = ?
");

mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);
mysqli_stmt_bind_result($query, $username, $nama_lengkap);

if (!mysqli_stmt_fetch($query)) {
    die("Data profil Admin tidak ditemukan.");
}

mysqli_stmt_close($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - SIMALAB</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="d-flex min-vh-100">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Konten -->
    <main class="flex-grow-1 p-4">

        <h2 class="fw-bold">Profil Admin</h2>

        <!-- Kartu Profil -->
        <div class="card border-0 shadow-sm mt-4 w-100">
            <div class="card-body p-4">

                <h4 class="mb-4">Data Admin</h4>

                <div class="row g-4">

                    <!-- Nama Lengkap -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control"
                            value="<?= htmlspecialchars($nama_lengkap); ?>" readonly>
                    </div>

                    <!-- Username -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" class="form-control"
                            value="<?= htmlspecialchars($username); ?>" readonly>
                    </div>

                    <!-- Role -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Role</label>
                        <input type="text" class="form-control"
                            value="Administrator" readonly>
                    </div>

                </div>

            </div>
        </div>

    </main>
</div>

</body>
</html>