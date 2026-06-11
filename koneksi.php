<?php

$conn = oci_connect(
"samsjuice",
"sams123",
"localhost/FREEPDB1"
);

if(!$conn){
    $e = oci_error();
    echo "Koneksi gagal: ".$e['message'];
}

?>