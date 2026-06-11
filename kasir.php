<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("Location: login.php");
    exit;
}

$queryProduk = "SELECT COUNT(*) TOTAL FROM PRODUK";
$stidProduk = oci_parse($conn,$queryProduk);
oci_execute($stidProduk);
$dataProduk = oci_fetch_assoc($stidProduk);

$totalProduk = $dataProduk['TOTAL'];

$queryStok = "SELECT NVL(SUM(STOK),0) TOTAL_STOK FROM PRODUK";
$stidStok = oci_parse($conn,$queryStok);
oci_execute($stidStok);
$dataStok = oci_fetch_assoc($stidStok);

$totalStok = $dataStok['TOTAL_STOK'];

$tanggal = date('d F Y');
$jam = date('H:i');
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Kasir Sam's Juice</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

*{
    font-family:'Poppins',sans-serif;
}

body{
    background:#eef7f0;
}

/* SIDEBAR */

.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    top:0;
    left:0;
    background:linear-gradient(180deg,#198754,#0f5132);
    padding:30px 20px;
    color:white;
}

.logo{
    font-size:32px;
    font-weight:800;
    margin-bottom:40px;
}

.menu-link{
    display:block;
    color:white;
    text-decoration:none;
    padding:15px 18px;
    border-radius:16px;
    margin-bottom:12px;
    transition:0.3s;
    font-weight:500;
}

.menu-link:hover{
    background:rgba(255,255,255,0.15);
    color:white;
    transform:translateX(8px);
}

.active-menu{
    background:white;
    color:#198754!important;
}

/* MAIN */

.main{
    margin-left:260px;
    padding:30px;
}

/* TOPBAR */

