<?php
session_start();

if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'kasir'){
        header("Location: kasir.php");
    } else {
        header("Location: customer.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - Sam's Juice</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #b8f5d1, #198754);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Animated bubbles */
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            animation: floatBubble 15s infinite linear;
            z-index: 0;
        }

        .bubble:nth-child(1) {
            width: 120px;
            height: 120px;
            left: 10%;
            top: 80%;
            animation-duration: 18s;
        }

        .bubble:nth-child(2) {
            width: 80px;
            height: 80px;
            left: 80%;
            top: 90%;
            animation-duration: 12s;
        }

        .bubble:nth-child(3) {
            width: 150px;
            height: 150px;
            left: 60%;
            top: 85%;
            animation-duration: 20s;
        }

        @keyframes floatBubble {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0.4;
            }
            100% {
                transform: translateY(-1000px) scale(1.3);
                opacity: 0;
            }
        }

        /* Main card */
        .auth-container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-radius: 35px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 1000px;
            display: flex;
            overflow: hidden;
            min-height: 620px;
            position: relative;
            z-index: 2;
            animation: floatCard 4s ease-in-out infinite;
        }

        @keyframes floatCard {
            0%,100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Left image */
        .auth-image {
            flex: 1;
            background: url('image/banner-jus.png') center center;
            background-size: cover;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
            color: white;
            position: relative;
            animation: zoomBg 15s infinite alternate ease-in-out;
        }

        @keyframes zoomBg {
            from { transform: scale(1); }
            to { transform: scale(1.08); }
        }

        .auth-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                rgba(0,0,0,0.45),
                rgba(0,0,0,0.35)
            );
        }

        .auth-image-content {
            position: relative;
            z-index: 2;
            animation: slideLeft 1s ease;
        }

        @keyframes slideLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Right form */
        .auth-form-container {
            flex: 1;
            padding: 55px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255,255,255,0.92);
            animation: slideRight 1s ease;
        }

        @keyframes slideRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .nav-pills {
            background: #ecfdf5;
            padding: 6px;
            border-radius: 50px;
            margin-bottom: 30px;
        }

        .nav-pills .nav-link {
            border-radius: 50px;
            color: #198754;
            font-weight: 700;
            transition: 0.4s;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #198754, #34d399);
            color: white;
            box-shadow: 0 10px 25px rgba(25,135,84,0.35);
        }

        .form-control {
            border-radius: 16px;
            padding: 14px 20px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 5px rgba(25,135,84,0.15);
            border-color: #198754;
            transform: scale(1.02);
        }

        .btn-auth {
            background: linear-gradient(135deg, #198754, #34d399);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 16px;
            font-weight: 700;
            width: 100%;
            margin-top: 10px;
            transition: 0.35s;
            box-shadow: 0 10px 25px rgba(25,135,84,0.25);
        }

        .btn-auth:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 15px 35px rgba(25,135,84,0.35);
        }

        .social-login {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            border-radius: 15px;
            border: 1px solid #e5e7eb;
            background: white;
            transition: 0.3s;
            font-size: 20px;
        }

        .social-btn:hover {
            transform: translateY(-5px);
            border-color: #198754;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }

        .fa-check-circle {
            animation: pulseIcon 2s infinite;
        }

        @keyframes pulseIcon {
            0%,100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        @media (max-width: 768px) {
            .auth-image {
                display: none;
            }

            .auth-container {
                max-width: 450px;
                min-height: auto;
            }

            .auth-form-container {
                padding: 35px;
            }
        }
    </style>
</head>
<body>

<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>

<div class="auth-container">

    <!-- Left -->
    <div class="auth-image">
        <div class="auth-image-content">
            <h1 class="fw-bold mb-3">Freshness in Every Drop 🍹</h1>
            <p class="opacity-75">
                Bergabunglah dengan pecinta jus sehat dan nikmati pengalaman pemesanan yang mudah.
            </p>

            <div class="mt-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-check-circle me-3 text-success"></i>
                    <span>Buah Asli & Segar</span>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-check-circle me-3 text-success"></i>
                    <span>Proses Higienis</span>
                </div>

                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 text-success"></i>
                    <span>Layanan Cepat</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right -->
    <div class="auth-form-container">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-success mb-1">Sam's Juice</h2>
            <p class="text-muted small">Pintu kesegaran menantimu!</p>
        </div>

        <ul class="nav nav-pills nav-justified" role="tablist">
            <li class="nav-item">
                <button class="nav-link active"
                        data-bs-toggle="pill"
                        data-bs-target="#login">
                    Login
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link"
                        data-bs-toggle="pill"
                        data-bs-target="#register">
                    Daftar
                </button>
            </li>
        </ul>

        <div class="tab-content">

            <!-- Login -->
            <div class="tab-pane fade show active" id="login">
                <form action="proses_login.php" method="POST">

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Username</label>
                        <input type="text"
                               name="username"
                               class="form-control"
                               placeholder="Masukkan username"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Password</label>
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Masukkan password"
                               required>
                    </div>

                    <button type="submit" class="btn btn-auth">
                        Masuk Sekarang
                    </button>
                </form>
            </div>

            <!-- Register -->
            <div class="tab-pane fade" id="register">
                <form action="proses_register.php" method="POST">

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Username</label>
                        <input type="text"
                               name="username"
                               class="form-control"
                               placeholder="Pilih username"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Password</label>
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Buat password"
                               required>
                    </div>

                    <input type="hidden" name="role" value="customer">

                    <button type="submit" class="btn btn-auth">
                        Buat Akun Customer
                    </button>

                </form>
            </div>

        </div>

        <div class="mt-4 text-center">
            <p class="text-muted small mb-0">Atau masuk dengan</p>

            <div class="social-login">
                <button class="social-btn" type="button">
                    <i class="fab fa-google text-danger"></i>
                </button>

                <button class="social-btn" type="button">
                    <i class="fab fa-facebook text-primary"></i>
                </button>

                <button class="social-btn" type="button">
                    <i class="fab fa-apple"></i>
                </button>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>