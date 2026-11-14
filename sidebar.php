<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMALAB</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div> <span class="logo-icon">🔬</span></div>
           <div>
                <div>SIMALAB</div>
                <div>Lab Management</div>
            </div> 
            <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">&#9776;</button>
        </div>
        <div class="navbar">
            <div class="nav-label">Menu utama</div>
            <a href="index.php">
               <div class="nav-label">Dasboard</div>
            </a>
            <a href="">
               <div class="nav-label">Data Alat</div>
            </a>
            <a href="">
                <div class="nav-label">Data Peminjam</div>
            </a>
            <div class="nav-label">Transaksi</div>
            <a href="">
                <div class="nav-label">Peminjaman</div>
            </a>
            <a href="">
                <div class="nav-label">Peminjaman</div>
            </a>
            <div class="nav-label">Laporan</div>
            <a href="">
                <div class="nav-label">Laporan</div>
            </a>
        </div>
        <div class="sidebar-footer">
            <div><?php($_SESSION['username'])?></div>
            <div class="btn-logout"><a href="logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a></div>
        </div>
    </aside>

</body>
</html>