<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

$query = "SELECT * FROM USERS WHERE USERNAME = :username";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":username", $username);
oci_execute($stid);

$user = oci_fetch_assoc($stid);

$foto = !empty($user['FOTO']) ? $user['FOTO'] : 'default-profile.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings Kasir</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
*{
    font-family:'Poppins',sans-serif;
}

body{
    background:linear-gradient(135deg,#eef7f0,#dff5e6);
}

.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    top:0;
    left:0;
    background:linear-gradient(180deg,#198754,#0f5132);
    padding:30px 20px;
    color:white;
}

.logo{
    font-size:32px;
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
    color:#198754 !important;
}

.main{
    margin-left:260px;
    padding:40px;
}

.settings-card{
    background:white;
    border-radius:30px;
    padding:40px;
    box-shadow:0 15px 35px rgba(0,0,0,0.08);
    animation:fadeUp 0.8s ease;
}

@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(30px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.profile-top{
    display:flex;
    align-items:center;
    gap:20px;
    margin-bottom:35px;
}

.profile-img{
    width:90px;
    height:90px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #198754;
}

.setting-item{
    background:#f8faf9;
    border-radius:20px;
    padding:20px 25px;
    margin-bottom:20px;
    transition:0.3s;
}

.setting-item:hover{
    transform:translateX(8px);
    box-shadow:0 8px 20px rgba(0,0,0,0.06);
}

.btn-custom{
    border-radius:20px;
    font-weight:600;
    padding:10px 20px;
}

@media(max-width:991px){
    .sidebar{
        position:relative;
        width:100%;
        height:auto;
    }

    .main{
        margin-left:0;
    }
}
</style>
</head>
<body>

<div class="sidebar">

    <div class="logo">🍹 Sam's Juice</div>

    <a href="kasir.php" class="menu-link">
        <i class="fa-solid fa-house me-2"></i>
        Dashboard
    </a>

    <a href="profil_kasir.php" class="menu-link">
        <i class="fa-solid fa-user me-2"></i>
        Profil Kasir
    </a>

    <a href="settings_kasir.php" class="menu-link active-menu">
        <i class="fa-solid fa-gear me-2"></i>
        Settings
    </a>

    <a href="logout.php" class="menu-link">
        <i class="fa-solid fa-right-from-bracket me-2"></i>
        Logout
    </a>

</div>

<div class="main">

    <div class="settings-card">

        <div class="profile-top">
            <img src="uploads/<?php echo $foto; ?>" class="profile-img">

            <div>
                <h2 class="fw-bold text-success mb-1">
                    <?php echo $user['USERNAME']; ?>
                </h2>
                <p class="text-muted mb-0">
                    Kasir Sam's Juice
                </p>
            </div>
        </div>

        <h3 class="fw-bold mb-4">
            Pengaturan Akun ⚙️
        </h3>

        <div class="setting-item d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Edit Profil</h5>
                <small class="text-muted">Ubah username, password, dan foto profil</small>
            </div>

            <a href="profil_kasir.php" class="btn btn-success btn-custom">
                Buka
            </a>
        </div>

        <div class="setting-item d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Mode Notifikasi</h5>
                <small class="text-muted">Simulasi notifikasi pesanan baru</small>
            </div>

            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" checked>
            </div>
        </div>

        <div class="setting-item d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Reset Session</h5>
                <small class="text-muted">Keluar dari akun kasir</small>
            </div>

            <a href="logout.php" class="btn btn-danger btn-custom">
                Logout
            </a>
        </div>

        <div class="setting-item d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Tentang Sistem</h5>
                <small class="text-muted">Sam's Juice Cashier Dashboard v1.0</small>
            </div>

            <button class="btn btn-primary btn-custom" onclick="showInfo()">
                Info
            </button>
        </div>

    </div>

</div>

<script>
function showInfo(){
    alert("Sam's Juice Cashier Dashboard v1.0\nDibuat untuk sistem kasir & customer.");
}
</script>

</body>
</html>