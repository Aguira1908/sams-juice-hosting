<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("Location: login.php");
    exit;
}

$queryProduk = "SELECT * FROM PRODUK ORDER BY ID_PRODUK";
$stidProduk  = oci_parse($conn, $queryProduk);
oci_execute($stidProduk);

$produk = [];
while ($row = oci_fetch_assoc($stidProduk)) {
    $produk[] = $row;
}

$tanggal = date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transaksi Penjualan – Sam's Juice</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ========== RESET & BASE ========== */
* { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
body { background: #eef7f0; margin: 0; }

/* ========== SIDEBAR ========== */
.sidebar {
    width: 260px; height: 100vh; position: fixed; top: 0; left: 0; z-index: 100;
    background: linear-gradient(180deg, #198754 0%, #0f5132 100%);
    padding: 30px 20px; color: white;
    display: flex; flex-direction: column;
}
.logo { font-size: 28px; font-weight: 800; margin-bottom: 40px; letter-spacing: -0.5px; }
.menu-link {
    display: flex; align-items: center; gap: 12px;
    color: rgba(255,255,255,0.85); text-decoration: none;
    padding: 13px 18px; border-radius: 16px; margin-bottom: 8px;
    transition: all 0.25s ease; font-weight: 500; font-size: 14.5px;
}
.menu-link:hover { background: rgba(255,255,255,0.15); color: white; transform: translateX(6px); }
.menu-link.active-menu { background: white; color: #198754; font-weight: 600; box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
.menu-link i { width: 20px; text-align: center; font-size: 15px; }
.sidebar-footer { margin-top: auto; font-size: 11px; color: rgba(255,255,255,0.4); text-align: center; padding-top: 20px; }

/* ========== MAIN ========== */
.main { margin-left: 260px; padding: 28px 32px; min-height: 100vh; }

/* ========== TOPBAR ========== */
.topbar {
    background: white; padding: 22px 28px;
    border-radius: 22px; box-shadow: 0 4px 18px rgba(0,0,0,0.07);
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;
}
.topbar h2 { font-size: 22px; font-weight: 700; color: #198754; margin: 0; }
.topbar .sub { color: #6c757d; font-size: 13px; margin: 3px 0 0; }
.profile-img { width: 58px; height: 58px; border-radius: 50%; object-fit: cover; border: 3px solid #198754; }

/* ========== WHITE BOX ========== */
.white-box {
    background: white; border-radius: 22px; padding: 30px;
    margin-top: 24px; box-shadow: 0 4px 18px rgba(0,0,0,0.07);
}
.section-title {
    font-size: 15px; font-weight: 700; color: #198754;
    letter-spacing: 0.5px; margin-bottom: 22px;
    padding-bottom: 10px; border-bottom: 2px solid #e8f5ef;
}

/* ========== FORM CONTROLS ========== */
.form-label { font-weight: 600; font-size: 13.5px; color: #344; margin-bottom: 6px; }
.form-control, .form-select {
    border-radius: 12px; padding: 11px 14px; font-size: 14px;
    border: 1.5px solid #dee2e6; transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus, .form-select:focus {
    border-color: #198754; box-shadow: 0 0 0 3px rgba(25,135,84,0.12); outline: none;
}
.form-control[readonly] { background: #f8faf9; color: #555; }
textarea { resize: none; }

/* ========== PAYMENT METHOD CARDS ========== */
.pay-option { cursor: pointer; }
.pay-option input[type="radio"] { display: none; }
.pay-label {
    display: flex; align-items: center; gap: 8px;
    padding: 11px 18px; border-radius: 14px;
    border: 2px solid #e0e0e0; font-weight: 600; font-size: 13.5px;
    color: #555; transition: all 0.2s; background: white; cursor: pointer;
}
.pay-label:hover { border-color: #198754; color: #198754; }
.pay-option input:checked + .pay-label {
    border-color: #198754; background: #e8f5ef; color: #198754;
    box-shadow: 0 3px 10px rgba(25,135,84,0.15);
}

/* ========== TABLE ========== */
.table-items thead th {
    background: #198754; color: white; font-size: 13px;
    font-weight: 600; padding: 11px 12px; border: none;
}
.table-items thead th:first-child { border-radius: 10px 0 0 0; }
.table-items thead th:last-child  { border-radius: 0 10px 0 0; }
.table-items tbody td { vertical-align: middle; padding: 8px 10px; font-size: 13.5px; }
.table-items tbody tr:hover { background: #f4fdf6; }
.table-items .form-control, .table-items .form-select {
    border-radius: 8px; padding: 7px 10px; font-size: 13px;
}
.btn-hapus-baris {
    width: 30px; height: 30px; border-radius: 50%; border: 1.5px solid #dc3545;
    background: transparent; color: #dc3545; font-size: 12px;
    display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;
}
.btn-hapus-baris:hover { background: #dc3545; color: white; }

/* ========== TOTAL BOX ========== */
.total-box {
    background: linear-gradient(135deg, #198754, #2ec47a);
    color: white; border-radius: 18px; padding: 22px 26px; text-align: center;
}
.total-box h6 { font-size: 13px; opacity: 0.85; margin-bottom: 6px; letter-spacing: 0.5px; }
.total-box .amount { font-size: 28px; font-weight: 800; line-height: 1.1; }

/* ========== BUTTONS ========== */
.btn-green  { background: #198754; color: white; border: none; border-radius: 30px; padding: 11px 24px; font-weight: 600; font-size: 14px; transition: 0.2s; }
.btn-green:hover  { background: #157347; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(25,135,84,0.3); }
.btn-red    { background: #dc3545; color: white; border: none; border-radius: 30px; padding: 11px 24px; font-weight: 600; font-size: 14px; transition: 0.2s; }
.btn-red:hover    { background: #b02a37; color: white; transform: translateY(-1px); }
.btn-outline-g  { background: transparent; color: #198754; border: 2px solid #198754; border-radius: 30px; padding: 10px 22px; font-weight: 600; font-size: 14px; transition: 0.2s; }
.btn-outline-g:hover { background: #198754; color: white; }
.btn-outline-b  { background: transparent; color: #0d6efd; border: 2px solid #0d6efd; border-radius: 30px; padding: 10px 22px; font-weight: 600; font-size: 14px; transition: 0.2s; }
.btn-outline-b:hover { background: #0d6efd; color: white; }
.btn-outline-s  { background: transparent; color: #6c757d; border: 2px solid #dee2e6; border-radius: 30px; padding: 10px 22px; font-weight: 600; font-size: 14px; transition: 0.2s; }
.btn-outline-s:hover { border-color: #adb5bd; }

/* ========== TOAST ========== */
#toast {
    position: fixed; top: 24px; right: 24px; z-index: 9999;
    padding: 14px 22px; border-radius: 16px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.18);
    font-weight: 600; font-size: 14px; color: white;
    display: none; align-items: center; gap: 10px;
    animation: toastIn 0.35s ease;
    max-width: 360px; line-height: 1.4;
}
@keyframes toastIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

/* ========== MODAL ========== */
.modal-content { border-radius: 22px; border: none; box-shadow: 0 16px 48px rgba(0,0,0,0.18); }
.modal-header  { border-bottom: 1.5px solid #e8f5ef; padding: 20px 26px 16px; }
.modal-footer  { border-top: 1.5px solid #e8f5ef; padding: 16px 26px 20px; }
.modal-body    { padding: 22px 26px; }
.modal-title   { font-weight: 700; color: #198754; font-size: 16px; }
.tab-mode-btn {
    padding: 7px 20px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;
    border: 2px solid #dee2e6; background: white; color: #6c757d; transition: 0.2s;
}
.tab-mode-btn.active { background: #198754; color: white; border-color: #198754; }

/* ========== TAMBAH ITEM BUTTON ========== */
.btn-tambah-item {
    border: 2px dashed #a8d5b8; background: transparent; color: #198754;
    border-radius: 10px; padding: 9px 20px; font-size: 13.5px;
    font-weight: 600; cursor: pointer; transition: 0.2s; width: 100%; margin-top: 8px;
}
.btn-tambah-item:hover { background: #e8f5ef; border-color: #198754; }

/* ========== RESPONSIVE ========== */
@media (max-width: 991px) {
    .sidebar { position: relative; width: 100%; height: auto; flex-direction: row; flex-wrap: wrap; padding: 16px; }
    .main { margin-left: 0; padding: 16px; }
    .logo { margin-bottom: 0; font-size: 22px; }
}
</style>
</head>
<body>

<!-- ==================== TOAST ==================== -->
<div id="toast"><i id="toastIcon" class="fa-solid fa-circle-check fa-lg"></i><span id="toastMsg"></span></div>

<!-- ==================== SIDEBAR ==================== -->
<div class="sidebar">
    <div class="logo">🍹 Sam's Juice</div>
    <a href="kasir.php"     class="menu-link"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a href="transaksi.php" class="menu-link active-menu"><i class="fa-solid fa-cart-shopping"></i> Transaksi</a>
    <a href="data_menu.php" class="menu-link"><i class="fa-solid fa-glass-water"></i> Data Menu</a>
    <a href="riwayat.php"   class="menu-link"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat</a>
    <a href="logout.php"    class="menu-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    <div class="sidebar-footer">Sam's Juice POS v2.0</div>
</div>

<!-- ==================== MAIN ==================== -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div>
            <h2>Transaksi Penjualan</h2>
            <p class="sub" id="realTimeClock"><?php echo $tanggal; ?> | 00:00:00 WIB</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <img src="image/kasir1.jpeg" class="profile-img" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=198754&color=fff'">
            <div>
                <div style="font-weight:700;font-size:15px;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <div style="color:#6c757d;font-size:12.5px;">Kasir Sam's Juice</div>
            </div>
        </div>
    </div>

    <!-- FORM TRANSAKSI -->
    <div class="white-box">
        <div class="section-title"><i class="fa-solid fa-file-invoice-dollar me-2"></i>DATA TRANSAKSI PENJUALAN</div>

        <form id="formTransaksi" autocomplete="off">

            <div class="row g-3">
                <!-- No. Transaksi -->
                <div class="col-md-6">
                    <label class="form-label">No. Transaksi <span class="text-danger">*</span></label>
                    <input type="text" id="noTransaksi" name="no_transaksi"
                           class="form-control fw-bold" style="color:#198754;"
                           placeholder="Contoh: TRX-001" required>
                </div>

                <!-- Tanggal & Waktu -->
                <div class="col-md-6">
                    <label class="form-label">Tanggal & Waktu</label>
                    <input type="datetime-local" id="tanggalWaktu" name="tanggal_waktu"
                           class="form-control" readonly>
                </div>

                <!-- Nama Kasir -->
                <div class="col-md-6">
                    <label class="form-label">Nama Kasir</label>
                    <select name="kasir" class="form-select">
                        <option><?php echo htmlspecialchars($_SESSION['username']); ?></option>
                    </select>
                </div>

                <!-- Metode Bayar -->
                <div class="col-md-6">
                    <label class="form-label">Metode Bayar</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <label class="pay-option">
                            <input type="radio" name="metode_bayar" value="Tunai" checked>
                            <span class="pay-label">💵 Tunai</span>
                        </label>
                        <label class="pay-option">
                            <input type="radio" name="metode_bayar" value="QRIS">
                            <span class="pay-label">📱 QRIS</span>
                        </label>
                        <label class="pay-option">
                            <input type="radio" name="metode_bayar" value="Transfer">
                            <span class="pay-label">🏦 Transfer</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- TABLE ITEM -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-items" id="tabelItem">
                    <thead>
                        <tr>
                            <th style="width:42px">#</th>
                            <th>Nama Menu</th>
                            <th style="width:155px">Ukuran</th>
                            <th style="width:90px">Qty</th>
                            <th style="width:145px">Harga Satuan</th>
                            <th style="width:150px">Subtotal</th>
                            <th style="width:46px"></th>
                        </tr>
                    </thead>
                    <tbody id="tbodyItem"></tbody>
                </table>
            </div>

            <button type="button" onclick="tambahBaris()" class="btn-tambah-item">
                <i class="fa-solid fa-plus me-2"></i>Tambah Item
            </button>

            <!-- DISKON, PELANGGAN, CATATAN -->
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label class="form-label">Diskon (%)</label>
                    <input type="number" id="diskon" name="diskon"
                           class="form-control" value="0" min="0" max="100" step="0.5">
                </div>
                <div class="col-md-9">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" id="pelanggan" name="pelanggan"
                           class="form-control" placeholder="Nama pelanggan / walk-in customer">
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan Pesanan</label>
                    <textarea name="catatan" id="catatan" class="form-control" rows="2"
                              placeholder="Permintaan khusus, tingkat kemanisan, dll..."></textarea>
                </div>
            </div>

            <!-- TOMBOL AKSI + TOTAL -->
            <div class="row mt-4 align-items-end g-3">
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-2">

                        <!-- Simpan -->
                        <button type="button" id="btnSimpan" onclick="simpanTransaksi()"
                                class="btn-green">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Transaksi
                        </button>

                        <!-- Reset -->
                        <button type="button" onclick="resetForm()" class="btn-red">
                            <i class="fa-solid fa-rotate-left me-2"></i>Reset
                        </button>

                        <!-- Cetak Invoice (muncul setelah simpan) -->
                        <a id="btnInvoice" href="#" target="_blank"
                           class="btn-outline-g d-none" style="text-decoration:none;">
                            <i class="fa-solid fa-print me-2"></i>Invoice
                        </a>

                        <!-- Lihat Riwayat (muncul setelah simpan) -->
                        <a id="btnRiwayat" href="riwayat.php"
                           class="btn-outline-b d-none" style="text-decoration:none;">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i>Riwayat
                        </a>

                        <!-- Cetak Laporan -->
                        <button type="button" onclick="bukaModalLaporan()" class="btn-outline-s">
                            <i class="fa-solid fa-chart-bar me-2"></i>Laporan
                        </button>

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="total-box">
                        <h6>TOTAL PEMBAYARAN</h6>
                        <div class="amount" id="grandTotal">Rp 0</div>
                    </div>
                </div>
            </div>

        </form>
    </div><!-- /white-box -->
</div><!-- /main -->


<!-- ==================== MODAL LAPORAN ==================== -->
<div class="modal fade" id="modalLaporan" tabindex="-1" aria-labelledby="judulModalLaporan" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="judulModalLaporan">
                    <i class="fa-solid fa-chart-bar me-2"></i>Cetak Laporan Penjualan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">

                <!-- Tab Periode -->
                <div class="mb-3">
                    <label class="form-label">Pilih Periode</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="tab-mode-btn active" id="tabMinggu"
                                onclick="gantiTabLaporan('mingguan')">
                            <i class="fa-solid fa-calendar-week me-1"></i>Mingguan
                        </button>
                        <button type="button" class="tab-mode-btn" id="tabBulan"
                                onclick="gantiTabLaporan('bulanan')">
                            <i class="fa-solid fa-calendar-days me-1"></i>Bulanan
                        </button>
                        <button type="button" class="tab-mode-btn" id="tabCustom"
                                onclick="gantiTabLaporan('custom')">
                            <i class="fa-solid fa-calendar-range me-1"></i>Rentang
                        </button>
                    </div>
                </div>

                <!-- Field: Mingguan -->
                <div id="fieldMinggu">
                    <label class="form-label">Pilih Minggu</label>
                    <input type="week" id="inpWeek" class="form-control">
                    <small class="text-muted">Laporan akan mencakup Senin s.d. Minggu pada minggu yang dipilih.</small>
                </div>

                <!-- Field: Bulanan -->
                <div id="fieldBulan" style="display:none;">
                    <label class="form-label">Pilih Bulan & Tahun</label>
                    <input type="month" id="inpMonth" class="form-control">
                    <small class="text-muted">Laporan mencakup seluruh transaksi dalam bulan tersebut.</small>
                </div>

                <!-- Field: Rentang Custom -->
                <div id="fieldCustom" style="display:none;">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" id="inpStart" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" id="inpEnd" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Format output -->
                <div class="mt-3">
                    <label class="form-label">Format Tampilan</label>
                    <select id="formatLaporan" class="form-select">
                        <option value="detail">Detail (semua transaksi)</option>
                        <option value="ringkas">Ringkas (rekapitulasi saja)</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn-outline-s" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i>Batal
                </button>
                <button type="button" onclick="cetakLaporan()" class="btn-green">
                    <i class="fa-solid fa-print me-2"></i>Buka Laporan
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ==================== SCRIPTS ==================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ======================================================
   DATA PRODUK dari PHP
====================================================== */
const dataProduk = <?php echo json_encode($produk); ?>;
let rowCount = 0;

/* ======================================================
   JAM REAL-TIME & DATETIME-LOCAL AUTO-UPDATE
====================================================== */
function updateClock() {
    const now  = new Date();
    const tgl  = now.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
    const jam  = now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById('realTimeClock').textContent = tgl + ' | ' + jam + ' WIB';

    const pad = n => String(n).padStart(2,'0');
    const dtv = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
    document.getElementById('tanggalWaktu').value = dtv;
}
setInterval(updateClock, 1000);
updateClock();

/* ======================================================
   BUILD <option> MENU
====================================================== */
function buatOptionMenu() {
    let opt = '<option value="">— Pilih Menu —</option>';
    dataProduk.forEach(p => {
        opt += `<option value="${p.HARGA}" data-nama="${p.NAMA_PRODUK}">${p.NAMA_PRODUK}</option>`;
    });
    return opt;
}

/* ======================================================
   TAMBAH BARIS ITEM
====================================================== */
function tambahBaris() {
    rowCount++;
    const tr = document.createElement('tr');
    tr.className  = 'item-row';
    tr.dataset.row = rowCount;
    tr.innerHTML = `
        <td class="text-center fw-bold text-muted">${rowCount}</td>
        <td>
            <select class="form-select menuSelect" onchange="onMenuChange(${rowCount})">
                ${buatOptionMenu()}
            </select>
            <input type="hidden" class="namaMenu" value="">
        </td>
        <td>
            <select class="form-select ukuranSelect" onchange="onUkuranChange(${rowCount})">
                <option value="0">Regular</option>
                <option value="5000">Large (+Rp5.000)</option>
                <option value="8000">Extra (+Rp8.000)</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control qty text-center"
                   value="1" min="1" max="999"
                   oninput="hitungTotal()">
        </td>
        <td>
            <input type="number" class="form-control harga" value="0" readonly>
        </td>
        <td>
            <input type="text" class="form-control subtotal fw-semibold" value="Rp 0" readonly
                   style="color:#198754;">
        </td>
        <td class="text-center">
            <button type="button" class="btn-hapus-baris" onclick="hapusBaris(${rowCount})"
                    title="Hapus baris ini">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </td>`;
    document.getElementById('tbodyItem').appendChild(tr);
    nomorUlang();
}

function hapusBaris(r) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) { showToast('⚠️ Minimal 1 item harus ada.', false); return; }
    document.querySelector(`.item-row[data-row="${r}"]`).remove();
    nomorUlang();
    hitungTotal();
}

function nomorUlang() {
    document.querySelectorAll('.item-row').forEach((tr, i) => {
        tr.cells[0].textContent = i + 1;
    });
}

/* ======================================================
   EVENT PERUBAHAN MENU & UKURAN
====================================================== */
function onMenuChange(r) {
    const tr   = document.querySelector(`.item-row[data-row="${r}"]`);
    const sel  = tr.querySelector('.menuSelect');
    const opt  = sel.options[sel.selectedIndex];
    const ukur = parseInt(tr.querySelector('.ukuranSelect').value) || 0;
    tr.querySelector('.harga').value = (parseInt(sel.value) || 0) + ukur;
    tr.querySelector('.namaMenu').value = opt ? (opt.dataset.nama || '') : '';
    hitungTotal();
}

function onUkuranChange(r) {
    const tr  = document.querySelector(`.item-row[data-row="${r}"]`);
    const mnu = parseInt(tr.querySelector('.menuSelect').value) || 0;
    const ukr = parseInt(tr.querySelector('.ukuranSelect').value) || 0;
    tr.querySelector('.harga').value = mnu + ukr;
    hitungTotal();
}

/* ======================================================
   HITUNG GRAND TOTAL
====================================================== */
function hitungTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(tr => {
        const qty = parseInt(tr.querySelector('.qty').value)   || 0;
        const hrg = parseInt(tr.querySelector('.harga').value) || 0;
        const sub = qty * hrg;
        tr.querySelector('.subtotal').value = 'Rp ' + sub.toLocaleString('id-ID');
        total += sub;
    });
    const disc = parseFloat(document.getElementById('diskon').value) || 0;
    const akhir = total - (total * disc / 100);
    document.getElementById('grandTotal').textContent = 'Rp ' + Math.round(akhir).toLocaleString('id-ID');
    return Math.round(akhir);
}

document.getElementById('diskon').addEventListener('input', hitungTotal);

/* ======================================================
   SIMPAN TRANSAKSI VIA AJAX
====================================================== */
function simpanTransaksi() {
    const noTrx = document.getElementById('noTransaksi').value.trim();
    if (!noTrx) { showToast('⚠️ No. Transaksi wajib diisi!', false); return; }

    // Validasi item
    const rows = document.querySelectorAll('.item-row');
    let valid  = false;
    rows.forEach(tr => {
        const menu = tr.querySelector('.menuSelect').value;
        const qty  = parseInt(tr.querySelector('.qty').value);
        if (menu && qty > 0) valid = true;
    });
    if (!valid) { showToast('⚠️ Pilih minimal 1 menu dan isi qty!', false); return; }

    // Kumpulkan item
    const items = [];
    rows.forEach(tr => {
        const menu = tr.querySelector('.menuSelect').value;
        if (!menu) return;
        const ukuranSel = tr.querySelector('.ukuranSelect');
        items.push({
            nama  : tr.querySelector('.namaMenu').value,
            ukuran: ukuranSel.options[ukuranSel.selectedIndex].text,
            qty   : tr.querySelector('.qty').value,
            harga : tr.querySelector('.harga').value,
        });
    });

    const menuRingkas = items.map(i => `${i.nama} (${i.qty}x)`).join(', ');
    const payload = {
        no_transaksi : noTrx,
        tanggal_waktu: document.getElementById('tanggalWaktu').value,
        pelanggan    : document.getElementById('pelanggan').value.trim() || 'Walk-in Customer',
        menu         : menuRingkas,
        metode       : document.querySelector('input[name="metode_bayar"]:checked').value,
        diskon       : document.getElementById('diskon').value,
        catatan      : document.querySelector('textarea[name="catatan"]').value,
        total        : hitungTotal(),
        items        : items,
    };

    // Loading state
    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';

    fetch('simpan_transaksi.php', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body   : JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Simpan Transaksi';

        if (res.sukses) {
            showToast('✅ Transaksi ' + noTrx + ' berhasil disimpan!', true);

            // Tampilkan tombol invoice & riwayat
            const btnInv = document.getElementById('btnInvoice');
            btnInv.href  = 'cetak_invoice.php?id=' + encodeURIComponent(noTrx);
            btnInv.classList.remove('d-none');
            document.getElementById('btnRiwayat').classList.remove('d-none');

            // Reset form setelah 2 detik
            setTimeout(() => {
                resetFormItems();
                btnInv.classList.add('d-none');
                document.getElementById('btnRiwayat').classList.add('d-none');
            }, 2500);
        } else {
            showToast('❌ Gagal: ' + (res.pesan || 'Terjadi kesalahan server.'), false);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Simpan Transaksi';
        showToast('❌ Tidak dapat terhubung ke server.', false);
        console.error(err);
    });
}

/* ======================================================
   RESET FORM
====================================================== */
function resetFormItems() {
    document.getElementById('tbodyItem').innerHTML = '';
    rowCount = 0;
    tambahBaris();
    tambahBaris();
    document.getElementById('diskon').value    = '0';
    document.getElementById('pelanggan').value = '';
    document.getElementById('noTransaksi').value = '';
    document.getElementById('catatan').value   = '';
    document.querySelector('input[name="metode_bayar"][value="Tunai"]').checked = true;
    hitungTotal();
}

function resetForm() {
    if (confirm('Reset semua data form? Data yang belum disimpan akan hilang.')) {
        resetFormItems();
    }
}

/* ======================================================
   TOAST NOTIFIKASI
====================================================== */
function showToast(msg, sukses) {
    const t  = document.getElementById('toast');
    const ti = document.getElementById('toastIcon');
    const tm = document.getElementById('toastMsg');
    t.style.background = sukses ? '#198754' : '#dc3545';
    ti.className = sukses ? 'fa-solid fa-circle-check fa-lg' : 'fa-solid fa-circle-xmark fa-lg';
    tm.textContent = msg;
    t.style.display = 'flex';
    clearTimeout(t._hideTimer);
    t._hideTimer = setTimeout(() => { t.style.display = 'none'; }, 3800);
}

/* ======================================================
   MODAL LAPORAN — INIT DEFAULT VALUES
====================================================== */
(function initLaporan() {
    const now  = new Date();
    const pad  = n => String(n).padStart(2,'0');
    const yr   = now.getFullYear();
    const bl   = pad(now.getMonth() + 1);
    const hr   = pad(now.getDate());

    // Hitung nomor minggu ISO
    const startTahun = new Date(yr, 0, 1);
    const mingguKe   = Math.ceil(((now - startTahun) / 86400000 + startTahun.getDay() + 1) / 7);

    document.getElementById('inpWeek').value  = `${yr}-W${pad(mingguKe)}`;
    document.getElementById('inpMonth').value = `${yr}-${bl}`;
    document.getElementById('inpStart').value = `${yr}-${bl}-${hr}`;
    document.getElementById('inpEnd').value   = `${yr}-${bl}-${hr}`;
})();

let modeLaporan = 'mingguan';

function bukaModalLaporan() {
    const modal = new bootstrap.Modal(document.getElementById('modalLaporan'));
    modal.show();
}

function gantiTabLaporan(mode) {
    modeLaporan = mode;

    // Reset semua tab
    ['tabMinggu','tabBulan','tabCustom'].forEach(id => {
        document.getElementById(id).classList.remove('active');
    });
    ['fieldMinggu','fieldBulan','fieldCustom'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });

    // Aktifkan tab & field yang dipilih
    const map = { mingguan: ['tabMinggu','fieldMinggu'], bulanan: ['tabBulan','fieldBulan'], custom: ['tabCustom','fieldCustom'] };
    document.getElementById(map[mode][0]).classList.add('active');
    document.getElementById(map[mode][1]).style.display = '';
}

function cetakLaporan() {
    let url = 'cetak_laporan.php?mode=' + modeLaporan;
    url    += '&format=' + document.getElementById('formatLaporan').value;

    if (modeLaporan === 'mingguan') {
        const w = document.getElementById('inpWeek').value;
        if (!w) { showToast('⚠️ Pilih minggu terlebih dahulu!', false); return; }
        url += '&minggu=' + encodeURIComponent(w);

    } else if (modeLaporan === 'bulanan') {
        const m = document.getElementById('inpMonth').value;
        if (!m) { showToast('⚠️ Pilih bulan terlebih dahulu!', false); return; }
        url += '&bulan=' + encodeURIComponent(m);

    } else {
        const s = document.getElementById('inpStart').value;
        const e = document.getElementById('inpEnd').value;
        if (!s || !e)   { showToast('⚠️ Isi tanggal mulai dan selesai!', false); return; }
        if (s > e)       { showToast('⚠️ Tanggal mulai tidak boleh setelah selesai!', false); return; }
        url += '&start=' + encodeURIComponent(s) + '&end=' + encodeURIComponent(e);
    }

    // Tutup modal lalu buka laporan
    bootstrap.Modal.getInstance(document.getElementById('modalLaporan')).hide();
    setTimeout(() => window.open(url, '_blank'), 300);
}

/* ======================================================
   INIT — 2 baris default
====================================================== */
tambahBaris();
tambahBaris();
</script>
</body>
</html>