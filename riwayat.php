<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("Location: login.php");
    exit;
}

$tanggal = date('d F Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Pemesanan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
* { font-family: 'Poppins', sans-serif; }
body { background: #eef7f0; }

/* SIDEBAR */
.sidebar {
    width: 260px; height: 100vh; position: fixed;
    top: 0; left: 0;
    background: linear-gradient(180deg, #198754, #0f5132);
    padding: 30px 20px; color: white;
}
.logo { font-size: 32px; font-weight: 800; margin-bottom: 40px; }
.menu-link {
    display: block; color: white; text-decoration: none;
    padding: 15px 18px; border-radius: 16px; margin-bottom: 12px;
    transition: 0.3s; font-weight: 500;
}
.menu-link:hover { background: rgba(255,255,255,0.15); color: white; transform: translateX(8px); }
.active-menu { background: white; color: #198754 !important; }

/* MAIN */
.main { margin-left: 260px; padding: 30px; }

/* TOPBAR */
.topbar {
    background: white; padding: 25px 30px;
    border-radius: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}
.profile-img {
    width: 65px; height: 65px; border-radius: 50%;
    object-fit: cover; border: 4px solid #198754;
}

/* BOX */
.white-box {
    background: white; border-radius: 25px; padding: 30px;
    margin-top: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    animation: fadeUp 0.8s ease;
}

/* CARD */
.stat-card {
    background: white; border-radius: 25px; padding: 30px;
    text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: 0.3s;
}
.stat-card:hover { transform: translateY(-8px); }

/* TABLE */
.table thead { background: #198754; color: white; }

/* BADGE */
.badge-custom { padding: 10px 15px; border-radius: 30px; font-size: 13px; }

/* SEARCH */
.search-box { border-radius: 30px; padding: 12px 20px; }

/* STATUS INDIKATOR */
.refresh-indicator {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; color: #6c757d;
}
.dot-live {
    width: 8px; height: 8px; border-radius: 50%; background: #198754;
    animation: blink 1.2s infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }

/* HIGHLIGHT baris baru */
@keyframes highlightRow {
    0%   { background-color: #d1f5dc; }
    100% { background-color: transparent; }
}
.row-new { animation: highlightRow 2s ease; }

/* tombol aksi status */
.btn-selesai {
    border-radius: 20px; font-size: 11px; padding: 4px 10px;
    font-weight: 600; border: 1.5px solid #198754;
    color: #198754; background: white; transition: 0.2s; white-space: nowrap;
}
.btn-selesai:hover:not(:disabled) { background: #198754; color: white; }
.btn-selesai:disabled { opacity: 0.45; cursor: not-allowed; }
.btn-batal-trx {
    border-radius: 20px; font-size: 11px; padding: 4px 10px;
    font-weight: 600; border: 1.5px solid #dc3545;
    color: #dc3545; background: white; transition: 0.2s; white-space: nowrap;
}
.btn-batal-trx:hover:not(:disabled) { background: #dc3545; color: white; }
.btn-batal-trx:disabled { opacity: 0.45; cursor: not-allowed; }

/* toast notifikasi */
.toast-riwayat {
    position: fixed; bottom: 24px; right: 24px; z-index: 9999;
    padding: 12px 20px; border-radius: 14px; font-size: 13px; font-weight: 600;
    color: white; display: none; align-items: center; gap: 8px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
@keyframes slideUp { from { transform: translateY(20px); opacity:0; } to { transform: translateY(0); opacity:1; } }

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}

@media(max-width:991px) {
    .sidebar { position: relative; width: 100%; height: auto; }
    .main { margin-left: 0; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="toast-riwayat" id="toastRiwayat"></div>
<div class="sidebar">
    <div class="logo">🍹 Sam's Juice</div>
    <a href="kasir.php"     class="menu-link"><i class="fa-solid fa-house me-2"></i>Dashboard</a>
    <a href="transaksi.php" class="menu-link"><i class="fa-solid fa-cart-shopping me-2"></i>Transaksi</a>
    <a href="data_menu.php" class="menu-link"><i class="fa-solid fa-glass-water me-2"></i>Data Menu</a>
    <a href="riwayat.php"   class="menu-link active-menu"><i class="fa-solid fa-clock-rotate-left me-2"></i>Riwayat</a>
    <a href="logout.php"    class="menu-link"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h2 class="fw-bold text-success">Riwayat Pemesanan</h2>
            <p class="text-muted mb-0" id="real-time-clock"><?php echo $tanggal; ?> | 00:00:00 WIB</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="profile-box d-flex align-items-center gap-3">
                <img src="image/kasir1.jpeg" class="profile-img">
                <div>
                    <h5 class="fw-bold mb-0"><?php echo $_SESSION['username']; ?></h5>
                    <p class="text-muted mb-0">Kasir Sam's Juice</p>
                </div>
            </div>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="row g-4 mt-1">
        <div class="col-md-4">
            <div class="stat-card">
                <h1 class="fw-bold text-success" id="stat-total-pesanan">—</h1>
                <p class="mb-0">Total Pesanan</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h1 class="fw-bold text-primary" id="stat-total-pendapatan">—</h1>
                <p class="mb-0">Total Pendapatan</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h1 class="fw-bold text-warning" id="stat-hari-ini">—</h1>
                <p class="mb-0">Pesanan Hari Ini</p>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="white-box">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div class="d-flex align-items-center gap-3">
                <h3 class="fw-bold mb-0">Data Riwayat Pesanan</h3>
                <span class="refresh-indicator">
                    <span class="dot-live"></span>
                    Live · diperbarui setiap <span id="interval-label">5</span>s
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select class="form-select form-select-sm rounded-pill" id="interval-select" style="width:auto">
                    <option value="3000">3 detik</option>
                    <option value="5000" selected>5 detik</option>
                    <option value="10000">10 detik</option>
                    <option value="30000">30 detik</option>
                </select>
                <input type="text" id="search-box" class="form-control search-box"
                    placeholder="Cari transaksi..." style="max-width:220px;">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Menu</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-riwayat">
                    <tr><td colspan="9" class="text-center text-muted py-4">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="text-end mt-2">
            <small class="text-muted">Terakhir diperbarui: <span id="last-update">—</span></small>
        </div>
    </div>

</div><!-- /main -->

<script>
/* =====================
   JAM REAL-TIME
===================== */
function updateClock() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById('real-time-clock').innerText = dateStr + ' | ' + timeStr + ' WIB';
}
setInterval(updateClock, 1000);
updateClock();

/* =====================
   FORMAT RUPIAH
===================== */
function fmtRupiah(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

/* =====================
   BADGE STATUS
===================== */
function badgeStatus(status) {
    const map = {
        'Selesai' : ['bg-success',  'Selesai'],
        'Diproses': ['bg-primary',  'Diproses'],
        'Batal'   : ['bg-danger',   'Batal'],
    };
    const [cls, label] = map[status] || ['bg-warning text-dark', 'Menunggu'];
    return `<span class="badge ${cls} badge-custom">${label}</span>`;
}

/* =====================
   FETCH & RENDER
===================== */
let prevFirstId = null;   // deteksi transaksi baru di baris pertama

function fetchRiwayat() {
    fetch('api_riwayat.php')
        .then(r => r.json())
        .then(data => {
            /* statistik */
            document.getElementById('stat-total-pesanan').textContent   = data.total_pesanan;
            document.getElementById('stat-total-pendapatan').textContent = fmtRupiah(data.total_pendapatan);
            document.getElementById('stat-hari-ini').textContent         = data.pesanan_hari_ini;

            /* tabel */
            const keyword = document.getElementById('search-box').value.toLowerCase();
            let filtered  = data.rows;

            if (keyword) {
                filtered = filtered.filter(r =>
                    r.ID_TRANSAKSI.toLowerCase().includes(keyword) ||
                    r.PELANGGAN.toLowerCase().includes(keyword)    ||
                    r.MENU.toLowerCase().includes(keyword)         ||
                    r.STATUS.toLowerCase().includes(keyword)
                );
            }

            const tbody = document.getElementById('tbody-riwayat');
            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data</td></tr>';
                return;
            }

            /* deteksi baris baru (ID pertama berubah) */
            const newFirstId = data.rows.length ? data.rows[0].ID_TRANSAKSI : null;
            const hasNew     = prevFirstId !== null && newFirstId !== prevFirstId;
            prevFirstId      = newFirstId;

            let html = '';
            filtered.forEach((r, i) => {
                const isNew    = hasNew && i === 0;
                const isMenunggu = (r.STATUS === 'Menunggu' || r.STATUS === 'Diproses');

                /* tombol aksi status — hanya muncul kalau belum Selesai/Batal */
                const btnAksi = isMenunggu
                    ? `<button class="btn-selesai me-1" onclick="ubahStatus('${r.ID_TRANSAKSI}','Selesai',this)" title="Tandai Selesai">
                           <i class="fas fa-check me-1"></i>Selesai
                       </button>
                       <button class="btn-batal-trx" onclick="ubahStatus('${r.ID_TRANSAKSI}','Batal',this)" title="Batalkan">
                           <i class="fas fa-times me-1"></i>Batal
                       </button>`
                    : `<span class="text-muted" style="font-size:11px">—</span>`;

                html += `<tr id="row-${r.ID_TRANSAKSI}" class="${isNew ? 'row-new' : ''}">
                    <td>${i + 1}</td>
                    <td style="font-size:12px">${r.ID_TRANSAKSI}</td>
                    <td>${r.TANGGAL}</td>
                    <td>${r.PELANGGAN}</td>
                    <td>${r.MENU}</td>
                    <td>${fmtRupiah(r.TOTAL)}</td>
                    <td>${r.METODE}</td>
                    <td id="status-${r.ID_TRANSAKSI}">${badgeStatus(r.STATUS)}</td>
                    <td style="white-space:nowrap">
                        <div id="aksi-${r.ID_TRANSAKSI}" class="d-inline-flex align-items-center gap-1 me-1">
                            ${btnAksi}
                        </div>
                        <a href="cetak_invoice.php?id=${r.ID_TRANSAKSI}" target="_blank"
                           class="btn btn-sm btn-outline-success rounded-pill" title="Cetak Invoice">
                           <i class="fas fa-print"></i>
                        </a>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;

            /* waktu update */
            document.getElementById('last-update').textContent =
                new Date().toLocaleTimeString('id-ID', { hour12: false });
        })
        .catch(() => {
            document.getElementById('tbody-riwayat').innerHTML =
                '<tr><td colspan="9" class="text-center text-danger py-4">Gagal memuat data</td></tr>';
        });
}

/* =====================
   INTERVAL OTOMATIS
===================== */
let refreshTimer = null;

function startRefresh(ms) {
    clearInterval(refreshTimer);
    refreshTimer = setInterval(fetchRiwayat, ms);
}

document.getElementById('interval-select').addEventListener('change', function () {
    const ms = parseInt(this.value);
    document.getElementById('interval-label').textContent = ms / 1000;
    startRefresh(ms);
});

/* pencarian real-time (filter lokal) */
document.getElementById('search-box').addEventListener('input', fetchRiwayat);

/* =====================
   UBAH STATUS (AJAX)
===================== */
function ubahStatus(id, status, btn) {
    const label  = status === 'Selesai' ? 'selesai' : 'dibatalkan';
    if (!confirm(`Tandai transaksi ${id} sebagai ${label}?`)) return;

    /* disable semua tombol di baris ini */
    const aksiDiv = document.getElementById('aksi-' + id);
    aksiDiv.querySelectorAll('button').forEach(b => { b.disabled = true; });

    fetch('update_status.php', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body   : JSON.stringify({ id: id, status: status }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.sukses) {
            /* update badge status langsung tanpa tunggu interval */
            document.getElementById('status-' + id).innerHTML = badgeStatus(status);
            /* hilangkan tombol aksi karena sudah final */
            aksiDiv.innerHTML = '<span class="text-muted" style="font-size:11px">—</span>';
            showToast('✅ Transaksi ' + id + ' ' + label, '#198754');
            /* refresh statistik segera */
            fetchRiwayat();
        } else {
            aksiDiv.querySelectorAll('button').forEach(b => { b.disabled = false; });
            showToast('❌ Gagal: ' + (res.pesan || 'Error'), '#dc3545');
        }
    })
    .catch(() => {
        aksiDiv.querySelectorAll('button').forEach(b => { b.disabled = false; });
        showToast('❌ Tidak dapat terhubung ke server', '#dc3545');
    });
}

/* =====================
   TOAST
===================== */
function showToast(msg, warna) {
    const t = document.getElementById('toastRiwayat');
    t.style.background  = warna;
    t.style.animation   = 'none';
    t.textContent       = msg;
    t.style.display     = 'flex';
    t.style.animation   = 'slideUp 0.35s ease';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => { t.style.display = 'none'; }, 3000);
}

/* jalankan pertama kali */
fetchRiwayat();
startRefresh(5000);
</script>
</body>
</html>