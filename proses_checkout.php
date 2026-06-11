<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: menu.php');
    exit;
}

$nama   = $_POST['nama'] ?? '';
$nohp   = $_POST['nohp'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$metode = $_POST['metode'] ?? 'Tunai';
$wallet = $_POST['wallet'] ?? '';

$total = 0;
$subtotal = 0;
$ongkir = 5000;
$service = 2000;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['harga'] * $item['qty'];
}

$total = $subtotal + $ongkir + $service;

$invoice = "INV-" . date("Ymd") . "-" . rand(1000, 9999);
$tanggal = date("d M Y");
$jam = date("H:i");

$status = ($metode == "Tunai") ? "PENDING CASH" : "PAID";

$cart = $_SESSION['cart'];

unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Sam's Juice</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #d4fc79, #96e6a1);
            font-family: 'Segoe UI', sans-serif;
        }

        .invoice-wrapper {
            max-width: 1000px;
            margin: 40px auto;
        }

        .invoice-card {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .invoice-header {
            background: linear-gradient(135deg, #198754, #146c43);
            color: white;
            padding: 35px;
        }

        .invoice-title {
            font-size: 42px;
            font-weight: 800;
        }

        .status-badge {
            padding: 10px 18px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 14px;
        }

        .paid {
            background: #d1fae5;
            color: #065f46;
        }

        .pending {
            background: #fef3c7;
            color: #92400e;
        }

        .invoice-body {
            padding: 35px;
        }

        .info-card {
            background: #f8fff9;
            border-radius: 20px;
            padding: 20px;
            height: 100%;
        }

        .summary-box {
            background: #198754;
            color: white;
            border-radius: 20px;
            padding: 25px;
        }

        .print-btn {
            background: #198754;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: bold;
        }

        .print-btn:hover {
            background: #146c43;
        }

        .back-btn {
            border-radius: 50px;
            padding: 14px 30px;
        }

        @media print {
            body {
                background: white;
            }

            .no-print {
                display: none;
            }

            .invoice-card {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

    <div class="invoice-wrapper">

        <div class="invoice-card">

            <!-- HEADER -->
            <div class="invoice-header">

                <div class="d-flex justify-content-between align-items-center flex-wrap">

                    <div>
                        <h1 class="invoice-title">
                            <i class="fa-solid fa-receipt me-2"></i>
                            Sam's Juice
                        </h1>

                        <p class="mb-0">
                            Fresh • Premium • Healthy 🍹
                        </p>
                    </div>

                    <div class="text-end mt-3 mt-md-0">
                        <h5><?php echo $invoice; ?></h5>
                        <p><?php echo $tanggal; ?> | <?php echo $jam; ?></p>

                        <span class="status-badge <?php echo ($status == 'PAID') ? 'paid' : 'pending'; ?>">
                            <?php echo $status; ?>
                        </span>
                    </div>

                </div>

            </div>

            <!-- BODY -->
            <div class="invoice-body">

                <div class="row g-4 mb-4">

                    <div class="col-md-6">
                        <div class="info-card">
                            <h5 class="fw-bold mb-3">Data Pembeli</h5>

                            <p><strong>Nama:</strong> <?php echo $nama; ?></p>
                            <p><strong>No HP:</strong> <?php echo $nohp; ?></p>
                            <p><strong>Alamat:</strong> <?php echo $alamat; ?></p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card">
                            <h5 class="fw-bold mb-3">Informasi Pembayaran</h5>

                            <p><strong>Metode:</strong> <?php echo $metode; ?></p>

                            <?php if ($wallet != '') { ?>
                                <p><strong>Wallet:</strong> <?php echo $wallet; ?></p>
                            <?php } ?>

                            <p><strong>Toko:</strong> Sam's Juice Medan</p>
                        </div>
                    </div>

                </div>

                <!-- TABEL -->
                <h4 class="fw-bold mb-3">Detail Pesanan</h4>

                <div class="table-responsive mb-4">

                    <table class="table table-bordered align-middle">

                        <thead class="table-success">
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($cart as $item) { ?>

                                <tr>
                                    <td><?php echo $item['nama']; ?></td>
                                    <td><?php echo $item['qty']; ?></td>
                                    <td>Rp <?php echo number_format($item['harga']); ?></td>
                                    <td>Rp <?php echo number_format($item['harga'] * $item['qty']); ?></td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

                <!-- TOTAL -->
                <div class="row justify-content-end">

                    <div class="col-md-5">

                        <div class="summary-box">

                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>Rp <?php echo number_format($subtotal); ?></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Biaya Layanan</span>
                                <span>Rp <?php echo number_format($service); ?></span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span>Ongkir</span>
                                <span>Rp <?php echo number_format($ongkir); ?></span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <h4>Total</h4>
                                <h3>Rp <?php echo number_format($total); ?></h3>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="text-center mt-5">
                    <h5>Terima kasih telah berbelanja di Sam's Juice 🍹</h5>
                    <p class="text-muted">
                        Struk ini adalah bukti pembayaran resmi.
                    </p>
                </div>

                <!-- BUTTON -->
                <div class="text-center mt-4 no-print">

                    <button onclick="window.print()" class="print-btn me-3">
                        <i class="fa-solid fa-print me-2"></i>
                        Cetak Invoice
                    </button>

                    <a href="menu.php" class="btn btn-outline-success back-btn">
                        Kembali ke Menu
                    </a>

                </div>

            </div>

        </div>

    </div>

</body>
</html>