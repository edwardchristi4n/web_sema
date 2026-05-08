<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "senat_mahasiswa";

$conn = new mysqli($host, $user, $pass, $db, 3306);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>