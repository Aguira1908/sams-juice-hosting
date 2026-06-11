<?php
session_start();
include "koneksi.php";

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    echo json_encode([
        'sukses' => false,
        'pesan' => 'Akses ditolak'
    ]);
    exit;
}

/* ambil JSON POST kalau ada */
$input = json_decode(file_get_contents('php://input'), true);

if ($input && !empty($input['id'])) {
    $id = $input['id'];
    $newStatus = $input['status'] ?? '';
} else {
    /* fallback GET untuk tombol link biasa */
    $id = $_GET['id'] ?? '';
    $newStatus = $_GET['status'] ?? '';
}

if (empty($id)) {
    echo json_encode([
        'sukses' => false,
        'pesan' => 'ID transaksi kosong'
    ]);
    exit;
}

if (!in_array($newStatus, ['Diproses', 'Selesai', 'Batal'])) {
    echo json_encode([
        'sukses' => false,
        'pesan' => 'Status tidak valid'
    ]);
    exit;
}

$query = "UPDATE TRANSAKSI SET STATUS = :status WHERE ID_TRANSAKSI = :id";
$stid = oci_parse($conn, $query);

oci_bind_by_name($stid, ':status', $newStatus);
oci_bind_by_name($stid, ':id', $id);

$result = oci_execute($stid);

if ($result) {
    /* kalau request dari fetch (POST JSON) */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode([
            'sukses' => true,
            'id' => $id,
            'status' => $newStatus
        ]);
    } else {
        /* kalau dari link biasa */
        header("Location: kasir.php");
    }
} else {
    $err = oci_error($stid);

    echo json_encode([
        'sukses' => false,
        'pesan' => $err['message']
    ]);
}
?>