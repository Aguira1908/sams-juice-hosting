<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
?>

<style>

*{
    font-family:'Poppins',sans-serif;
}

.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:linear-gradient(180deg,#198754,#0f5132);
    padding:30px 20px;
    color:white;
}

.logo{
    font-size:30px;
    font-weight:800;
    margin-bottom:40px;
}

.menu-link{
    display:block;
    color:white;
    text-decoration:none;
    padding:15px 18px;
    border-radius:16px;
    margin-bottom:12px;
    transition:0.3s;
    font-weight:500;
}

.menu-link:hover{
    background:rgba(255,255,255,0.15);
    color:white;
    transform:translateX(8px);
}

.active-menu{
    background:white;
    color:#198754!important;
}

.main-content{
    margin-left:260px;
    padding:30px;
}

</style>

<div class="sidebar">

<div class="logo">
🍹 Sam's Juice
</div>

<a href="kasir.php" class="menu-link">
<i class="fa-solid fa-house me-2"></i>
Dashboard
</a>

<a href="transaksi.php" class="menu-link">
<i class="fa-solid fa-cart-shopping me-2"></i>
Transaksi
</a>

<a href="data_menu.php" class="menu-link">
<i class="fa-solid fa-glass-water me-2"></i>
Data Menu
</a>

<a href="riwayat.php" class="menu-link">
<i class="fa-solid fa-clock-rotate-left me-2"></i>
Riwayat
</a>

<a href="logout.php" class="menu-link">
<i class="fa-solid fa-right-from-bracket me-2"></i>
Logout
</a>

</div>