<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "customer"){
    header("Location: login.php");
    exit;
}

$pelanggan  = $_SESSION['username'];
$tanggal    = date('d-M-Y');
$metode     = $_POST['metode'];
$no_hp      = $_POST['no_hp'];
$alamat     = $_POST['alamat'];
$status     = "Menunggu";

// ============================================================
// CEK: apakah dari keranjang (cart) atau order single lama
// ============================================================

if(!empty($_POST['cart_data'])) {

    // ===== MODE KERANJANG =====
    $cartItems = json_decode($_POST['cart_data'], true);

    if(!$cartItems || count($cartItems) === 0){
        echo "<script>alert('Keranjang kosong!'); history.back();</script>";
        exit;
    }

    $successCount = 0;
    $failMessages = [];

    foreach($cartItems as $item) {
        $id         = "TRX-" . date('Ymd') . "-" . rand(1000,9999);
        $id_produk  = $item['id'];
        $qty        = (int) $item['qty'];
        $nama_menu  = $item['name'];
        $harga      = (float) $item['price'];
        $total      = $harga * $qty;
        $menu       = $nama_menu . " (" . $qty . ")";

        $query = "INSERT INTO TRANSAKSI 
        (ID_TRANSAKSI, TANGGAL, PELANGGAN, MENU, TOTAL, METODE, STATUS, NO_HP, ALAMAT) 
        VALUES 
        (:id, TO_DATE(:tanggal, 'DD-MON-YYYY'), :pelanggan, :menu, :total, :metode, :status, :no_hp, :alamat)";

        $stid = oci_parse($conn, $query);

        oci_bind_by_name($stid, ":id",        $id);
        oci_bind_by_name($stid, ":tanggal",   $tanggal);
        oci_bind_by_name($stid, ":pelanggan", $pelanggan);
        oci_bind_by_name($stid, ":menu",      $menu);
        oci_bind_by_name($stid, ":total",     $total);
        oci_bind_by_name($stid, ":metode",    $metode);
        oci_bind_by_name($stid, ":status",    $status);
        oci_bind_by_name($stid, ":no_hp",     $no_hp);
        oci_bind_by_name($stid, ":alamat",    $alamat);

        $execute = oci_execute($stid);

        if($execute) {
            // Kurangi stok
            $queryStok = "UPDATE PRODUK SET STOK = STOK - :qty WHERE ID_PRODUK = :id_produk";
            $stidStok  = oci_parse($conn, $queryStok);
            oci_bind_by_name($stidStok, ":qty",       $qty);
            oci_bind_by_name($stidStok, ":id_produk", $id_produk);
            oci_execute($stidStok);

            $successCount++;
        } else {
            $e = oci_error($stid);
            $failMessages[] = $nama_menu . ": " . $e['message'];
        }
    }

    if($successCount > 0 && count($failMessages) === 0) {
        echo "
        <script>
            alert('$successCount pesanan berhasil dibuat!');
            window.location='customer.php#orders';
        </script>
        ";
    } elseif($successCount > 0 && count($failMessages) > 0) {
        $gagal = implode('\n', $failMessages);
        echo "
        <script>
            alert('$successCount pesanan berhasil, tapi ada yang gagal:\n$gagal');
            window.location='customer.php#orders';
        </script>
        ";
    } else {
        $gagal = implode('\n', $failMessages);
        echo "
        <script>
            alert('Semua pesanan gagal:\n$gagal');
            history.back();
        </script>
        ";
    }

} else {

    // ===== MODE SINGLE (lama) — tetap berfungsi =====
    $id        = "TRX-" . date('Ymd') . "-" . rand(1000,9999);
    $id_produk = $_POST['id_produk'];
    $qty       = $_POST['qty'];
    $menu      = $_POST['nama_menu'] . " (" . $qty . ")";
    $total     = $_POST['harga_satuan'] * $qty;

    $query = "INSERT INTO TRANSAKSI 
    (ID_TRANSAKSI, TANGGAL, PELANGGAN, MENU, TOTAL, METODE, STATUS, NO_HP, ALAMAT) 
    VALUES 
    (:id, TO_DATE(:tanggal, 'DD-MON-YYYY'), :pelanggan, :menu, :total, :metode, :status, :no_hp, :alamat)";

    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":id",        $id);
    oci_bind_by_name($stid, ":tanggal",   $tanggal);
    oci_bind_by_name($stid, ":pelanggan", $pelanggan);
    oci_bind_by_name($stid, ":menu",      $menu);
    oci_bind_by_name($stid, ":total",     $total);
    oci_bind_by_name($stid, ":metode",    $metode);
    oci_bind_by_name($stid, ":status",    $status);
    oci_bind_by_name($stid, ":no_hp",     $no_hp);
    oci_bind_by_name($stid, ":alamat",    $alamat);

    $execute = oci_execute($stid);

    if($execute) {
        $queryStok = "UPDATE PRODUK SET STOK = STOK - :qty WHERE ID_PRODUK = :id_produk";
        $stidStok  = oci_parse($conn, $queryStok);
        oci_bind_by_name($stidStok, ":qty",       $qty);
        oci_bind_by_name($stidStok, ":id_produk", $id_produk);
        oci_execute($stidStok);

        echo "
        <script>
            alert('Pesanan berhasil dibuat!');
            window.location='customer.php#orders';
        </script>
        ";
    } else {
        $e = oci_error($stid);
        echo 'Gagal memesan: ' . $e['message'];
    }
}
?>