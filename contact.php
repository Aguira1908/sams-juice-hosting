<?php
// Contact Sam's Juice
?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Sam's Juice</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

*{
    font-family:'Poppins',sans-serif;
}

body{
    margin:0;
    overflow-x:hidden;
    background:#f8fff9;
}

/* NAVBAR */

.navbar{
    background:#198754 !important;
    padding:18px 0;
    box-shadow:0 5px 20px rgba(0,0,0,0.15);
}

.navbar-brand{
    font-size:26px;
    font-weight:800;
    color:white !important;
}

.navbar-nav .nav-link{
    color:white !important;
    margin-left:10px;
    padding:10px 16px;
    border-radius:30px;
    transition:0.3s;
    font-weight:500;
}

.navbar-nav .nav-link:hover{
    background:white;
    color:#198754 !important;
}

/* HERO */

.hero{
    min-height:60vh;
    background:
    linear-gradient(rgba(0,0,0,0.55),rgba(0,0,0,0.55)),
    url('image/background.png') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    color:white;
    padding:120px 20px;
}

.hero h1{
    font-size:65px;
    font-weight:800;
    animation:fadeDown 1s ease;
}

.hero p{
    font-size:20px;
    margin-top:15px;
    animation:fadeUp 1.2s ease;
}

/* CONTACT CARD */

.contact-card{
    background:rgba(255,255,255,0.95);
    border-radius:30px;
    padding:40px;
    box-shadow:0 20px 45px rgba(0,0,0,0.1);
    animation:fadeUp 1s ease;
}

.contact-btn{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    padding:25px;
    border-radius:20px;
    background:white;
    color:#198754;
    transition:0.35s;
    font-weight:600;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    height:100%;
}

.contact-btn:hover{
    background:#198754;
    color:white;
    transform:translateY(-10px);
}

.contact-btn i{
    font-size:42px;
    margin-bottom:15px;
}

/* INFO */

.info-box{
    background:white;
    padding:30px;
    border-radius:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    height:100%;
    transition:0.3s;
}

.info-box:hover{
    transform:translateY(-8px);
}

.info-box i{
    font-size:45px;
    color:#198754;
    margin-bottom:15px;
}

/* MAP */

.map-box{
    border-radius:30px;
    overflow:hidden;
    box-shadow:0 20px 45px rgba(0,0,0,0.12);
}

/* FOOTER */

footer{
    background:#0d2f1d;
    color:white;
    text-align:center;
    padding:30px;
    margin-top:70px;
}

/* ANIMATION */

@keyframes fadeDown{
    from{
        opacity:0;
        transform:translateY(-30px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(40px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* MOBILE */

@media(max-width:768px){

.hero h1{
    font-size:42px;
}

.contact-card{
    padding:25px;
}

}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">

<div class="container">

<a class="navbar-brand" href="index.php">
<i class="fa-solid fa-glass-water me-2"></i>
Sam's Juice
</a>

<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarNav">

<ul class="navbar-nav ms-auto">

<li class="nav-item">
<a class="nav-link" href="index.php">Home</a>
</li>

<li class="nav-item">
<a class="nav-link" href="menu.php">Menu</a>
</li>

<li class="nav-item">
<a class="nav-link" href="promo.php">Promo</a>
</li>

<li class="nav-item">
<a class="nav-link" href="ulasan.php">Ulasan</a>
</li>

<li class="nav-item">
<a class="nav-link active bg-white text-success" href="contact.php">Contact</a>
</li>

</ul>

</div>

</div>

</nav>

<!-- HERO -->

<section class="hero">

<div class="container">

<h1>Hubungi Sam's Juice 🍹</h1>

<p>
Kami siap membantu pesanan, pertanyaan, dan kebutuhan segarmu
</p>

</div>

</section>

<!-- CONTACT -->

<section class="py-5">

<div class="container">

<div class="contact-card">

<h2 class="text-center fw-bold text-success mb-5">
Pilih Platform Favoritmu
</h2>

<div class="row g-4">

<div class="col-md-4">

<a href="https://www.instagram.com/sums_juice" target="_blank" class="contact-btn">

<i class="fa-brands fa-instagram"></i>

Instagram

</a>

</div>

<div class="col-md-4">

<a href="https://wa.me/6281269593573" target="_blank" class="contact-btn">

<i class="fa-brands fa-whatsapp"></i>

WhatsApp

</a>

</div>

<div class="col-md-4">

<a href="mailto:jelitamasnami@student.polmed.ac.id" class="contact-btn">

<i class="fa-solid fa-envelope"></i>

Email

</a>

</div>

</div>

</div>

</div>

</section>

<!-- INFO -->

<section class="container py-4">

<div class="row g-4">

<div class="col-md-4">

<div class="info-box text-center">

<i class="fa-solid fa-location-dot"></i>

<h4 class="fw-bold">
Alamat
</h4>

<p>
Jl. Dr Mansyur, Medan, Sumatera Utara
</p>

</div>

</div>

<div class="col-md-4">

<div class="info-box text-center">

<i class="fa-solid fa-phone"></i>

<h4 class="fw-bold">
Telepon
</h4>

<p>
+62 812 6959 3573
</p>

</div>

</div>

<div class="col-md-4">

<div class="info-box text-center">

<i class="fa-solid fa-clock"></i>

<h4 class="fw-bold">
Jam Operasional
</h4>

<p>
08:00 - 21:00 WIB
</p>

</div>

</div>

</div>

</section>

<!-- MAP -->

<section class="container py-5">

<h2 class="text-center fw-bold text-success mb-4">
Lokasi Kami 📍
</h2>

<div class="map-box">

<iframe
src="https://www.google.com/maps?q=Medan&output=embed"
width="100%"
height="450"
style="border:0;"
allowfullscreen=""
loading="lazy">
</iframe>

</div>

</section>

<!-- FOOTER -->

<footer>

<h5 class="fw-bold">
Sam's Juice 🍹
</h5>

<p class="mb-0">
Fresh • Premium • Healthy
</p>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>