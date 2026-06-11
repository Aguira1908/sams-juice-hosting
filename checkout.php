<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: menu.php');
    exit;
}

$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $total += $item['harga'] * $item['qty'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Sam's Juice</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: linear-gradient(135deg, #d4fc79, #96e6a1);
            font-family: 'Segoe UI', sans-serif;
        }

        .card-box {
            background: #ffffff;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .pay-option {
            border: 2px solid #dddddd;
            border-radius: 16px;
            padding: 18px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }

        .pay-option:hover,
        .pay-option.active {
            border-color: #198754;
            background: #ecfff4;
        }

        .wallet-btn {
            border: 1px solid #dddddd;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }

        .wallet-btn:hover,
        .wallet-btn.active {
            background: #198754;
            color: white;
            border-color: #198754;
        }

        .qris-box {
            display: none;
            background: #ffffff;
            border: 2px solid #dddddd;
            border-radius: 18px;
            padding: 20px;
            text-align: center;
        }

        .confirm-btn {
            background: #198754;
            color: white;
            border: none;
            border-radius: 40px;
            padding: 15px;
            font-weight: 700;
            width: 100%;
            transition: 0.3s;
        }

        .confirm-btn:hover {
            background: #146c43;
            transform: translateY(-2px);
        }

        .summary-item {
            border-bottom: 1px solid #e9e9e9;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .total-box {
            background: #198754;
            color: white;
            border-radius: 20px;
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <div class="row g-4">

            <!-- FORM CHECKOUT -->
            <div class="col-lg-7">

                <div class="card-box">

                    <h2 class="fw-bold text-success mb-4">
                        Checkout Sam's Juice
                    </h2>

                    <form action="proses_checkout.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No HP</label>
                            <input
                                type="text"
                                name="nohp"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea
                                name="alamat"
                                class="form-control"
                                rows="4"
                                required
                            ></textarea>
                        </div>

                        <input
                            type="hidden"
                            name="metode"
                            id="metodePembayaran"
                            value="Tunai"
                        >

                        <input
                            type="hidden"
                            name="wallet"
                            id="walletTerpilih"
                            value=""
                        >

                        <h4 class="fw-bold mt-4 mb-3">
                            Pembayaran
                        </h4>

                        <div class="row g-3 mb-4">

                            <div class="col-md-4">
                                <div
                                    class="pay-option active"
                                    onclick="setPayment('Tunai', this)"
                                >
                                    <i class="fa-solid fa-money-bill fa-2x mb-2"></i>
                                    <br>
                                    Tunai
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div
                                    class="pay-option"
                                    onclick="setPayment('QRIS', this)"
                                >
                                    <i class="fa-solid fa-qrcode fa-2x mb-2"></i>
                                    <br>
                                    QRIS
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div
                                    class="pay-option"
                                    onclick="setPayment('E-Wallet', this)"
                                >
                                    <i class="fa-solid fa-wallet fa-2x mb-2"></i>
                                    <br>
                                    E-Wallet
                                </div>
                            </div>

                        </div>

                        <!-- QRIS -->
                        <div id="qrisBox" class="qris-box mb-4">

                            <img
                                src="image/qris.png"
                                width="300"
                                alt="QRIS"
                            >

                            <h5 class="mt-3">
                                Scan QRIS untuk pembayaran
                            </h5>

                        </div>

                        <!-- WALLET -->
                        <div id="walletBox" style="display:none;" class="mb-4">

                            <div class="row g-2">

                                <div class="col-6">
                                    <div
                                        class="wallet-btn"
                                        onclick="selectWallet('GoPay', this)"
                                    >
                                        GoPay
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div
                                        class="wallet-btn"
                                        onclick="selectWallet('OVO', this)"
                                    >
                                        OVO
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div
                                        class="wallet-btn"
                                        onclick="selectWallet('DANA', this)"
                                    >
                                        DANA
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div
                                        class="wallet-btn"
                                        onclick="selectWallet('ShopeePay', this)"
                                    >
                                        ShopeePay
                                    </div>
                                </div>

                            </div>

                        </div>

                        <button type="submit" class="confirm-btn">
                            <i class="fa-solid fa-credit-card me-2"></i>
                            Konfirmasi Bayar
                        </button>

                    </form>

                </div>

            </div>

            <!-- RINGKASAN -->
            <div class="col-lg-5">

                <div class="card-box">

                    <h4 class="fw-bold mb-4">
                        Ringkasan Pesanan
                    </h4>

                    <?php foreach ($_SESSION['cart'] as $item) { ?>

                        <div class="summary-item d-flex justify-content-between">

                            <div>
                                <strong><?php echo $item['nama']; ?></strong>
                                <br>

                                <small>
                                    <?php echo $item['qty']; ?> x Rp
                                    <?php echo number_format($item['harga']); ?>
                                </small>
                            </div>

                            <div class="fw-bold text-success">
                                Rp <?php echo number_format($item['harga'] * $item['qty']); ?>
                            </div>

                        </div>

                    <?php } ?>

                    <div class="total-box d-flex justify-content-between align-items-center mt-4">
                        <h5 class="mb-0">Total Bayar</h5>
                        <h2 class="mb-0">Rp <?php echo number_format($total); ?></h2>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>
        function setPayment(method, element) {
            document.getElementById('metodePembayaran').value = method;

            document.querySelectorAll('.pay-option').forEach(button => {
                button.classList.remove('active');
            });

            element.classList.add('active');

            document.getElementById('qrisBox').style.display =
                method === 'QRIS' ? 'block' : 'none';

            document.getElementById('walletBox').style.display =
                method === 'E-Wallet' ? 'block' : 'none';

            if (method !== 'E-Wallet') {
                document.getElementById('walletTerpilih').value = '';

                document.querySelectorAll('.wallet-btn').forEach(button => {
                    button.classList.remove('active');
                });
            }
        }

        function selectWallet(wallet, element) {
            document.getElementById('walletTerpilih').value = wallet;

            document.querySelectorAll('.wallet-btn').forEach(button => {
                button.classList.remove('active');
            });

            element.classList.add('active');
        }
    </script>

</body>
</html>