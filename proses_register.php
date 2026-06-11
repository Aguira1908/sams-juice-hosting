<?php
session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = "customer";

    if (empty($username) || empty($password)) {
        die("Semua field wajib diisi!");
    }

    // Cek username
    $queryCheck = "SELECT COUNT(*) AS TOTAL FROM USERS WHERE USERNAME = :username";
    $stidCheck = oci_parse($conn, $queryCheck);

    oci_bind_by_name($stidCheck, ":username", $username);
    oci_execute($stidCheck);

    $rowCheck = oci_fetch_assoc($stidCheck);

    if ($rowCheck['TOTAL'] > 0) {
        echo "
        <script>
            alert('Username sudah terdaftar!');
            window.location='login.php';
        </script>
        ";
        exit;
    }

    // Insert user baru (pakai sequence)
    $query = "INSERT INTO USERS (ID_USER, USERNAME, PASSWORD, ROLE)
              VALUES (user_seq.NEXTVAL, :username, :password, :role)";

    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":username", $username);
    oci_bind_by_name($stid, ":password", $password);
    oci_bind_by_name($stid, ":role", $role);

    $execute = oci_execute($stid);

    if ($execute) {
        echo "
        <script>
            alert('Registrasi berhasil! Silakan login.');
            window.location='login.php';
        </script>
        ";
    } else {
        $e = oci_error($stid);
        echo "Registrasi gagal: " . $e['message'];
    }
}
?>