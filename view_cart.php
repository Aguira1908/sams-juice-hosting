<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['username'])) {
    exit("Unauthorized");
}

$username = $_SESSION['username'];

$query = "
SELECT c.ID_CART, c.JUMLAH, p.NAMA_PRODUK, p.HARGA, p.GAMBAR
FROM CART c
JOIN PRODUK p ON c.ID_PRODUK = p.ID_PRODUK
WHERE c.USERNAME = :username
";

$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":username", $username);
oci_execute($stid);

$total = 0;

while ($row = oci_fetch_assoc($stid)) {
    $subtotal = $row['HARGA'] * $row['JUMLAH'];
    $total += $subtotal;
    ?>

    <div class="d-flex align-items-center border rounded p-2 mb-2">
        <img src="gambar/<?php echo $row['GAMBAR']; ?>"
             width="70"
             class="rounded me-3">

        <div class="flex-grow-1">
            <h6 class="mb-1"><?php echo $row['NAMA_PRODUK']; ?></h6>
            <small>Qty: <?php echo $row['JUMLAH']; ?></small><br>
            <strong>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></strong>
        </div>
    </div>

    <?php
}

echo "<hr>";
echo "<h5>Total: Rp " . number_format($total, 0, ',', '.') . "</h5>";
?>