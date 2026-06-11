<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['role'])){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

// Fetch Transaction Detail
$query = "SELECT * FROM TRANSAKSI WHERE ID_TRANSAKSI = :id";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":id", $id);
oci_execute($stid);
$data = oci_fetch_assoc($stid);

if(!$data){
    echo "Transaksi tidak ditemukan.";
    exit;
}

$tanggal = date('d M Y', strtotime($data['TANGGAL']));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Courier New', Courier, monospace;
        }
        .invoice-card {
            background: white;
            max-width: 500px;
            margin: 50px auto;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-top: 10px solid #198754;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-logo {
            font-size: 30px;
            font-weight: bold;
            color: #198754;
        }
        .line {
            border-top: 2px dashed #dee2e6;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .total-row {
            font-size: 20px;
            font-weight: bold;
            color: #198754;
            margin-top: 20px;
        }
        @media print {
            body { background: white; }
            .no-print { display: none; }
            .invoice-card { box-shadow: none; margin: 0 auto; border-top: none; }
        }
    </style>
</head>
<body>

    <div class="invoice-card">
        <div class="invoice-header">
            <div class="invoice-logo">🍹 Sam's Juice</div>
            <p class="text-muted">Fresh & Healthy Drink</p>
            <p class="small mb-0">Jl. Contoh No. 123, Medan</p>
            <p class="small">HP: 0812-3456-7890</p>
        </div>

        <div class="line"></div>

        <div class="detail-row">
            <span>No. Transaksi:</span>
            <span class="fw-bold"><?php echo $data['ID_TRANSAKSI']; ?></span>
        </div>
        <div class="detail-row">
            <span>Tanggal:</span>
            <span><?php echo $tanggal; ?></span>
        </div>
        <div class="detail-row">
            <span>Pelanggan:</span>
            <span><?php echo $data['PELANGGAN']; ?></span>
        </div>
        <div class="detail-row">
            <span>Metode:</span>
            <span><?php echo $data['METODE']; ?></span>
        </div>

        <div class="line"></div>

        <div class="fw-bold mb-3">Pesanan:</div>
        <div class="detail-row">
            <span><?php echo $data['MENU']; ?></span>
            <span>Rp <?php echo number_format($data['TOTAL']); ?></span>
        </div>

        <div class="line"></div>

        <div class="detail-row total-row">
            <span>TOTAL:</span>
            <span>Rp <?php echo number_format($data['TOTAL']); ?></span>
        </div>

        <div class="text-center mt-5">
            <p class="small">Status: <span class="badge <?php echo ($data['STATUS'] == 'Selesai') ? 'bg-success' : 'bg-warning text-dark'; ?>"><?php echo $data['STATUS']; ?></span></p>
            <p class="fw-bold">Terima Kasih!</p>
            <p class="small text-muted">Selamat Menikmati Kesegaran Jus Kami</p>
        </div>

        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-success rounded-pill px-4">
                <i class="fas fa-print me-2"></i> Cetak Invoice
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary rounded-pill px-4 ms-2">
                Tutup
            </button>
        </div>
    </div>

</body>
</html>
