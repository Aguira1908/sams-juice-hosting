<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("Location: login.php");
    exit;
}

/* =========================
   CRUD MENU
========================= */

if(isset($_POST['simpan'])){

$id         = $_POST['id_produk'];
$nama       = $_POST['nama_produk'];
$harga      = $_POST['harga_regular'];
$stok       = $_POST['stok'] ?? 10;
$deskripsi  = $_POST['deskripsi'];

$query = "INSERT INTO PRODUK
(ID_PRODUK,NAMA_PRODUK,HARGA,STOK,GAMBAR,ID_KATEGORI,DESKRIPSI)
VALUES
(:id,:nama,:harga,:stok,'default.jpg',1,:deskripsi)";

$stid = oci_parse($conn,$query);

oci_bind_by_name($stid,":id",$id);
oci_bind_by_name($stid,":nama",$nama);
oci_bind_by_name($stid,":harga",$harga);
oci_bind_by_name($stid,":stok",$stok);
oci_bind_by_name($stid,":deskripsi",$deskripsi);

oci_execute($stid);

echo "
<script>
alert('Menu berhasil disimpan');
window.location='data_menu.php';
</script>
";

}

/* =========================
   UPDATE MENU
========================= */

if(isset($_POST['edit'])){

$id         = $_POST['id_produk'];
$nama       = $_POST['nama_produk'];
$harga      = $_POST['harga_regular'];
$deskripsi  = $_POST['deskripsi'];

$query = "UPDATE PRODUK SET
NAMA_PRODUK=:nama,
HARGA=:harga,
DESKRIPSI=:deskripsi
WHERE ID_PRODUK=:id";

$stid = oci_parse($conn,$query);

oci_bind_by_name($stid,":nama",$nama);
oci_bind_by_name($stid,":harga",$harga);
oci_bind_by_name($stid,":deskripsi",$deskripsi);
oci_bind_by_name($stid,":id",$id);

oci_execute($stid);

echo "
<script>
alert('Menu berhasil diupdate');
window.location='data_menu.php';
</script>
";

}

/* =========================
   UPDATE PROMO
========================= */

if(isset($_POST['update_promo'])){
    $id = $_POST['id_produk'];
    $is_promo = $_POST['is_promo'];
    $diskon = $_POST['diskon'];

    $query = "UPDATE PRODUK SET IS_PROMO = :is_promo, DISKON_PERSEN = :diskon WHERE ID_PRODUK = :id";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":is_promo", $is_promo);
    oci_bind_by_name($stid, ":diskon", $diskon);
    oci_bind_by_name($stid, ":id", $id);
    oci_execute($stid);

    echo "<script>alert('Promo berhasil diupdate'); window.location='data_menu.php';</script>";
}

if(isset($_GET['hapus'])){

$id = $_GET['hapus'];

$query = "DELETE FROM PRODUK
WHERE ID_PRODUK=:id";

$stid = oci_parse($conn,$query);

oci_bind_by_name($stid,":id",$id);

oci_execute($stid);

echo "
<script>
alert('Menu berhasil dihapus');
window.location='data_menu.php';
</script>
";

}

/* =========================
   TAMPIL DATA
========================= */

$queryProduk = "SELECT * FROM PRODUK ORDER BY ID_PRODUK";

$stidProduk = oci_parse($conn,$queryProduk);

oci_execute($stidProduk);

$tanggal = date('d F Y');
$jam = date('H:i');

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Data Menu & Harga</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
rel="stylesheet">

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
    padding:25px 30px;
    border-radius:25px;
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

/* BOX */

.white-box{
    background:white;
    border-radius:25px;
    padding:30px;
    margin-top:30px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    animation:fadeUp 0.8s ease;
}

/* FORM */

.form-control,
.form-select{
    border-radius:14px;
    padding:12px;
}

textarea{
    resize:none;
}

/* BUTTON */

.btn-custom{
    border-radius:30px;
    padding:12px 22px;
    font-weight:600;
}

