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
<title>Profil Kasir - Sam's Juice</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
*{
    font-family:'Poppins',sans-serif;
}

body{
    background: linear-gradient(135deg,#eef7f0,#dff5e6);
    overflow-x:hidden;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    top:0;
    left:0;
    background:linear-gradient(180deg,#198754,#0f5132);
    padding:30px 20px;
    color:white;
    z-index:100;
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

/* MAIN */
.main{
    margin-left:260px;
    padding:40px;
    position:relative;
}

/* BUBBLES */
.bubble{
    position:absolute;
    border-radius:50%;
    background:rgba(25,135,84,0.15);
    animation: float 8s infinite ease-in-out;
}

.b1{
    width:80px;
    height:80px;
    top:50px;
    right:80px;
}

.b2{
    width:120px;
    height:120px;
    top:300px;
    right:150px;
    animation-delay:2s;
}

.b3{
    width:60px;
    height:60px;
    bottom:100px;
    right:300px;
    animation-delay:4s;
}

@keyframes float{
    0%{transform:translateY(0);}
    50%{transform:translateY(-20px);}
    100%{transform:translateY(0);}
}

/* PROFILE CARD */
.profile-card{
    background:rgba(255,255,255,0.8);
    backdrop-filter:blur(15px);
    border-radius:30px;
    padding:40px;
    box-shadow:0 20px 40px rgba(0,0,0,0.08);
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

.avatar-box{
    text-align:center;
    margin-bottom:30px;
}

.avatar{
    width:170px;
    height:170px;
    border-radius:50%;
    object-fit:cover;
    border:6px solid #198754;
    box-shadow:0 15px 30px rgba(25,135,84,0.25);
    transition:0.3s;
}

.avatar:hover{
    transform:scale(1.05) rotate(3deg);
}

.upload-btn{
    margin-top:15px;
    border:none;
    background:#198754;
    color:white;
    padding:10px 20px;
    border-radius:30px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.upload-btn:hover{
    background:#0f5132;
}

.form-control{
    border-radius:16px;
    padding:14px 18px;
    border:none;
    background:#f4f7f5;
}

.form-control:focus{
    box-shadow:0 0 0 4px rgba(25,135,84,0.15);
}

.btn-save{
    background:linear-gradient(135deg,#198754,#34d399);
    border:none;
    padding:14px;
    border-radius:20px;
    font-weight:700;
    color:white;
    width:100%;
    transition:0.3s;
}

.btn-save:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 20px rgba(25,135,84,0.25);
}

.info-box{
    background:white;
    padding:25px;
    border-radius:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    margin-top:25px;
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
        <i class="fa-solid fa-house me-2"></i> Dashboard
    </a>

    <a href="profil_kasir.php" class="menu-link active-menu">
        <i class="fa-solid fa-user me-2"></i> Profil Kasir
    </a>

    <a href="settings_kasir.php" class="menu-link">
        <i class="fa-solid fa-gear me-2"></i> Settings
    </a>

    <a href="logout.php" class="menu-link">
        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
    </a>
</div>

<div class="main">

    <div class="bubble b1"></div>
    <div class="bubble b2"></div>
    <div class="bubble b3"></div>

    <div class="profile-card">
        <h1 class="fw-bold text-success mb-4">
            Profil Kasir
        </h1>

        <form action="update_profil_kasir.php" method="POST" enctype="multipart/form-data">

            <div class="avatar-box">
                <img src="uploads/<?php echo $foto; ?>" id="preview" class="avatar">

                <br>

                <label class="upload-btn">
                    <i class="fa-solid fa-camera me-2"></i> Upload Foto
                    <input type="file" name="foto" accept="image/*" hidden onchange="previewImage(event)">
                </label>
            </div>

            <div class="mb-4">
                <label class="fw-semibold mb-2">Username</label>
                <input type="text" name="username"
                       class="form-control"
                       value="<?php echo $user['USERNAME']; ?>"
                       required>
            </div>

            <div class="mb-4">
                <label class="fw-semibold mb-2">Password Baru</label>
                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Kosongkan jika tidak ingin ganti">
            </div>

            <button type="submit" class="btn-save">
                <i class="fa-solid fa-floppy-disk me-2"></i>
                Simpan Perubahan
            </button>

        </form>

        <div class="info-box">
            <h5 class="fw-bold text-success">Info Akun</h5>
            <p class="mb-2"><strong>ID User:</strong> <?php echo $user['ID_USER']; ?></p>
            <p class="mb-0"><strong>Role:</strong> <?php echo $user['ROLE']; ?></p>
        </div>

    </div>
</div>

<script>
function previewImage(event){
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('preview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>