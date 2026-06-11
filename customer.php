<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "customer"){
    header("Location: login.php");
    exit;
}

// Fetch Menu
$queryProduk = "SELECT * FROM PRODUK ORDER BY NAMA_PRODUK";
$stidProduk = oci_parse($conn, $queryProduk);
oci_execute($stidProduk);

$produk = [];
while($row = oci_fetch_array($stidProduk, OCI_ASSOC | OCI_RETURN_LOBS)){
    $produk[] = $row;
}

// Fetch My Orders
$username = $_SESSION['username'];
$queryOrders = "SELECT * FROM TRANSAKSI WHERE PELANGGAN = :username ORDER BY TANGGAL DESC";
$stidOrders = oci_parse($conn, $queryOrders);
oci_bind_by_name($stidOrders, ":username", $username);
oci_execute($stidOrders);

$orders = [];
while($row = oci_fetch_assoc($stidOrders)){
    $orders[] = $row;
}

// Fetch User Profile
$queryUser = "SELECT * FROM USERS WHERE USERNAME = :username";
$stidUser = oci_parse($conn, $queryUser);
oci_bind_by_name($stidUser, ":username", $username);
oci_execute($stidUser);
$user = oci_fetch_assoc($stidUser);

if(!$user){
    $user = [
        'USERNAME' => $username,
        'EMAIL'    => '',
        'NO_HP'    => '',
        'ALAMAT'   => ''
    ];
}