/* TABLE */

.table thead{
    background:#198754;
    color:white;
}

/* ANIMATION */

@keyframes fadeUp{

from{
    opacity:0;
    transform:translateY(30px);
}

to{
    opacity:1;
    transform:translateY(0);
}

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

<a href="kasir.php" class="menu-link">
<i class="fa-solid fa-house me-2"></i>
Dashboard
</a>

<a href="transaksi.php" class="menu-link">
<i class="fa-solid fa-cart-shopping me-2"></i>
Transaksi
</a>

<a href="data_menu.php"
class="menu-link active-menu">

<i class="fa-solid fa-glass-water me-2"></i>
Data Menu

</a>

<a href="riwayat.php" class="menu-link">
<i class="fa-solid fa-clock-rotate-left me-2"></i>
Riwayat
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

<h2 class="fw-bold text-success">
Data Menu & Harga
</h2>

<p class="text-muted mb-0" id="real-time-clock">
<?php echo $tanggal; ?> | 00:00:00 WIB
</p>

</div>

<div class="profile-box">

<img src="image/kasir1.jpeg"
class="profile-img">

<div>

<h5 class="fw-bold mb-0">
<?php echo $_SESSION['username']; ?>
</h5>

<p class="text-muted mb-0">
Kasir Sam's Juice
</p>

</div>

</div>

</div>

<!-- FORM MENU -->

<div class="white-box">

<h3 class="fw-bold mb-4">
C. DATA MENU & HARGA
</h3>

<form method="POST"
id="formMenu">

<div class="row g-4">

<div class="col-md-6">

<label class="fw-semibold mb-2">
Kode Menu
</label>

<input
type="text"
name="id_produk"
id="id_produk"
class="form-control"
placeholder="MNU-XXXX"
required>

</div>

<div class="col-md-6">

<label class="fw-semibold mb-2">
Nama Menu
</label>

<input
type="text"
name="nama_produk"
id="nama_produk"
class="form-control"
placeholder="Contoh: Jus Semangka"
required>

</div>

<div class="col-md-4">
<label class="fw-semibold mb-2">
Harga Regular
</label>
<input
type="number"
name="harga_regular"
id="harga_regular"
class="form-control"
placeholder="Rp"
required>
</div>

<div class="col-md-4">
<label class="fw-semibold mb-2">
Stok
</label>
<input
type="number"
name="stok"
id="stok"
class="form-control"
placeholder="Jumlah stok"
required>
</div>

<div class="col-md-4">

<label class="fw-semibold mb-2">
Harga Large
</label>

<input
type="number"
class="form-control"
placeholder="Rp">

</div>

<div class="col-md-4">

<label class="fw-semibold mb-2">
Harga Extra
</label>

<input
type="number"
class="form-control"
placeholder="Rp">

</div>

<div class="col-12">

<label class="fw-semibold mb-3">
Ketersediaan
</label>

<div class="d-flex gap-4 flex-wrap">

<div class="form-check">

<input class="form-check-input"
type="checkbox"
checked>

<label class="form-check-label">
Tersedia
</label>

</div>

<div class="form-check">

<input class="form-check-input"
type="checkbox">

<label class="form-check-label">
Habis
</label>

</div>

<div class="form-check">

<input class="form-check-input"
type="checkbox">

<label class="form-check-label">
Musiman
</label>

</div>

</div>

</div>

<div class="col-12">

<label class="fw-semibold mb-2">
Deskripsi Singkat
</label>

<textarea
name="deskripsi"
id="deskripsi"
class="form-control"
rows="4"
placeholder="Bahan utama, keterangan alergi, dll..."></textarea>

</div>

<div class="col-12 d-flex gap-3 flex-wrap">

<button
type="submit"
name="simpan"
class="btn btn-success btn-custom">

<i class="fa-solid fa-floppy-disk me-2"></i>
Simpan Menu

</button>

<button
type="submit"
name="edit"
class="btn btn-warning btn-custom">

<i class="fa-solid fa-pen me-2"></i>
Update Menu

</button>

<button
type="reset"
class="btn btn-danger btn-custom">

<i class="fa-solid fa-trash me-2"></i>
Reset

</button>

</div>

</div>

</form>

</div>

<!-- TABLE -->

<div class="white-box">

<h3 class="fw-bold mb-4">
Daftar Menu
</h3>

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead>

<tr>

<th>ID</th>
<th>Nama Produk</th>
<th>Harga</th>
<th>Stok</th>
<th>Promo</th>
<th>Deskripsi</th>
<th>Aksi</th>

</tr>

</thead>

<tbody>

<?php while($row = oci_fetch_assoc($stidProduk)){ ?>

<tr>

<td>
<?php echo $row['ID_PRODUK']; ?>
</td>

<td>
<?php echo $row['NAMA_PRODUK']; ?>
</td>

<td>
Rp <?php echo number_format($row['HARGA']); ?>
</td>

<td>

<span class="badge bg-success">
<?php echo $row['STOK']; ?>
</span>

</td>

<td>
    <?php if($row['IS_PROMO'] == 1): ?>
        <span class="badge bg-danger"><?php echo $row['DISKON_PERSEN']; ?>% OFF</span>
    <?php else: ?>
        <span class="badge bg-secondary">No Promo</span>
    <?php endif; ?>
    <button class="btn btn-sm btn-outline-danger d-block mt-1" onclick="openPromoModal('<?php echo $row['ID_PRODUK']; ?>', '<?php echo $row['DISKON_PERSEN']; ?>', '<?php echo $row['IS_PROMO']; ?>')">Set</button>
</td>

<td>
<?php echo $row['DESKRIPSI']; ?>
</td>

<td>

<button
type="button"
class="btn btn-warning btn-sm"

onclick="editMenu(
'<?php echo $row['ID_PRODUK']; ?>',
'<?php echo $row['NAMA_PRODUK']; ?>',
'<?php echo $row['HARGA']; ?>',
'<?php echo $row['DESKRIPSI']; ?>',
'<?php echo $row['STOK']; ?>'
)">

Edit

</button>

<a
href="?hapus=<?php echo $row['ID_PRODUK']; ?>"
class="btn btn-danger btn-sm"

onclick="return confirm('Yakin hapus menu?')">

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

<script>

function editMenu(id,nama,harga,deskripsi,stok){

document.getElementById('id_produk').value = id;

document.getElementById('nama_produk').value = nama;

document.getElementById('harga_regular').value = harga;

document.getElementById('deskripsi').value = deskripsi;

if(document.getElementById('stok')) {
    document.getElementById('stok').value = stok;
}

window.scrollTo({
top:0,
behavior:'smooth'
});

}

document.getElementById('formMenu')
.addEventListener('reset',function(){

setTimeout(function(){

document.getElementById('id_produk').value = '';

document.getElementById('nama_produk').value = '';

document.getElementById('harga_regular').value = '';

document.getElementById('deskripsi').value = '';

},100);

});

</script>

<!-- Modal Promo -->
<div class="modal fade" id="promoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold">Atur Promo Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST">
                    <input type="hidden" name="id_produk" id="promo_id">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Status Promo</label>
                        <select name="is_promo" id="promo_status" class="form-select rounded-3">
                            <option value="0">Tidak Promo</option>
                            <option value="1">Aktif Promo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Diskon (%)</label>
                        <input type="number" name="diskon" id="promo_diskon" class="form-control rounded-3" placeholder="Contoh: 10">
                    </div>
                    <button type="submit" name="update_promo" class="btn btn-danger w-100 rounded-pill py-3 fw-bold">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openPromoModal(id, diskon, status) {
    document.getElementById('promo_id').value = id;
    document.getElementById('promo_diskon').value = diskon;
    document.getElementById('promo_status').value = status;
    new bootstrap.Modal(document.getElementById('promoModal')).show();
}
</script>

</body>
</html>