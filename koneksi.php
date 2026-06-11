<?php

$conn = oci_connect(
  "sams_juice",
  "PassSams123",
  "localhost:1521/xepdb1"
);

if (!$conn) {
  $e = oci_error();
  echo "Koneksi gagal: " . $e['message'];
}