$tanggal = date('d F Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Sam's Juice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #198754;
            --secondary-color: #34d399;
            --bg-color: #f0fdf4;
            --text-dark: #1f2937;
            --card-bg: white;
        }

        body.dark-mode {
            --bg-color: #111827;
            --text-dark: #f9fafb;
            --card-bg: #1f2937;
        }

        * { font-family: 'Poppins', sans-serif; }

        body {
            background: var(--bg-color);
            color: var(--text-dark);
            transition: 0.3s;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, var(--primary-color), #064e3b);
            padding: 30px 20px;
            color: white;
            z-index: 1000;
            transition: all 0.3s;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 15px 20px;
            border-radius: 16px;
            margin-bottom: 10px;
            transition: 0.3s;
            font-weight: 500;
        }

        .menu-link i { width: 30px; font-size: 20px; }

        .menu-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .active-menu {
            background: white !important;
            color: var(--primary-color) !important;
        }

        /* ===== MAIN ===== */
        .main { margin-left: 280px; padding: 40px; }

        .topbar,
        .order-table,
        .profile-card,
        .settings-card {
            background: var(--card-bg);
            border-radius: 25px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        }

        .topbar {
            padding: 20px 35px;
            margin-bottom: 40px;
        }

        /* ===== WELCOME CARD ===== */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 30px;
            padding: 40px;
            color: white;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::after {
            content: '🍹';
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 150px;
            opacity: 0.2;
        }

        /* ===== PRODUCT CARD ===== */
        .product-card {
            background: var(--card-bg);
            border: none;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transition: 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover { transform: translateY(-8px); }

        .product-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .product-card .card-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 20px;
        }

        .product-card h5 {
            font-size: 20px;
            font-weight: 700;
            min-height: 50px;
            margin-bottom: 8px;
        }

        .product-desc {
            min-height: 50px;
            margin-bottom: 18px;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }

        .product-footer {
            margin-top: auto;
            display: flex;
            gap: 10px;
            align-items: center;
            width: 100%;
        }

        .price-badge {
            flex: 1;
            background: linear-gradient(135deg, #198754, #16a34a);
            color: white;
            height: 46px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .btn-order {
            flex: 1;
            height: 46px;
            border-radius: 14px;
            border: 2px solid #198754;
            background: white;
            color: #198754;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
            transition: 0.3s;
            cursor: pointer;
        }

        .btn-order:hover {
            background: #198754;
            color: white;
        }

        /* ===== BADGE STATUS ===== */
        .badge-status {
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        /* ===== PROFILE & SETTINGS ===== */
        .profile-card,
        .settings-card { padding: 30px; }

        /* ===== CART FAB ===== */
        .cart-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            border: none;
            font-size: 22px;
            box-shadow: 0 8px 25px rgba(25,135,84,0.45);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
            cursor: pointer;
        }

        .cart-fab:hover { transform: scale(1.1); }

        .cart-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== CART ITEM ROW ===== */
        .cart-item-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1.5px solid #198754;
            background: white;
            color: #198754;
            font-weight: 700;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            padding: 0;
        }

        .qty-btn:hover { background: #198754; color: white; }

        /* ===== TOAST ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOutDown {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(20px); }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
        }

        @media (max-width: 768px) {
            .product-footer { flex-direction: column; }
            .price-badge, .btn-order { width: 100%; }
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <div class="logo">
        <i class="fas fa-glass-whiskey"></i> Sam's Juice
    </div>

    <a href="#dashboard" class="menu-link active-menu" onclick="showSection('dashboard', this)">
        <i class="fas fa-th-large"></i> Dashboard
    </a>
    <a href="#menu" class="menu-link" onclick="showSection('menu', this)">
        <i class="fas fa-utensils"></i> Menu Jus
    </a>
    <a href="#orders" class="menu-link" onclick="showSection('orders', this)">
        <i class="fas fa-receipt"></i> Pesanan Saya
    </a>
    <a href="#profile" class="menu-link" onclick="showSection('profile', this)">
        <i class="fas fa-user"></i> Profil Saya
    </a>
    <a href="#settings" class="menu-link" onclick="showSection('settings', this)">
        <i class="fas fa-gear"></i> Settings
    </a>
    <a href="logout.php" class="menu-link mt-5">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<!-- ===== MAIN ===== -->
<div class="main">

    <!-- Topbar -->
    <div class="topbar d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Halo, <?php echo $_SESSION['username']; ?>! 👋</h5>
            <p class="text-muted small mb-0"><?php echo $tanggal; ?></p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-sm-block">
                <p class="mb-0 fw-bold"><?php echo $_SESSION['username']; ?></p>
                <p class="text-muted small mb-0" id="real-time-clock"></p>
            </div>
            <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['username']; ?>&background=198754&color=fff"
                 class="rounded-circle" width="45">
        </div>
    </div>

    <!-- ===== DASHBOARD ===== -->
    <div id="section-dashboard" class="content-section">
        <div class="welcome-card">
            <h1 class="fw-bold mb-3">Mau Minum Jus Apa Hari Ini?</h1>
            <p class="opacity-75 mb-4">Nikmati kesegaran buah asli setiap hari dengan koleksi jus terbaik kami.</p>
            <button class="btn btn-light rounded-pill px-4 py-2 fw-bold text-success"
                    onclick="showSection('menu', document.querySelector('a[href=\'#menu\']'))">
                Pesan Sekarang
            </button>
        </div>

        <h4 class="fw-bold mb-4">Rekomendasi Terlaris</h4>
        <div class="row g-4">
            <?php
            $gambarRekomendasi = [
                'Jus Alpukat' => 'alpukat.jpg',
                'Jus Jeruk'   => 'jeruk.jpg',
                'Jus Mangga'  => 'mangga.jpg'
            ];
            $rekomendasi = ['Jus Alpukat', 'Jus Jeruk', 'Jus Mangga'];
            foreach($rekomendasi as $namaJus):
            ?>
            <div class="col-md-4">
                <div class="product-card">
                    <img src="image/<?php echo $gambarRekomendasi[$namaJus]; ?>" class="product-img"
                         onerror="this.src='https://images.unsplash.com/photo-1600266175173-05bb70dc20a6?auto=format&fit=crop&w=500&q=80'">
                    <div class="card-body p-4">
                        <h5 class="fw-bold"><?php echo $namaJus; ?></h5>
                        <p class="text-muted small">Jus segar berkualitas tinggi dengan nutrisi maksimal.</p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ===== MENU JUS ===== -->
    <div id="section-menu" class="content-section" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Menu Lengkap</h3>
            <button class="btn btn-success rounded-pill px-4 fw-bold d-flex align-items-center gap-2"
                    onclick="openCartModal()" id="btnLihatKeranjang" style="display:none !important;">
                <i class="fas fa-shopping-cart"></i>
                Lihat Keranjang
                <span class="badge bg-white text-success ms-1" id="cartCountMenu">0</span>
            </button>
        </div>

        <div class="row g-4">
            <?php foreach($produk as $p): ?>
            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                <div class="product-card">
                    <img src="image/<?php echo $p['GAMBAR']; ?>"
                         class="product-img"
                         alt="<?php echo $p['NAMA_PRODUK']; ?>"
                         onerror="this.src='https://images.unsplash.com/photo-1600266175173-05bb70dc20a6?auto=format&fit=crop&w=500&q=80'">

                    <div class="card-body p-4 d-flex flex-column">
                        <h5 class="fw-bold mb-2"><?php echo $p['NAMA_PRODUK']; ?></h5>
                        <p class="text-muted small mb-3 product-desc"><?php echo $p['DESKRIPSI']; ?></p>

                        <div class="product-footer">
                            <div class="price-badge">
                                Rp <?php echo number_format($p['HARGA']); ?>
                            </div>
                            <button class="btn-order"
                                onclick="addToCart(
                                    '<?php echo $p['ID_PRODUK']; ?>',
                                    '<?php echo addslashes($p['NAMA_PRODUK']); ?>',
                                    <?php echo $p['HARGA']; ?>
                                )">
                                <i class="fas fa-cart-plus"></i> Pesan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ===== PESANAN SAYA ===== -->
    <div id="section-orders" class="content-section" style="display:none;">
        <h3 class="fw-bold mb-4">Status Pesanan Saya</h3>
        <div class="order-table table-responsive p-4">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Menu</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($orders as $o): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><span class="fw-bold"><?php echo $o['ID_TRANSAKSI']; ?></span></td>
                        <td><?php echo date('d M Y', strtotime($o['TANGGAL'])); ?></td>
                        <td><?php echo $o['MENU']; ?></td>
                        <td>Rp <?php echo number_format($o['TOTAL']); ?></td>
                        <td><?php echo $o['METODE']; ?></td>
                        <td>
                            <?php
                            $status = $o['STATUS'];
                            if($status == 'Selesai'){
                                echo '<span class="badge bg-success badge-status">Selesai</span>';
                            } elseif($status == 'Diproses'){
                                echo '<span class="badge bg-primary badge-status">Diproses</span>';
                            } elseif($status == 'Menunggu'){
                                echo '<span class="badge bg-warning text-dark badge-status">Menunggu</span>';
                            } else {
                                echo '<span class="badge bg-danger badge-status">'.$status.'</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="cetak_invoice.php?id=<?php echo $o['ID_TRANSAKSI']; ?>"
                               target="_blank"
                               class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== PROFIL ===== -->
    <div id="section-profile" class="content-section" style="display:none;">
        <h3 class="fw-bold mb-4">Profil Saya</h3>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="profile-card text-center">
                    <img src="https://ui-avatars.com/api/?name=<?php echo $user['USERNAME']; ?>&background=198754&color=fff&size=200"
                         class="rounded-circle mb-4" width="130">
                    <h4 class="fw-bold"><?php echo $user['USERNAME']; ?></h4>
                    <p class="text-muted">Customer Sam's Juice</p>
                    <p>Total Pesanan: <strong><?php echo count($orders); ?></strong></p>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="profile-card">
                    <form action="update_customer_profile.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" name="username" class="form-control"
                                       value="<?php echo $user['USERNAME']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?php echo $user['EMAIL']; ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor HP</label>
                            <input type="text" name="no_hp" class="form-control"
                                   value="<?php echo $user['NO_HP']; ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Alamat</label>
                            <textarea name="alamat" rows="4" class="form-control"><?php echo $user['ALAMAT']; ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SETTINGS ===== -->
    <div id="section-settings" class="content-section" style="display:none;">
        <h3 class="fw-bold mb-4">Settings</h3>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="settings-card">
                    <h5 class="fw-bold mb-4">Dark Mode</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="darkModeToggle"
                               style="transform: scale(1.5);">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="settings-card">
                    <h5 class="fw-bold mb-4">Ganti Password</h5>
                    <form action="update_password_customer.php" method="POST">
                        <div class="mb-3">
                            <input type="password" name="old_password" class="form-control"
                                   placeholder="Password Lama">
                        </div>
                        <div class="mb-3">
                            <input type="password" name="new_password" class="form-control"
                                   placeholder="Password Baru">
                        </div>
                        <div class="mb-3">
                            <input type="password" name="confirm_password" class="form-control"
                                   placeholder="Konfirmasi Password">
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div><!-- end .main -->

<!-- ===== FLOATING CART BUTTON ===== -->
<button class="cart-fab" onclick="openCartModal()" id="cartFab">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-badge" id="cartCount">0</span>
</button>

<!-- ===== MODAL KERANJANG ===== -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius:25px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-shopping-cart me-2 text-success"></i>Keranjang Belanja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">

                <!-- Daftar item keranjang -->
                <div id="cartItemsList"></div>

                <!-- Total keranjang -->
                <div class="p-3 bg-light rounded-4 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Keranjang</span>
                        <h4 class="mb-0 fw-bold text-success" id="cartTotalDisplay">Rp 0</h4>
                    </div>
                </div>

                <!-- Form checkout -->
                <form action="proses_order_customer.php" method="POST" id="cartCheckoutForm">
                    <input type="hidden" name="cart_data" id="cartDataInput">

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Metode Bayar</label>
                            <select name="metode" id="cartMetode" class="form-select">
                                <option value="Tunai">Tunai</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Nomor HP</label>
                            <input type="text" name="no_hp" class="form-control"
                                   placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat Pengantaran</label>
                        <textarea name="alamat" rows="3" class="form-control"
                                  placeholder="Masukkan alamat lengkap"></textarea>
                    </div>

                    <div id="qrisBoxCart" class="text-center mb-3" style="display:none;">
                        <img src="image/qris.png" width="220" class="img-fluid rounded shadow">
                        <p class="mt-3 fw-semibold text-success">Silakan scan QRIS untuk pembayaran</p>
                    </div>

                    <button type="submit" id="btnCheckoutCart"
                            class="btn btn-success w-100 py-3 fw-bold">
                        <i class="fas fa-check-circle me-2"></i>Konfirmasi Pesanan
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

// ===================================================
// NAVIGATION
// ===================================================
function showSection(id, element) {
    document.querySelectorAll('.content-section').forEach(s => s.style.display = 'none');
    document.getElementById('section-' + id).style.display = 'block';
    document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('active-menu'));
    element.classList.add('active-menu');
}

// ===================================================
// CART
// ===================================================
let cart = [];
const cartModalEl  = document.getElementById('cartModal');
const cartModalObj = new bootstrap.Modal(cartModalEl);

function addToCart(id, name, price) {
    const existing = cart.find(i => i.id === id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ id: id, name: name, price: price, qty: 1 });
    }
    updateCartUI();
    showToast('<i class="fas fa-check-circle me-2"></i>' + name + ' ditambahkan ke keranjang!');
}

function updateCartUI() {
    const totalHarga = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const totalQty   = cart.reduce((s, i) => s + i.qty, 0);

    // Update badge FAB
    document.getElementById('cartCount').innerText = totalQty;
    const fab = document.getElementById('cartFab');
    fab.style.display = totalQty > 0 ? 'flex' : 'none';

    // Update badge di header menu
    document.getElementById('cartCountMenu').innerText = totalQty;
    const btnKeranjang = document.getElementById('btnLihatKeranjang');
    btnKeranjang.style.display = totalQty > 0 ? 'flex' : 'none';

    // Update total display
    document.getElementById('cartTotalDisplay').innerText =
        'Rp ' + totalHarga.toLocaleString('id-ID');

    // Render daftar item
    const list = document.getElementById('cartItemsList');

    if (cart.length === 0) {
        list.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted fw-semibold">Keranjang masih kosong</p>
                <p class="text-muted small">Tambahkan jus favoritmu dulu!</p>
            </div>`;
        document.getElementById('btnCheckoutCart').disabled = true;
        return;
    }

    document.getElementById('btnCheckoutCart').disabled = false;

    list.innerHTML = cart.map((item, idx) => `
        <div class="cart-item-row">
            <div class="flex-grow-1">
                <div class="fw-bold">${item.name}</div>
                <div class="text-muted small">Rp ${item.price.toLocaleString('id-ID')} / pcs</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="qty-btn" onclick="changeQty(${idx}, -1)">−</button>
                <span class="fw-bold" style="min-width:26px;text-align:center">${item.qty}</span>
                <button class="qty-btn" onclick="changeQty(${idx}, 1)">+</button>
            </div>
            <div class="fw-bold text-success" style="min-width:95px;text-align:right">
                Rp ${(item.price * item.qty).toLocaleString('id-ID')}
            </div>
            <button class="btn btn-sm btn-outline-danger rounded-circle"
                    onclick="removeItem(${idx})"
                    style="width:32px;height:32px;padding:0;flex-shrink:0;">
                <i class="fas fa-times" style="font-size:11px"></i>
            </button>
        </div>
    `).join('');
}

function changeQty(idx, delta) {
    cart[idx].qty += delta;
    if (cart[idx].qty <= 0) cart.splice(idx, 1);
    updateCartUI();
}

function removeItem(idx) {
    cart.splice(idx, 1);
    updateCartUI();
}

function openCartModal() {
    updateCartUI();
    cartModalObj.show();
}

// Serialize cart ke hidden input sebelum submit
document.getElementById('cartCheckoutForm').addEventListener('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        showToast('<i class="fas fa-exclamation-circle me-2"></i>Keranjang masih kosong!', '#ef4444');
        return;
    }
    document.getElementById('cartDataInput').value = JSON.stringify(cart);
});

// Toggle QRIS cart
document.getElementById('cartMetode').addEventListener('change', function() {
    document.getElementById('qrisBoxCart').style.display =
        this.value === 'QRIS' ? 'block' : 'none';
});

// ===================================================
// TOAST NOTIFIKASI
// ===================================================
function showToast(msg, bgColor = '#198754') {
    const toast = document.createElement('div');
    toast.innerHTML = msg;
    toast.style.cssText = `
        position: fixed;
        bottom: 110px;
        right: 30px;
        background: ${bgColor};
        color: white;
        padding: 13px 22px;
        border-radius: 16px;
        font-weight: 600;
        font-size: 14px;
        z-index: 99999;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        animation: fadeInUp 0.3s ease;
        max-width: 320px;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'fadeOutDown 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

// ===================================================
// CLOCK
// ===================================================
function updateClock() {
    const now = new Date();
    document.getElementById('real-time-clock').innerText =
        now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) +
        ' | ' + now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
}
setInterval(updateClock, 1000);
updateClock();

// ===================================================
// DARK MODE
// ===================================================
const darkToggle = document.getElementById('darkModeToggle');
if (localStorage.getItem('darkMode') === 'on') {
    document.body.classList.add('dark-mode');
    darkToggle.checked = true;
}
darkToggle.addEventListener('change', function() {
    document.body.classList.toggle('dark-mode', this.checked);
    localStorage.setItem('darkMode', this.checked ? 'on' : 'off');
});

// ===================================================
// INIT — buka section sesuai hash URL
// ===================================================
window.onload = () => {
    const hash = window.location.hash.replace('#', '') || 'dashboard';
    document.querySelectorAll('.menu-link').forEach(link => {
        if (link.getAttribute('href') === '#' + hash) link.click();
    });
    updateCartUI(); // pastikan state awal cart kosong
};

</script>
</body>
</html>