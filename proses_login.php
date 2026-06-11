<?php
session_start();
include "koneksi.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM USERS
          WHERE USERNAME = :username
          AND PASSWORD = :password";

$stid = oci_parse($conn, $query);

oci_bind_by_name($stid, ":username", $username);
oci_bind_by_name($stid, ":password", $password);

oci_execute($stid);

$row = oci_fetch_assoc($stid);

if ($row) {
    $_SESSION['username'] = $row['USERNAME'];
    $_SESSION['role'] = $row['ROLE'];

    if ($row['ROLE'] == 'kasir') {
        header("Location: kasir.php");
    } else {
        header("Location: customer.php");
    }
    exit;
} else {
    echo "
    <script>
        alert('Login gagal! Username atau password salah.');
        window.location='login.php';
    </script>
    ";
}
?>