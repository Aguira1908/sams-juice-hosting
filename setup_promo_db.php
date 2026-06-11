<?php
include "koneksi.php";

$sql1 = "ALTER TABLE PRODUK ADD IS_PROMO NUMBER(1) DEFAULT 0";
$sql2 = "ALTER TABLE PRODUK ADD DISKON_PERSEN NUMBER DEFAULT 0";

$stid1 = oci_parse($conn, $sql1);
$execute1 = @oci_execute($stid1);

$stid2 = oci_parse($conn, $sql2);
$execute2 = @oci_execute($stid2);

if($execute1 || $execute2){
    echo "Database updated for Promo feature.";
} else {
    echo "Database already updated or error occurred.";
}
?>
