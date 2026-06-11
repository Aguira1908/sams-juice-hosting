<?php
include "koneksi.php";

$nama     = $_POST['nama'];
$rating   = $_POST['rating'];
$komentar = $_POST['ulasan'];

$id = rand(100, 99999);

$query = "INSERT INTO ULASAN (ID_ULASAN, NAMA, KOMENTAR, RATING)
          VALUES (:id, :nama, :komentar, :rating)";

$stid = oci_parse($conn, $query);

oci_bind_by_name($stid, ":id", $id);
oci_bind_by_name($stid, ":nama", $nama);
oci_bind_by_name($stid, ":komentar", $komentar);
oci_bind_by_name($stid, ":rating", $rating);

$execute = oci_execute($stid);

if($execute){
    echo "
    <script>
        alert('Terima kasih atas ulasan Anda!');
        window.location='ulasan.php';
    </script>
    ";
} else {
    $e = oci_error($stid);
    echo "Gagal mengirim ulasan: " . $e['message'];
}
?>