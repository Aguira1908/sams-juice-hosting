<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("Location: login.php");
    exit;
}

$username_lama = $_SESSION['username'];
$username_baru = $_POST['username'];
$password_baru = $_POST['password'];

$queryUser = "SELECT * FROM USERS WHERE USERNAME = :username";
$stidUser = oci_parse($conn, $queryUser);
oci_bind_by_name($stidUser, ":username", $username_lama);
oci_execute($stidUser);

$user = oci_fetch_assoc($stidUser);

$foto = !empty($user['FOTO']) ? $user['FOTO'] : 'default-profile.png';


// upload foto
if (isset($_FILES['foto']) && $_FILES['foto']['name'] != '') {

    // buat folder uploads kalau belum ada
    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    $namaFile = time() . "_" . basename($_FILES['foto']['name']);
    $tmp = $_FILES['foto']['tmp_name'];
    $folder = "uploads/" . $namaFile;

    // cek upload berhasil
    if (move_uploaded_file($tmp, $folder)) {
        $foto = $namaFile;
    } else {
        echo "
        <script>
            alert('Upload foto gagal! Cek folder uploads.');
            window.location='profil_kasir.php';
        </script>
        ";
        exit;
    }
}


// update data
if (!empty($password_baru)) {

    $query = "UPDATE USERS
              SET USERNAME = :username,
                  PASSWORD = :password,
                  FOTO = :foto
              WHERE USERNAME = :username_lama";

    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":username", $username_baru);
    oci_bind_by_name($stid, ":password", $password_baru);
    oci_bind_by_name($stid, ":foto", $foto);
    oci_bind_by_name($stid, ":username_lama", $username_lama);

} else {

    $query = "UPDATE USERS
              SET USERNAME = :username,
                  FOTO = :foto
              WHERE USERNAME = :username_lama";

    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":username", $username_baru);
    oci_bind_by_name($stid, ":foto", $foto);
    oci_bind_by_name($stid, ":username_lama", $username_lama);
}

$execute = oci_execute($stid);

if ($execute) {

    $_SESSION['username'] = $username_baru;

    echo "
    <script>
        alert('Profil berhasil diperbarui!');
        window.location='profil_kasir.php';
    </script>
    ";

} else {

    $e = oci_error($stid);
    echo $e['message'];
}
?>