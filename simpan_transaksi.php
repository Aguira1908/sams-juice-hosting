<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    http_response_code(403);
    echo json_encode(['sukses' => false, 'pesan' => 'Akses ditolak']);
    exit;
}

header('Content-Type: application/json');

/* =====================
   TERIMA JSON dari AJAX
===================== */
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['sukses' => false, 'pesan' => 'Data tidak valid']);
    exit;
}

$noTrx     = trim($input['no_transaksi'] ?? '');
$pelanggan = trim($input['pelanggan']    ?? 'Customer');
$menu      = trim($input['menu']         ?? '');
$metode    = trim($input['metode']       ?? 'Tunai');
$total     = (int)($input['total']       ?? 0);

/* format tanggal sama persis seperti simpan_transaksi lama: DD-MON-YYYY */
$tanggal   = date('d-M-Y');   /* contoh: 12-May-2026 */

/* validasi */
if (!$noTrx || !$menu || $total <= 0) {
    echo json_encode(['sukses' => false, 'pesan' => 'Data tidak lengkap']);
    exit;
}

/* =====================
   CEK DUPLIKAT No. Transaksi
===================== */
$qCek  = "SELECT COUNT(*) CNT FROM TRANSAKSI WHERE ID_TRANSAKSI = :id";
$stCek = oci_parse($conn, $qCek);
oci_bind_by_name($stCek, ':id', $noTrx);
oci_execute($stCek);
$rowCek = oci_fetch_assoc($stCek);

if ((int)$rowCek['CNT'] > 0) {
    /* No. sudah ada → generate ulang nomor baru dan kirim ke client */
    $nextNo = generateNextNo($conn);
    echo json_encode([
        'sukses'  => false,
        'pesan'   => 'No. Transaksi sudah ada, silakan coba lagi',
        'next_no' => $nextNo,
    ]);
    exit;
}

/* =====================
   INSERT ke TRANSAKSI
   Kolom: ID_TRANSAKSI, TANGGAL, PELANGGAN, MENU, TOTAL, METODE, STATUS
   (sesuai struktur tabel asli — tanpa DISKON)
===================== */
$status = 'Menunggu';

$query = "INSERT INTO TRANSAKSI
          (ID_TRANSAKSI, TANGGAL, PELANGGAN, MENU, TOTAL, METODE, STATUS)
          VALUES
          (:id,
           TO_DATE(:tanggal, 'DD-MON-YYYY'),
           :pelanggan,
           :menu,
           :total,
           :metode,
           :status)";

$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ':id',        $noTrx);
oci_bind_by_name($stid, ':tanggal',   $tanggal);
oci_bind_by_name($stid, ':pelanggan', $pelanggan);
oci_bind_by_name($stid, ':menu',      $menu);
oci_bind_by_name($stid, ':total',     $total);
oci_bind_by_name($stid, ':metode',    $metode);
oci_bind_by_name($stid, ':status',    $status);

$result = @oci_execute($stid);

if (!$result) {
    $err = oci_error($stid);
    echo json_encode(['sukses' => false, 'pesan' => $err['message']]);
    exit;
}

/* =====================
   GENERATE No. Transaksi BERIKUTNYA
===================== */
$nextNo = generateNextNo($conn);

echo json_encode([
    'sukses'  => true,
    'pesan'   => 'Transaksi berhasil disimpan',
    'id'      => $noTrx,
    'next_no' => $nextNo,
]);

/* =====================
   FUNGSI GENERATE NEXT NO.
===================== */
function generateNextNo($conn) {
    $tgl     = date('Ymd');
    $prefix  = "TRX-$tgl-";
    $like    = $prefix . '%';

    $q  = "SELECT COUNT(*) CNT FROM TRANSAKSI WHERE ID_TRANSAKSI LIKE :prefix";
    $st = oci_parse($conn, $q);
    oci_bind_by_name($st, ':prefix', $like);
    oci_execute($st);
    $row = oci_fetch_assoc($st);
    $num = (int)$row['CNT'] + 1;

    return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
}
?>