<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

$password_lama = $_POST['password_lama'];
$password_baru = $_POST['password_baru'];
$konfirmasi    = $_POST['konfirmasi_password'];

// Ambil data user
$query = "SELECT * FROM USERS WHERE USERNAME = :username";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":username", $username);
oci_execute($stid);

$user = oci_fetch_assoc($stid);

if (!$user) {
    echo "
    <script>
        alert('User tidak ditemukan!');
        window.location='settings_kasir.php';
    </script>
    ";
    exit;
}

// cek password lama
if ($user['PASSWORD'] != $password_lama) {
    echo "
    <script>
        alert('Password lama salah!');
        window.location='settings_kasir.php';
    </script>
    ";
    exit;
}

// cek konfirmasi password
if ($password_baru != $konfirmasi) {
    echo "
    <script>
        alert('Konfirmasi password tidak cocok!');
        window.location='settings_kasir.php';
    </script>
    ";
    exit;
}

// update password
$queryUpdate = "UPDATE USERS 
                SET PASSWORD = :password_baru
                WHERE USERNAME = :username";

$stidUpdate = oci_parse($conn, $queryUpdate);

oci_bind_by_name($stidUpdate, ":password_baru", $password_baru);
oci_bind_by_name($stidUpdate, ":username", $username);

$execute = oci_execute($stidUpdate);

if ($execute) {
    echo "
    <script>
        alert('Password berhasil diperbarui!');
        window.location='settings_kasir.php';
    </script>
    ";
} else {
    $e = oci_error($stidUpdate);
    echo $e['message'];
}
?>