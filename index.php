<?php
session_start();
include "koneksi.php";

$produk = [];
$queryProduk = "SELECT * FROM PRODUK ORDER BY ID_PRODUK";
$stidProduk = oci_parse($conn, $queryProduk);
oci_execute($stidProduk);

while ($row = oci_fetch_assoc($stidProduk)) {
    $produk[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam's Juice - Premium Fresh Juice</title>

    <!-- CSS External -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            overflow-x: hidden;
            background: #f8fff9;
        }

        .navbar {
            background: #198754 !important;
            padding: 18px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 26px;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: white !important;
            margin-left: 10px;
            padding: 10px 16px;
            border-radius: 30px;
            transition: 0.3s;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            background: white;
            color: #198754 !important;
        }

        .hero {
            min-height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)),
                        url('image/banner-jus.png') center/cover no-repeat;
            display: flex;
            align-items: center;
            color: white;
            position: relative;
        }

        .hero h1 {
            font-size: 70px;
            font-weight: 800;
            line-height: 1.1;
        }

        .hero p {
            font-size: 20px;
            margin: 20px 0 30px;
        }

        .btn-main {
            background: linear-gradient(135deg, #198754, #34d399);
            border: none;
            padding: 15px 35px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 12px 30px rgba(25, 135, 84, 0.4);
            transition: 0.3s;
        }

        .btn-main:hover {
            transform: translateY(-5px);
            color: white;
        }

        .btn-outline-custom {
            border: 2px solid white;
            padding: 14px 30px;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-outline-custom:hover {
            background: white;
            color: #198754;
        }

        .juice-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .juice-ring {
            width: 380px;
            height: 380px;
            border-radius: 50%;
            overflow: hidden;
            border: 10px solid rgba(52, 211, 153, 0.45);
            box-shadow: 0 0 0 18px rgba(52, 211, 153, 0.15),
                        0 0 40px rgba(52, 211, 153, 0.25);
            animation: float 4s ease-in-out infinite;
        }

        .juice-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-18px); }
            100% { transform: translateY(0px); }
        }

        .stats-box {
            background: white;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transition: 0.3s;
        }

        .stats-box:hover {
            transform: translateY(-10px);
        }

        .section-title {
            font-size: 42px;
            font-weight: 800;
            color: #14532d;
        }

        .why-card {
            background: white;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transition: 0.3s;
            height: 100%;
        }

        .why-card:hover { transform: translateY(-12px); }
        .why-card i { font-size: 50px; color: #198754; }

        .product-card {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transition: 0.3s;
            height: 100%;
        }

        .product-card:hover { transform: translateY(-12px); }

        .product-card img {
            height: 260px;
            width: 100%;
            object-fit: cover;
            transition: 0.4s;
        }

        .product-card:hover img { transform: scale(1.08); }

        .price {
            font-size: 24px;
            font-weight: 700;
            color: #198754;
        }

        .cta-section {
            background: linear-gradient(135deg, #198754, #0f5132);
            color: white;
            border-radius: 30px;
            padding: 70px;
        }

        footer {
            background: #0d2f1d;
            color: white;
            padding: 25px;
        }

        @media(max-width:768px) {
            .hero {
                text-align: center;
                padding-top: 120px;
            }
            .hero h1 { font-size: 42px; }
            .juice-ring {
                width: 300px;
                height: 300px;
                margin-top: 40px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fa-solid fa-glass-water me-2"></i>Sam's Juice
            </a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="promo.php">Promo</a></li>
                    <li class="nav-item"><a class="nav-link" href="ulasan.php">Ulasan</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item">
                        <a href="login.php#register" class="btn btn-light rounded-pill px-4 ms-3 fw-semibold">
                        Daftar Sekarang
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1>Fresh Juice,<br>Better Mood 🍹</h1>
                    <p>Nikmati jus premium dengan rasa segar alami dan pengalaman digital modern.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="menu.php" class="btn-main">Pesan Sekarang</a>
                        <a href="#produk" class="btn-outline-custom">Lihat Menu</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center" data-aos="fade-left">
                    <div class="juice-wrapper">
                        <div class="juice-ring">
                            <img src="image/botol.png" class="juice-image" alt="Sam's Juice">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="stats-box">
                        <h2 class="text-success fw-bold">1000+</h2>
                        <p>Pelanggan Puas</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-box">
                        <h2 class="text-success fw-bold">6+</h2>
                        <p>Menu Premium</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-box">
                        <h2 class="text-success fw-bold">100%</h2>
                        <p>Buah Segar</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Us Section -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="section-title mb-5">Kenapa Pilih Sam's Juice?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="why-card">
                        <i class="fas fa-apple-whole"></i>
                        <h4 class="mt-4">Buah Segar</h4>
                        <p>Kami hanya memilih buah berkualitas premium.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="why-card">
                        <i class="fas fa-heart-pulse"></i>
                        <h4 class="mt-4">Sehat Alami</h4>
                        <p>Tanpa pengawet, tanpa kompromi kualitas.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="why-card">
                        <i class="fas fa-bolt"></i>
                        <h4 class="mt-4">Cepat Disajikan</h4>
                        <p>Fresh dibuat saat order untuk kualitas maksimal.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Section -->
    <section id="produk" class="py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">Best Seller Menu</h2>
            <div class="row g-4">
                <?php foreach ($produk as $p): ?>
                <div class="col-md-4">
                    <div class="product-card">
                        <img src="image/<?php echo $p['GAMBAR']; ?>" alt="<?php echo $p['NAMA_PRODUK']; ?>">
                        <div class="p-4 text-center">
                            <h5 class="fw-bold"><?php echo $p['NAMA_PRODUK']; ?></h5>
                            <p><?php echo $p['DESKRIPSI']; ?></p>
                            <div class="price">Rp <?php echo number_format($p['HARGA']); ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5">
        <div class="container">
            <div class="cta-section text-center">
                <h2 class="fw-bold">Ready for Fresh Experience?</h2>
                <p class="mt-3">Pesan sekarang dan rasakan kesegaran premium Sam's Juice.</p>
                <a href="menu.php" class="btn btn-light rounded-pill px-5 py-3 fw-bold mt-3">Order Now</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        Sam's Juice — Fresh • Premium • Healthy 🍹
    </footer>

    <!-- Modal Login -->
    <div class="modal fade" id="loginModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4">
                <div class="modal-body p-5">
                    <h3 class="text-center text-success fw-bold mb-4">Login Sam's Juice</h3>
                    <form action="proses_login.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control form-control-lg rounded-4" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control form-control-lg rounded-4" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <select name="role" class="form-select form-select-lg rounded-4">
                                <option value="customer">Customer</option>
                                <option value="kasir">Kasir</option>
                            </select>
                        </div>
                        <button class="btn btn-success w-100 rounded-4 py-3 fw-bold">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>