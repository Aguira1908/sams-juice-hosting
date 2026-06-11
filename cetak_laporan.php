<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("Location: login.php"); exit;
}

/* =============================================
   TENTUKAN RENTANG TANGGAL BERDASARKAN MODE
============================================= */
$mode   = $_GET['mode']   ?? 'bulanan';
$format = $_GET['format'] ?? 'detail';
$label  = '';
$where  = '';

if ($mode === 'mingguan' && isset($_GET['minggu'])) {
    // format: 2025-W23
    $bagian = explode('-W', $_GET['minggu']);
    $yr = (int)($bagian[0] ?? date('Y'));
    $wk = (int)($bagian[1] ?? 1);

    $sen = new DateTime();
    $sen->setISODate($yr, $wk, 1);   // Senin
    $mgg = clone $sen;
    $mgg->modify('+6 days');          // Minggu

    $tgl1  = $sen->format('Y-m-d');
    $tgl2  = $mgg->format('Y-m-d');
    $label = 'Minggu ke-' . $wk . ' Tahun ' . $yr
           . ' (' . $sen->format('d M') . ' – ' . $mgg->format('d M Y') . ')';
    $where = "TRUNC(TANGGAL) BETWEEN TO_DATE('$tgl1','YYYY-MM-DD')
                                       AND TO_DATE('$tgl2','YYYY-MM-DD')";

} elseif ($mode === 'bulanan' && isset($_GET['bulan'])) {
    // format: 2025-06
    $bagian = explode('-', $_GET['bulan']);
    $yr = (int)($bagian[0] ?? date('Y'));
    $bl = (int)($bagian[1] ?? date('n'));
    $label = date('F Y', mktime(0,0,0,$bl,1,$yr));
    $label = iconv('UTF-8', 'UTF-8//IGNORE', $label); // aman di Oracle
    $blPad = str_pad($bl, 2, '0', STR_PAD_LEFT);
    $where = "TO_CHAR(TANGGAL,'YYYY-MM') = '$yr-$blPad'";

} else {
    // Rentang custom
    $tgl1  = $_GET['start'] ?? date('Y-m-01');
    $tgl2  = $_GET['end']   ?? date('Y-m-d');
    // Sanitasi dasar (hanya tanggal YYYY-MM-DD)
    $tgl1  = preg_replace('/[^0-9\-]/', '', $tgl1);
    $tgl2  = preg_replace('/[^0-9\-]/', '', $tgl2);
    $label = date('d M Y', strtotime($tgl1)) . ' s.d. ' . date('d M Y', strtotime($tgl2));
    $where = "TRUNC(TANGGAL) BETWEEN TO_DATE('$tgl1','YYYY-MM-DD')
                                       AND TO_DATE('$tgl2','YYYY-MM-DD')";
}

/* =============================================
   QUERY DETAIL TRANSAKSI
============================================= */
$sqlDetail = "SELECT ID_TRANSAKSI,
                     TO_CHAR(TANGGAL,'DD-MM-YYYY') AS TGL,
                     TO_CHAR(TANGGAL,'HH24:MI') AS JAM,
                     PELANGGAN,
                     MENU,
                     METODE,
                     TOTAL
              FROM TRANSAKSI
              WHERE $where
              ORDER BY TANGGAL";
$stDetail = oci_parse($conn, $sqlDetail);
oci_execute($stDetail);

$rows       = [];
$grandTotal = 0;
$jmlTrx     = 0;
while ($r = oci_fetch_assoc($stDetail)) {
    $rows[]      = $r;
    $grandTotal += (int)$r['TOTAL'];
    $jmlTrx++;
}

/* =============================================
   REKAP PER METODE BAYAR
============================================= */
$sqlMetode = "SELECT METODE,
                     COUNT(*) AS JML,
                     SUM(TOTAL) AS SUBTOTAL
              FROM TRANSAKSI
              WHERE $where
              GROUP BY METODE
              ORDER BY METODE";
$stMetode = oci_parse($conn, $sqlMetode);
oci_execute($stMetode);
$metodes = [];
while ($m = oci_fetch_assoc($stMetode)) $metodes[] = $m;

/* =============================================
   REKAP PER HARI (untuk grafik sederhana)
============================================= */
$sqlHarian = "SELECT TO_CHAR(TANGGAL,'DD-MM-YYYY') AS HARI,
                     COUNT(*) AS JML,
                     SUM(TOTAL) AS TOTAL_HARI
              FROM TRANSAKSI
              WHERE $where
              GROUP BY TO_CHAR(TANGGAL,'DD-MM-YYYY')
              ORDER BY MIN(TANGGAL)";
