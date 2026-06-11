<?php
include "koneksi.php";

$query = "SELECT * FROM ULASAN ORDER BY ID_ULASAN DESC";
$stid = oci_parse($conn,$query);
oci_execute($stid);

// Get average rating
$queryAvg = "SELECT AVG(RATING) as RERATA, COUNT(*) as TOTAL FROM ULASAN";
$stidAvg = oci_parse($conn, $queryAvg);
oci_execute($stidAvg);
$rowAvg = oci_fetch_assoc($stidAvg);
$rerata = round($rowAvg['RERATA'] ?? 0, 1);
$totalUlasan = $rowAvg['TOTAL'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan Pelanggan - Sam's Juice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #198754;
            --secondary-color: #34d399;
            --bg-color: #f0fdf4;
            --text-dark: #1f2937;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--bg-color);
            color: var(--text-dark);
            padding-bottom: 50px;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 80px 0;
            color: white;
            border-radius: 0 0 50px 50px;
            margin-bottom: 50px;
            text-align: center;
        }

        .review-card {
            background: white;
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: 0.3s;
            position: relative;
            border: none;
        }

        .review-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(25, 135, 84, 0.1);
        }

        .quote-icon {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 40px;
            color: #dcfce7;
        }

        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: -75px;
            margin-bottom: 20px;
        }

        .rating-stars {
            color: #fbbf24;
            margin-bottom: 15px;
        }

        .review-text {
            font-style: italic;
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .reviewer-info h5 {
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--primary-color);
        }

        .reviewer-info p {
            font-size: 13px;
            color: #9ca3af;
        }

        /* Form Styling */
        .form-card {
            background: white;
            border-radius: 30px;
            padding: 45px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            margin-top: 50px;
        }

        .form-control, .form-select {
            border-radius: 15px;
            padding: 12px 20px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1);
            border-color: var(--primary-color);
        }

        .btn-submit {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 15px;
            font-weight: 700;
            width: 100%;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #146c43;
            transform: scale(1.02);
            color: white;
        }

        .back-home {
            position: absolute;
            top: 30px;
            left: 30px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 10;
        }

        .back-home:hover {
            color: #d1fae5;
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-home">
        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
    </a>

    <div class="header-section">
        <div class="container">
            <h1 class="fw-800 mb-3" style="font-weight: 800; font-size: 3.5rem;">Suara Pelanggan 🍹</h1>
            <p class="fs-5 opacity-75">Apa kata mereka tentang kesegaran Sam's Juice?</p>
            <div class="mt-4 d-inline-flex align-items-center gap-3 bg-white bg-opacity-25 px-4 py-2 rounded-pill">
                <div class="text-warning">
                    <i class="fas fa-star"></i> <?php echo $rerata; ?>/5
                </div>
                <div class="vr"></div>
                <div><?php echo $totalUlasan; ?> Ulasan</div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row g-5">
            <?php while($row = oci_fetch_assoc($stid)): ?>
            <div class="col-md-4 mt-5">
                <div class="review-card">
                    <i class="fas fa-quote-right quote-icon"></i>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['NAMA']); ?>&background=198754&color=fff"
                        class="profile-img">
                    <div class="rating-stars">
                        <?php for($i=0; $i<$row['RATING']; $i++): ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                        <?php for($i=$row['RATING']; $i<5; $i++): ?>
                            <i class="far fa-star"></i>
                        <?php endfor; ?>
                    </div>

                    <p class="review-text">"<?php echo $row['KOMENTAR']; ?>"</p>
                    
                    <div class="reviewer-info">
                        <h5><?php echo $row['NAMA']; ?></h5>
                        <p><i class="fas fa-user me-1"></i> Pelanggan Sam's Juice</p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Create Review Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold text-success">Bagikan Pengalamanmu!</h2>
                        <p class="text-muted">Ulasanmu sangat berarti untuk kami terus memberikan yang terbaik.</p>
                    </div>

                    <form action="proses_ulasan.php" method="POST">   
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2 text-muted">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" placeholder="Contoh: Budi Santoso" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2 text-muted">Rating Kesegaran</label>
                                <select name="rating" class="form-select">
                                    <option value="5">Sangat Puas (⭐⭐⭐⭐⭐)</option>
                                    <option value="4">Puas (⭐⭐⭐⭐)</option>
                                    <option value="3">Cukup (⭐⭐⭐)</option>
                                    <option value="2">Kurang (⭐⭐)</option>
                                    <option value="1">Buruk (⭐)</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold mb-2 text-muted">Ceritakan Kesegaranmu</label>
                                <textarea name="ulasan" class="form-control" rows="4" placeholder="Tuliskan ulasanmu di sini..."></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn-submit">Kirim Ulasan Sekarang <i class="fas fa-paper-plane ms-2"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>