<?php
include "koneksi.php";

$query = "SELECT * FROM PRODUK WHERE IS_PROMO = 1 ORDER BY DISKON_PERSEN DESC";
$stid = oci_parse($conn, $query);
oci_execute($stid);

$promos = [];
while ($row = oci_fetch_assoc($stid)) {
    $promos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Promo Eksklusif - Sam's Juice</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
    --primary:#198754;
    --secondary:#34d399;
    --accent:#ff3b30;
    --bg:#f5fff8;
}

*{
    font-family:'Poppins',sans-serif;
}

body{
    background:var(--bg);
    overflow-x:hidden;
}

/* FLOATING FRUITS */
.fruit{
    position:fixed;
    z-index:1;
    opacity:0.15;
    animation: floatFruit 10s infinite ease-in-out;
    font-size:50px;
}

.f1{top:15%;left:5%;}
.f2{top:65%;right:8%;animation-delay:2s;}
.f3{top:40%;left:85%;animation-delay:4s;}
.f4{bottom:10%;left:10%;animation-delay:6s;}

@keyframes floatFruit{
    0%,100%{transform:translateY(0) rotate(0deg);}
    50%{transform:translateY(-30px) rotate(15deg);}
}

/* NAVBAR */
.navbar{
    background:rgba(25,135,84,0.95);
    backdrop-filter:blur(15px);
    padding:18px 0;
    box-shadow:0 8px 25px rgba(0,0,0,0.1);
    z-index:1000;
}

.navbar-brand{
    font-weight:800;
    font-size:28px;
}

.btn-back{
    border-radius:50px;
    padding:10px 28px;
    font-weight:700;
}

/* HERO */
.promo-hero{
    margin-top:90px;
    margin-left:20px;
    margin-right:20px;
    border-radius:40px;
    overflow:hidden;
    position:relative;
    background:
            linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
            url('image/banner-jus.png')
    center/cover no-repeat;
    min-height:580px;
    display:flex;
    align-items:center;
    color:white;
    animation: zoomBg 18s infinite alternate ease-in-out;
}

@keyframes zoomBg{
    from{background-size:100%;}
    to{background-size:110%;}
}

.hero-content{
    z-index:2;
    position:relative;
}

.hero-title{
    font-size:72px;
    font-weight:800;
    line-height:1.1;
    animation: slideUp 1s ease;
}

.hero-text{
    font-size:22px;
    opacity:0.9;
    margin-top:20px;
    animation: slideUp 1.4s ease;
}

@keyframes slideUp{
    from{
        opacity:0;
        transform:translateY(40px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* TIMER */
.timer-box{
    margin-top:35px;
    display:flex;
    gap:20px;
    flex-wrap:wrap;
}

.timer-unit{
    width:110px;
    height:110px;
    background:rgba(255,255,255,0.18);
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.25);
    border-radius:28px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
    animation: pulse 2s infinite;
}

.timer-unit h2{
    margin:0;
    font-size:34px;
    font-weight:800;
}

.timer-unit small{
    font-weight:600;
    opacity:0.9;
}

@keyframes pulse{
    0%,100%{transform:scale(1);}
    50%{transform:scale(1.05);}
}

/* SECTION TITLE */
.section-title{
    font-size:48px;
    font-weight:800;
    color:#14532d;
}

/* CARDS */
.promo-card{
    background:white;
    border:none;
    border-radius:30px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,0.08);
    transition:0.4s;
    height:100%;
    position:relative;
    z-index:2;
}

.promo-card:hover{
    transform:translateY(-15px);
    box-shadow:0 30px 60px rgba(25,135,84,0.18);
}

.promo-badge{
    position:absolute;
    top:20px;
    left:20px;
    background:var(--accent);
    color:white;
    padding:10px 18px;
    border-radius:20px;
    font-weight:800;
    z-index:5;
    animation: bounce 1.8s infinite;
}

@keyframes bounce{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-6px);}
}

.promo-img-wrapper{
    height:280px;
    overflow:hidden;
}

.promo-img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:0.5s;
}

.promo-card:hover .promo-img{
    transform:scale(1.1);
}

.price-new{
    font-size:30px;
    font-weight:800;
    color:var(--primary);
}

.price-old{
    font-size:18px;
    color:#9ca3af;
    text-decoration:line-through;
    margin-left:10px;
}

.btn-claim{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:white;
    border:none;
    width:100%;
    padding:14px;
    border-radius:18px;
    font-weight:700;
    transition:0.3s;
}

.btn-claim:hover{
    transform:translateY(-4px);
    color:white;
}

