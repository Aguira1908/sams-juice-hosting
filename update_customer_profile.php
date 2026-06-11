<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "customer"){
    header("Location: login.php");
    exit;
}

$username_lama = $_SESSION['username'];
$username_baru = $_POST['username'];
$password_baru = $_POST['password'];

if(!empty($password_baru)){

    $query = "UPDATE USERS
              SET USERNAME = :username, PASSWORD = :password
              WHERE USERNAME = :lama";

    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":username", $username_baru);
    oci_bind_by_name($stid, ":password", $password_baru);
    oci_bind_by_name($stid, ":lama", $username_lama);

}else{

    $query = "UPDATE USERS
              SET USERNAME = :username
              WHERE USERNAME = :lama";

    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":username", $username_baru);
    oci_bind_by_name($stid, ":lama", $username_lama);
}

$execute = oci_execute($stid);

if($execute){
    $_SESSION['username'] = $username_baru;

    echo "
    <script>
        alert('Profil berhasil diperbarui!');
        window.location='customer.php#profile';
    </script>
    ";
}
?>