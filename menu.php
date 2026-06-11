<?php
include "koneksi.php";

$query = "SELECT * FROM PRODUK ORDER BY ID_PRODUK";
$stid = oci_parse($conn, $query);
oci_execute($stid);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu Sam's Juice</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
* {
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(135deg, #f0fff4, #dcfce7);
    min-height: 100vh;
}

.navbar {
    background: #198754;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    padding: 15px 0;
}

.navbar-brand {
    font-weight: 800;
    font-size: 26px;
}

.hero {
    padding: 60px 0 30px;
    text-align: center;
}

.hero h1 {
    font-weight: 800;
    font-size: 52px;
    color: #14532d;
}

.hero p {
    font-size: 18px;
    color: #6b7280;
}

.search-box {
    border: none;
    border-radius: 50px;
    padding: 18px 24px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    font-size: 16px;
}

.menu-card {
    border: none;
    border-radius: 28px;
    overflow: hidden;
    background: white;
    box-shadow: 0 15px 35px rgba(0,0,0,.08);
    transition: 0.35s;
    height: 100%;
}

.menu-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 22px 45px rgba(25,135,84,.12);
}

.menu-card img {
    height: 260px;
    width: 100%;
    object-fit: cover;
    transition: 0.4s;
}

.menu-card:hover img {
    transform: scale(1.05);
}

.price {
    font-size: 24px;
    font-weight: 800;
    color: #198754;
}

.badge-stock {
    background: #dcfce7;
    color: #166534;
    padding: 10px 16px;
    border-radius: 30px;
    font-weight: 700;
}

.btn-view {
    background: linear-gradient(135deg, #198754, #34d399);
    border: none;
    border-radius: 40px;
    padding: 14px;
    font-weight: 700;
    color: white;
    text-decoration: none;
    display: block;
    transition: 0.3s;
}

.btn-view:hover {
    color: white;
    transform: translateY(-2px);
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #6b7280;
}

.empty-state i {
    font-size: 70px;
    margin-bottom: 20px;
    opacity: 0.3;
}

footer {
    margin-top: 80px;
    background: #14532d;
    color: white;
    text-align: center;
    padding: 25px;
    font-weight: 500;
}

@media(max-width:768px){
    .hero h1 {
        font-size: 36px;
    }

    .menu-card img {
        height: 220px;
    }
}
</style>
</head>

<body>

<nav class="navbar navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-glass-water me-2"></i>Sam's Juice
        </a>

        <a href="index.php" class="btn btn-light rounded-pill px-4 fw-bold text-success">
            Kembali
        </a>
    </div>
</nav>

<div class="container py-5">

    <div class="hero">
        <h1>Menu Jus Segar Premium 🍹</h1>
        <p>Pilih jus favoritmu dengan kualitas premium dan rasa alami terbaik.</p>
    </div>

    <input type="text" class="form-control search-box mb-5" placeholder="Cari menu favoritmu...">

    <div class="row g-4">

        <?php
        $adaProduk = false;
        while ($row = oci_fetch_array($stid, OCI_ASSOC | OCI_RETURN_LOBS)) {
            $adaProduk = true;
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="menu-card">
                    <img src="image/<?php echo $row['GAMBAR']; ?>" alt="produk">

                    <div class="p-4 text-center d-flex flex-column">
                        <h5 class="fw-bold">
                            <?php echo $row['NAMA_PRODUK']; ?>
                        </h5>

                        <p class="text-muted small">
                            <?php echo $row['DESKRIPSI']; ?>
                        </p>

                        <div class="price mb-3">
                            Rp <?php echo number_format($row['HARGA']); ?>
                        </div>

                        <div class="mb-4">
                            <span class="badge-stock">
                                Stok: <?php echo $row['STOK']; ?>
                            </span>
                        </div>

                        <a href="promo.php" class="btn-view mt-auto">
                            Lihat Promo
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!$adaProduk) { ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="fa-solid fa-glass-water"></i>
                    <h3>Menu belum tersedia</h3>
                    <p>Produk akan segera ditambahkan.</p>
                </div>
            </div>
        <?php } ?>

    </div>

</div>

<footer>
    Sam's Juice — Fresh • Premium • Healthy 🍹
</footer>

</body>
</html>