/* FLASH */
.flash-sale{
    background:linear-gradient(135deg,#f59e0b,#d97706);
    border-radius:35px;
    padding:60px;
    color:white;
    text-align:center;
    margin-top:90px;
    box-shadow:0 20px 50px rgba(245,158,11,0.3);
    animation: glow 3s infinite alternate;
}

@keyframes glow{
    from{box-shadow:0 20px 50px rgba(245,158,11,0.2);}
    to{box-shadow:0 25px 70px rgba(245,158,11,0.45);}
}

footer{
    padding:40px;
    text-align:center;
    color:#6b7280;
}

@media(max-width:768px){
    .hero-title{
        font-size:42px;
    }

    .hero-text{
        font-size:18px;
    }

    .promo-hero{
        min-height:500px;
    }

    .timer-unit{
        width:85px;
        height:85px;
    }

    .timer-unit h2{
        font-size:24px;
    }
}
</style>
</head>
<body>

<div class="fruit f1">🍊</div>
<div class="fruit f2">🍋</div>
<div class="fruit f3">🍓</div>
<div class="fruit f4">🥭</div>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">🍹 Sam's Juice</a>
        <a href="index.php" class="btn btn-light btn-back text-success">
            Kembali
        </a>
    </div>
</nav>

<section class="promo-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                Promo Spesial Hari Ini! 🎉
            </h1>
            <p class="hero-text">
                Hemat lebih banyak dengan promo segar terbaik dari Sam's Juice.
            </p>

            <div class="timer-box">
                <div class="timer-unit">
                    <h2 id="hours">00</h2>
                    <small>JAM</small>
                </div>
                <div class="timer-unit">
                    <h2 id="minutes">00</h2>
                    <small>MENIT</small>
                </div>
                <div class="timer-unit">
                    <h2 id="seconds">00</h2>
                    <small>DETIK</small>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container my-5">
    <h2 class="section-title text-center mb-5">
        Produk Promo Hari Ini 🎊
    </h2>

    <div class="row g-5">
        <?php if(empty($promos)): ?>
            <div class="col-12 text-center py-5">
                <h3>Belum ada promo aktif.</h3>
            </div>
        <?php else: ?>
            <?php foreach($promos as $p): 
                $diskon = ($p['HARGA'] * $p['DISKON_PERSEN']) / 100;
                $hargaBaru = $p['HARGA'] - $diskon;
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="promo-card">
                    <div class="promo-badge">
                        <?php echo $p['DISKON_PERSEN']; ?>% OFF
                    </div>

                    <div class="promo-img-wrapper">
                        <img src="image/<?php echo $p['GAMBAR']; ?>" class="promo-img">
                    </div>

                    <div class="p-4">
                        <h3 class="fw-bold"><?php echo $p['NAMA_PRODUK']; ?></h3>
                        <p class="text-muted"><?php echo $p['DESKRIPSI']; ?></p>

                        <div class="mb-4">
                            <span class="price-new">
                                Rp <?php echo number_format($hargaBaru); ?>
                            </span>
                            <span class="price-old">
                                Rp <?php echo number_format($p['HARGA']); ?>
                            </span>
                        </div>

                        <a href="customer.php" class="btn btn-claim">
                            Ambil Promo
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="flash-sale">
        <h2 class="fw-bold">⚡ Flash Sale Hari Ini</h2>
        <p class="mt-3 fs-5">
            Beli 2 Gratis 1 untuk semua menu promo!
        </p>
    </div>
</div>

<footer>
    © 2026 Sam's Juice — Freshness Delivered 🍹
</footer>

<script>
const countdownDate = new Date();
countdownDate.setHours(countdownDate.getHours() + 48);

function updateCountdown() {
    const now = new Date().getTime();
    const distance = countdownDate - now;

    if(distance < 0){
        document.getElementById("hours").innerHTML = "00";
        document.getElementById("minutes").innerHTML = "00";
        document.getElementById("seconds").innerHTML = "00";
        return;
    }

    const hours = Math.floor((distance % (1000*60*60*24)) / (1000*60*60));
    const minutes = Math.floor((distance % (1000*60*60)) / (1000*60));
    const seconds = Math.floor((distance % (1000*60)) / 1000);

    document.getElementById("hours").innerHTML = String(hours).padStart(2,'0');
    document.getElementById("minutes").innerHTML = String(minutes).padStart(2,'0');
    document.getElementById("seconds").innerHTML = String(seconds).padStart(2,'0');
}

setInterval(updateCountdown, 1000);
updateCountdown();
</script>

</body>
</html>