<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    http_response_code(403);
    exit;
}

header('Content-Type: application/json');

/* =========================
   STATISTIK
========================= */
$queryTotal = "
SELECT
    COUNT(*) TOTAL_PESANAN,
    NVL(SUM(TOTAL), 0) TOTAL_PENDAPATAN,
    COUNT(CASE WHEN TRUNC(TANGGAL) = TRUNC(SYSDATE) THEN 1 END) PESANAN_HARI_INI
FROM TRANSAKSI";

$stidTotal = oci_parse($conn, $queryTotal);
oci_execute($stidTotal);
$dataTotal = oci_fetch_assoc($stidTotal);

/* =========================
   DATA RIWAYAT
========================= */
$query = "SELECT * FROM TRANSAKSI ORDER BY TANGGAL DESC";
$stid = oci_parse($conn, $query);
oci_execute($stid);

$rows = [];
while ($row = oci_fetch_assoc($stid)) {
    $rows[] = [
        'ID_TRANSAKSI' => $row['ID_TRANSAKSI'],
        'TANGGAL'      => date('d M Y', strtotime($row['TANGGAL'])),
        'PELANGGAN'    => $row['PELANGGAN'],
        'MENU'         => $row['MENU'],
        'TOTAL'        => $row['TOTAL'],
        'METODE'       => $row['METODE'],
        'STATUS'       => $row['STATUS'],
    ];
}

echo json_encode([
    'total_pesanan'    => $dataTotal['TOTAL_PESANAN'],
    'total_pendapatan' => $dataTotal['TOTAL_PENDAPATAN'],
    'pesanan_hari_ini' => $dataTotal['PESANAN_HARI_INI'],
    'rows'             => $rows,
]);
?>