$stHarian = oci_parse($conn, $sqlHarian);
oci_execute($stHarian);
$harianData = [];
while ($h = oci_fetch_assoc($stHarian)) $harianData[] = $h;

/* =============================================
   REKAP MENU TERLARIS
============================================= */
$sqlMenu = "SELECT MENU,
                   COUNT(*) AS JML_TRX,
                   SUM(TOTAL) AS OMSET
            FROM TRANSAKSI
            WHERE $where
            GROUP BY MENU
            ORDER BY COUNT(*) DESC
            FETCH FIRST 10 ROWS ONLY";
$stMenu = oci_parse($conn, $sqlMenu);
oci_execute($stMenu);
$menuData = [];
while ($mn = oci_fetch_assoc($stMenu)) $menuData[] = $mn;

/* =============================================
   HELPER
============================================= */
function rupiah($n) {
    return 'Rp ' . number_format((int)$n, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Penjualan – <?php echo htmlspecialchars($label); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* ============ BASE ============ */
* { font-family: 'Segoe UI', Arial, sans-serif; box-sizing: border-box; }
body { background: #f5faf7; margin: 0; padding: 0; font-size: 13px; color: #222; }

/* ============ CONTAINER ============ */
.laporan-wrap { max-width: 960px; margin: 0 auto; padding: 28px 24px 60px; }

/* ============ TOOLBAR (tidak ikut print) ============ */
.toolbar {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    background: white; border-bottom: 2px solid #e8f5ef;
    padding: 14px 24px; position: sticky; top: 0; z-index: 100;
}
.toolbar .ttl { font-size: 15px; font-weight: 700; color: #198754; margin-right: auto; }
.btn-cetak {
    background: #198754; color: white; border: none;
    border-radius: 20px; padding: 9px 22px; font-size: 13px;
    font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px;
}
.btn-cetak:hover { background: #157347; transform: translateY(-1px); }
.btn-tutup {
    background: transparent; color: #6c757d;
    border: 1.5px solid #dee2e6; border-radius: 20px;
    padding: 8px 20px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: 0.2s;
}
.btn-tutup:hover { border-color: #adb5bd; }

/* ============ KOP ============ */
.kop {
    background: white; border-radius: 16px; padding: 24px 28px;
    border-left: 6px solid #198754; margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.kop-brand { display: flex; align-items: center; gap: 14px; }
.kop-logo { font-size: 40px; }
.kop-nama { font-size: 22px; font-weight: 800; color: #198754; }
.kop-sub  { font-size: 12px; color: #666; margin-top: 2px; }
.kop-divider { border: none; border-top: 1.5px solid #e0ece5; margin: 14px 0; }
.kop-info { display: flex; flex-wrap: wrap; gap: 24px; font-size: 12.5px; }
.kop-info span b { color: #198754; }

/* ============ STAT CARDS ============ */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap: 14px; margin-bottom: 20px; }
.stat-card {
    background: white; border-radius: 14px; padding: 18px 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06); text-align: center;
    border-top: 3px solid transparent;
}
.stat-card.green  { border-top-color: #198754; }
.stat-card.blue   { border-top-color: #0d6efd; }
.stat-card.amber  { border-top-color: #e67e22; }
.stat-card.teal   { border-top-color: #20c997; }
.stat-card .val   { font-size: 20px; font-weight: 800; color: #198754; line-height: 1.2; }
.stat-card.blue .val  { color: #0d6efd; }
.stat-card.amber .val { color: #e67e22; }
.stat-card.teal .val  { color: #20c997; }
.stat-card .lbl   { font-size: 11px; color: #888; font-weight: 600; letter-spacing: 0.3px; margin-top: 4px; text-transform: uppercase; }

/* ============ SECTION CARD ============ */
.sec-card {
    background: white; border-radius: 14px; padding: 20px 22px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 18px;
}
.sec-title {
    font-size: 13.5px; font-weight: 700; color: #198754;
    margin-bottom: 14px; padding-bottom: 10px;
    border-bottom: 1.5px solid #e8f5ef; display: flex; align-items: center; gap: 8px;
}

/* ============ REKAP METODE ============ */
.metode-grid { display: flex; flex-wrap: wrap; gap: 12px; }
.metode-item {
    flex: 1; min-width: 120px; background: #f4fdf6;
    border: 1.5px solid #d1fae5; border-radius: 12px;
    padding: 14px 16px; text-align: center;
}
.metode-item .m-nama { font-weight: 700; color: #0f5132; font-size: 14px; }
.metode-item .m-jml  { font-size: 11px; color: #666; margin: 3px 0; }
.metode-item .m-tot  { font-weight: 700; color: #198754; font-size: 13px; }

/* ============ BAR CHART HARIAN ============ */
.bar-wrap { display: flex; align-items: flex-end; gap: 6px; height: 110px; }
.bar-col  { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; min-width: 0; }
.bar-val  { font-size: 9px; color: #555; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 60px; }
.bar      { background: linear-gradient(180deg, #34d399, #198754); border-radius: 4px 4px 0 0; width: 100%; transition: 0.3s; min-height: 3px; }
.bar-lbl  { font-size: 9px; color: #888; text-align: center; transform: rotate(-35deg); transform-origin: top left; margin-left: 6px; white-space: nowrap; }

/* ============ TABLE ============ */
.tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
.tbl thead th {
    background: #198754; color: white; padding: 10px 12px;
    text-align: left; font-weight: 600; font-size: 12px; white-space: nowrap;
}
.tbl thead th:first-child { border-radius: 8px 0 0 0; }
.tbl thead th:last-child  { border-radius: 0 8px 0 0; }
.tbl tbody td { padding: 9px 12px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
.tbl tbody tr:last-child td { border-bottom: none; }
.tbl tbody tr:hover td { background: #f4fdf6; }
.tbl tfoot td {
    background: #e8f5ef; font-weight: 700; padding: 11px 12px;
    color: #0f5132; border-top: 2px solid #b7e4c7;
}
.badge-met {
    display: inline-block; padding: 3px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700; background: #d1fae5; color: #0f5132;
}
.badge-met.qris     { background: #dbeafe; color: #1e40af; }
.badge-met.transfer { background: #fef3c7; color: #92400e; }
.text-right { text-align: right; }

/* ============ KOSONG ============ */
.empty-state {
    text-align: center; padding: 50px 20px; color: #aaa;
}
.empty-state .icon { font-size: 48px; margin-bottom: 12px; }

/* ============ FOOTER ============ */
.lap-footer { text-align: center; font-size: 11px; color: #bbb; margin-top: 30px; padding-top: 16px; border-top: 1px solid #eee; }

/* ============ PRINT ============ */
@media print {
    .toolbar { display: none !important; }
    body { background: white !important; padding: 0; }
    .laporan-wrap { max-width: 100%; padding: 16px; }
    .kop, .sec-card, .stat-card { box-shadow: none !important; }
    .bar { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    thead { background-color: #198754 !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
}
</style>
</head>
<body>

<!-- TOOLBAR -->
<div class="toolbar no-print">
    <span class="ttl">🍹 Laporan Penjualan — <?php echo htmlspecialchars($label); ?></span>
    <button class="btn-cetak" onclick="window.print()">
        🖨️ Cetak / Simpan PDF
    </button>
    <button class="btn-tutup" onclick="window.close()">✖ Tutup</button>
</div>

<div class="laporan-wrap">

    <!-- KOP SURAT -->
    <div class="kop">
        <div class="kop-brand">
            <div class="kop-logo">🍹</div>
            <div>
                <div class="kop-nama">Sam's Juice</div>
                <div class="kop-sub">Laporan Penjualan — <?php echo htmlspecialchars($label); ?></div>
            </div>
        </div>
        <hr class="kop-divider">
        <div class="kop-info">
            <span><b>Periode:</b> <?php echo htmlspecialchars($label); ?></span>
            <span><b>Mode:</b> <?php echo ucfirst($mode); ?></span>
            <span><b>Format:</b> <?php echo $format === 'detail' ? 'Detail' : 'Ringkas'; ?></span>
            <span><b>Dicetak:</b> <?php echo date('d M Y, H:i'); ?> WIB</span>
            <span><b>Kasir:</b> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="stat-grid">
        <div class="stat-card green">
            <div class="val"><?php echo rupiah($grandTotal); ?></div>
            <div class="lbl">Total Pendapatan</div>
        </div>
        <div class="stat-card blue">
            <div class="val"><?php echo $jmlTrx; ?></div>
            <div class="lbl">Total Transaksi</div>
        </div>
        <div class="stat-card amber">
            <div class="val"><?php echo $jmlTrx ? rupiah(round($grandTotal / $jmlTrx)) : 'Rp 0'; ?></div>
            <div class="lbl">Rata-rata / Transaksi</div>
        </div>
        <div class="stat-card teal">
            <div class="val"><?php echo count($harianData); ?></div>
            <div class="lbl">Hari Aktif</div>
        </div>
    </div>

    <!-- REKAP METODE BAYAR -->
    <?php if (!empty($metodes)): ?>
    <div class="sec-card">
        <div class="sec-title">💳 Rekap per Metode Bayar</div>
        <div class="metode-grid">
            <?php foreach ($metodes as $m): ?>
            <div class="metode-item">
                <div class="m-nama">
                    <?php
                    $ikon = ['Tunai'=>'💵','QRIS'=>'📱','Transfer'=>'🏦'];
                    echo ($ikon[$m['METODE']] ?? '💰') . ' ' . htmlspecialchars($m['METODE']);
                    ?>
                </div>
                <div class="m-jml"><?php echo $m['JML']; ?> transaksi</div>
                <div class="m-tot"><?php echo rupiah($m['SUBTOTAL']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- GRAFIK PENJUALAN HARIAN -->
    <?php if (!empty($harianData) && count($harianData) > 1): ?>
    <div class="sec-card">
        <div class="sec-title">📊 Grafik Penjualan Harian</div>
        <?php
        $maxTotal = max(array_column($harianData, 'TOTAL_HARI'));
        ?>
        <div class="bar-wrap">
            <?php foreach ($harianData as $hd): ?>
            <?php $pct = $maxTotal > 0 ? round(($hd['TOTAL_HARI'] / $maxTotal) * 100) : 0; ?>
            <div class="bar-col">
                <div class="bar-val"><?php echo rupiah($hd['TOTAL_HARI']); ?></div>
                <div class="bar" style="height:<?php echo max(3,$pct); ?>px;" title="<?php echo $hd['HARI']; ?>: <?php echo rupiah($hd['TOTAL_HARI']); ?>"></div>
                <div class="bar-lbl"><?php echo $hd['HARI']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- TABEL DETAIL (hanya jika format=detail) -->
    <?php if ($format === 'detail'): ?>
    <div class="sec-card">
        <div class="sec-title">📋 Detail Seluruh Transaksi</div>
        <?php if (empty($rows)): ?>
            <div class="empty-state">
                <div class="icon">🔍</div>
                <div>Tidak ada data transaksi pada periode ini.</div>
            </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Pelanggan</th>
                        <th>Menu</th>
                        <th>Metode</th>

                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $r): ?>
                    <tr>
                        <td><?php echo $i+1; ?></td>
                        <td style="font-weight:700;color:#198754;"><?php echo htmlspecialchars($r['ID_TRANSAKSI']); ?></td>
                        <td><?php echo $r['TGL']; ?></td>
                        <td><?php echo $r['JAM']; ?></td>
                        <td><?php echo htmlspecialchars($r['PELANGGAN']); ?></td>
                        <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?php echo htmlspecialchars($r['MENU']); ?>">
                            <?php echo htmlspecialchars($r['MENU']); ?>
                        </td>
                        <td>
    <?php
    $cls = ['QRIS'=>'qris','Transfer'=>'transfer'];
    $c = $cls[$r['METODE']] ?? '';
    ?>
    <span class="badge-met <?php echo $c; ?>">
        <?php echo htmlspecialchars($r['METODE']); ?>
    </span>
</td>
<td class="text-right" style="font-weight:700;">
    <?php echo rupiah($r['TOTAL']); ?>
</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-right">
                            GRAND TOTAL &nbsp;(<?php echo $jmlTrx; ?> transaksi)
                        </td>
                        <td class="text-right"><?php echo rupiah($grandTotal); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; /* end format detail */ ?>

    <!-- REKAP HARIAN (Tabel) -->
    <?php if (!empty($harianData)): ?>
    <div class="sec-card">
        <div class="sec-title">📅 Rekapitulasi per Hari</div>
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th class="text-right">Jml Transaksi</th>
                        <th class="text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($harianData as $i => $hd): ?>
                    <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo $hd['HARI']; ?></td>
                        <td class="text-right"><?php echo $hd['JML']; ?></td>
                        <td class="text-right" style="font-weight:700;color:#198754;"><?php echo rupiah($hd['TOTAL_HARI']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>    
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right">TOTAL</td>
                        <td class="text-right"><?php echo $jmlTrx; ?></td>
                        <td class="text-right"><?php echo rupiah($grandTotal); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- FOOTER -->
    <div class="lap-footer">
        Laporan digenerate otomatis oleh Sistem POS Sam's Juice &nbsp;·&nbsp;
        <?php echo date('d M Y H:i'); ?> WIB &nbsp;·&nbsp;
        Kasir: <?php echo htmlspecialchars($_SESSION['username']); ?>
    </div>

</div><!-- /laporan-wrap -->
</body>
</html>