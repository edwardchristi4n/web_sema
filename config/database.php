<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "senat_mahasiswa";

$conn = new mysqli($host, $user, $pass, $db, 3306);

if ($conn->connect_error) {
    error_log('Database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    die('Database connection failed.');
}
?>