.topbar{
    background:white;
    border-radius:25px;
    padding:25px 30px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.profile-box{
    display:flex;
    align-items:center;
    gap:15px;
}

.profile-img{
    width:65px;
    height:65px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #198754;
}

/* CARD */

.stat-card{
    border:none;
    border-radius:25px;
    padding:30px;
    color:white;
    transition:0.3s;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.stat-card:hover{
    transform:translateY(-8px);
}

.bg1{
    background:linear-gradient(135deg,#16a34a,#34d399);
}

.bg2{
    background:linear-gradient(135deg,#2563eb,#60a5fa);
}

/* BOX */

.white-box{
    background:white;
    border-radius:25px;
    padding:30px;
    margin-top:30px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* BUTTON */

.btn-custom{
    border-radius:30px;
    padding:10px 20px;
    font-weight:600;
}

/* MOBILE */

@media(max-width:991px){

.sidebar{
    position:relative;
    width:100%;
    height:auto;
}

.main{
    margin-left:0;
}

}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<div class="logo">
🍹 Sam's Juice
</div>

<a href="kasir.php" class="menu-link active-menu">
<i class="fa-solid fa-house me-2"></i>
Dashboard
</a>

<a href="transaksi.php" class="menu-link">
<i class="fa-solid fa-cart-shopping me-2"></i>
Transaksi
</a>

<a href="data_menu.php" class="menu-link">
<i class="fa-solid fa-glass-water me-2"></i>
Data Menu
</a>

<a href="riwayat.php" class="menu-link">
<i class="fa-solid fa-clock-rotate-left me-2"></i>
Riwayat
</a>

<a href="profil_kasir.php" class="menu-link">
    <i class="fa-solid fa-user me-2"></i>
    Profil Kasir
</a>

<a href="settings_kasir.php" class="menu-link">
    <i class="fa-solid fa-gear me-2"></i>
    Settings
</a>

<a href="logout.php" class="menu-link">
<i class="fa-solid fa-right-from-bracket me-2"></i>
Logout
</a>

</div>

<!-- MAIN -->

<div class="main">

<!-- TOPBAR -->

<div class="topbar d-flex justify-content-between align-items-center flex-wrap">

<div>

<h1 class="fw-bold text-success">
Dashboard Kasir
</h1>

<p class="text-muted mb-0" id="real-time-clock">
<?php echo $tanggal; ?> | 00:00:00 WIB
</p>

</div>

<div class="profile-box">

<img src="image/kasir1.jpeg" class="profile-img">

<div>

<h4 class="mb-0 fw-bold">
<?php echo $_SESSION['username']; ?>
</h4>

<p class="text-muted mb-0">
Kasir Sam's Juice
</p>

</div>

</div>

</div>

<!-- STATS -->
<div class="row g-4 mt-1">
    <div class="col-md-4">
        <div class="stat-card bg1">
            <h4>Total Produk</h4>
            <h1 class="fw-bold"><?php echo $totalProduk; ?></h1>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg2">
            <h4>Total Stok</h4>
            <h1 class="fw-bold"><?php echo $totalStok; ?></h1>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-warning text-dark">
            <h4>Pesanan Aktif</h4>
            <h1 class="fw-bold">
                <?php
                $queryActive = "SELECT COUNT(*) TOTAL FROM TRANSAKSI WHERE STATUS IN ('Menunggu', 'Diproses')";
                $stidActive = oci_parse($conn, $queryActive);
                oci_execute($stidActive);
                $dataActive = oci_fetch_assoc($stidActive);
                echo $dataActive['TOTAL'];
                ?>
            </h1>
        </div>
    </div>
</div>

<!-- STATS ROW 2 -->
<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="stat-card bg-primary text-white">
            <h4>Total Pesanan Selesai</h4>
            <h1 class="fw-bold">
                <?php
                $queryDone = "SELECT COUNT(*) TOTAL FROM TRANSAKSI WHERE STATUS = 'Selesai'";
                $stidDone = oci_parse($conn, $queryDone);
                oci_execute($stidDone);
                $dataDone = oci_fetch_assoc($stidDone);
                echo $dataDone['TOTAL'];
                ?>
            </h1>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card bg-success text-white">
            <h4>Total Pendapatan</h4>
            <h1 class="fw-bold">
                <?php
                $queryIncome = "SELECT SUM(TOTAL) TOTAL FROM TRANSAKSI WHERE STATUS = 'Selesai'";
                $stidIncome = oci_parse($conn, $queryIncome);
                oci_execute($stidIncome);
                $dataIncome = oci_fetch_assoc($stidIncome);
                echo "Rp " . number_format($dataIncome['TOTAL'] ?? 0);
                ?>
            </h1>
        </div>
    </div>
</div>


<!-- PESANAN MASUK -->
<div class="white-box">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Pesanan Masuk 🔔</h3>
        <span class="badge bg-danger rounded-pill px-3 py-2">Real-time</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Menu</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $queryIncoming = "SELECT * FROM TRANSAKSI WHERE STATUS IN ('Menunggu', 'Diproses') ORDER BY TANGGAL DESC";
                $stidIncoming = oci_parse($conn, $queryIncoming);
                oci_execute($stidIncoming);
                
                $hasIncoming = false;
                while($row = oci_fetch_assoc($stidIncoming)):
                    $hasIncoming = true;
                ?>
                <tr>
                    <td><span class="fw-bold"><?php echo $row['ID_TRANSAKSI']; ?></span></td>
                    <td><?php echo $row['PELANGGAN']; ?></td>
                    <td><?php echo $row['MENU']; ?></td>
                    <td>Rp <?php echo number_format($row['TOTAL']); ?></td>
                    <td>
                        <?php if($row['STATUS'] == 'Menunggu'): ?>
                            <span class="badge bg-warning text-dark">Menunggu</span>
                        <?php else: ?>
                            <span class="badge bg-primary">Diproses</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="cetak_invoice.php?id=<?php echo $row['ID_TRANSAKSI']; ?>" target="_blank" class="btn btn-sm btn-info text-white"><i class="fas fa-print"></i></a>
                            <?php if($row['STATUS'] == 'Menunggu'): ?>
                                <a href="update_status.php?id=<?php echo $row['ID_TRANSAKSI']; ?>&status=Diproses" class="btn btn-sm btn-outline-primary">Proses</a>
                            <?php endif; ?>
                            <a href="update_status.php?id=<?php echo $row['ID_TRANSAKSI']; ?>&status=Selesai" class="btn btn-sm btn-success text-white">Selesai</a>
                            <a href="update_status.php?id=<?php echo $row['ID_TRANSAKSI']; ?>&status=Batal" class="btn btn-sm btn-danger text-white" onclick="return confirm('Yakin ingin membatalkan?')">Batal</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; 
                if(!$hasIncoming): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada pesanan aktif saat ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MENU CEPAT -->

<div class="white-box">

<h3 class="fw-bold mb-4">
Menu Cepat
</h3>

<div class="row g-4">

<div class="col-md-4">

<div class="card border-0 shadow-sm rounded-4 p-4 text-center">

<div class="fs-1 text-success">
<i class="fa-solid fa-cart-shopping"></i>
</div>

<h5 class="fw-bold mt-3">
Transaksi
</h5>

<p class="text-muted">
Input transaksi penjualan
</p>

<a href="transaksi.php" class="btn btn-success btn-custom">
Buka
</a>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow-sm rounded-4 p-4 text-center">

<div class="fs-1 text-primary">
<i class="fa-solid fa-glass-water"></i>
</div>

<h5 class="fw-bold mt-3">
Data Menu
</h5>

<p class="text-muted">
Kelola menu dan harga
</p>

<a href="data_menu.php" class="btn btn-primary btn-custom">
Buka
</a>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow-sm rounded-4 p-4 text-center">

<div class="fs-1 text-warning">
<i class="fa-solid fa-clock-rotate-left"></i>
</div>

<h5 class="fw-bold mt-3">
Riwayat
</h5>

<p class="text-muted">
Lihat riwayat transaksi
</p>

<a href="riwayat.php" class="btn btn-warning btn-custom">
Buka
</a>

</div>

</div>

</div>

</div>

</div>

<!-- SCRIPT -->
<script>
function updateClock() {
    const now = new Date();
    const options = { day: '2-digit', month: 'long', year: 'numeric' };
    const dateStr = now.toLocaleDateString('id-ID', options);
    const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById('real-time-clock').innerText = dateStr + ' | ' + timeStr + ' WIB';
}
setInterval(updateClock, 1000);
updateClock();
</script>
</body>

</html>